<?php

namespace App\Models;

/**
 * Table `user` (MyISAM, latin1_swedish_ci).
 * Columns: id:int, username:varchar, password:varchar
 *
 * Generated from the live schema (2026-11-13). Domain query methods are added
 * below this line by the step-4 port; keep $allowedFields in sync with the table.
 */
class UserModel extends BaseModel
{
    protected $table         = 'user';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'username',
        'password',
    ];

    /**
     * Verify an admin password and transparently upgrade legacy MD5 values.
     * Returns the row only after successful verification.
     */
    public function findByCredentials(string $username, string $password): ?array
    {
        $user = $this->builder()->where('username', $username)->get()->getRowArray();
        if ($user === null || ! isset($user['password'])) {
            return null;
        }

        $stored = (string) $user['password'];
        $isModernHash = password_get_info($stored)['algo'] !== null;
        $valid = $isModernHash
            ? password_verify($password, $stored)
            : hash_equals(strtolower($stored), md5($password));

        if (! $valid) {
            return null;
        }

        if (! $isModernHash) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            if ($newHash === false || ! $this->builder()->where('id', (int) $user['id'])->update(['password' => $newHash])) {
                return null;
            }
            $user['password'] = $newHash;
        } elseif (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            if ($newHash !== false) {
                $this->builder()->where('id', (int) $user['id'])->update(['password' => $newHash]);
            }
        }

        unset($user['password']);

        return $user;
    }

}
