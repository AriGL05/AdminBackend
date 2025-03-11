<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    protected $table = 'film';
    protected $primaryKey = 'film_id';
    public $timestamps = false;

    protected $fillable = [
        'film_id',
        'title',
        'description',
        'release_year',
        'language_id',
        'original_language_id',
        'rental_duration',
        'rental_rate',
        'length',
        'replacement_cost',
        'rating',
        'special_features',
        'last_update',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id', 'language_id');
    }
    public function filmText()
    {
        return $this->hasOne(Film_Text::class, 'film_id', 'film_id');
    }
    public function filmActors()
    {
        return $this->hasMany(Film_Actor::class, 'film_id', 'film_id');
    }
    public function filmCategories()
    {
        return $this->hasMany(Film_Category::class, 'film_id', 'film_id');
    }
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'film_id', 'film_id');
    }
}
