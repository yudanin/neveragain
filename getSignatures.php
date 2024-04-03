<?php

//Get signatures

$lang = $_GET['lang'];

if ((!isset($_GET['lang'])) || (strlen($lang) > 3) || (is_numeric($lang))) {
    echo "lang parameter not specified";
    exit;
}

require 'includes/dbconn.php';

$sql = "SELECT o.SignID, s.SignName, s.SignNameLatin, s.CityCountry, o.Occupation
          FROM tbl_signatures s, tbl_occupations o
        WHERE s.IfApproved = 1
	          AND
	          s.ID = o.SignID 
              AND (CASE WHEN EXISTS (SELECT 1 FROM tbl_occupations oc WHERE o.SignID = oc.SignID
                                    AND oc.LangCode = '" . $lang . "')
                        THEN o.LangCode = '" . $lang . "'
                        ELSE o.LangCode <> ''
                    END)
        GROUP BY (o.SignID)
        ORDER BY s.DateTimeStamp ASC;";

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