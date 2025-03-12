<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $table = 'language';
    protected $primaryKey = 'language_id';
    public $timestamps = false;

    protected $fillable = [
        'language_id',
        'name',
        'last_update',
    ];

    public function film()
    {
        return $this->hasMany(Film::class, 'language_id', 'language_id');
    }
    public function filmOriginal()
    {
        return $this->hasMany(Film::class, 'original_language_id', 'language_id');
    }
}
