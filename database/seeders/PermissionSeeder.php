<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{

    /**
     * @param string $controllerInfo
     * @return array
     */
    private function getControllerName(string $controllerInfo): array
    {
        $names = explode("@" , $controllerInfo);
        $functionName = $names[1]??"";
        $controllerName = class_basename($names[0]);
        $name = str_replace("Controller",'',$controllerName);
        return [
            "name" => $functionName . " " .$name,
            "group_name" =>  $name
        ];
    }

    /**
     * @param string $name
     * @return string
     */
    private function getNameRoute(string $name = ""): string
    {
        $names = explode(".",$name);
        $action = $names[1] ?? "";
        $module = $names[0] ?? "";
        return $action  . " " . $module ;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = Route::getRoutes();
        $permissions=[];
        foreach ($routes as $route){
            if(in_array('permission:admin',$route->action['middleware'])){
                $permission = $this->getControllerName($route->action['controller']);
                if($permission['name'] == $this->getNameRoute($route->action['as'])){
                    $permissions[] =  [
                        'name'=> $permission['name'],
                        'guard_name'=>'admin',
                        'group_name' =>  $permission['group_name']
                    ];
                }
            }

        }

        foreach($permissions as $permission){
            if(empty(Permission::whereName($permission["name"])->first())){
                Permission::firstOrCreate(
                    $permission
                );
            }
        }


        $role = Role::where('name','Super Admin')->first();
        if(!empty($role)){
            $role->givePermissionTo(Permission::all());
        }
        $admin = Admin::where('email','abdullahzahidjoy@gmail.com')->first();
        if(!empty($admin)){
           $admin->assignRole('Super Admin');
        }

    }
}