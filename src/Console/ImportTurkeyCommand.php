<?php

namespace Mca\Address\Console;

use Illuminate\Console\Command;
use Mca\Address\Models\District;
use Mca\Address\Models\Neighborhood;
use Mca\Address\Services\TurkeyNeighborhoodImporter;
use Mca\Address\Support\McaAddressLocale;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'mca:address:import-turkey')]
class ImportTurkeyCommand extends Command
{
    protected $signature = 'mca:address:import-turkey
                            {--fresh : Mevcut mahalleleri sil ve yeniden yükle}
                            {--source= : Yerel neighbourhoods.json dosya yolu}
                            {--force-download : Uzak veriyi yeniden indir}
                            {--chunk=500 : Toplu ekleme boyutu}';

    protected $description = 'Türkiye mahalle verisini turkey-neighbourhoods kaynağından içe aktarır';

    public function __construct(
        private readonly TurkeyNeighborhoodImporter $importer,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        McaAddressLocale::apply();

        if (District::query()->count() === 0) {
            $this->components->error(mca_addr('console.import.no_districts'));

            return self::FAILURE;
        }

        $this->components->info(mca_addr('console.import.start'));

        try {
            $path = $this->importer->resolveDataPath(
                $this->option('source') ?: null,
                (bool) $this->option('force-download'),
            );
        } catch (\Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->line('  '.mca_addr('console.import.source', ['path' => $path]));

        $this->components->task(mca_addr('console.import.loading_json'), function () use (&$rows, $path) {
            $rows = $this->importer->loadRows($path);

            return $rows !== [];
        });

        if ($rows === []) {
            $this->components->error(mca_addr('console.import.empty'));

            return self::FAILURE;
        }

        $this->importer->buildDistrictMap();
        $this->line('  '.mca_addr('console.import.district_map', [
            'count' => $this->importer->districtMapCount(),
        ]));

        if ((bool) $this->option('fresh')) {
            $this->components->task(mca_addr('console.import.clearing'), function () {
                $this->importer->clearNeighborhoods();

                return true;
            });
        }

        $chunk = max(100, (int) $this->option('chunk'));
        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        $result = $this->importer->importRows(
            $rows,
            $chunk,
            fn (int $processed, int $total) => $bar->setProgress($processed),
        );

        $bar->finish();
        $this->newLine(2);

        $this->components->info(mca_addr('console.import.done'));
        $this->line('  '.mca_addr('console.import.imported', ['count' => $result['imported']]));
        $this->line('  '.mca_addr('console.import.skipped', ['count' => $result['skipped']]));
        $this->line('  '.mca_addr('console.import.total', ['count' => Neighborhood::query()->count()]));

        if ($result['missing_districts'] > 0) {
            $this->newLine();
            $this->components->warn(mca_addr('console.import.missing_districts', [
                'count' => $result['missing_districts'],
            ]));

            foreach ($this->importer->sampleMissingDistrictKeys() as $key) {
                $this->line("  - {$key}");
            }
        }

        return self::SUCCESS;
    }
}
