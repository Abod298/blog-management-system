<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory , SoftDeletes;

    public const DEFAULT_ROLE = 'User';
    protected $fillable = ['title'];
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
