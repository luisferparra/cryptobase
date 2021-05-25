<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingCompanys extends Model
{
    use HasFactory;

    protected $fillable = ['name','is_api_trader','is_active'];

    public function wallet() {
        return $this->hasMany(Wallets::class);
    }

    public function wallet_investments() {
        return $this->hasMany(WalletInvestments::class);
    }

}
