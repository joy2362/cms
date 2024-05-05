<?php



use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return \Illuminate\Support\Facades\Artisan::output();
})->name('cache.clear');

Route::get('/test', function () {
    $routes = Route::getRoutes();
    foreach ($routes as $route){
        if(in_array('permission:admin',$route->action['middleware'])){

            dd(class_basename(explode("@",$route->action['controller'])[0]));
        }
        //dd($route->getActionName());
    }
});