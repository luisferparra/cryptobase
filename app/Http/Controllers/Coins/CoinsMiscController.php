<?php

namespace App\Http\Controllers\Coins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CoinsCurrentValues;
use Illuminate\Support\Str;

use App\Http\Controllers\Misc\MiscController;

class CoinsMiscController extends Controller
{
    /**
     * Form Function. Returns a Collection (or array if $returnsAsArray ==  true) of the available Coins
     * 
     *
     * @param boolean $returnAsArray
     * @return void
     */
    public static function getCoinsAvailableList($returnAsArray = false) {
        $dat = CoinsCurrentValues::select('coins_current_values.coin_id','coins_current_values.slug','coins.name','coins_current_values.eur')
        ->join('coins','coins_current_values.coin_id','=','coins.id')
        ->where('coins.is_active',1)
        ->orderBy('coins.name')
        ->get();
        if (!$returnAsArray)
            return $dat;
        $newCollection = $dat->mapWithKeys(function ($item) {
            return [$item->coin_id => $item->name . " (".Str::upper($item->slug)." - ".MiscController::number_format($item->eur).")"];
        });
        return $newCollection->all();
    }

    /**
     * Function that will return the current value of a currency
     *
     * @param CoinsCurrentValues $coin
     * @return double
     */
    public static function getCoinCurrentValue($coin_id) {
        $dat = CoinsCurrentValues::where('coin_id',$coin_id)->first();
        return $dat->eur;
    }
}
