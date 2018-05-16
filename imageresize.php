<?php
function img_resize($filename, $newfilename, $new_width = 180, $quolity = 100) {
    list($width, $height, $type) = getimagesize($filename);
    if ($width < 610) {
        $new_width = $width;
        $quolity = 100;
    }
    $new_height = $height / ($width / $new_width);
    $image_p = imagecreatetruecolor($new_width, $new_height);
    switch ($type) {
        case 1:
            $image = imagecreatefromgif($filename);
            break;
        case 2:
            $image = imagecreatefromjpeg($filename);
            break;
        case 3:
            $image = imagecreatefrompng($filename);
            break;
        default:
            echo "unsupperted file format";
    }
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    imagejpeg($image_p, $newfilename, $quolity);
    imagedestroy($image_p);
}