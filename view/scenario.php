<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 23.10.2014
 * Time: 23:23
 */
//src http://top-frog.com/2006/09/30/setting_the_base_directory_in_php/
define('DOCUMENT_ROOT', dirname(realpath(__FILE__)).'/');

require_once(DOCUMENT_ROOT."../code/mLearn4WebAPI.php");

$scenarioHolder = $login->getScenarioHolder();
$scenarios = $scenarioHolder->getScenarios();
$output = "";

if(!isset($_GET["id"])){
    /* SHOW ALL SCENARIOS */

    $output = "<h2>Scenarios</h2>";
    foreach($scenarios as $scenario){
        $output .= "<div>";
        $output .=   "<h3>".$scenario->getTitle() ."</h3>";
        $output .=   "<p>".$scenario->getDescription()."</p>";
        $output .=   "<a href='?page=scenario&id=".$scenario->getScenarioID()."'>view scenario</a>";
        $output .= "</div>";
    }
}else{
    /* SHOW A CERTAIN SCENARIO */

    foreach($scenarios as $scenario){
        if($scenario->getScenarioID()!=$_GET["id"]) {
            continue;
        }else{
            $output = "<h2>".$scenario->getTitle()."</h2>";
            foreach($scenario->getDataCollection() as $dataCollection){
                $output .= "<div>";
                $output .=   "<h3>".$dataCollection->getGroupname()."</h3>";
                foreach($dataCollection->getElements() as $element){
                    $output .= "<h4>".$element->getTitle()."</h4>";
                    if(is_array($element->getValue())){
                        $output .= "<ul>";
                        foreach($element->getValue() as $item){
                            $output .= "<li>$item</li>";
                        }
                        $output .= "</ul>";
                    }else{
                        $media = $element->getMedia();
                        if($media!=null){
                            $output .= $media->__toString();
                        }else{
                            $output .= $element->getValue();
                        }
                    }
                    $output .= "</p>";
                }
                $output .= "</div>";
            }
        }
    }
}

$output .= "<a href='?'>back to main</a>";
echo $output;



