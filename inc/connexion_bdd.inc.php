<?php
$mysqli = @new Mysqli("cl1-sql18","chriscros1","kJv8LwHb4K9g","chriscros1");
//le @ permet d'éviter le message d'erreur généré par PHP. 
if($mysqli->connect_error)
  //connect_error retourne le message d'erreur de connexion Mysql
{
  die('Un problème est survenu lors de la tentative de connexion à la BDD : ' . $mysqli->connect_error);
}