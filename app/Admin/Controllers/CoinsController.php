<?php

namespace App\Admin\Controllers;

use App\Models\Coins;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use App\Models\CoinsCurrentValues;

use App\Http\Controllers\Data\DataManagementController;

class CoinsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Coins';


    /**
     * We have overwritten the laravel-admin update functionality. 
     *
     * @param integer $id
     * @param Request $data
     * @return void
     */
    function update($id,$data=null) {
        $data = ($data) ?: request()->all();
        
        $model = Coins::find($id);
        $model->is_active= ($data['is_active']=='on' ? 1 : 0);
        $model->save();
        $id = DataManagementController::CreateDataStructure($model);
        return response()->json([
            'status'    => true,
            'message'   => 'Coin Updated',
            'display'   => $id,
        ]);
        
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Coins());

        $grid->column('id', __('Id'));
        $grid->column('coin_cod', __('Coin cod'))->filter('like')->sortable();
        $grid->column('symbol', __('Symbol'))->filter('like')->sortable();
        $grid->column('name', __('Name'))->filter('like')->sortable();
        
        $states = [
            'on' => ['value' => 1, 'text' => 'Yes', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => 'No', 'color' => 'warning'],
        ];
        $grid->column('is_active', __('Active'))->switch($states)->sortable();
        $grid->column('created_at', __('Created at'))->hide();
        $grid->column('updated_at', __('Updated at'))->hide();
        $grid->model()->orderBy('is_active', 'desc');

        $grid->disableCreateButton();
        $grid->disableFilter();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableActions();
        $grid->disableColumnSelector();
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
        $show = new Show(Coins::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('coin_cod', __('Coin cod'));
        $show->field('symbol', __('Symbol'));
        $show->field('name', __('Name'));
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
        $form = new Form(new Coins());

        $form->text('coin_cod', __('Coin cod'));
        $form->text('symbol', __('Symbol'));
        $form->text('name', __('Name'));
        $form->switch('is_active', __('Is active'));

        return $form;
    }
}
