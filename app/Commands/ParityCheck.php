<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Throwable;

/**
 * php spark parity:check [domain]
 *
 * Dev-only verification for the CI3 -> CI4 data-layer port: runs every suite in
 * tests/_support/Parity and compares old CI3 SQL results with the new model
 * methods on the same database (staging MySQL 8 with the live data).
 */
class ParityCheck extends BaseCommand
{
    protected $group       = 'Coldwell';
    protected $name        = 'parity:check';
    protected $description = 'Compare CI3 query results with the ported CI4 model methods.';
    protected $usage       = 'parity:check [domain]';

    public function run(array $params)
    {
        if (ENVIRONMENT === 'production') {
            CLI::error('parity:check is a development tool.');

            return EXIT_ERROR;
        }

        $only  = $params[0] ?? null;
        $dir   = ROOTPATH . 'tests/_support/Parity/';
        $db    = db_connect();
        $total = $fail = 0;

        foreach (glob($dir . '*Parity.php') ?: [] as $file) {
            $class = 'Tests\\Support\\Parity\\' . basename($file, '.php');
            if (! class_exists($class)) {
                require_once $file;
            }
            $suite = new $class();
            if ($only !== null && $suite->domain() !== $only) {
                continue;
            }
            CLI::write('== ' . $suite->domain(), 'yellow');

            foreach ($suite->checks() as $c) {
                $total++;
                $mode = $c['compare'] ?? 'rows';
                try {
                    $oldRows = $db->query($c['old_sql'])->getResultArray();
                    $old     = match ($mode) {
                        'row'    => $oldRows[0] ?? null,
                        'count'  => count($oldRows),
                        'scalar' => $oldRows === [] ? null : array_values($oldRows[0])[0],
                        default  => $oldRows,
                    };
                    $new = ($c['new'])();
                    if (is_object($new)) {
                        $new = json_decode(json_encode($new), true);
                    }
                    if ($mode === 'count' && is_array($new)) {
                        $new = count($new);
                    }
                    $ok = $mode === 'set'
                        ? $this->canon($old, true) === $this->canon($new, true)
                        : $this->canon($old) === $this->canon($new);
                } catch (Throwable $e) {
                    $ok  = false;
                    $old = $new = null;
                    $c['note'] = 'EXCEPTION: ' . $e->getMessage();
                }

                $n = is_array($old) ? count($old) : (string) json_encode($old);
                if ($ok) {
                    CLI::write('  PASS  ' . $c['label'] . "  ({$mode}: {$n})" . (isset($c['note']) ? '  - ' . $c['note'] : ''), 'green');
                } else {
                    $fail++;
                    CLI::write('  FAIL  ' . $c['label'] . "  ({$mode})" . (isset($c['note']) ? '  - ' . $c['note'] : ''), 'red');
                    CLI::write('        old: ' . substr((string) json_encode($old), 0, 300));
                    CLI::write('        new: ' . substr((string) json_encode($new), 0, 300));
                }
            }
        }

        CLI::newLine();
        CLI::write("parity: {$total} checks, " . ($total - $fail) . " passed, {$fail} failed", $fail ? 'red' : 'green');

        return $fail ? EXIT_ERROR : EXIT_SUCCESS;
    }

    /** Normalise values so int/string differences from drivers don't count as a mismatch. */
    private function canon(mixed $v, bool $unordered = false): string
    {
        $norm = static function ($x) use (&$norm) {
            if (is_array($x)) {
                return array_map($norm, $x);
            }

            return $x === null ? null : (string) $x;
        };
        $v = $norm($v);
        if ($unordered && is_array($v)) {
            $rows = array_map(static fn ($r) => json_encode($r), $v);
            sort($rows);

            return json_encode($rows);
        }

        return (string) json_encode($v);
    }
}
