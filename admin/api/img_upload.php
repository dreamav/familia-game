<?php
define('DR',rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/');
require DR . 'lib/functions.php';

$output_dir = DR . "images/";

if (isset($_POST['q_nomer'])) $file_name = "question".$_POST['q_nomer']."-img".$_POST['img_nomer'];
if (isset($_POST['level_id'])) $file_name = "game_level".$_POST['level_id']."-img";

if(isset($_FILES["file"]))
{
    //Filter the file types , if you want.
    if ($_FILES["file"]["error"] > 0)
    {
      echo "Error: " . $_FILES["file"]["error"] . "<br>";
    }
    else
    {
    	$full_file_name = $file_name.'.'.pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
        //move the uploaded file to uploads folder;
        move_uploaded_file($_FILES["file"]["tmp_name"], $output_dir. $full_file_name);

        // $link = 'http://'.$_SERVER['HTTP_HOST'].'/letter/img/electron/logos/'.$full_file_name;

        // editPartnerCode('logo',$link,$_POST['cert_id']);
     

    }

}

echo $full_file_name;


?>