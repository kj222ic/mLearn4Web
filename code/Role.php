<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 25.10.2014
 * Time: 22:23
 */

class Role {
    private $id;
    private $name;
    private $description;

    public function add($id=null,$name,$description=null){

    }

}

/**
 * Class Permission
 */
class Permission{
    public function __construct($class){
    }
}

/**
 * Class Operation
 */
class Operation{
    public function __construct(){

    }
}


class Authorization{
    public $role;
    public $Permission;
    public $operation;
    public $user;

    public function __construct(){
        $this->role = new Role();
    }
}