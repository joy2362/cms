<?php

namespace Database\Seeders;

use App\Models\System\BackendMenu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class BackendMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = [
            [
                'parent_id' => null,
                'title' => 'Dashboard',
                'icon' => "fa-solid fa-house" ,
                'sorting' => 1,
                'route' => 'Dashboard.index',
                'sub_menu' => []    
            ],
            [
                'parent_id' => null,
                'title' => 'Master Setup',
                'icon' => "fa-solid fa-globe" ,
                'sorting' => 2,
                'route' => null,
                'sub_menu' => []  
            ],
            [
                'parent_id' => null,
                'title' => 'User',
                'icon' => "fa-solid fa-user" ,
                'sorting' => 3,
                'route' => null,
                'sub_menu' => [
                    [
                        'title' => 'User Role',
                        'icon' => "fa-solid fa-user-shield",
                        'sorting' => 1,
                        'route' => 'UserRole.index'
                    ],
                    [
                        'title' => 'User',
                        'icon' => "fa-solid fa-user-group",
                        'sorting' => 2,
                        'route' => 'User.index'
                    ],
                ]  
            ],
            [
                'parent_id' => null,
                'title' => 'Admin',
                'icon' => "fa-solid fa-user-secret",
                'sorting' => 4,
                'route' => null,
                'sub_menu' => [
                    [
                        'title' => 'Admin Role',
                        'icon' => "fa-solid fa-user-gear",
                        'sorting' => 1,
                        'route' => 'AdminRole.index'
                    ],
                    [
                        'title' => 'Admin',
                        'icon' => "fa-solid fa-user-gear",
                        'sorting' => 2,
                        'route' => 'Admin.index'
                    ],
                ]  
            ],
            [
                'parent_id' => null,
                'title' => 'System Setting',
                'icon' => "fa-solid fa-gears",
                'sorting' => 5,
                'route' => null,
                'sub_menu' => [
                    [
                        'title' => 'Module',
                        'icon' => "fa-solid fa-star",
                        'sorting' => 1,
                        'route' => 'Module.index'
                    ],
                    [
                        'title' => 'Setting',
                        'icon' => "fa-solid fa-gears",
                        'sorting' => 2,
                        'route' => 'Setting.index'
                    ],
                    [
                        'title' => 'Activity Log',
                        'icon' => "fa-solid fa-clock-rotate-left",
                        'sorting' => 4,
                        'route' => 'ActivityLog.index'
                    ],
                ]
            ],
            [
                'parent_id' => null,
                'title' => 'Recycle Bin',
                'icon' => "fa-solid fa-trash-can",
                'sorting' => 6,
                'route' => 'RecycleBin.index',
                'sub_menu' => []  
            ],
        ];
        foreach ($menus as $menu){
            $menuItem = BackendMenu::firstOrCreate(Arr::except($menu , 'sub_menu'));
            $sub = Arr::only($menu,'sub_menu');
            if(!empty($sub['sub_menu'])){
                foreach($sub['sub_menu'] as $sub_item){
                    BackendMenu::updateOrCreate(
                        [
                        'parent_id' => $menuItem->id , 
                        'title'=>$sub_item['title']
                        ],[
                            'icon' => $sub_item['icon'] , 
                            'sorting' => $sub_item['sorting'] , 
                            'route' =>$sub_item['route']
                        ]
                    );
                }
            }
        }
    }
}