<?php

$sprite = substr(parse_url($_GET["url"], PHP_URL_PATH),1);
//echo $sprite;
$hue = $_GET["hue"];

function modulateImage($imagePath, $hue, $brightness, $saturation) {
    $imagick = new \Imagick(realpath($imagePath));
    $imagick = $imagick->coalesceImages();

    do {
      $imagick->modulateImage($brightness, $saturation, $hue);
    } while( $imagick->nextImage() );

    //$imagick = $imagick->deconstructImages();
    header("Content-Type: image/gif");
    echo $imagick->getImagesBlob();
}

modulateImage($sprite, $hue, 100, 100);

?>