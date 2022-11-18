<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Book extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'author_id',
        'name',
        'year',
    ];

    public function author() {
        return $this->belongsTo(Author::class);
    }
}
