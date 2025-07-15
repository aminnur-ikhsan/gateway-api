<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvidersModel extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'custom.providers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'token',
        'salt',
        'created_at',
        'updated_at',
    ];
}
