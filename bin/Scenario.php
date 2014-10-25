<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 24.10.2014
 * Time: 00:24
 */

require_once("mLearn4WebAPI.php");
require_once("Media.php");

/* TODO create a DOM */

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

                    $id = 0;
                    foreach($scenario->screenData as $screenKey=>$tmpScreen){

                        foreach($tmpScreen->screenElements as $elementKey=>$tmpElements){
                            $currentElement = new Element(
                                empty($tmpElements->title)?"":$tmpElements->title,
                                $tmpElements->type,
                                $tmpElements->required,
                                ++$id
                                );
                            if($tmpElements->type == "image"){
                                $media = new Media(
                                    $currentScenario->getScenarioID(),
                                    $currentDataCollection->getId(),
                                    $currentElement->getId(),
                                    0,
                                    null,
                                    null,
                                    $currentElement->getTitle(),
                                    null,
                                    $currentElement->getType()
                                    );
                                $currentElement->setMedia($media);
                            }
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

    public function getScenarioByID($id){
        foreach($this->scenarios as $scenario){
            if($scenario->getScenarioID()==$id)
                return $scenario;
        }
        return null;
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

    public function getCollectionByID($id){
        foreach($this->dataCollection as $collection){
            if($collection->getId()==$id)
                return $collection;
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getScenarioID()
    {
        return $this->scenarioID;
    }

    /**
     * @param mixed $scenarioID
     */
    public function setScenarioID($scenarioID)
    {
        $this->scenarioID = $scenarioID;
    }

    /**
     * @return array
     */
    public function getDataCollection()
    {
        return $this->dataCollection;
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

    public function addElement($element){array_push($this->elements , $element);}

    public function getElementByID($id){
        foreach($this->elements as $element){
            if($element->getId()==$id)
                return $element;
        }
        return null;
    }

    /**
     * @return array Elements
     */
    public function getElements(){return $this->elements;}

    public function addElementValue($id,$value){
        $this->elements[$id-1]->setValue($value);


        if($this->elements[$id-1]->getType() =="image"){
            // save media only if it has a valid extension
            if(preg_match("(.(jpg|jpeg|png|gif)$)i",$value)){
                !preg_match("(^\/)i",$value)?$value="/".$value:null;
                $media = $this->elements[$id-1]->getMedia();
                $media->setFile(config::$LEARN_API_URL.$value);
                $this->elements[$id-1]->setMedia($media);
                $media->update();
            }else{
                $this->elements[$id-1]->setMedia(null);
            }
        }
        //http://stackoverflow.com/questions/321158/checking-for-file-extensions-in-php-with-regular-expressions

    }

    /**
     * @return String
     */
    public function getGroupname(){return $this->groupname;}

    /**
     * @param String $groupname
     */
    public function setGroupname($groupname){$this->groupname = $groupname;}

    /**
     * @return String
     */
    public function getTimestamp(){return $this->timestamp;}

    /**
     * @param String $timestamp
     */
    public function setTimestamp($timestamp){$this->timestamp = $timestamp;}

    /**
     * @return Integer
     */
    public function getVersion(){return $this->version;}

    /**
     * @param Integer $version
     */
    public function setVersion($version){$this->version = $version;}

    /**
     * @return Integer
     */
    public function getId(){return $this->_id;}

    /**
     * @param Integer $id
     */
    public function setId($id){$this->_id = $id;}
}

class Element {
    private $title;
    private $type;
    private $required;
    private $id;
    private $value;
    private $media;

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
    /**
     * @return String
     */
    public function getTitle(){return $this->title;}

    /**
     * @param String $title
     */
    public function setTitle($title){$this->title = $title;}

    /**
     * @return String
     */
    public function getType(){return $this->type;}

    /**
     * @param String $type
     */
    public function setType($type){$this->type = $type;}

    /**
     * @return boolean
     */
    public function getRequired(){return $this->required;}

    /**
     * @param Boolean $required
     */
    public function setRequired($required){$this->required = $required;}

    /**
     * @return Integer
     */
    public function getId(){return $this->id;}

    /**
     * @param mixed $id
     */
    public function setId($id){$this->id = $id;}

    /**
     * @return String
     */
    public function getValue(){return $this->value;}

    /**
     * @param String $value
     */
    public function setValue($value){$this->value = $value;}

    /**
     * @return Media
     */
    public function getMedia(){return $this->media;}

    /**
     * @param Media $media
     */
    public function setMedia($media){$this->media = $media;}
}