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


$login = Login::getInstance();
$user = $login->getUser();
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
        </section>
    </body>
</html>
