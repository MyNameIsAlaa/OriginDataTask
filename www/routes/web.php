<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return 'Welcome To Our API!';
});


$router->group(['prefix' => 'api/'], function ($router) {
    
    $router->post('login/','UserController@login'); // login and get api key
    //$router->post('logout/','UserController@authenticate'); // clear api key from db
    //$router->post('signup/','UserController@authenticate'); // create a new api user


    $router->group(['middleware' => 'auth'], function () use ($router) { 
        

        $router->group(['prefix' => 'companies/'], function ($router){
          $router->get('/', 'CompaniesController@index'); // list all companies
          $router->post('/', 'CompaniesController@store'); // create new company
          $router->post('/{id}', 'CompaniesController@update'); // update company
          $router->get('/{id}', 'CompaniesController@show'); // get single company with all employees
          $router->delete('/{id}', 'CompaniesController@destroy'); // delete single company 
          $router->get('/{id}/projects', 'CompaniesController@projects'); // list all projects for company
        });


        $router->group(['prefix' => 'employees/'], function ($router){
            $router->get('/', 'EmployeesController@index'); // list all employees
            $router->post('/', 'EmployeesController@store'); // create new employee
            $router->post('/{id}', 'EmployeesController@update'); // update employee
            $router->get('/{id}', 'EmployeesController@show'); // get single employee 
            $router->delete('/{id}', 'EmployeesController@destroy'); // delete employee
            $router->get('/{id}/projects', 'EmployeesController@projects'); // list all projects for employee
          });

        $router->group(['prefix' => 'projects/'], function ($router){
            $router->get('/', 'ProjectController@index'); // list all projects
            $router->post('/', 'ProjectController@store'); // create new projects
            $router->post('/{id}', 'ProjectController@update'); // update project
            $router->get('/{id}', 'ProjectController@show'); // get single project
            $router->delete('/{id}', 'ProjectController@destroy'); // delete project
          });



     });


});