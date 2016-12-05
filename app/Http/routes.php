<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Authentication Route
Route::get('auth/login',['uses' => 'Auth\AuthController@getLogin', 'as' => 'login']);
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', ['uses' => 'Auth\AuthController@getLogout', 'as' => 'logout']);

//Registration routes
Route::get('auth/register', ['uses' => 'Auth\AuthController@getRegister', 'as' => 'register']);
Route::post('auth/register', 'Auth\AuthController@postRegister');

//Password reset routes
Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Auth\PasswordController@reset');

//Categories
Route::resource('categories', 'CategoryController', ['except' => ['create']]);

//Tags
Route::resource('tags', 'TagController', ['except' => ['create']]);

//Comment
Route::post('comments/{post_id}', ['uses' => 'CommentsController@store', 'as' => 'comments.store']);
Route::get('comments/{id}/edit', ['uses' => 'CommentsController@edit', 'as' => 'comments.edit']);
Route::put('comments/{id}', ['uses' => 'CommentsController@update', 'as' => 'comments.update']);
Route::delete('comments/{id}', ['uses' => 'CommentsController@destroy', 'as' => 'comments.destroy']);
Route::get('comments/{id}/delete', ['uses' => 'CommentsController@delete', 'as' => 'comments.delete']);

//Slugs
Route::get('blog/{slug}', [
  'as' => 'blog.single',
  'uses' => 'BlogController@getSingle'
])->where('slug', '[\w\d\-\_]+');

Route::get('blog', ['uses' => 'BlogController@getIndex', 'as' => 'blog.index']);
Route::get('/', ['uses' => 'PagesController@getIndex', 'as' => '/']);
Route::get('about', 'PagesController@getAbout');

//contact
Route::get('contact', 'PagesController@getContact');
Route::post('contact', 'PagesController@postContact');

//Posts
Route::resource('posts', 'PostController');
