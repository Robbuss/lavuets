<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

trait AssociateMediaTrait
{
    use HasMediaTrait;

    public function associateMedia($name, $file, $ext = ['png', 'jpg', 'jpeg', 'svg', 'webp'], array $properties = [])
    {
        if (strlen($file) == 0) {
            return false;
        }

        if (!is_array($ext)) {
            $ext = [$ext];
        }

        if (!file_exists(storage_path('uploads'))) {
            mkdir(storage_path('uploads'), 0700);
        }

        $userid = Auth::id() ?: 0;
        if ($file == "1") {
            return false;
        }

        file_put_contents(storage_path('uploads/' . $userid . '_' . $name . '_' . time() . '_' . uniqid()), $file);

        // this can be better; resuse mimey loop
        if (Str::startsWith($file, 'http')) {
            if (!$this->isValidUrl($file)) {
                return false;
            }
            $mimey = (new \Mimey\MimeTypes);
            try {
                return $this->addMediaFromUrl($file, [
                    $mimey->getMimeType('png'),
                    $mimey->getMimeType('jpg'),
                    $mimey->getMimeType('svg'),
                    'image/svg',
                ])
                    ->toMediaCollection('logo');
            } catch (\Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\MimeTypeNotAllowed $ex) {
                return false;
            } catch (\Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\UnreachableUrl $ex) {
                return false;
            }
        }

        $splitted = explode(';', $file, 2);
        if (count($splitted) !== 2)
            return false;
        list($type, $data) = $splitted;

        $splitted = explode(',', $data, 2);
        if (count($splitted) !== 2)
            return false;
        list(, $data) = $splitted;

        $data = base64_decode($data);

        $extension = null;
        $mimetype = null;
        foreach ($ext as $e) {
            $mimes = new \Mimey\MimeTypes;
            $mimetype = $mimes->getMimeType($e);
            if ($type === "data:" . $mimetype) {
                $extension = $e;
            }
        }
        if ($extension === null) {
            return false;
        }

        // associate the file with a model
        return $this->addMediaFromBase64($file)
            ->usingName($name . '.' . $extension)
            ->withCustomProperties($properties)
            ->usingFileName($name . '.' . $extension)
            ->toMediaCollection($name);
    }

    public function getLastMedia($key)
    {
        return $this->getMedia($key)->last();
    }

    public static function getHost($url)
    {
        $matches = [];
        if (!preg_match('/^http[s]{0,1}:\/\/(([A-z0-9\-]{1,}\.){0,}([A-z0-9\-]{2,}){1})/', $url, $matches)) {
            return null;
        }
        return $matches[1];
    }

    public static function isValidUrl($url)
    {
        if (($host = static::getHost($url)) === null) {
            return false;
        }
        $ip = gethostbyname($host);

        if (!$ip || Str::startsWith($ip, ['10.', '192.168.', '127.'])) {
            return false;
        }
        return true;
    }
}
