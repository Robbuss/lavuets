<?php

namespace App\Http\Controllers;

use ZipArchive;
use Carbon\Carbon;
use App\Models\Tenant;
use Illuminate\Http\Request;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /* this controller is not called atm, but the code might be interesting for later
    * When customers need the ability to export all their data
    *

    public function getDirectories($directory = "")
    {
        $directories = [];
        foreach (Storage::directories($directory) as $key => $dir) {
            $c = Tenant::withTrashed()->whereId(intval($dir))->first();
            $directories[$key] = [
                'title' => $dir,
                'meta_info' => $c ? $c->name : ''
            ];
        }

        return $directories;
    }

    public function backup()
    {
        $file = 'backup-'. Carbon::now()->format('d-m-Y-His') .'.zip';
        $path =  storage_path() . '/backup/';
        $this->zip(storage_path() . '/app/', $path . $file);

        return [
            'title' => $file,
            'content' => base64_encode(file_get_contents($path . $file)),
            'mime' => 'application/zip',
            'extension' => 'zip'
        ];
    }

    function zip($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));
        if (is_dir($source) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);
                // Ignore "." and ".." folders
                if (in_array(substr($file, strpos($file, '/') + 1), ['.', '..'])) {
                    continue;
                }
                $file = realpath($file);
                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace(storage_path(), '', $file));
                    // $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } elseif (is_file($file) === true) {
                    $zip->addFromString(str_replace(storage_path(), '', $file), file_get_contents($file));
                }
            }
        } elseif (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }
    */
}
