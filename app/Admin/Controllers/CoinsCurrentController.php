<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Data\DataManagementController;
use App\Models\CoinsCurrentValues;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Box;

use App\Http\Controllers\Misc\MiscController;
use App\Http\Controllers\Charts\ChartsController;

use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Row;

use Carbon\Carbon;
use App\Models\Coins;

class CoinsCurrentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'CoinsCurrentValues';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new CoinsCurrentValues());

        $grid->column('id', __('Id'))->hide();
        $grid->column('coin_id', __('Coin'))->display(function ($model) {

            $dat = Coins::find($this->coin_id);
            return $dat->name;
        })->sortable()->filter(MiscController::getCoinsActive());
        $grid->column('slug', __('Symbol'))->filter('like')->sortable();
        $grid->column('eur', __('Eur'))->filter('range')->sortable();
        $grid->column('eur_24h_change', __('% 24h change'))->display(function () {
            return MiscController::number_format($this->eur_24h_change) . ' %';
        })->sortable()->filter('range');
        $grid->column('last_updated_at', __('Last updated at'))->hide();
        $grid->column('created_at', __('Created at'))->display(function () {
            return $this->created_at->format('Y-m-d H:i:s');
        })->filter('range', 'date')->sortable();
        $grid->column('updated_at', __('Last Updated at'))->display(function () {
            return $this->updated_at->format('Y-m-d H:i:s');
        })->filter('range', 'date')->sortable();
        $grid->disableCreateButton();
        $grid->disableFilter();
        $grid->disableExport();
        $grid->disableRowSelector();
        //$grid->disableActions();
        $grid->disableColumnSelector();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
            // $actions->disableView();
        });
        $grid->model()->orderBy('eur_24h_change');
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




        $obj = CoinsCurrentValues::findOrFail($id);
        /**
         * Se take information about the last row of the coin history
         */
        $coinObj = $obj->coin()->get();
        $slug = '';
        foreach ($coinObj as $coinObjDatum) {
            $slug = $coinObjDatum->symbol;
        }
        $coinLastEntry = DataManagementController::getLastEntry($slug);

        
        
        //dd($coinLastEntry);
        $idCoin = $obj->coin_id;
        $show = new Show($obj);
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                //$tools->disableList();
                $tools->disableDelete();
            });
        $show->field('coin.coin_cod', __('Coin Cod'))->setWidth(5);
        $show->field('coin.symbol', __('Symbol'))->setWidth(3);
        $show->field('coin.name', __('Name'))->setWidth(5);
        $show->field('eur', __('Current Value €'))->as(function($eur) {
            return MiscController::number_format($eur);
        })->setWidth(5);
        $eur24 = MiscController::number_format($obj->eur_24h_change);
        $eur24LabelColor = ($eur24 < 0) ? 'danger' : 'success';

        $show->field('eur_24h_change', __('Last 24h Eur 24h change'))->as(function($eur_24h_change) {
            return MiscController::number_format($eur_24h_change);
        })->label($eur24LabelColor)->setWidth(4, 3);

        $show->field('abc',__('Currency Market Capital'))->as(function($abc) use ($coinLastEntry) {
            return MiscController::number_format($coinLastEntry->eur_market_cap);
        })->setWidth(5,3);
        $show->field('abcc',__('Vol. 24h Capital'))->as(function($abcc) use ($coinLastEntry) {
            return MiscController::number_format($coinLastEntry->eur_24h_vol);
        })->setWidth(5,3);

        $row = new Row();

        $tableLastEntriesTopHeader = ['Eur','% Change','Date'];
        $tableLastEntriesTop = new Table($tableLastEntriesTopHeader,MiscController::getLastCurrencyEntryForTable($slug));

        $boxTableLastEntriesTop = new Box('Last Entries', $tableLastEntriesTop);
        $boxTableLastEntriesTop->collapsable();
        $boxTableLastEntriesTop->style('success');
        $boxTableLastEntriesTop->solid();
        $row->column(4, function (Column $column)  use ($show,$boxTableLastEntriesTop) {
            $column->append($show);
            $column->append($boxTableLastEntriesTop);
        });
        $row->column(4, function (Column $column) use ($slug) {
            $column->append(ChartsController::testChart($slug));
            $column->append(Dashboard::environment());
        });
        return $row->render();




        $tab = new Tab();
        $tab->add('Row', $row);
        $tab->add('Info', $row);
        $tab->add('Hola', Dashboard::environment());



        return $tab->render();

        /*
        $show->field('id', __('Id'));
        $show->field('coin_id', __('Coin id'));
        
        $show->field('eur', __('Eur'));
        $show->field('eur_24h_change', __('Eur 24h change'));
        $show->field('last_updated_at', __('Last updated at'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;*/
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CoinsCurrentValues());

        $form->number('coin_id', __('Coin id'));
        $form->text('slug', __('Slug'));
        $form->decimal('eur', __('Eur'));
        $form->decimal('eur_24h_change', __('Eur 24h change'));
        $form->number('last_updated_at', __('Last updated at'));

        return $form;
    }
}
