<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinsInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'coin_id','description','links','images','scores','community'
    ];

    public function coin() {
        return $this->belongsTo(Coins::class);
    }
}
