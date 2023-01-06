<?php

use App\Blog;

Route::get('/blog/all', 'BlogController@all')->name('blog.all');
Route::resource('blog', 'BlogController');

Route::resource('/tag', 'TagController');

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
