<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinsCurrentValues extends Model
{
    use HasFactory;

    protected $fillable = ['coin_id','slug','eur','eur_24h_change','last_updated_at'];


    public function coin() {
        return $this->belongsTo(Coins::class);
    }

    public function wallet() {
        return $this->hasMany(Wallets::class,'coin_id','coin_id');
    }
}
