<?php

namespace App\Admin\Controllers;

use App\Models\TradingCompanys;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TraderCompanysController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Trading Companies';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TradingCompanys());


        $states = [
            'on' => ['value' => 1, 'text' => 'Yes', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => 'No', 'color' => 'warning'],
        ];
        $grid->column('id', __('Id'))->filter('range');
        $grid->column('name', __('Name'))->editable()->filter('like')->sortable();
        $grid->column('is_api_trader', __('Trade Api Available'))->switch($states)->help("Available in a Future");

        $grid->column('is_active', __('Is active'))->switch($states)->sortable();
        $grid->column('created_at', __('Created at'))->hide()->filter('range', 'date');
        $grid->column('updated_at', __('Updated at'))->hide()->filter('range', 'date');

        $grid->model()->orderBy('name');


        $grid->filter(function ($filter) {

            // Remove the default id filter
            $filter->disableIdFilter();
        });

        $grid->disableFilter();

        $grid->disableRowSelector();
        $grid->disableExport();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            //$actions->disableEdit();
            $actions->disableView();
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
        $show = new Show(TradingCompanys::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('is_api_trader', __('Is api trader'));
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
        $form = new Form(new TradingCompanys());

        $form->text('name', __('Name'))->required()->setWidth(4);
        $form->switch('is_api_trader', __('Can Operate via Api'))->help('Available in Future Versions');
        $form->switch('is_active', __('Is active'))->default(1);


        $form->footer(function ($footer) {
            // disable reset btn
            
                $footer->disableReset();
            

            // disable submit btn
            //$footer->disableSubmit();

            // disable `View` checkbox
            $footer->disableViewCheck();

            // disable `Continue editing` checkbox
            $footer->disableEditingCheck();

            // disable `Continue Creating` checkbox
            $footer->disableCreatingCheck();
        });
        $form->tools(function (Form\Tools $tools) {
            //$tools->disableDelete();
            
                $tools->disableView();
            
            //$tools->disableList();
        });


        return $form;
    }
}
