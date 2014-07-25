<?php
function cssMin($filename) {
    $file = fopen ($filename, 'r');
    $css = fread ($file, filesize($filename));
    $css = preg_replace ('/\s+/', ' ', $css);
    $css = preg_replace ('/\/\*.*?\*\//', '', $css);
    $css = preg_replace ('/\}/', '}'.chr(13), $css);
    return $css;
}
?>
