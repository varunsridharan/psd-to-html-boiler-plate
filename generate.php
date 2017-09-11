<?php
$files = glob(__DIR__.'/*.php');
$exc = array('functions.php','generate.php');

define("GEN_HTML",true);

foreach($files as $file){
    if(!in_array(basename($file),$exc)){
        ob_start();
            include($file);
        $data = ob_get_clean();
        save_file($file,$data);
    }
}

function save_file($file_name,$data){
    $file_name = basename($file_name);
    $file_name = str_replace(".php",'.html',$file_name);
    file_put_contents(__DIR__.'/output/'.$file_name,$data);
}