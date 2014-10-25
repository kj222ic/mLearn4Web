<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 24.10.2014
 * Time: 22:15
 */
require_once("bin/dbConnector.php");
require_once("bin/Login.php");
require_once("bin/Config.php");

$login = Login::getInstance();
$user = $login->getUser();
$scenarios = $login->getScenarioHolder();

$scenario = $scenarios->getScenarioByID($_GET["sID"]);
$collection = $scenario->getCollectionByID($_GET["cID"]);
$element = $collection->getElementByID($_GET["eID"]);
$media = $element->getMedia();

$db = new dbConnector();


$dbMedia = $db->doSql("getMediaBySCEID",array($_GET["sID"],$_GET["cID"],$_GET["eID"]));
if(empty($dbMedia)){
    $db->doSql("addMedia",array(
        $_GET["sID"],
        $_GET["cID"],
        $_GET["eID"],
        0,
        $user->getProfileId(),
        null,
        $media->getTitle(),
        $media->getType(),
        "http://celtest1.lnu.se:3030".$media->getFile()
    ));
    $insertID = mysql_insert_id();
}else{
    $insertID = $dbMedia[0]["Media_id"];
}

$db->doSql("addMedia",array(
    $_GET["sID"],
    $_GET["cID"],
    $_GET["eID"],
    ++$dbMedia[0]["Version"],
    $user->getProfileId(),
    $insertID,
    $media->getTitle(),
    $media->getType(),
    Config::$MY_URL.Config::$UPLOAD_FOLDER.basename($_GET["file"])
));

$image = file_get_contents($_GET["file"]);

define('DOCUMENT_ROOT', dirname(realpath(__FILE__)).'/');
$file = DOCUMENT_ROOT.Config::$UPLOAD_FOLDER.basename($_GET["file"]);
file_put_contents($file, $image);

$media->update();
//add new Media


echo  "<script type='text/javascript'>";
echo "window.close();";
echo "</script>";
