<?php

namespace App\Providers;

use App\Models\System\BackendMenu;
use App\Models\System\Setting;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton("SystemSetting", function(){
            return  Cache::rememberForever('setting',function(){
                return Setting::all()->pluck('value','name');
            });
        });
        if(request()->is('admin/*')){
            $this->app->singleton('BackendMenu', function(){
                return Cache::rememberForever("backendMenu",function(){
                   return BackendMenu::with('subMenu')->orderBy('sorting','asc')->whereNull('parent_id')->get();
                });
            });
        }
        Blueprint::macro('userLog',function(){
            $this->unsignedBigInteger('created_by')->nullable();
            $this->unsignedBigInteger('updated_by')->nullable();
            $this->unsignedBigInteger('deleted_by')->nullable();
            $this->date('deleted_at')->nullable();

        });

        Blueprint::macro('status',function(){
            $this->enum('status', ['inactive', 'active'])->default('active');
            $this->enum('is_deleted', ['yes', 'no'])->default('no');
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}