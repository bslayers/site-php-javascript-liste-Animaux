<?php
set_include_path("./src");
require_once("model/Animal.php");
require_once("model/AnimalBuilder.php");
class View{

    protected $router;
	protected $title;
	protected $content;
    protected $menu;
    protected $feedback;
    protected $script;

    public function __construct(Router $router,$feedback){
        $this->router = $router;
		$this->title = null;
		$this->content = null;
        $this->feedback = $feedback;
        $this->script = "";
        $this->menu = [
            ['url' => '', 'text' => 'Accueil'],
            ['url' => '/liste', 'text' => 'Liste des Animaux'],
            ['url' => $router->getAnimalCreationURL(), 'text' => 'Ajouter Animaux']
        ];
    }

    public function render(){
        
        echo '<!DOCTYPE html>';
        echo '<html lang="fr">';
        echo '<head>';
        echo '<title>' . $this->title . '</title>';
        echo $this->script;
        echo '</head>';
        echo '<body>';
        echo '<h1>' . $this->title . '</h1>';
        echo '<p>' . $this->feedback . '</p>';
        echo '<ul>';
        foreach ($this->menu as $item) {
            echo '<li><a href="'.$_SERVER['SCRIPT_NAME'] . $item['url'] . '">' . $item['text'] . '</a></li>';
        }
        echo '</ul>';
        echo $this->content;
        echo '</body>';
        echo '</html>';
    }

    public function prepareTestPage(){
        $this->title = 'Page de Test';
        $this->content = 'TEST';
    }

    public function prepareAnimalPage(Animal $animal){
        $this->title = "Page sur ". $animal->getNom();
        $this->content = $animal->getNom() ." est un animal de l'espèce ". $animal->getEspece();
        $this->content .= "<br>Il a ".$animal->getAge()."ans.";
    }

    public function prepareUnknownAnimalPage(){
        $this->title = "Page sur Animal inconnu";
        $this->content = "Animal inconnu";
    }
    public function prepareMainPage(){
        $this->title = "Page Principal";
        $this->content = "Aucun contenu";
    }

    public function prepareListePage(array $ListeAnimal){
        $this->script = '<script src="../src/view/detailAnimal.js"></script>';
        $this->title = "Liste des Animaux";
        $this->content = "";
        foreach ($ListeAnimal as $animal => $value){
            $this->content .= "<a href=" . $this->router->getAnimalURL($animal) . ">" . $value->getNom() . "</a>";
            $this->content.=" <br> <a href = ".$this->router->getAnimalModificationURL($animal).">  modifier </a>";
            $this->content.=" <a href = ".$this->router->getAnimalDeleteURL($animal)."> suprimer  </a>";
            $this->content .= '<button onclick="getAnimalDetails(\'' . $animal . '\')" style="margin-left: 10px;" id="bouton-'.$animal.'">Détails</button><br>';
            $this->content .='<p id=details-'.$animal.' > </p>';
        }
    }

