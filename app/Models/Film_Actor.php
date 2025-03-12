<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film_Actor extends Model
{
    use HasFactory;

    protected $table = 'film_actor';
    protected $primaryKey = ['actor_id', 'film_id'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'actor_id',
        'film_id',
        'last_update',
    ];

    public function actor()
    {
        return $this->belongsTo(Actor::class, 'actor_id', 'actor_id');
    }
    public function film()
    {
        return $this->belongsTo(Film::class, 'film_id', 'film_id');
    }
}
