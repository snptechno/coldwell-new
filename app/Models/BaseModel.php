<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Shared defaults for every Coldwell table model.
 *
 * The legacy schema has no created_at/updated_at convention, no soft deletes
 * and no foreign keys, so timestamps and soft deletes are off. Rows are plain
 * arrays (CI3 code used result_array()/row_array() almost everywhere).
 *
 * Every query in models built on this class must go through the Query Builder
 * or query bindings - never string concatenation (audit DL-01/SEC-03).
 */
abstract class BaseModel extends Model
{
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;
    protected $protectFields    = true;
    protected $skipValidation   = false;

    /**
     * Status toggle used by ~20 admin modules (CI3: change_status_* in home_model.php).
     * The status column name differs per table (`status` or `is_active`).
     */
    public function setStatus(int $id, int $status, string $column = 'status'): bool
    {
        return $this->builder()
            ->where($this->primaryKey, $id)
            ->update([$column => $status === 1 ? 1 : 0]);
    }
}
