<?php

// S1 Copy And Create
$presentationname = $_POST["presname"];
$prodname = $_POST["prodname"];
$pkcountry = $_POST["pkcountry"];
// $pkname = 'PCSK9_KSA_001';
$pkname = $prodname . "_" . $pkcountry;


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
$src = 'players/polaris';
$dst = 'output/' . $presentationname;
recurse_copy($src,$dst);


// S3 Get images BG Name

$inputimages = $_POST["inputimages"];
$inputimages = str_replace("\\","/",$inputimages);
$inputimages = $inputimages . '/';
echo $inputimages;
$slidesname = scandir($inputimages);
$results = [];
foreach($slidesname as $key => $value){
	$my_name_array = explode(".", $value);
	$results[$key] = $my_name_array[0];
}
$numofslides = count($results);




// S4 create folders for all slides need
//
for ($i=2; $i < $numofslides; $i++) {
  $src = 'output/' . $presentationname . '/001';
  $dst = 'output/' . $presentationname . '/' . $pkname . "_" . $results[$i] . '_Slide';
  recurse_copy($src,$dst);
}



// S5 remove Mail slide
//
function deleteDirectory($path) {
    if (!file_exists($path)) {
        return true;
    }

    if (!is_dir($path)) {
        return unlink($path);
    }

    foreach (scandir($path) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($path . DIRECTORY_SEPARATOR . $item)) {
            return false;
            echo "no";
        }

    }

    return rmdir($path);
}
deleteDirectory('output/' . $presentationname . '/001');



// put images as background
//
for ($i=2; $i < $numofslides; $i++) {

  $file = $inputimages . $slidesname[$i];

  $newfile = 'output/' . $presentationname . '/' . $pkname . '_' . $results[$i]  . '_Slide' . '/images/bg.jpg';

  if (!copy($file, $newfile)) {
      echo "failed to copy $file...\n";
  }
}
// put images as full
//
for ($i=2; $i < $numofslides; $i++) {

  $file = $inputimages . $slidesname[$i];

  $newfile = 'output/' . $presentationname . '/' . $pkname . '_' . $results[$i] . '_Slide' . '/' . $pkname . '_' . $results[$i] . '_Slide' . '.jpg' ;

  if (!copy($file, $newfile)) {
      echo "failed to copy $file...\n";
  }
}
//
// S Get thumbs
//
// $inputimagesthumbs = $_POST["inputimagesthumbs"];
// for ($i=2; $i < $numofslides; $i++) {
//
//   $file = $inputimagesthumbs . $slidesname[$i];
//
//   $newfile = 'output/' . $presentationname . '/' . $pkname . '_' . $results[$i] . '/media/images/thumbnails/200x150.jpg';
//
//   if (!copy($file, $newfile)) {
//       echo "failed to copy $file...\n";
//   }
// }


// rename index file

for ($i=2; $i < $numofslides; $i++) {
  rename('output/' . $presentationname . '/' . $pkname . '_' . $results[$i] . '_Slide' . '/index.html','output/' . $presentationname . '/' . $pkname . '_' . $results[$i] . '_Slide' . '/' . $pkname . '_' . $results[$i] . '_Slide' . '.html');
}



// S6 Edite html files to change head title to slide name

for ($i=2; $i < $numofslides; $i++) {

  $indexspath = 'output/' . $presentationname . '/' . $pkname . '_' . $results[$i] . '_Slide' . '/' . $pkname . '_' . $results[$i] . '_Slide' . '.html';

  $file = fopen($indexspath,"w");
  fwrite($file,'﻿<!DOCTYPE>
          <html>
          	<head>
          		<title>'. $pkname . '_' . $results[$i] .'</title>
          		<meta charset="UTF-8">
          		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
          		<meta name="keywords" content="css animation, animation, css3, webkit, mozilla, w3c, dan eden, @_dte, animated, animate.css, dan eden">
          		<meta name="viewport" content="width=1024">
          		<meta name="viewport" content="initial-scale = 1.0, user-scalable = yes, maximum-scale = 2.0">
          		<meta name="apple-mobile-web-app-capable" content="yes">
          		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
          		<meta name="format-detection" content="telephone=no">
          		<link rel="stylesheet" href="css/style.css" type="text/css">
          		<script type="text/javascript" src="js/jquery.js"></script>
          		<script type="text/javascript" src="js/NativeBridge.js"></script>
          		<script type="text/javascript" src="js/insert.js"></script>
          		<link rel="stylesheet" href="css/animate.css">
          		<script type="text/javascript" id="OnAnimat-js"></script>
          		<script type="text/javascript" id="customs-js">
          			function animat(x){
          				if(x>=1){

          				}
          				else if(x<=0){

          				}
          			}

          			$(document).ready(function () {
          				animat(1);
          			});
          		</script>
          	</head>
          	<body>
          	<div id="Main_Container">


          	</div>
          	</body>
          </html>
  ');
  fclose($file);
}


header("Location: polaris.php");
exit;

?>
