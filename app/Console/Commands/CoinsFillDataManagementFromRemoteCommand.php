<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Models\Coins;
use App\Models\CoinsCurrentValues;

class CoinsFillDataManagementFromRemoteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cryptobase:coins:fillremotedata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that will take the active Coins, and get Current Value and Store it';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Coins $coins)
    {
        $url = config('cryptbase.api_url') . 'simple/price';
        $data = $coins->where('is_active', 1)->get();
        $coinsArr = [];
        $coinsArrData = [];
        foreach ($data as $datum) {
            $coinsArr[] = $datum->coin_cod;
            $coinsArrData[$datum->coin_cod] = [
                'id' => $datum->id,
                'slug' => $datum->symbol
            ];
        }
        if (count($coinsArr) < 1) {
            $this->error('Nothing to update');
            return 1;
        }
        $bar = $this->output->createProgressBar((count($coinsArr) + 1));
        $bar->start();
        $dataHttp = Http::get($url, [
            'ids' => implode(',', $coinsArr),
            'vs_currencies' => 'eur',
            'include_24hr_change' => 'true',
            'include_last_updated_at' => 'true',
            'include_market_cap' => 'true',
            'include_24hr_vol' => 'true'
        ]);
        $bar->advance();
        if ($dataHttp->failed()) {
            $this->error('Error at communications');
            $bar->finish();

            return 1;
        }
        $dataJson = $dataHttp->json();
        
        foreach ($dataJson as $currency => $datum) {
            $eur = $datum['eur'];
            $eur_market_cap = $datum['eur_market_cap'];
            $eur_24h_vol = $datum['eur_24h_vol'];
            $eur_24h_change = $datum['eur_24h_change'];
            $last_updated_at = $datum['last_updated_at'];
            $currId = $coinsArrData[$currency]['id'];
            $currSlug = $coinsArrData[$currency]['slug'];

            CoinsCurrentValues::updateOrCreate(
                ['coin_id' => $currId],
                [
                    'slug' => $currSlug,
                    'eur' => $eur,
                    'eur_24h_change' => $eur_24h_change,
                    'last_updated_at' => $last_updated_at,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString()
                ]
            );
            DB::connection('mysql_data')->table($currSlug)->insert([
                [
                    'eur' => $eur,
                    'eur_market_cap' => $eur_market_cap,
                    'eur_24h_vol' => $eur_24h_vol,
                    'eur_24h_change' => $eur_24h_change,
                    'last_updated_at' => $last_updated_at,
                    'created_at' => Carbon::now()->toDateTimeString()
                ]
            ]);
            $bar->advance();
            //CoinsCurrentValues::where('coin_id',$currId)->delete();
            //CoinsCurrentValues

        }
        $bar->finish();
        $this->info("\n".count($coinsArr)." Have been updated");
        return 0;
    }
}
