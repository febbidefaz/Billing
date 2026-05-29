<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserBilling extends Authenticatable
{
    protected $table = 'UserBilling';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'Nama',
        'Username',
        'Password',
        'Role',
        'Aktif',
    ];

    protected $hidden = [
        'Password',
    ];

    public function getAuthPassword()
    {
        return $this->Password;
    }
}
