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
     * Admin user row matching username and password, or null
     * (CI3: login_model::login). The stored value is the md5 of the password,
     * as in CI3. Setting the session stays in the controller.
     */
    public function findByCredentials(string $username, string $password): ?array
    {
        return $this->builder()
            ->where('username', $username)
            ->where('password', md5($password))
            ->get()
            ->getRowArray();
    }
}
