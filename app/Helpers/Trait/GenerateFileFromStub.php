<?php
//@abdullah zahid joy
namespace App\Helpers\Trait;

use Illuminate\Filesystem\Filesystem;

/**
 *
 */
trait GenerateFileFromStub
{
    public static function getStubPath($type): string
    {
        return base_path("stubs/{$type}.stub");
    }

    public static function createFile($path, $contents): bool
    {
        $file = new Filesystem();
        if (!$file->exists($path)) {
            $file->put($path, $contents);
            return true;
        }
        return false;
    }

    public static function getSourceFilePath($nameSpace, $name): string
    {
        return base_path($nameSpace) . '/' . $name . '.php';
    }

    protected static function makeDirectory($path): string
    {
        $file = new Filesystem();
        if (! $file->isDirectory($path)) {
            $file->makeDirectory($path, 0777, true, true);
        }
        return $path;
    }

    public static function checkFile($name): bool
    {
        return file_exists($name);
    }
}