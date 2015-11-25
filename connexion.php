<?php

// COOKIE


if(isset($_POST['remember'])){
	$pseudo = htmlspecialchars($_POST['pseudo']);
	setcookie('pseudo', $pseudo, time()+3600*24*31, '/', 'localhost', false, true); // setcookie ('nom du cookie', 'valeur récupérée', 'temps avant destruction du cookie', 'cookie valbale pour ? (/ = tout le site)', 'préciser le nom de domaine', 'cookie envoyé de manière sécurisé = https et true, sinon false', 'cookie non éditable en JS avec le true')
}

require_once('inc/init.inc.php');
require_once('inc/haut_de_site.inc.php');


echo '<section>';

echo "<h1>Connexion</h1>";

if(utilisateurEstConnecte())
{
   header('location:profil.php');
   exit();
}

if(isset($_POST['connexion']))
{  
  $query = sprintf("SELECT * FROM membre WHERE pseudo='%s'",$mysqli->real_escape_string($_POST['pseudo'])); // Protection injection SQL : $mysqli->real_escape_string : empêche les ' en mettant des \ devant et pour les POST, utile que lors de la connection au site pour éviter la faille 'OR 1=1 OR 1=' dans le pseudo. %s sert à appeler la chaîne de charactère créer par $mysqli pour ensuite vérifier avec la requête que le pseudo existe bien.
  $selection_membre = executeRequete($query);
 
  if($selection_membre->num_rows !=0)
  {
    $membre = $selection_membre->fetch_assoc();
	
	$mdpRecu = passwordHash($_POST['mdp']);

	// $mdpBDDRecup = $membre['mdp'];
	
	if ($mdpRecu == $membre['mdp'])
    {
      foreach($membre as $indice => $valeurs)
      {
        if($indice != 'mdp')
        {
            $_SESSION['utilisateur'][$indice] = $valeurs;
        }
      }
      header("location:profil.php");
	  exit();
    }
    else
    {
      $msg .="<div class='erreur'>Mot de passe incorrect</div>";
    }
  }
  else
  {
      $msg .="<div class='erreur'>Pseudo incorrect</div>";    
  }
}

echo $msg;

?>
<div id="deja_inscrit">
	<h2>Déjà inscrit ?</h2>

	<form method="post" action="">
	  <label for="pseudo" class="design_label">Pseudo</label><br>
  
<?php
if(empty($_COOKIE['pseudo']))
{
	echo '<input type="text" id="pseudo" name="pseudo" placeholder="Pseudo" class="design_input"><br>';
}
else
    {		
		if((isset($_COOKIE['pseudo'])) && (!empty($_COOKIE['pseudo']))) // Si la variable existe et qu'elle est remplie
        {
                $pseudo = htmlentities($_COOKIE['pseudo'], ENT_QUOTES); // On neutralise le HTML
				echo '<input type="text" id="pseudo" name="pseudo" placeholder="Pseudo" value="' . $pseudo . '" class="design_input"><br>';				
        }		    		
	}
?>
		<label for="mdp" class="design_label">Mot de passe</label><br>
			<input type="password" id="mdp" name="mdp" placeholder="Mot de passe" class="design_input" ><br> 
		<label for="checkbox" id="checkboxText-Connection">Se souvenir de moi ?</label>
			<input type="checkbox" name="remember" ><br>	
			<input type="submit" name="connexion" value="Connexion" class="design2_submitBtn">
	</form>

	<a href="mdpperdu.php" class="mdp_forget_design">Mot de passe oublié ?</a>
</div>

<div id="separation_verticale"></div>

<div id="pas_encore_inscrit">
	<h2>Pas encore inscrit ?</h2>

	<a href="inscription.php" class="btn_inscription">S'inscrire</a>
	
</div>

<?php	
	
require_once('inc/footer.inc.php');









