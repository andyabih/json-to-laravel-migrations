<?php
namespace Andyabih\JsonToLaravelMigrations\Traits;
use Error;

trait HasBackpackFunctions{
    public function addToSidebar($modelName, $routeName)
    {
        $path = 'resources/views/vendor/backpack/base/inc/sidebar_content.blade.php';
        $disk_name = config('backpack.base.root_disk_name');

        $disk = \Storage::disk($disk_name);
        $itemName = \Str::plural($modelName);

        $code = "<li class='nav-item'><a class='nav-link' href='{{ backpack_url('{$routeName}') }}'>{$itemName}</a></li>";

        if ($disk->exists($path)) {
            $contents = $disk->get($path);
            $file_lines = file($disk->path($path), FILE_IGNORE_NEW_LINES);


            if (!$disk->put($path, $contents . PHP_EOL . $code)) {
                throw new Error('Could not write to sidebar_content file.');
            }
        } else {
            throw new Error('The sidebar_content file does not exist. Make sure Backpack is properly installed.');
        }
    }

    public function addRoute($modelName)
    {
        $path = 'routes/backpack/custom.php';
        $disk_name = config('backpack.base.root_disk_name');
        $disk = \Storage::disk($disk_name);
        $routeName = lcfirst($modelName);
        $controllerName = $modelName . 'CrudController';
        $code = "Route::crud('{$routeName}', '{$controllerName}');";

        if ($disk->exists($path)) {
            $old_file_path = $disk->path($path);

            // insert the given code before the file's last line
            $file_lines = file($old_file_path, FILE_IGNORE_NEW_LINES);

            $end_line_number = $this->customRoutesFileEndLine($file_lines);
            $file_lines[$end_line_number + 1] = $file_lines[$end_line_number];
            $file_lines[$end_line_number] = '    ' . $code;
            $new_file_content = implode(PHP_EOL, $file_lines);

            if (!$disk->put($path, $new_file_content)) {
                throw new Error('Could not write to file: ' . $path);
            }
        }
        return $routeName;
    }


    public function customRoutesFileEndLine($file_lines)
    {
        // in case the last line has not been modified at all
        $end_line_number = array_search('}); // this should be the absolute last line of this file', $file_lines);

        if ($end_line_number) {
            return $end_line_number;
        }

        // otherwise, in case the last line HAS been modified
        // return the last line that has an ending in it
        $possible_end_lines = array_filter($file_lines, function ($k) {
            return strpos($k, '});') === 0;
        });

        if ($possible_end_lines) {
            end($possible_end_lines);
            $end_line_number = key($possible_end_lines);

            return $end_line_number;
        }
    }

}