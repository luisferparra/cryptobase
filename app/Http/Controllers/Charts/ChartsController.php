<?php

namespace App\Http\Controllers\Charts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Encore\Admin\Widgets\Box;

use App\Http\Controllers\Data\DataManagementController;
use App\Http\Controllers\Misc\MiscController;

class ChartsController extends Controller
{

    /**
     * Generate Random Colors
     *
     * @return string
     */
    private static function  rand_color() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Function for Charts and Graphics. 
     * Returns an array of random colors
     *
     * @param integer $number or colors to random
     * @return array
     */
    private static function getColor($number) {
        $arrColors = [
            "'#188fa7'","'#b57ba6'","'#b8d5b8'","'#ffc6ac'","'#aa5042'",
            "'#706993'","'#9ac2c9'","'#8ea604'","'#725752'","'#73d2de'",
            "'#8b9556'","'#756d54'","'#56282d'","'#907ad6'","'#9b7ede'"
        ];
        if (empty($number)) {
            return [];
        }
        $out = [];

        $arrayRand = array_rand($arrColors,(($number>count($arrColors)) ? count($arrColors) : $number)); //1

        if ($number>1) {
            
            foreach ($arrayRand as $ind) {
                $out[] = $arrColors[$ind];
            }
            
            if (count($out)<$number) {
                
                $newCont = $number-count($out);
                for ($i=0;$i<$newCont;$i++) {
                    $out[] = "'".static::rand_color()."'";
                }
                
            }
            return $out;
        }
        else {
            return [$arrColors[$arrayRand]];
        }
        
    }
    public static function testChart($slug) {
        $data = DataManagementController::getLastEntryByHours($slug);
        $rows = [];
        foreach ($data as $datum) {
            $rows['"'.$datum->created_at.'"'] = MiscController::number_format($datum->eur,4);
        }
        
        $colors = self::getColor(count($rows));

        $box =  new Box('Test',view('charts.chart',['id'=>random_int(0,1000000),'title'=>'Chart Soports/Provincia','legend'=>'# de Soportes','layer'=>implode(',',array_keys($rows)),'data'=>implode(',',array_values($rows)),
        'colors'=>implode(',',$colors)]));
        
        return $box->render();
    }
}
