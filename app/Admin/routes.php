<?php

use App\Admin\Controllers\WalletsController;
use App\Models\Wallets;
use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('manage/coins', CoinsController::class);
    $router->resource('manage/trading-companies', TraderCompanysController::class);

    $router->resource('user/coins-current-values', CoinsCurrentController::class);
    $router->resource('user/wallets', WalletController::class);

    
});

