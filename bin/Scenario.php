<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 24.10.2014
 * Time: 00:24
 */

require_once("mLearn4WebAPI.php");

class ScenarioHolder{
    private $scenarios = array();
    private static $instance;

    static public function getInstance(){
        if (null === self::$instance){
            self::$instance = new self;
            $ml4webClient = new mLearn4WebAPI();
            $senarios = $ml4webClient->getScenarios();
            //run trough senario templates
            foreach($senarios as $scenario) {
                $dataCollections = $ml4webClient->getDataFromScenario($scenario->_id);
                //has this template data, then create a new scenario with data
                if(empty($dataCollections)) continue;

                $currentScenario = new Scenario(
                    $scenario->_id,
                    $scenario->title,
                    $scenario->description
                );

                // go trough collected data
                foreach($dataCollections as $dataCollection){

                    if(empty($dataCollection->data) || $dataCollection->_id == "54477d4af70096dc60645676") continue;

                    $currentDataCollection = new DataCollection(
                        $dataCollection->_id,
                        $dataCollection->groupname,
                        $dataCollection->timestamp,
                        $dataCollection->__v
                    );

                    $currentScenario->addDataCollection($currentDataCollection);

                    foreach($scenario->screenData as $screenKey=>$tmpScreen){
                        foreach($tmpScreen->screenElements as $elementKey=>$tmpElements){
                            $currentElement = new Element(
                                empty($tmpElements->title)?"":$tmpElements->title,
                                $tmpElements->type,
                                $tmpElements->required,
                                $elementKey+1
                                );
                            $currentDataCollection->addElement($currentElement);
                        }
                    }

                    //fill elements with the values
                    foreach($dataCollection->data as $tmpScreen){
                        foreach($tmpScreen as $dataValues){
                            $id = str_split($dataValues->elementId, 2);
                            $prefix = $id[0];
                            $id = $id[1];
                            $currentDataCollection->addElementValue($id,$dataValues->value);
                        }
                    }
                }
                array_push(self::$instance->scenarios, $currentScenario);
            }
        }
        return self::$instance;
    }

    public function getScenarios(){
        return $this->scenarios;
    }
}

class Scenario {
    private $title;
    private $description;
    private $dataCollection = array();
    private $scenarioID;


    /**
     * @param $scenarioID
     * @param $title
     * @param $des
     */
    public function __construct($scenarioID,$title,$des){
        $this->title = $title;
        $this->description = $des;
        $this->scenarioID = $scenarioID;

    }

    public function addDataCollection($collection){
        array_push($this->dataCollection, $collection);
    }

}

class DataCollection {
    private $elements = array();
    private $groupname;
    private $timestamp;
    private $version;
    private $_id;

    /**
     * @param $_id
     * @param $groupname
     * @param $timestamp
     * @param $version
     */
    public function __construct($_id,$groupname,$timestamp,$version){
        $this->_id = $_id;
        $this->groupname = $groupname;
        $this->timestamp = $timestamp;
        $this->version = $version;
    }

    public function addElement($element){
        array_push($this->elements , $element);
    }

    public function getElements(){
        return $this->elements;
    }


    public function addElementValue($id,$value){
        $this->elements[$id-1]->addValue($value);
    }
}

class Element {
    private $title;
    private $type;
    private $required;
    private $id;
    private $value;

    /**
     * @param $title
     * @param $type
     * @param $required
     * @param $id
     * @param null $value
     */
    public function __construct($title,$type,$required,$id,$value=null){
        $this->title = $title;
        $this->type = $type;
        $this->required = $required;
        $this->id = $id;
        $this->value = $value;
    }

    public function addValue($value){
        $this->value = $value;
    }
}