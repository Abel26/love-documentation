<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoryQuote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quote',
        'author',
    ];

    /**
     * Get a random quote for today
     */
    public static function getDailyQuote()
    {
        $dayOfYear = now()->dayOfYear;
        $quoteCount = self::count();
        
        // Use day of year to get consistent quote for the same day
        $quoteIndex = ($dayOfYear - 1) % $quoteCount;
        
        return self::offset($quoteIndex)->first();
    }
}
