<?php

//Route::get('/', function () {
//    return '<center>int.answer.updrv.com</center>';
//});
Route::get('/tj.js', "Api\TjController@tj");
Route::get('/count', "Api\TjController@count");
Route::get('/auto', "Api\TjController@auto");

