<?php

namespace App\Http\Controllers\Data;

/**
 * Controlador que controla las tablas de Data
 */

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use  Illuminate\Support\Str;
use Carbon\Carbon;

use App\Http\Controllers\Misc\MiscController;

use App\Models\Coins;
use App\Models\DataContol;

class DataManagementController extends Controller
{
    /**
     * Static Function for Create Data Structure
     *
     * @return void
     */
    public static function CreateDataStructure(Coins $coin)
    {
        $id = $coin->id;
        $slug = MiscController::strNormalize(Str::lower($coin->symbol));
        $name = Str::ucfirst($coin->name);
        /**
         * First, we will search if the coin already exists. If it exists we will return. If not, we will create the structure
         */
        $datCounter = DataContol::where('coin_id', $id)->count();
        if ($datCounter > 0) {
            return 0;
        }
        //We insert the item
        DataContol::insert([
            [
                'coin_id' => $id,
                'title' => $name,
                'slug' => $slug,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ]
        ]);
        static::_createDataStructure_table($slug);
        return $id;
    }

    /**
     * Static function that creates the data table
     *
     * @param string $slug
     * @return void
     */
    private static function _createDataStructure_table($slug)
    {
        Schema::connection('mysql_data')->create($slug, function ($table) {
            $table->id();
            $table->double('eur', 50, 20);
            $table->double('eur_market_cap', 50, 20)->nullable();
            $table->double('eur_24h_vol', 50, 20)->nullable();
            $table->double('eur_24h_change', 50, 20)->nullable();
            $table->unsignedBigInteger('last_updated_at')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Function that will be retrieve a collection of the last item inserted for a Currency
     *
     * @param string $slug currency symbol
     * @return Collection
     */
    public static function getLastEntry($slug)
    {
        return DB::connection('mysql_data')->table($slug)->latest()->first();
        
    }

    /**
     * Function that will return a collection of the last ($number) entries of the currency
     *
     * @param string $slug currency symbol
     * @param integer $number
     * @return Collection
     */
    public static function getLastEntryNumber($slug,$number=20) {
        return DB::connection('mysql_data')->table($slug)->orderBy('id','desc')->limit($number)->get();
    }


    /**
     * Function that will return a collection of the last entries of currency greater than x hours
     *
     * @param string $slug currency symbol
     * @param integer $hours
     * @return Collection
     */
    public static function getLastEntryByHours($slug,$hours=24) {
        $initDate = Carbon::now()->subHours($hours);
        return DB::connection('mysql_data')->table($slug)->where('created_at','>=',$initDate)->orderBy('id','desc')->get();
    }
}
