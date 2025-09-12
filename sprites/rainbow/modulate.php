<?php
/**
 * Returns a GIF sprite with hue adjustment
 *
 * Example:
 *   /sprites/rainbow/modulate.php?url=https://static.typo.rip/sprites/regular/sptFlying_Scarf.gif&hue=50
 *
 * Params:
 *   - url (string, required)  URL to sprite (path must resolve inside sprites dir or scenes dir)
 *   - hue (float, required)   Hue shift in range from 0 to 200, 100 means no shift, 0 is a special value that makes the sprite greyscale
 *
 * Output:
 *   - image/gif
 */

$projectRoot = realpath(__DIR__ . '/../../');

if ($projectRoot === false) {
    http_response_code(500);
    exit('Server misconfig');
}

$projectRoot = rtrim($projectRoot, '/') . '/';
$spritesDir = realpath($projectRoot . 'sprites');
$scenesDir  = realpath($projectRoot . 'scenes');

if (
    $spritesDir === false 
    || !is_dir($spritesDir)
    || $scenesDir === false 
    || !is_dir($scenesDir)
) {
    http_response_code(500); 
    exit('Server misconfig');
}

$spritesDir = rtrim($spritesDir, '/') . '/';
$scenesDir = rtrim($scenesDir, '/') . '/';

// Resolve path
$rawUrl = $_GET['url'] ?? '';
$path = parse_url($rawUrl, PHP_URL_PATH) ?? '';
$path = ltrim($path, '/');

$candidatePath = $projectRoot . $path;
$resolvedPath = realpath($candidatePath);

if (
    $resolvedPath === false
    || (
        !str_starts_with($resolvedPath, $spritesDir) 
        && !str_starts_with($resolvedPath, $scenesDir)
    )
    || !str_ends_with(strtolower($resolvedPath), '.gif')
    || !is_readable($resolvedPath)
) {
    http_response_code(404);
    exit('Not found');
}

// Resolve hue and saturation
$hue = $_GET['hue'] ?? null;

if ($hue === null) {
    http_response_code(400);
    exit('Hue must be provided');
}

$hue = (float) $hue;

if ($hue < 0.0 || $hue > 200.0) {
    http_response_code(400);
    exit('Hue must be within range from 0 to 200');
}

$saturation = match ($hue) {
    0.0 => 0.0,
    default => 100.0,
};

// Modulate sprite
try {
    $imagick = new Imagick($resolvedPath);
    $imagick = $imagick->coalesceImages();

    do {
        $imagick->modulateImage(
            brightness: 100.0,
            saturation: $saturation,
            hue: $hue,
        );
    } while ($imagick->nextImage());

    $imagick->setFirstIterator();

    header('Content-Type: image/gif');
    header('Cache-Control: max-age=2592000');

    echo $imagick->getImagesBlob();
} catch (Throwable $e) {
    http_response_code(500);
    exit('Unable to process image');
}
