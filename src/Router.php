<?php
require_once("view/View.php");
require_once("view/ViewJSON.php");
require_once("control/Controller.php");
require_once("model/AnimalStorageStub.php");
require_once("model/AnimalBuilder.php");

class Router {

	private $view;
	private $controller;
	private $AnimalStorageStub;

	public function __construct(AnimalStorage $AnimalStorageStub) {
		$this->AnimalStorageStub = $AnimalStorageStub;
	}

	public function main() {
		if (key_exists('feedback', $_SESSION)) {
			$feedback = $_SESSION['feedback'];
			$this->view = new View($this, $feedback);
		}
		else{
			$this->view = new View($this, "");
		}
		$this->controller = new Controller($this->view, $this->AnimalStorageStub);
		if (key_exists('PATH_INFO', $_SERVER)) {
			$chemin = trim($_SERVER['PATH_INFO'], '/');
			$chemins = explode('/', $chemin);
			if (array_key_exists(trim($_SERVER['PATH_INFO'], '/'), $this->AnimalStorageStub->readAll())){
				$id = trim($_SERVER['PATH_INFO']);
				$this->controller->showInformation($id);
			}
			elseif (key_exists('PATH_INFO', $_SERVER) and trim($_SERVER['PATH_INFO'] === "/liste")){
				$this->controller->showList();
			}
			elseif (key_exists('PATH_INFO', $_SERVER) and trim($_SERVER['PATH_INFO'] === "/nouveau")){
				$animalBuilder = new AnimalBuilder($_POST);
				$this->view->prepareAnimalCreationPage($animalBuilder);
				$this->view->render();
			}
			elseif (key_exists('PATH_INFO', $_SERVER) and trim($_SERVER['PATH_INFO'] === "/sauverNouveau")){
				$this->controller->saveNewAnimal($_POST);
			}

			elseif (key_exists(0, $chemins) && key_exists(1, $chemins) && key_exists(2, $chemins) && $chemins[0] === 'action' && $chemins[1] === 'json'){
				$id = $chemins[2];
				$json = new ViewJSON($this);
				if (array_key_exists($id, $this->AnimalStorageStub->readAll())) {
					$animal = $this->AnimalStorageStub->read($id);
					$json->ajouterAnimal($animal);
					$json->render();
				}
				else{
					$json->inconnu();
					$json->render();
				}
			}

			elseif ($chemins[0] === 'modifier' && array_key_exists(1, $chemins)) {
				$id = $chemins[1];
				if ($id !== '') {
					$animalBuilder = new AnimalBuilder($_POST);
					$this->view->prepareAnimalModificationPage($animalBuilder, $id);
					$this->view->render();
				} 
				else {
					$animalBuilder = new AnimalBuilder($_POST);
					$this->view->prepareAnimalModificationPage($animalBuilder, $id);
					$this->view->render();
				}
			}
			elseif ($chemins[0] === 'update' && key_exists(1, $chemins)) {
				$id = $chemins[1];
				if ($id !== '') {
					$this->controller->updateAnimal($_POST, $id);
				} 
				else {
					$animalBuilder = new AnimalBuilder($_POST);
					$this->view->prepareAnimalModificationPage($animalBuilder, $id);
					$this->view->render();
				}
			} 
			elseif ($chemins[0] === 'delete' && key_exists(1, $chemins)) {
				$id = $chemins[1];
				if ($id !== '') {
					$this->view->prepareAnimalSupressionPage($id);
					$this->view->render();
				} else {
					$this->controller->showList();
					$this->view->render();
				}
			} 
			elseif ($chemins[0] === 'confirmation_del' && key_exists(1, $chemins)) {
				$id = $chemins[1];
				if ($id !== '') {
					$this->controller->supressionAnimal($id);
				} 
				else {
					$this->controller->showList();
					$this->view->render();
				}	
			}
			else{
				$this->view->prepareMainPage();
				$this->view->render();
			}

			unset($_SESSION['feedback']);
			}
			
		else{
			$this->view->prepareMainPage();
			$this->view->render();
		}
	}

	public function getAnimalURL($id) {
		return $id;
	}

	public function getAnimalURLDetails($id) {
		return "action/json/". $id;
	}

	public function getAnimalCreationURL(){
		return "/nouveau";
	}

	public function getAnimalSaveURL(){
		return "sauverNouveau";
	}

	public function POSTredirect($url, $feedback) {
		$_SESSION['feedback'] = $feedback;
		header('Location:/exoMVCR/site.php/' . $url);
		exit;
	}
	public function getAnimalModificationURL($id){
		return "modifier/".$id;
	}
	public function getAnimalUpdateURL($id){
		return "../update/".$id;
	}
	public function getAnimalDeleteURL($id){
		return "./delete/".$id;
	}
	public function getListeURL(){
		return "liste";
	}
	public function getConfirmationSupressionURL($id){
		return "../confirmation_del/".$id;
	}
	public function getAnimalStorageStub(){
		return $this->AnimalStorageStub;
	}

}

?>
