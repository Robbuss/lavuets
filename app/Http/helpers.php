<?php

// media object, default kan url zijn
function mediaToBase64($media, $default="", $width = null, $height = null, $returnFilename = false, $mediaQuality = 100, $allowsvg = true) {
    if($media === null || !file_exists($media->getPath()))
        return $default;

    // $file = media2cached($media, $width, $height, $mediaQuality, false, $allowsvg);
    $base64 = base64_encode(file_get_contents($media->getPath()));
    $mime = ($media->mime_type === 'image/svg' && !$allowsvg) ? 'image/png': $media->mime_type;
    return "data:".$mime.";base64,".$base64;
}
