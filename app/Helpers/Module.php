<?php
//@abdullah zahid joy
namespace App\Helpers;

use Illuminate\Support\Str;
use PharIo\Version\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Helpers\Trait\GenerateFileFromStub;
use Illuminate\Filesystem\Filesystem;

/**
 *
 */
class Module
{
    use GenerateFileFromStub;

    /**
     * @param string $name
     * @param $field
     * @return bool|false
     */
    public static function create(string $name , $field): bool
    {
        //make table field
        $dbField = self::makeTableField($field );
        $formBuilder = self::makeInputField($field);
        $indexData = self::makeIndexData($field);
        
        //create model
        $model = self::generateModel($name,$indexData['files']);
        if(!$model) return false;

        //create migration
        $migration = self::generateMigration($name, $dbField);
        if(!$migration) return false;
       
        //generate view file
        $view = self::generateFrontend($name , $formBuilder, $indexData);
        if(!$view) return false;

        //create controller
        $controller = self::generateController($name , $indexData['files']);
        if(!$controller) return false;
        
        //add route name in backend.php
        $route = self::addRoute($name);
        if(!$route) return false;

        //save module recode
       return self::storeModuleInfo( $name , $migration );
    }

    /**
     * @param $db
     * @param $field
     * @return bool|int
     */
    public static function generateMigration($name , $field): bool|int
    {
        $path = base_path("database/migrations/".date('Y_m_d_His')."_create_".lcfirst($name)."_table.php");
        if(self::checkFile($path)) return false;
        Self::makeDirectory(dirname($path));
        $contents = self::generateStubContents(self::getStubPath("migration"), self::getMigrationStubVariables($name , $field));
        return self::createFile($path, $contents) ?  $path : false;
    }

    /**
     * @param $name
     * @param $files
     * @return array|bool|string
     */
    public static function generateModel($name,$attributes): array|bool|string
    {
        try {
            $path = app_path("Models/".ucfirst($name).".php");
            if(self::checkFile($path)) return false;
            Self::makeDirectory(dirname($path));
            $fileAttributes = self::makeFileAttributeForModel($attributes);
            $contents = self::generateStubContents(self::getStubPath("model"), self::getModelStubVariables(ucfirst($name) , $fileAttributes));
            return self::createFile($path, $contents) ?  $path : false;
        }catch (Exception $ex){
            return $ex;
        }
    }

    /**
     * @param $name
     * @param array $fileData
     * @return Exception|\Exception|string|bool
     */
    public static function generateController($name, array $fileData = []): Exception|\Exception|string|bool
    {
        try {
            $path = app_path("Http/Controllers/Backend/Modules/".ucfirst($name)."Controller.php");
            if (self::checkFile($path)) return false;
            Self::makeDirectory(dirname($path));
            $contents = self::generateStubContents(self::getStubPath("controller"), self::getControllerStubVariables(ucfirst($name) , json_encode($fileData)));
            return self::createFile($path, $contents) ?  $path : false;
        }catch (Exception $ex){
            return $ex;
        }
    }

     /**
     * @param $name
     * @param $formBuilder
     * @param $indexData
     * @return string|bool
     */
    public static function generateFrontend($name , $formBuilder , $indexData): string|bool
    {
        $path = base_path("resources/views/admin/pages/Modules/" . ucfirst($name) . "/index.blade.php");
        if(self::checkFile($path)) return false;
        Self::makeDirectory(dirname($path));
        $contents = self::generateStubContents(self::getStubPath("view"), self::getViewStubVariables(lcFirst($name),ucFirst($name),$formBuilder, $indexData));
        return self::createFile($path, $contents) ?  $path : false;
    }

    public static function getMigrationStubVariables($name , $fields): array
    {
       return [
            'table' => Str::snake(Str::pluralStudly(class_basename($name))),
            'fields' => $fields
        ];
    }

