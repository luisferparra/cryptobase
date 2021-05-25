<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Http\Controllers\TradingCompanies\TradingCompaniesController;

use App\Models\Wallets;
use App\Models\WalletInvestments;

class WalletTransaction extends RowAction
{
    public $name = 'New Transaction';




    public function handle(Model $model, Request $request)
    {
        // $model ...


        $tradingCompany = $request->get('trading_company_id');
        $operationTypeId = $request->get('operation_type');
        $operationType = config('cryptbase.operations_type')[$operationTypeId];
        $quantity = $request->get('quantity');
        $value = $request->get('value');
        $total = $quantity * $value;
        $walletTransaction = new WalletInvestments;
        $walletTransaction->wallet_id = $model->id;
        $walletTransaction->operation_type = $operationType;
        $walletTransaction->quantity = $quantity;
        $walletTransaction->value = $value;
        $walletTransaction->total_amount = $total;
        $walletTransaction->save();

        $model->trading_company_id = $tradingCompany;
        $quantityOriginal = $model->quantity;
        $valueOriginal = $model->value;
        $totalOriginal = $model->value_original;
        //dump(($quantityOriginal + $quantity) . " -- ". $quantityOriginal." -- ".$quantity);
        //dump(($valueOriginal + $value ) . " -- ". $valueOriginal." -- ".$value);
        //dd(($totalOriginal + $total) . " -- ". $totalOriginal." -- ".$total);


        $model->quantity = ($operationType == 'PURCHASE') ? ($quantityOriginal + $quantity) : ($quantityOriginal - $quantity);
        $model->value = ($operationType == 'PURCHASE') ? ($valueOriginal + $value) : ($valueOriginal - $value);

        $model->value_original = ($operationType == 'PURCHASE') ? ($totalOriginal + $total) : ($totalOriginal - $total);
        $model->purchased_last_at = Carbon::now()->format('Y-m-d H:i:s');
        $model->save();
        return $this->response()->success('Success message.')->refresh();
    }

    public function form()
    {

        $tradingCompanyArray = TradingCompaniesController::getTradingCompanies(true);

        $this->select('trading_company_id', __('Trading Company'))->options($tradingCompanyArray)->required();
        $this->select('operation_type', __('Type of Operation Company'))->options(config('cryptbase.operations_type'))->required();

        $this->text('quantity', __('Quantity'))->default(0)->required()->rules('numeric|min:0|regex:/^[0-9]{1,3}([0-9]{3})*\.[0-9]+$/'); //
        $this->text('value', __('Price per Coin'))->default(0)->required()->help('Coint Value whenever it was purchased')->rules('numeric|min:0|regex:/^[0-9]{1,3}([0-9]{3})*\.[0-9]+$/');
        //$this->text('total', __('Price Total'))->default(0)->rules('numeric|regex:/^[0-9]{1,3}([0-9]{3})*\.[0-9]+$/');
    }
}
