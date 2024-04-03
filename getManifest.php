<?php

//Get Manifest

$lang = $_GET['lang'];

if ((!isset($_GET['lang'])) || (strlen($lang) > 3) || (is_numeric($lang))) {
    echo "lang parameter not specified";
    exit;
}

require 'includes/dbconn.php';

$sql = "SELECT Title, Subtitle, ManifestText FROM tbl_manifests WHERE LangCode = '$lang';";
$res = mysqli_query($conn, @$sql);
if($res === false){
    echo mysqli_error($conn);
} else {
    $data = array();
    while ($row=mysqli_fetch_assoc($res)){
        $data[]=$row;
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

?>