    public static function getControllerStubVariables($name , $files = ""): array
    {
        $nameSpace = app()->getNamespace()."Http\Controllers\Backend\Modules";
        $names = explode('/', $name);
        $className = end($names)."Controller";
        array_pop($names);
        if (!empty($names)) {
            foreach ($names as $name) {
                $nameSpace .= "\{$name}";
            }
        }
        return [
            'namespace' => $nameSpace,
            'class' => $className,
            'model' => ucfirst($name),
            'files' => $files
        ];
    }

    public static function getModelStubVariables($name , $attributes = ""): array
    {
        $nameSpace = app()->getNamespace()."Models";
        $names = explode('/', $name);
        $className = end($names);
        array_pop($names);
        if (!empty($names)) {
            foreach ($names as $name) {
                $nameSpace .= "\{$name}";
            }
        }
        return [
            'namespace' => $nameSpace,
            'class' => $className,
            'fileAttributes' => $attributes
        ];
    }

    public static function getViewStubVariables($name,$model,$formBuilder,$indexData): array
    {
        return [
            'NAME' => $name,
            'MODEL' => $model,
            'createForm' => $formBuilder["createInputField"],
            'updateForm' => $formBuilder["updateInputField"],
            'indexField' => $indexData['indexField'],
            'indexTable' => $indexData['indexTable'],
            'editField' => $indexData['editField'],
            'TEXTAREA' => json_encode($indexData['textArea']),
        ];
    }

