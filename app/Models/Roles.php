<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;

    protected $table = 'rol';
    protected $primaryKey = 'rol_id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'last_update',
    ];
    public function staff()
    {
        return $this->hasMany(Staff::class, 'rol_id', 'rol_id');
    }
}
