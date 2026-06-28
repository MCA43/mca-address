<?php

namespace Mca\Address\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Mca\Address\Models\District;
use Mca\Address\Models\Neighborhood;
use Mca\Address\Support\AddressMatchKey;
use Mca\Address\Support\AddressSlug;
use RuntimeException;

class TurkeyNeighborhoodImporter
{
    /** @var array<string, int> */
    private array $districtMap = [];

    /** @var array<string, true> */
    private array $missingDistrictKeys = [];

    public function resolveDataPath(?string $source, bool $forceDownload = false): string
    {
        if ($source !== null && $source !== '') {
            if (! is_file($source)) {
                throw new RuntimeException("Kaynak dosya bulunamadı: {$source}");
            }

            return $source;
        }

        $cachePath = (string) config('address.import.turkey.cache_path', 'mca-address/neighbourhoods.json');
        $disk = Storage::disk('local');

        if ($disk->exists($cachePath) && ! $forceDownload) {
            return $disk->path($cachePath);
        }

        $url = (string) config(
            'address.import.turkey.neighbourhoods_url',
            'https://raw.githubusercontent.com/muratgozel/turkey-neighbourhoods/master/src/data/neighbourhoods.json',
        );

        $response = Http::timeout(180)->retry(2, 1000)->get($url);

        if (! $response->successful()) {
            throw new RuntimeException("Veri indirilemedi (HTTP {$response->status()}): {$url}");
        }

        $disk->makeDirectory(dirname($cachePath));
        $disk->put($cachePath, $response->body());

        return $disk->path($cachePath);
    }

    /**
     * @return list<array{0: string, 1: string, 2: string, 3: string, 4: string}>
     */
    public function loadRows(string $path): array
    {
        $contents = file_get_contents($path);

        if ($contents === false || $contents === '') {
            throw new RuntimeException("JSON okunamadı: {$path}");
        }

        $rows = json_decode($contents, true);

        if (! is_array($rows)) {
            throw new RuntimeException('Geçersiz mahalle JSON formatı.');
        }

        return $rows;
    }

    public function buildDistrictMap(): void
    {
        $this->districtMap = [];
        $this->missingDistrictKeys = [];

        District::query()
            ->where('is_active', true)
            ->whereNotNull('city_code')
            ->get(['id', 'city_code', 'title'])
            ->each(function (District $district): void {
                $key = AddressMatchKey::district((string) $district->city_code, (string) $district->title);
                $this->districtMap[$key] = (int) $district->id;
            });
    }

    public function districtMapCount(): int
    {
        return count($this->districtMap);
    }

    /**
     * @param  list<array{0: string, 1: string, 2: string, 3: string, 4: string}>  $rows
     * @param  callable(int, int): void|null  $onProgress
     * @return array{imported: int, skipped: int, missing_districts: int}
     */
    public function importRows(array $rows, int $chunkSize = 500, ?callable $onProgress = null): array
    {
        if ($this->districtMap === []) {
            $this->buildDistrictMap();
        }

        $table = (new Neighborhood)->getTable();
        $imported = 0;
        $skipped = 0;
        $batch = [];
        $sortCounters = [];
        $now = now()->toDateTimeString();
        $total = count($rows);
        $processed = 0;

        foreach ($rows as $row) {
            $processed++;

            if (! is_array($row) || count($row) < 4) {
                $skipped++;
                $onProgress?->__invoke($processed, $total);

                continue;
            }

            $cityCode = (string) ($row[0] ?? '');
            $districtTitle = (string) ($row[2] ?? '');
            $neighborhoodTitle = trim((string) ($row[3] ?? ''));
            $postalCode = isset($row[4]) ? (string) $row[4] : null;

            if ($cityCode === '' || $districtTitle === '' || $neighborhoodTitle === '') {
                $skipped++;
                $onProgress?->__invoke($processed, $total);

                continue;
            }

            $districtKey = AddressMatchKey::district($cityCode, $districtTitle);
            $districtId = $this->districtMap[$districtKey] ?? null;

            if ($districtId === null) {
                $this->missingDistrictKeys[$districtKey] = true;
                $skipped++;
                $onProgress?->__invoke($processed, $total);

                continue;
            }

            $sortCounters[$districtId] = ($sortCounters[$districtId] ?? 0) + 1;

            $batch[] = [
                'district_id' => $districtId,
                'title' => $neighborhoodTitle,
                'slug' => AddressSlug::from($neighborhoodTitle),
                'postal_code' => $postalCode !== '' ? $postalCode : null,
                'is_active' => true,
                'sort_order' => $sortCounters[$districtId],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= $chunkSize) {
                DB::table($table)->insert($batch);
                $imported += count($batch);
                $batch = [];
            }

            $onProgress?->__invoke($processed, $total);
        }

        if ($batch !== []) {
            DB::table($table)->insert($batch);
            $imported += count($batch);
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'missing_districts' => count($this->missingDistrictKeys),
        ];
    }

    public function clearNeighborhoods(): void
    {
        $table = (new Neighborhood)->getTable();
        DB::table($table)->delete();
    }

    /** @return list<string> */
    public function sampleMissingDistrictKeys(int $limit = 10): array
    {
        return array_slice(array_keys($this->missingDistrictKeys), 0, $limit);
    }
}