    public static function generateStubContents($stub, $stubVariables = [], $separator = '$'): array|bool|string
    {
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace($separator . $search . $separator, $replace, $contents);
        }
        return $contents;
    }

    /**
     * @param array $files
     * @return string
     */
    public static function makeFileAttributeForModel(array $files = []){
        $attribute = "";
        if (!empty($files)){
            foreach ($files as $file){
                $attribute .= "public function get".ucfirst($file)."Attribute(\$value)\n\t{\n";
                $attribute .= "\t \t return !empty(\$value) ? Storage::url(\$value) : null ;\n\t}\n";
            }
        }
        return $attribute;
    }

    /**
     * @param $name
     * @param $migration
     * @return bool
     */
    public static function storeModuleInfo($name , $migration): bool
    {
       return DB::table('modules')->insert([
            'name' => ucFirst($name),
            'controller' => ucFirst($name)."Controller",
            'route' => lcFirst($name).".index",
            'migration' => $migration,
        ]);
    }

    /**
     * @param $name
     * @return bool|int
     */
    public static function addRoute($name): bool|int
    {
        $search = "Route::group(['middleware'=>'permission:admin'],function(){";
        $route = "Route::resource('". lcFirst($name) ."', ".ucFirst($name)."Controller::Class,['names'=>'".ucFirst($name)."']);";
        return self::addFileContent($search,$search. "\n \t".  $route,base_path('routes/Backend.php')) ? self::importControllerInRoute($name) : false;
    }

    /**
     * @param $name
     * @return bool|int
     */
    public static function importControllerInRoute($name): bool|int
    {
        $search = "use Illuminate\Support\Facades\Route;";
        $import = 'use App\Http\Controllers\Backend\\Modules\\'.ucFirst($name)."Controller;";
        return self::addFileContent($search,$search. "\n".  $import,base_path('routes/Backend.php'));
    }

    /**
     * @param $search
     * @param $replace
     * @param $path
     * @return bool|int
     */
    public static function addFileContent($search, $replace, $path): bool|int
    {
       return file_put_contents( $path , str_replace($search, $replace, file_get_contents( $path)));
    }

    /**
     * @return array
     */
    public static function  getAllDatatype(): array
    {
        return collect([
            'bigInteger' ,
            'boolean' ,
            'char' ,
            'dateTime' ,
            'date' ,
            'decimal' ,
            'double' ,
            'enum' ,
            'float' ,
            'integer' ,
            'longText',
            'mediumInteger',
            'mediumText',
            'smallInteger',
            'string',
            'text',
            'time',
            'timestamp',
            'timestamps',
            'tinyInteger',
            'tinyText',
            'unsignedBigInteger',
            'unsignedDecimal',
            'unsignedInteger',
            'unsignedMediumInteger',
            'unsignedSmallInteger',
            'unsignedTinyInteger',
          
        ])->toArray();
    }

    /**
     * @return array
     */
    public static function  getAllInputType(): array
    {
        return collect([
            'text' ,
            'password' ,
            'file' ,
            'date' ,
            'textarea' ,
            'number' ,
            'checkbox' ,
            'radio' ,
            'select' ,
        ])->toArray();
    }

    /**
     * @return array
     */
    public static function getAllModel(): array
    {
        return DB::table('modules')->select('name')->get()->toArray();
    }

    /**
     * @param $field
     * @return string
     */
    public static function makeTableField($field): string
    {
        $tableField = "";
        for ($key = 0 ; $key < count($field["type"]); $key++){
            $type = $field['type'][$key];
            $condition = "";
            if(!empty($field['is_nullable'])){
                if($field['is_nullable'][$key] == "yes" ){
                    $condition .= "->nullable()";
                }
            }
            if(!empty($field['is_unique'])){
                if($field['is_unique'][$key] == "yes"){
                    $condition .= "->unique()";
                }
            }
            if(!empty($field['default'])){
                if(!empty($field['default'][$key])){
                    $condition .= "->default('{$field['default'][$key]}')";
                }
            }

            $addition = '';
            if(!empty($field['char'][$key]) && ($type == "char" || $type == "string")){
                $addition .= ",{$field['char'][$key]}";
            }
            if(!empty($field['enum1'][$key]) && !empty($field['enum2'][$key]) && $type == "enum"){
                $addition .= ", ['{$field['enum1'][$key]}','{$field['enum2'][$key]}']";
            }
            if(!empty($field['precision'][$key]) && !empty($field['scale'][$key]) && ($type == "float" || $type == "double" || $type == "decimal" || $type == "unsignedDecimal")){
                $addition .= ", {$field['precision'][$key]},{$field['scale'][$key]}";
            }
            if(!empty($field['foreign'][$key]) && ($type == "bigInteger" || $type == "unsignedBigInteger" || $type == "unsignedInteger" || $type == "unsignedMediumInteger" || $type == "unsignedSmallInteger" || $type == "unsignedTinyInteger")){
                $table =  App::make( 'App\\Models\\'. $field['foreign'][$key] )->getTable();
                $foreign = "\$table->foreign('{$field['name'][$key]}')->references('id')->on('{$table}')->onDelete('cascade');\n";
            }
            $tableField .= "\$table->{$type}('{$field['name'][$key]}'{$addition}){$condition};\n";
            if(!empty($foreign)){
                $tableField .= $foreign;
            }

        }
        //dd($tableField);
        return $tableField;
    }

    /**
     * @param $field
     * @return string
     */
    public static function makeInputField($field ): array
    {
        $createInputField = "";
        $updateInputField = "";
        for ($key = 0 ; $key < count($field["inputType"]); $key++){
            $type = $field['inputType'][$key];
            $name = $field['name'][$key];
            $title = ucFirst($field['name'][$key]);
            $condition = "";
            if(!empty($field['is_nullable'])){
                if($field['is_nullable'][$key] == "no" ){
                    $condition .= " required";
                }
            }
            if( $type == 'file'){
                $condition .=" accept=\"image/*\"";
            }
            $enum = [];
            if(!empty($field['enum1'][$key]) && !empty($field['enum2'][$key]) ){
                $enum[] = $field['enum1'][$key];
                $enum[] = $field['enum2'][$key];
            }

            $createInputField .= self::generateInputField("create",$name,$title,$type,$condition,$enum);
            $updateInputField .= self::generateInputField("update",$name,$title,$type,$condition,$enum);
        }

        return [
            'createInputField'=> $createInputField,
            'updateInputField'=> $updateInputField,
        ];

    }

    /**
     *
     * @param $formType
     * @param $name
     * @param $title
     * @param $type
     * @param $condition
     * @param array $enums
     * @return string
     */
    public static function generateInputField($formType ,$name , $title , $type , $condition , array $enums = []): string
    {
        if($formType == "update"){
            $field ="<div class=\"form-group mb-3 edit_{$name}\"> \n";
        }else{
            $field ="<div class=\"form-group mb-3\"> \n";
        }

        if($type == 'text' || $type == 'password' || $type == 'number' || $type == 'date' ){
            if($formType == 'create'){
              $field .="\t<label for=\"{$name}\" class=\"form-label \">{$title}</label>\n";
              $field .="\t<input type=\"{$type}\" class=\"form-control\" id=\"{$name}\" name=\"{$name}\" {$condition}>\n";
            }else{
                $field .="\t<label for=\"edit_{$name}\" class=\"form-label \">{$title}</label>\n";
                $field .="\t<input type=\"{$type}\" class=\"form-control\" id=\"edit_{$name}\" name=\"{$name}\" {$condition}>\n";
            }
        }

        if( $type == 'file'){
            if($formType == 'update'){
                $field .= "\t<p  class=\"form-label\">Current {$name}</p>\n";
                $field .= "\t<a href=\"#\" class=\"edit_{$name}_link\"><img src=\"#\" width=\"100px\" height=\"100px\" alt=\"image\" class=\"edit_{$name}_preview\"></a>\n";
                $field .= "\t</div>\n";
                $field .= "<div class=\"form-group mb-3\">\n";

                $field .="\t<label for=\"edit_{$name}\" class=\"form-label \">{$title}</label>\n";
                $field .="\t<input type=\"{$type}\" class=\"form-control\" id=\"edit_{$name}\" name=\"{$name}\" {$condition}>\n";

            }else{
                $field .="\t<label for=\"{$name}\" class=\"form-label \">{$title}</label>\n";
                $field .="\t<input type=\"{$type}\" class=\"form-control\" id=\"{$name}\" name=\"{$name}\" {$condition}>\n";
            }
        }

        if($type == 'select'){
            if($formType == "create"){
                $field .="\t<label for=\"{$name}\" class=\"form-label \">{$title}</label>\n";
                $field .="\t<select class=\"form-select\" id=\"{$name}\" name=\"{$name}\" $condition>\n";
            }else{
                $field .="\t<label for=\"edit_{$name}\" class=\"form-label \">{$title}</label>\n";
                $field .="\t<select class=\"form-select\" id=\"edit_{$name}\" name=\"{$name}\" $condition>\n";
            }

            $field .="\t<option selected>Choose...</option>\n";
            if(count($enums) > 0){
                foreach ($enums as $enum){
                    $title = ucFirst($enum);
                    $field .="\t <option value=\"{$enum}\">{$title}</option>\n";
                }
            }
            $field .="\t</select>\n";
        }
        if($type == 'textarea') {
            if ($formType == "create"){
                $field .= "\t<label for=\"{$name}\" class=\"form-label \">{$title}</label>\n";
                $field .= "\t<textarea class=\"form-control\" id=\"{$name}\" name=\"{$name}\" $condition></textarea>\n";
            }else {
                $field .= "\t<label for=\"edit_{$name}\" class=\"form-label \">{$title}</label>\n";
                $field .= "\t<textarea class=\"form-control\" id=\"edit_{$name}\" name=\"{$name}\" $condition></textarea>\n";
            }
        }

        if($type == 'radio' || $type == 'checkbox'){
            $field .="\t<label>{$title}</label>\n";
            $field .="\t<br>\n";
            if(count($enums) > 0){
                foreach ($enums as $enum){
                    $title = ucFirst($enum);
                    if($formType == "create"){
                        $field .="\t <input class=\"form-check-input\" type=\"{$type}\" name=\"{$name}\" id=\"{$enum}\" value=\"{$enum}\">\n";
                        $field .="\t  <label class=\"form-check-label\" for=\"{$enum}\">{$title}</label>\n";
                    }else{
                        $field .="\t <input class=\"form-check-input\" type=\"{$type}\" name=\"{$name}\" id=\"edit_{$enum}\" value=\"{$enum}\">\n";
                        $field .="\t  <label class=\"form-check-label\" for=\"edit_{$enum}\">{$title}</label>\n";
                    }
                }
            }else{

                if ($formType == "create"){
                    $field .="\t <input class=\"form-check-input\" type=\"{$type}\" name=\"{$name}\" id=\"{$name}_1\" value=\"1\">\n";
                    $field .="\t  <label class=\"form-check-label\" for=\"{$name}_1\">1</label>\n";
                }else{
                    $field .="\t <input class=\"form-check-input\" type=\"{$type}\" name=\"{$name}\" id=\"edit_{$name}_1\" value=\"1\">\n";
                    $field .="\t  <label class=\"form-check-label\" for=\"edit_{$name}_1\">1</label>\n";
                }
                if($formType == "create"){
                    $field .="\t <input class=\"form-check-input\" type=\"{$type}\" name=\"{$name}\" id=\"{$name}_2\" value=\"2\">\n";
                    $field .="\t  <label class=\"form-check-label\" for=\"{$name}_2\">2</label>\n";
                }else{
                    $field .="\t <input class=\"form-check-input\" type=\"{$type}\" name=\"{$name}\" id=\"edit_{$name}_2\" value=\"2\">\n";
                    $field .="\t  <label class=\"form-check-label\" for=\"edit_{$name}_2\">2</label>\n";
                }
            }
        }

        $field .="</div>\n";
        return $field;
    }

    /**
     * @param $field
     * @return array
     */
    public static function makeIndexData($field): array
    {
        $indexField = "";
        $indexTable = "";
        $editField = "";
        $files = [];
        $textArea = [];
        for ($key = 0 ; $key < count($field["type"]); $key++){
            $name = $field['name'][$key];
            $title = ucFirst($field['name'][$key]);
            if($field['inputType'][$key] == 'file'){
                $indexField .= "{data:'{$name}',name:'{$title}'}, \n";
                $indexTable .= "<th>{$title}</th> \n";
                $files[] = $field['name'][$key];
                $editField .= "$(\".edit_{$name}_preview\").attr(\"src\",res.data.{$name});\n";
                $editField .= "$(\".edit_{$name}_link\").attr(\"href\",res.data.{$name});\n";
            }else if($field['inputType'][$key] == 'textarea'){
                $textArea[] = $name;
                $textArea[] = "edit_".$name;
                $editField .= "$('#edit_{$name}').val(res.data.{$name});\n";
            }else if($field['inputType'][$key] == 'radio' || $field['inputType'][$key] == 'checkbox'){
                $type = $field['inputType'][$key];
                $editField .= "\$(`.edit_{$name} > input[type=\"{$type}\"]`).each((index , input) =>{\n";
                $editField .= "\t if(res.data.{$name} === input.value){\n";
                $editField .= "\t input.checked= true;\n";
                $editField .= "\t}\n";
                $editField .= "\t });\n";

                $indexField .= "{data:'{$name}',name:'{$title}'}, \n";
                $indexTable .= "<th>{$title}</th> \n";

            }else{
                $indexField .= "{data:'{$name}',name:'{$title}'}, \n";
                $indexTable .= "<th>{$title}</th> \n";
                $editField .= "$('#edit_{$name}').val(res.data.{$name});\n";
            }


        }
        return ['indexField' => $indexField ,'indexTable' => $indexTable , 'files' => $files,'textArea'=> $textArea,'editField'=> $editField];
    }

}