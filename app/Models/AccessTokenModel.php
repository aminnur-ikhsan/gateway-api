<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessTokenModel extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'custom.provider_access_tokens';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_user',
        'hit',
        'token',
        'last_used_at',
        'expires_at',
        'created_at',
        'updated_at',
    ];
}