    public function prepareAnimalCreationPage(AnimalBuilder $animalBuilder) {
        $this->title = "Créer animal";
        $error = $animalBuilder->getError();
        $nom = "";
        if (array_key_exists(AnimalBuilder::NAME_REF, $animalBuilder->getData())) {
            $nom = trim(htmlspecialchars($animalBuilder->getData()[AnimalBuilder::NAME_REF], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8'));
        }
        $espece = "";
        if (array_key_exists(AnimalBuilder::SPECIES_REF, $animalBuilder->getData())) {
            $espece = trim(htmlspecialchars($animalBuilder->getData()[AnimalBuilder::SPECIES_REF], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8'));
        }
        $age = "";
        if (array_key_exists(AnimalBuilder::AGE_REF, $animalBuilder->getData())) {
            $age = trim(htmlspecialchars($animalBuilder->getData()[AnimalBuilder::AGE_REF], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8'));
        }
        $this->content = '<form method="POST" action="' . $this->router->getAnimalSaveURL() . '">
                            <label>Nom :</label>
                            <input type="text" name="' . AnimalBuilder::NAME_REF . '" id="' . AnimalBuilder::NAME_REF . '" placeholder="' . $nom . '" value="' . $nom . '"><br>
                            <label>Espèce :</label>
                            <input type="text" name="' . AnimalBuilder::SPECIES_REF . '" id="' . AnimalBuilder::SPECIES_REF . '" placeholder="' . $espece . '" value="' . $espece . '"><br>
                            <label>Âge :</label>
                            <input type="number" name="' . AnimalBuilder::AGE_REF . '" id="' . AnimalBuilder::AGE_REF . '" placeholder="' . $age . '" value= "' . $age . '"><br>';
        if ($error !== null) {
            foreach ($error as $field => $errors) {
                $this->content .= '<p style="color: red">Erreur pour le champ ' . $field . ' : ' . $errors . '</p>';
            }
        }
        $this->content .= '<input type="submit" value="Enregistrer">
                        </form>';
    }


    public function prepareDebugPage($variable) {
        $this->title = 'Debug';
        $this->content = '<pre>'.htmlspecialchars(var_export($variable, true)).'</pre>';
    }

    public function prepare404Page() {
        $this->title = '404';
        $this->content = 'Page non trouvée';
    }

    public function displayAnimalCreationSuccess($id) {
        $url = $this->router->getAnimalURL($id);
        $_SESSION['feedback'] = 'Animal créé avec succès';
        $this->router->POSTredirect($url,$_SESSION['feedback']);

    }

    public function displayAnimalModificationSuccess($id){
        $url = $this->router->getAnimalURL($id);
        $_SESSION['feedback'] = 'Animal modifié avec succès';
        $this->router->POSTredirect($url, $_SESSION['feedback']);
    }

    public function displayAnimalSupressionSuccess($animal){
        $url = $this->router->getListeURL();
        $_SESSION['feedback'] = $animal->getNom().' surpimé avec succès';
        $this->router->POSTredirect($url,$_SESSION['feedback']);
    }

    public function prepareAnimalModificationPage(AnimalBuilder $animalBuilder, $id) {
        $this->title = "Modifier animal";
        if (!empty($id)) {
            $animal = $this->router->getAnimalStorageStub()->read($id);
            if ($animal !== null) {
                $nom = $animal->getNom();
                $espece = $animal->getEspece();
                $age = $animal->getAge();
                $this->content = '<form method="POST" action="' . $this->router->getAnimalUpdateURL($id) . '">
                                    <label>Nom :</label>
                                    <input type="text" name="' . AnimalBuilder::NAME_REF . '" id="' . AnimalBuilder::NAME_REF . '" placeholder="' . $nom . '" value="' . $nom . '"><br>
                                    <label>Espèce :</label>
                                    <input type="text" name="' . AnimalBuilder::SPECIES_REF . '" id="' . AnimalBuilder::SPECIES_REF . '" placeholder="' . $espece . '" value="' . $espece . '"><br>
                                    <label>Âge :</label>
                                    <input type="number" name="' . AnimalBuilder::AGE_REF . '" id="' . AnimalBuilder::AGE_REF . '" placeholder="' . $age . '" value= "' . $age . '"><br>';
    
                $error = $animalBuilder->getError();
                if ($error !== null) {
                    foreach ($error as $field => $errors) {
                        $this->content .= '<p style="color: red">Erreur pour le champ ' . $field . ' : ' . $errors . '</p>';
                    }
                }
    
                $this->content .= '<input type="submit" value="Enregistrer">
                                </form>';
            } 
            else {
                //si l'animal que l'on cherche à modifier ne se trouve pas dans la liste , on renvoir la liste  
                $url = $this->router->getListeURL();
                $_SESSION["feedback"] = "La modification a échoué, veuillez recommencer";
                $this->router->POSTredirect($url, $_SESSION['feedback']);
            }
        } 
        else {
            //si l'id est vide , alors on renvoie la liste 
            $url = $this->router->getListeURL();
            $_SESSION["feedback"] = "La modification a échoué, veuillez recommencer";
            $this->router->POSTredirect($url, $_SESSION['feedback']);
        }
    }
    
    
    //formulaire de confirmation de la supression 
    public function prepareAnimalSupressionPage($id) {
        $this->title = "supprimer animal";
        if (!empty($id)) {
            $animal = $this->router->getAnimalStorageStub()->read($id); // recuperation des information de l'animal à supprimer 
            if ($animal !== null) {
                $nom = $animal->getNom();
                $this->content .= '<form action="'.$this->router->getConfirmationSupressionURL($id).'" method="post">
                <input type="hidden" name="id" value="'.$id.'">
                <label>Voulez-vous vraiment supprimer '.$nom.' ?</label><br><br>
                <input type="submit" value="Confirmer" name="confirm">
                <button type="button">Annuler</button>
              </form>';
            } 
            else {
                //si l'id reçu est vide alors on renvoie la liste 
                $url = $this->router->getListeURL();
                $_SESSION["feedback"] = "La suppression a échoué, veuillez recommencer";
                $this->router->POSTredirect($url, $_SESSION['feedback']);
            }
        } 
        else {
            $url = $this->router->getListeURL();
            $_SESSION["feedback"] = "La suppression a échoué, veuillez recommencer";
            $this->router->POSTredirect($url, $_SESSION['feedback']);
        }
    }

}

?>