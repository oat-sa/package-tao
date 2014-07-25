<?php
require_once(dirname (__FILE__).'/jsmin.php');
require_once(dirname (__FILE__).'/cssmin.php');
    
function minifyJSFiles($files, $outputFileName) {
    $i = 0;
    foreach ($files as $file){
		var_dump($file);
        $jsMin = new JSMin($file, $outputFileName, null, !$i);
        $jsMin -> minify();
        $i++;   
    }
}

function minifyCSSFiles ($files, $outputFileName) {
    $cssMin = '';
    foreach ($files as $file){
        $cssMin .= cssMin($file)."\n";
    }
    $outputFile = fopen($outputFileName, 'w');
    fwrite($outputFile, $cssMin, strlen($cssMin));
    fclose($outputFile);
}

?>
