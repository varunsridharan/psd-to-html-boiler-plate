<?php
$files = glob(__DIR__.'/*.php');
$exc = array('functions.php','generate.php');
$folder = __DIR__.'/output/';
$is_gen = false;
if(!file_exists($folder)){ mkdir($folder); }

define("GENERATE_HTML",true);

if(isset($_GET['demo'])) {
    $folder .= 'demo/';
    $is_gen = true;
    define("GENERATE_HTML_DEMO",true);
    
}

if(isset($_GET['live'])) {
    $folder .= 'live/';
    $is_gen = true;
    define("GENERATE_HTML_LIVE",true);
    
}
function save_file($file_name,$data,$folder){
$file_name = basename($file_name);
$file_name = str_replace(".php",'.html',$file_name);
$data = str_replace(".php",'.html',$data);
file_put_contents($folder.$file_name,$data);
}

function recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
}

if($is_gen){
if(file_exists($folder)){ unlink($folder); }
mkdir($folder);

global $current_file;
foreach($files as $file){ 
    $current_file = $file;
   
    if(!in_array(basename($file),$exc)){
        ob_start();
            include($file);
        $data = ob_get_clean();
        
        save_file($file,$data,$folder);
        recurse_copy(__DIR__.'/assets/',$folder);
    }
    $current_file = null;
$current_active_page = null;
$current_active_page_link = null;
                         
}

}
?>

<hr/><br/><br/>
<a href="generate.php?live" class="myButton">Generate Live</a><br/><br/><br/>
<hr/><br/><br/>
<a href="generate.php?demo" class="map3">Generate Demo</a>

<style>
    
    .map3 {
	-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
	-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
	box-shadow:inset 0px 1px 0px 0px #ffffff;
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #f9f9f9), color-stop(1, #e9e9e9));
	background:-moz-linear-gradient(top, #f9f9f9 5%, #e9e9e9 100%);
	background:-webkit-linear-gradient(top, #f9f9f9 5%, #e9e9e9 100%);
	background:-o-linear-gradient(top, #f9f9f9 5%, #e9e9e9 100%);
	background:-ms-linear-gradient(top, #f9f9f9 5%, #e9e9e9 100%);
	background:linear-gradient(to bottom, #f9f9f9 5%, #e9e9e9 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#f9f9f9', endColorstr='#e9e9e9',GradientType=0);
	background-color:#f9f9f9;
	-moz-border-radius:6px;
	-webkit-border-radius:6px;
	border-radius:6px;
	border:1px solid #dcdcdc;
	display:inline-block;
	cursor:pointer;
	color:#666666;
	font-family:Arial;
	font-size:21px;
	font-weight:bold;
	padding:20px 76px;
	text-decoration:none;
	text-shadow:0px 1px 0px #ffffff;
}
.map3:hover {
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #e9e9e9), color-stop(1, #f9f9f9));
	background:-moz-linear-gradient(top, #e9e9e9 5%, #f9f9f9 100%);
	background:-webkit-linear-gradient(top, #e9e9e9 5%, #f9f9f9 100%);
	background:-o-linear-gradient(top, #e9e9e9 5%, #f9f9f9 100%);
	background:-ms-linear-gradient(top, #e9e9e9 5%, #f9f9f9 100%);
	background:linear-gradient(to bottom, #e9e9e9 5%, #f9f9f9 100%);
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#e9e9e9', endColorstr='#f9f9f9',GradientType=0);
	background-color:#e9e9e9;
}
.map3:active {
	position:relative;
	top:1px;
}

.myButton {
	-moz-box-shadow: 2px 2px 5px -3px #3dc21b;
	-webkit-box-shadow: 2px 2px 5px -3px #3dc21b;
	box-shadow: 2px 2px 5px -3px #3dc21b;
	background-color:#44c767;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
	display:inline-block;
	cursor:pointer;
	color:#ffffff;
	font-family:Arial;
	font-size:21px;
	font-weight:bold;
	font-style:italic;
	padding:20px 76px;
	text-decoration:none;
	text-shadow:0px 1px 2px #2f6627;
}
.myButton:hover {
	background-color:#5cbf2a;
}
.myButton:active {
	position:relative;
	top:1px;
}

</style>