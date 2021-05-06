<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallets extends Model
{
    use HasFactory;


    protected $fillable = ['admin_user_id','trading_company_id','coin_id','quantity','value','value_original','is_active'];


    public function trading_company() {
        return $this->belongsTo(TradingCompanys::class);
    }

    public function coin() {
        return $this->belongsTo(CoinsCurrentValues::class,'coin_id','coin_id');
    }

    public function investments() {
        return $this->hasMany(WalletInvestments::class,'wallet_id');
    }
}
