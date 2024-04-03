<?php

//Get Occupation

$id = $_GET['id'];

if ((!isset($_GET['id']))) {
    echo "id parameter not specified";
    exit;
}

require 'includes/dbconn.php';

$sql = "SELECT o.ID, l.LangCode, l.LangName, o.Occupation
        FROM tbl_langs l
        LEFT JOIN
        tbl_occupations o
        ON o.LangCode = l.LangCode AND o.SignID = '$id';"; 

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