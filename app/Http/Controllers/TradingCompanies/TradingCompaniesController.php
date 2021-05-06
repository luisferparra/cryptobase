<?php

namespace App\Http\Controllers\TradingCompanies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TradingCompanys;

class TradingCompaniesController extends Controller
{

    /**
     * Form Function.
     * 
     * Returns a Collection (or array if $returnAsArray == true). Function used for Select from Forms
     *
     * @param boolean $returnAsArray
     * @return mixed Array or Collection
     */
    public static function getTradingCompanies($returnAsArray = false)
    {
        $dat = TradingCompanys::select('id', 'name')->where('is_active', 1)->get();
        if (!$returnAsArray)
            return $dat;
        $newCollection = $dat->mapWithKeys(function ($item) {
            return [$item->id => $item->name];
        });
        return $newCollection->all();
    }
}
