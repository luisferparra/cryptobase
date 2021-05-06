<?php

namespace App\Admin\Controllers;

use App\Models\Wallets;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use Encore\Admin\Actions\RowAction;

use Encore\Admin\Widgets\Table;

use Carbon\Carbon;
use Illuminate\Support\Str;

use Encore\Admin\Facades\Admin;

use App\Http\Controllers\Misc\MiscController;
use App\Http\Controllers\TradingCompanies\TradingCompaniesController;
use App\Http\Controllers\Coins\CoinsMiscController;


use App\Models\WalletInvestments;

class WalletController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Wallet';

    /**
     * Used for the grid displaying information
     *
     * @var string
     */
    protected $valueIncrease = "0";

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        // dump(Admin::user()->id);
        // dump(Admin::user()->isAdministrator());     

        $obj = new Wallets();
        $grid = new Grid($obj);

        if (!Admin::user()->isAdministrator()) {
            $grid->model()->where('admin_user_id', Admin::user()->id);
        }

        $grid->column('id', __('Id'));
        if (Admin::user()->isAdministrator()) {
            $grid->column('admin_user_id', __('User'))->display(function () {
                return MiscController::getUsersData($this->admin_user_id);
            });
        }
        $grid->column('trading_company.name', __('Trading company id'));
        $grid->column('coin.slug', __('Coin'))->expand(function ($model) {
            $walletInvestments = $model->investments()->orderBy('id','desc')->get()->map(function ($invest) {
                $label = (($invest->operation_type=='PURCHASE') ? 'info' : 'warning');
                $arrow = (($invest->operation_type=='PURCHASE') ? 'fa-arrow-circle-up' : 'fa-arrow-circle-down');
                return [
                    '<i class="fa '.$arrow.'">&nbsp;</i><span class="label label-'.$label.'">'.Str::ucfirst($invest->operation_type).'</span>',
                    MiscController::number_format($invest->quantity),
                    MiscController::number_format($invest->value),
                    MiscController::number_format($invest->total_amount),
                    $invest->created_at
                ];
                
                //return $invest->only(['operation_type','quantity','value','total_amount','created_at']);
            });
            return new Table(['Operation', 'quantity', 'value','total_amount','created_at'], $walletInvestments->toArray());
        });
        $grid->column('quantity', __('Quantity'));

        //$grid->column('value', __('Amount/Coin'));
        $grid->column('value_original', __('Investment'));
        $grid->column('current_value', __('Current Investment'))->display(function () {
            $coin_id = $this->coin_id;
            $quantity = $this->quantity;
            $valueOriginal = $this->value_original;
            $valueCurrentCurrency = CoinsMiscController::getCoinCurrentValue($coin_id);
            $valueCurrent = MiscController::number_format($quantity * $valueCurrentCurrency);
            $labelColor = "warning";
            if ($valueCurrent > $valueOriginal) {
                $labelColor = "success";
            }
            $this->valueIncrease = MiscController::number_format((($valueCurrent * 100) / $valueOriginal)-100,3);
            return '<span class="label label-' . $labelColor . '">' . $valueCurrent . '€</span>';
        });
        $grid->column('performance',__('%'))->display(function(){  
            $labelColor = "primary";
            //primary, info, success, warning, danger
            $increase = MiscController::number_format($this->valueIncrease,3);
            if ($increase < 0 && $increase>-10) {
                $labelColor = "warning";
            } elseif ($increase < -10) {
                $labelColor = "danger";
            
            } elseif ($increase>0 and $increase < 5) {
                $labelColor = "primary";
            } elseif ($increase>5 && $increase < 20) {
                $labelColor = "info";
            } elseif ($increase>20) {
                $labelColor = "success";
            } else $labelColor = "default";
            return '<span class="label label-' . $labelColor . '">' . $this->valueIncrease . '%</span>';
            
        });
        $grid->column('is_active', __('Is active'));
        $grid->column('created_at', __('Created at'))->display(function () {
            return $this->created_at->format('Y-m-d H:i:s');
        })->filter('range', 'date')->sortable();
        $grid->column('updated_at', __('Last Update'))->display(function () {
            return $this->updated_at->format('Y-m-d H:i:s');
        })->filter('range', 'date')->sortable();

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->append('<a href=""><i class="fa fa-eye">ablabl</i></a>');
            $actions->prepend('<a href=""><i class="fa fa-paper-plane">blibli</i></a>');
            //$actions->disableView();
        //    $rowAction = RowAction::name('hola');
        
        });
        $grid->add('mybutton','mybutton')->cell( function ($value, $row) {

            //$my_custom_condition = $row->something == ....
            //$my_custom_link = route('my.route',['id'=>$row->ID])
            //if ($my_custom_condition)
            //{
             //   return $my_custom_link;
            //}
    
    });
        
        

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Wallets::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('admin_user_id', __('Admin user id'));
        $show->field('trading_company_id', __('Trading company id'));
        $show->field('coin_id', __('Coin id'));
        $show->field('amount', __('Amount'));
        $show->field('is_active', __('Is active'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Wallets());
        $isEditing = $form->isEditing();

        $form->column(1 / 2, function ($form) use ($isEditing) {
            if (Admin::user()->isAdministrator()) {
                $usersData = MiscController::getUsersList(true);
                if ($isEditing) {
                    $form->select('admin_user_id', __('User'))->options($usersData)->required()->readOnly();
                } else
                    $form->select('admin_user_id', __('User'))->options($usersData)->required();
            }
            $tradingCompanyArray = TradingCompaniesController::getTradingCompanies(true);
            $coinsArray = CoinsMiscController::getCoinsAvailableList(true);
            if ($isEditing) {
                $form->select('trading_company_id', __('Trading Company'))->options($tradingCompanyArray)->required()->readOnly();
                $form->select('coin_id', __('Coin id'))->options($coinsArray)->required()->readOnly();
                $form->decimal('quantity', __('Quantity'))->default(0)->required()->readOnly();
                $form->decimal('value', __('Price per Coin'))->default(0)->required()->help('Coint Value whenever it was purchased')->readOnly();
                $form->decimal('value_original', __('Price Total'))->default(0)->readOnly();
            } else {
                $form->select('trading_company_id', __('Trading Company'))->options($tradingCompanyArray)->required();
                $form->select('coin_id', __('Coin id'))->options($coinsArray)->required();
                $form->decimal('quantity', __('Quantity'))->default(0)->required();
            $form->decimal('value', __('Price per Coin'))->default(0)->required()->help('Coint Value whenever it was purchased');
            $form->decimal('value_original', __('Price Total'))->default(0);
            }
            

            $form->switch('is_active', __('Is active'))->default(1);
        });
        /*
        if ($isEditing) {
        

            $form->column(1/2,function($form) use ($isEditing) {
                $id = request()->route()->parameter('wallet');
                $datMain = Wallets::findOrFail($id);
                $frm = new WidgetsForm();
                $frm->action('blabla');
                $frm->method('POST');
                $frm->text('hola',__('hola'));
                //echo $frm->render();
                
        }
        */
        
        $form->footer(function ($footer) use ($isEditing) {
            // disable reset btn

            $footer->disableReset();


            // disable submit btn
            if ($isEditing) 
            $footer->disableSubmit();

            // disable `View` checkbox
            $footer->disableViewCheck();

            // disable `Continue editing` checkbox
            $footer->disableEditingCheck();

            // disable `Continue Creating` checkbox
            $footer->disableCreatingCheck();
        });
        $form->tools(function (Form\Tools $tools) {
            //$tools->disableDelete();

            //$tools->disableView();

            //$tools->disableList();
        });


        /**
         * CallBacks Functions
         */
        /**
         * Before Saving
         */
        $form->saving(function (Form $form) {
            if (!Admin::user()->isAdministrator()) {
                $form->admin_user_id = Admin::user()->id;
            }
            $valueOriginal = $form->value_original;
            $valueOriginalCalculated = $form->quantity * $form->value;
            if ($valueOriginal != $valueOriginalCalculated) {
                $form->value_original = $valueOriginalCalculated;
            }
        });


        /**
         * After saved
         */
        $form->saved(function (Form $form) {

            $walletId = $form->model()->id;
            $operationType = 'PURCHASE';
            $quantity = $form->model()->quantity;
            $value = $form->model()->value;

            $total_amount = $form->model()->value_original;
            $walletInvestment = new WalletInvestments();
            $walletInvestment->wallet_id = $walletId;
            $walletInvestment->operation_type = $operationType;
            $walletInvestment->quantity = $quantity;
            $walletInvestment->value = $value;
            $walletInvestment->total_amount = $total_amount;
            $walletInvestment->save();
        });

        return $form;
    }
}
