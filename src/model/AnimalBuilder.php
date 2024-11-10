<?php
include_once("Animal.php");
class AnimalBuilder{

    private $data;
    private $error;
    const NAME_REF = 'NOM';
    const SPECIES_REF = 'ESPECE';
    const AGE_REF = 'AGE';

    public function __construct($data){
        $this->data = $data;
        $this->error = null;
    }

    public function getData() {
        return $this->data;
    }

    public function getError() {
        return $this->error;
    }

    public function createAnimal(){
        $nom = trim(htmlspecialchars($this->data[self::NAME_REF], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8'));
        $espece = trim(htmlspecialchars($this->data[self::SPECIES_REF], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8'));
        $age = trim(htmlspecialchars($this->data[self::AGE_REF], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8'));
        $age = $this->data[self::AGE_REF];
        settype($age, "integer");
        return new Animal($nom,$espece,$age);
    }

    public function modificationAnimal(Animal $animal, $nom, $espece, $age) {
        $animal->setNom($nom);
        $animal->setEspece($espece);
        $animal->setAge($age);
    }

    public function isValid() {
        if ($this->data!=null){
            $nom = trim(htmlspecialchars($this->data[self::NAME_REF], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8'));
            $espece = trim(htmlspecialchars($this->data[self::SPECIES_REF], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8'));
            $age = trim(htmlspecialchars($this->data[self::AGE_REF], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8'));
        }
        else{
            $nom = "";
            $espece = "";
            $age = 0;
        }
        settype($age, "integer");

        $errors = [];
        if ($nom==="" || is_numeric($nom)){
            $errors[self::NAME_REF] = "Veuillez ecrire un nom valide";
        }
        if ($espece==="" || is_numeric($espece)){
            $errors[self::SPECIES_REF] = "Veuillez ecrire une espece valide";
        }
        if (gettype($age)!== "integer" || $age==="null" || $age <= 0 || !is_numeric($age)) {
            $errors[self::AGE_REF] = "L'age doit etre un chiffre/nombre et ne doit pas être négative ni nul";
        }
        $this->error = $errors;
    }
}
?>