<?php
set_include_path("./src");
require_once("model/Animal.php");
require_once("model/AnimalBuilder.php");
class ViewJSON {
    protected $router;
    protected $content;

    public function __construct(Router $router) {
        $this->router = $router;
        $this->content = [];
    }

    public function ajouterAnimal(Animal $animal) {
        $this->content = [
            AnimalBuilder::NAME_REF => $animal->getNom(),
            AnimalBuilder::SPECIES_REF => $animal->getEspece(),
            AnimalBuilder::AGE_REF => $animal->getAge()
        ];
    }

    public function render() {
        header('Content-Type: application/json');
        echo json_encode($this->content);
    }

    public function inconnu() {
        $this->content = [
            AnimalBuilder::NAME_REF => 'INCONNU',
            AnimalBuilder::SPECIES_REF => 'INCONNU',
            AnimalBuilder::AGE_REF => 'INCONNU'
        ];
    }
    
}
?>