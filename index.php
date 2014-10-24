<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kevin
 * Date: 03.10.14
 * Time: 20:55
 * To change this template use File | Settings | File Templates.
 */
require_once("autoload.php");
require_once("./bin/Config.php");
require_once("./bin/Login.php");
require_once("./bin/dbConnector.php");
require_once("./bin/mLearn4WebAPI.php");
require_once("./bin/Media.php");

$login = Login::getInstance();
$user = $login->getUser();
/**
 * PUT
 */
/*
    // The data to send to the API
    $postData = array(
        "data" =>
            array(
                "screen1" =>
                    array(
                        "elementId" => "cl3",
                        "type" => "location",
                        "value" => ""
                    ),array(
                        "elementId" => "ci2",
                        "type" => "image",
                        "value" => "/files/24796-gll960"
                    )
            )
    );

    $str_data = json_encode($postData);
    $serviceHost = "http://celtest1.lnu.se:3030";
    $baseUrlAPI = $serviceHost . "/mlearn4web";
    $datasetPost = $baseUrlAPI . "/updatedata";
    $url = $datasetPost . '/54477d4af70096dc60645676';

    $headers= array('Accept: application/json','Content-Type: application/json');

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$str_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close($ch);
*/
?>


<!DOCTYPE HTML>
<html class="<?php echo ($user->isVerified()==true)?$user->getRole():"" ?>">
    <head>
        <!link href="css/main.css" type="text/css" rel="stylesheet">
    </head>
    <body>
        <header>
            <h1>Hello <?php echo ($user->isVerified()==true)?$user->getUsername():"Stranger"?></h1>
            <div>
                <?php if($user->isVerified()==true){
                    echo "<span>Role :".$user->getRole()."</span>";
                    echo "<span>ID :".$user->getProfileId()."</span>";

                }?>
            </div>
            <ul class="nav">
                <?php echo $login ?>
            </ul>
        </header>
        <section>
        <?php
            isset($_GET["site"])?:$_GET["site"]="";
            switch($_GET["site"]){
                case "scenario" : include("scenario.php"); break;
                case "" : include("scenarios.php"); break;
            }
        ?>
        </section>
    </body>
</html>
