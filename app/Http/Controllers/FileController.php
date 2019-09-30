<?php

namespace App\Http\Controllers;

use ZipArchive;
use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Http\Request;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $dir = $request->directory;
        if (is_array($dir)) {
            $dir = $request->directory['title'];
        }
        return [
            'files' => $this->getFileContents($dir),
            'directories' => $this->getDirectories($dir)
        ];
    }

    public function getFileContents($directory = "")
    {
        $files = [];
        foreach (Storage::files($directory) as $key => $file) {
            if(strpos($file, ".gitignore") !== false)
                continue;
            $content = file_get_contents(storage_path('app/' . $file));

            $files[$key] = [
                'last_modified' => Carbon::parse(Storage::lastModified($file))->isoFormat('LL'),
                'size' => Storage::size($file),
                'title' => (substr_count($file, '/') > 0) ? substr($file, strpos($file, '/') + 1) : $file,
                'content' => base64_encode($content),
                'mime' => 'application/pdf',
                'extension' => 'pdf'
            ];
        }

        return $files;
    }

    public function getDirectories($directory = "")
    {
        $directories = [];
        foreach (Storage::directories($directory) as $key => $dir) {
            $c = Customer::withTrashed()->whereId(intval($dir))->first();
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
}
