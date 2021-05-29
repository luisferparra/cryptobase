<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http;

use App\Models\Coins;
use App\Models\CoinsInformation;

class CoinsFillDataInformationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cryptobase:coins:fillcoininfo {coin_id? : Coin Cod. If not inserted, will execute like batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that will connect with the api and will load information about the coins, ie description... etc';

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
    public function handle(Coins $model)
    {
        $url = config('cryptbase.api_url') . 'coins/';
        $urlPost = '?localization=false&tickers=false&market_data=false&developer_data=false&sparkline=false';
        $coinCod = (!empty($this->argument('coin_id'))) ? $this->argument('coin_id') : false;
        $objCoins = $model->select('id', 'coin_cod');
        if (!empty($coinCod)) {
            $objCoins = $objCoins->where('coin_cod', $coinCod);
        } else {
            $objCoins = $objCoins->whereNotIn('id', function ($q) {
                $q->select('coin_id')->from('coins_information');
            });
        }
        $objCoins = $objCoins->limit(5)->get();
        $counter = $objCoins->count();
        if ($counter == 0) {
            $this->error('Nothing to Process\n');
            return 1;
        }
        $this->info($counter);
        $bar = $this->output->createProgressBar($counter);
        $bar->start();
        foreach ($objCoins as $objCoin) {
            $id = $objCoin->id;
            $coinCod = $objCoin->coin_cod;
            $urlApi = $url . $coinCod;
            $this->info($urlApi . "\n");


            $dataHttp = Http::get($urlApi, [
                'localization' => 'false',
                'tickers' => 'false',
                'market_data' => 'false',
                'developer_data' => 'false',
                'sparkline' => 'false'
            ]);
            if ($dataHttp->failed()) {
                $this->error('Error at communications');
                $bar->finish();

                return 1;
            }
            $this->info($dataHttp);
            $dataJson = $dataHttp->json();
            $description = $dataJson['description']['en'];
            $scores = [
                'market_cap_rank' => $dataJson['market_cap_rank'],
                'coingecko_rank'  => $dataJson['coingecko_rank'],
                'coingecko_score'  => $dataJson['coingecko_score'],
                'developer_score'  => $dataJson['developer_score'],
                'community_score'  => $dataJson['community_score'],
                'liquidity_score'  => $dataJson['liquidity_score'],
                'public_interest_score'  => $dataJson['public_interest_score']

            ];
            $community = [
                'community_data'  => $dataJson['community_data'],
                'public_interest_stats'  => $dataJson['public_interest_stats']
            ];
            
            $this->info('ID: '.$id);
            CoinsInformation::updateOrCreate(
                [
                  'coin_id' => $id  
                ],
                [
                    
                    'description' => $dataJson['description']['en'],
                    'links' => json_encode($dataJson['links']),
                    'images' => json_encode($dataJson['image']),
                    'scores' => json_encode($scores),
                    'community' => json_encode($community)
                 ]
                
            );
            
            $bar->advance();
        }
        $bar->finish();
        $this->info("\n");
        return 0;
    }
}
