<?php

$base = dirname(__DIR__, 6);
$syo = $base.'/laravel-syo/database/seeders/Locations';
$out = dirname(__DIR__).'/data';

if (! is_dir($out)) {
    mkdir($out, 0777, true);
}

function parseCreates(string $file, string $model): array
{
    $contents = file_get_contents($file);
    $blocks = [];
    $offset = 0;

    while (($start = strpos($contents, $model.'::create([', $offset)) !== false) {
        $open = strpos($contents, '[', $start);
        $depth = 0;
        $len = strlen($contents);

        for ($i = $open; $i < $len; $i++) {
            $char = $contents[$i];
            if ($char === '[') {
                $depth++;
            } elseif ($char === ']') {
                $depth--;
                if ($depth === 0) {
                    $blocks[] = substr($contents, $open + 1, $i - $open - 1);
                    $offset = $i + 1;
                    break;
                }
            }
        }

        if ($depth !== 0) {
            break;
        }
    }

    return $blocks;
}

$cityBlocks = parseCreates($syo.'/CitySeeder.php', 'City');
$cities = [];
foreach ($cityBlocks as $block) {
    if (! preg_match('/\'title\'\s*=>\s*"([^"]+)"/', $block, $title)) {
        continue;
    }
    if (! preg_match("#'code'\s*=>\s*'TR-(\d+)'#", $block, $code)) {
        continue;
    }
    $cities[] = ['name' => $title[1], 'plate' => $code[1]];
}

$distBlocks = parseCreates($syo.'/DistrictSeeder.php', 'District');
$districts = [];
foreach ($distBlocks as $block) {
    if (! preg_match('/\'city_code\'\s*=>\s*"TR-(\d+)"/', $block, $plate)) {
        continue;
    }
    if (! preg_match('/\'title\'\s*=>\s*"([^"]+)"/', $block, $title)) {
        continue;
    }
    $districts[$plate[1]] ??= [];
    $districts[$plate[1]][] = $title[1];
}

file_put_contents($out.'/turkey-cities.php', "<?php\n\nreturn ".var_export($cities, true).";\n");
file_put_contents($out.'/turkey-districts.php', "<?php\n\nreturn ".var_export($districts, true).";\n");

echo count($cities).' cities, '.count($districts).' plates, '.array_sum(array_map('count', $districts))." districts\n";
