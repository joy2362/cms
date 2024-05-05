<?php
//@abdullah zahid joy
namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class BaseController extends Controller
{
    public function __construct()
    {
        $systemSetting = App::make("SystemSetting");
        $menus = App::make("BackendMenu");
        // Sharing is caring
        View::share(['menus' => $menus, 'systemSetting'=> $systemSetting ]);
    }

    protected function upload( $file, $path, $old = null ) {

        $code = date( 'ymdhis' ) . '-' . rand( 1111, 9999 );

        //DELETE OLD FILE
        if ( !empty( $old ) ) {
            $oldFile = $this->oldFile( $old );
            if ( Storage::disk( 'public' )->exists(  $oldFile  ) ){
                Storage::disk( 'public' )->delete(  $oldFile  );
            }
        }

        //FILE UPLOAD
        if ( !empty( $file ) ) {
            $fileName = $code ."." . $file->getClientOriginalExtension();
            $this->makeDir($path);
            return Storage::disk( 'public' )->putFileAs( 'upload/' . $path, $file, $fileName );
        }
    }

    //Folder Create
    public function makeDir( $folder ) {
        $main_dir = storage_path( "app/public/upload/{$folder}" );

        if ( !file_exists( $main_dir ) ) {
            mkdir( $main_dir, 0777, true );
        }
    }
   //old file
    public function oldFile( $file ) {
        $ex = explode( 'storage/', $file );
        return $ex[1] ?? "";
    }
    //delete file
    public function deleteFile( $file ) {
        if ( Storage::disk( 'public' )->exists( $file ) ):
            Storage::delete( $file );
        endif;
    }
}