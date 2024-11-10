<?php
include_once("AnimalStorage.php");
include_once("Animal.php");
class AnimalStorageStub implements AnimalStorage{

    private $animalsTab;

    public function __construct() {
        $this->animalsTab = array(
            'medor' => new Animal('Médor', 'chien', 2),
            'felix' => new Animal('Félix', 'chat',3),
            'denver' => new Animal('Denver', 'dinosaure',5),
        );
    }

    public function read($id){  
        if (array_key_exists($id, $this->animalsTab)) {
            return $this->animalsTab[$id];
        }
        return null;
    }

    public function readAll() {
        return $this->animalsTab;
    }

    public function create(Animal $a) {
        throw new Exception("create");
    }

    public function delete($id) {
        throw new Exception("delete");
    }

    public function update($id, Animal $a) { 
        throw new Exception("update");
    }

}


?>