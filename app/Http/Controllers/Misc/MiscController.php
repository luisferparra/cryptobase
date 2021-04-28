<?php
/**
 * Controlador que tendrá varias funciones.
 */
namespace App\Http\Controllers\Misc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Str;

use App\Models\CoinsCurrentValues;
use App\Http\Controllers\Data\DataManagementController;

class MiscController extends Controller
{
    /**
     * Function that normalize an input string
     *
     * @param string $input
     * @return string
     */
    public static function strNormalize($input) {
        return str_replace([' ','-'],['','_'],$input);
    }

    /**
     * Function for displaying float number with 4 digits
     *
     * @param float $input
     * @return float
     */
    public static function number_format($input,$digits=4) {
        return number_format($input,$digits);
    }

    /**
     * Function for creating a table for display information. This function will get the last (number) entries of a currency
     * and will return an array for display
     *
     * @param string $slug Currency
     * @param integer $number Number of entries
     * @param mixed $columns
     * @return array
     */
    public static function getLastCurrencyEntryForTable($slug,$number=20,$columns=['eur','eur_24h_change','created_at']) {
        $dat = DataManagementController::getLastEntryNumber($slug,$number);
        $out = [];
        foreach ($dat as  $datum) {
            $item = [];
            foreach ($columns as $column) {
                $insert = $datum->$column;
                if ($column != 'created_at') {
                    $insert = static::number_format($datum->$column);
                } 
                $item[$column] = $insert;
            }
            $out[] = $item;
        }
        return $out;
    }

    /**
     * Grid Function. 
     * returns a normalized array with all the active currencies. 
     * For Filter
     *      *
     * @param CoinsCurrentValues $coinCurrent
     * @return array
     */
    public static function getCoinsActive() {
        $dat = CoinsCurrentValues::select('coins.id as id','coins.name as name')
        ->join('coins','coins_current_values.coin_id','=','coins.id')
        ->where('coins.is_active',1)
        ->orderBy('coins.name')
        ->get();
        $newCollection= $dat->mapWithKeys(function ($item) {
            return [$item['id'] => Str::ucfirst($item['name'])];
        });
    
        return $newCollection->all();
    }
}
