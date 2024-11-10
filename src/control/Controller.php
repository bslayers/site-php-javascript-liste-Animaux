<?php
set_include_path("./src");
require_once("model/Animal.php");
require_once("model/AnimalStorageStub.php");
require_once("model/AnimalBuilder.php");
class Controller{

    private $view;

    private $animalsStorage;

    public function __construct(View $view, AnimalStorage $animalsStorage) {
        $this->view = $view;
        $this->animalsStorage = $animalsStorage;
    }

    public function showInformation($id) {
        $id = ltrim($id, '/');
        if (key_exists($id, $this->animalsStorage->readAll())) {
            $this->view->prepareAnimalPage($this->animalsStorage->read($id));
            $this->view->render();
        } 

        else {
            $this->view->prepareUnknownAnimalPage();
            $this->view->render();
        }
    }

    public function showList(){
        $this->view->prepareListePage($this->animalsStorage->readAll());
        $this->view->render();
    }
    
    public function saveNewAnimal(array $data) { 
        $animalBuilder = new AnimalBuilder($data);
        $animalBuilder->isValid();
        $errors = $animalBuilder->getError();
        if ($errors!=null){
            $this->view->prepareAnimalCreationPage($animalBuilder);
            $this->view->render();
        }
        else{
            $animal = $animalBuilder->createAnimal();
            $a = $this->animalsStorage->create($animal);
            $this->view->displayAnimalCreationSuccess($a);
            $this->view->render();
        }
       
    }
    
    //fonction qui vas lancer la mise à jour de l'animal avec les nouvelles informations
    public function updateAnimal(array $data, $id) {
        $animal = $this->animalsStorage->read($id); 
        $animalBuilder = new AnimalBuilder($data);
        $animalBuilder->isValid();
        $errors = $animalBuilder->getError();
        
        if ($errors != null) {
            $this->view->prepareAnimalModificationPage($animalBuilder, $id);
            $this->view->render();
        } 
        else {
            $animalBuilder->modificationAnimal($animal, $data[AnimalBuilder::NAME_REF], $data[AnimalBuilder::SPECIES_REF], $data[AnimalBuilder::AGE_REF]);
            
            $this->animalsStorage->update($id, $animal); 
            $this->view->displayAnimalModificationSuccess($id);
            $this->view->prepareAnimalPage($this->animalsStorage->read($id)); 
            $this->view->render();
        }
    }

    //fonction qui vas effectuer la supression de l'animal 
    public function supressionAnimal($id) {
        $animal = $this->animalsStorage->read($id); 
        if ($animal !== null) {
            // c'est ici que l'animal est effectivement supprimé de la liste
            $this->animalsStorage->delete($id); 
            $this->view->displayAnimalSupressionSuccess($animal);
        } 
        else {
            //l'animal n'as pas été trouvé on envoie un feedback
            $_SESSION["feedback"] = "La suppression a échoué, l'animal n'a pas été trouvé";
        }
        $this->view->render();
    }

}

?>