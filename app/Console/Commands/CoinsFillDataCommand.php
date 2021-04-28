<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;


use App\Models\Coins;
use App\Models\Alerts;
use App\Models\AlertsViews;



class CoinsFillDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cryptobase:coins:filldata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that fill the Coin List of the App';

    /**
     * Roles to assign Alerts
     *
     * @var array
     */
    protected $roleUserAlerts = [1, 2];

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
        $url = config('cryptbase.api_url') . 'coins/list';
        //dd($url);
        $contInit = $coins->all()->count();
        $data = Http::get($url);
        if ($data->failed()) {
            $this->error('Error at communications');
            return 1;
        }
        $dataJson = $data->json();
        $insertArr = [];
        foreach ($dataJson as $datum) {
            $item = [
                'coin_cod' => $datum['id'],
                'symbol' => $datum['symbol'],
                'name' => $datum['name'],
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ];
            $insertArr[] = $item;
        }
        $coins->insertOrIgnore($insertArr);
        $contEnd = $coins->all()->count();
        if ($contEnd > $contInit) {
            /**
             * We are going to create alerts. 
             * Alerts for All Users
             */
            $idAlert = Alerts::insertGetId([
                'title' => ($contEnd - $contInit) . ' New Coins have been Inserted',
                'body' => 'New Coins Inserted. Take a look at them',
                'roles' => implode(',',$this->roleUserAlerts),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);
            $datUsers = DB::table('admin_role_users')->whereIn('role_id', $this->roleUserAlerts)->get();
            $arrAlertUsers = [];
            foreach ($datUsers as $datUser) {
                $item = [
                    'alert_id' => $idAlert,
                    'admin_user_id' => $datUser->user_id,
                    'viewed' => 0,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString()
                ];
                $arrAlertUsers[] = $item;
            }
            AlertsViews::insert($arrAlertUsers);
        }
        $this->info('Init: ' . $contInit . ' End: ' . $contEnd);
        return 0;
    }
}
