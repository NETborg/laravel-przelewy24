<?php

Route::group([
    'prefix' => 'p24', 
    'namespace' => 'NetborgTeam\P24\Controllers', 
    'middleware' => ['web']
], function() {
    Route::get('/listen', 'P24ListenerController@confirmation')->name('p24TransactionConfirmationListener');
});