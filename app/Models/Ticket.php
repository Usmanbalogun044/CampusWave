<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'price'];

    // price is stored as integer (cents/kobo). Helper to get human price.
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price / 100, 2);
    }
}
