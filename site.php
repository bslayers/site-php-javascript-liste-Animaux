<?php


/*
 * On indique que les chemins des fichiers qu'on inclut
 * seront relatifs au répertoire src.
 */
set_include_path("./src");
require_once("model/Animal.php");
require_once("model/AnimalStorageStub.php");
require_once("model/AnimalStorageSession.php");

/* Inclusion des classes utilisées dans ce fichier */
require_once("Router.php");
/*
 * Cette page est simplement le point d'arrivée de l'internaute
 * sur notre site. On se contente de créer un routeur
 * et de lancer son main.
 */
$AnimalStorageStub = new AnimalStorageStub();
session_start();
$AnimalStorageSession = new AnimalStorageSession();

$router = new Router($AnimalStorageSession);
$router->main();
?>