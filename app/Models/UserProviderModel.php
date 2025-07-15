<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProviderModel extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'custom.provider_users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_user',
        'id_provider',
        'email',
        'password',
        'name',
        'email_verified_at',
        'remember_token',
        'created_at',
        'updated_at',
    ];
}
