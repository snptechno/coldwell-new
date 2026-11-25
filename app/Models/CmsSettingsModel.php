<?php

namespace App\Models;

/**
 * Table `cms_settings` (key/value site settings; keys in App\Libraries\Cms\SiteSchema::SETTINGS).
 */
class CmsSettingsModel extends BaseModel
{
    protected $table            = 'cms_settings';
    protected $primaryKey       = 'setting_key';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['setting_key', 'value', 'updated_at'];

    /** @return array<string, string> */
    public function map(): array
    {
        $out = [];
        foreach ($this->builder()->get()->getResultArray() as $r) {
            $out[$r['setting_key']] = (string) $r['value'];
        }

        return $out;
    }

    /** @param array<string, string> $values keys must already be allow-listed */
    public function saveMany(array $values): void
    {
        if ($values === []) {
            return;
        }
        $now  = date('Y-m-d H:i:s');
        $rows = [];
        foreach ($values as $k => $v) {
            $rows[] = ['setting_key' => $k, 'value' => $v, 'updated_at' => $now];
        }
        $this->builder()->upsertBatch($rows);
    }
}
