<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 23.10.2014
 * Time: 23:28
 */

$ml4wClient = new mLearn4WebAPI();
$scenarios = $ml4wClient->getScenarios();

$scenarios2 = $login->getScenarioHolder()->getScenarios();

$output = "<h2>Scenarios</h2>";
foreach($scenarios as $key=>$scenario){
    $output .= "<div>";
    $output .=   "<h3>".$scenario->title ."</h3>";
    $output .=   "<p>".$scenario->description."</p>";
    $output .=   "<a href='?site=scenario&id=$scenario->_id'>view scenario</a>";
    $output .= "</div>";
}
echo $output;

