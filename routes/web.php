<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'QuestionsController@index');

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('email/verfiry/{token}', [
    'uses'  => 'EmailController@verfiry',
    'as'    => 'email.verfiry',
]);

Route::resource('questions', 'QuestionsController', ['names' => [
        'create'    => 'question.create',
        'show'      => 'question.show',
    ]]);

Route::post('questions/{question}/answer', 'AnswersController@store');

Route::get('question/{question}/follow', 'QuestionFollowController@follow');
