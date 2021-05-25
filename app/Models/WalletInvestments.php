<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletInvestments extends Model
{
    use HasFactory;

    protected $fillable = ['wallet_id','trading_company_id','operation_type','quantity','value','total_amount'];


    public function wallet() {
        return $this->belongsTo(Wallets::class,'wallet_id');
    }

    public function trading_company() {
        return $this->belongsTo(TradingCompanys::class);
    }
}
