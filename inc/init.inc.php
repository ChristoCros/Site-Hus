<?php
//fichier d'initialisation
require_once('connexion_bdd.inc.php');
require_once('fonctions.inc.php');

session_start(); //je crée ma session pour les connectés. Je la place ici car le init.inc.php est inclu dans l'ensemble de mes pages. 



//pour éviter d'avoir des problèmes de liens : 
define('RACINE_SITE','/Hus/');

//si le site venait être transféré sur Internet, on définit un chemin automatique : 
define('RACINE_SERVEUR',$_SERVER['DOCUMENT_ROOT']);
//echo RACINE_SERVEUR;

//cette variable vide nous permettra de placer du texte comme par exemple pour générer un texte d'erreur en cas de mauvaise saisie d'un formulaire 
$msg = '';