<?php
require_once('inc/init.inc.php');
require_once('inc/haut_de_site.inc.php');

echo '<section>';

//Si l'utilisateur est connecté : il est redirigé vers son profil
if(utilisateurEstConnecte()){
  header('location:profil.php');
  exit();
}

if(isset($_POST['inscription']) && (empty($_POST['pseudo']) || empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['email']) || empty($_POST['ville']) || empty($_POST['cp']) || empty($_POST['adresse']) || empty($_POST['mdp']))){
       $msg .= "<div class='erreur'>Vous devez renseigner tous les champs.</div>";
}

if(isset($_POST['inscription']) && !empty($_POST['pseudo']) && !empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['email']) && !empty($_POST['ville']) && !empty($_POST['cp']) && !empty($_POST['adresse']) && !empty($_POST['mdp']))
{
  $verif_caracteres = preg_match('#^[a-zA-Z0-9._-]+$#',$_POST['pseudo']);
  $verif_email = preg_match("#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email']);

  if(!$verif_caracteres && !empty($_POST['pseudo']))
    // si l'utilisateur a posté un pseudo et qu'il a un mauvais caractère
  {
    $msg .= "<div class='erreur'>Caractères acceptés : A à Z et de 0 à 9</div>";
  }
  if(!$verif_email && !empty($_POST['email']))
    // si l'utilisateur a posté un pseudo et qu'il a un mauvais caractère
  {
    $msg .= "<div class='erreur'>Votre adresse E-Mail n\'a pas un format valide</div>";
  }
  // vérification de la taille du pseudo
  if(strlen($_POST['pseudo']) < 3 || strlen($_POST['pseudo']) > 15) //on aurait également pu utiliser la fonction trim()
  {
    $msg .= "<div class='erreur'>Le pseudo doit être compris entre 4 et 14 caractères</div>";
  }
    // vérification de la taille du mot de passe
  if(strlen($_POST['mdp']) < 3 || strlen($_POST['mdp']) > 32)//on aurait également pu utiliser la fonction trim()
  {
    $msg .= "<div class='erreur'>Le mot de passe doit être compris entre 4 et 14 caractères</div>";
  }

  if(empty($msg))
  {
    $membre = executerequete("SELECT * FROM membre WHERE pseudo='$_POST[pseudo]'");
    if($membre->num_rows > 0)
    {
       $msg .= "<div class='erreur'>Pseudo indisponible</div>";
    }
    else
    {

	  // protection faille XSS :

	  $pseudo = htmlspecialchars(addslashes($_POST['pseudo']));
	  $nom = htmlspecialchars(addslashes($_POST['nom']));
	  $prenom = htmlspecialchars(addslashes($_POST['prenom']));
	  $email = htmlspecialchars(addslashes($_POST['email']));
	  $ville = htmlspecialchars(addslashes($_POST['ville']));
	  $cp = htmlspecialchars(addslashes($_POST['cp']));
	  $adresse = htmlspecialchars(addslashes($_POST['adresse']));

	  $mdp = passwordHash($_POST['mdp']);

      executeRequete("INSERT INTO membre (pseudo,mdp,nom,prenom,mail,sexe,ville,cp,adresse) VALUES ('$pseudo','$mdp','$nom','$prenom','$email','$_POST[sexe]','$ville','$cp','$adresse')");

      $msg .="<div class='validation'>Félicitations ! Inscription effectuée.</div>";
    }
  }
}

echo $msg;

?>
	<h1>Inscription</h1>

	<div id="form_inscription">
		  <form method="POST" action="inscription.php">
			<label for="pseudo">Pseudo</label>
			<input type="text" id="pseudo" name="pseudo" maxlength="14" placeholder="Pseudo" title="caractères acceptés : a-zA-Z0-9_."  class="design_input"><br>
			<!--required="required" pattern="[a-zA-Z0-9_.]"-->
			<label for="mdp">Mot de passe</label>
			<input type="text" id="mdp" name="mdp" maxlength="14" placeholder="Mot de passe" title="caractères acceptés : a-zA-Z0-9_."  class="design_input"><br>

			<label for="nom">Nom</label>
			<input type="text" id="nom" name="nom" placeholder="Nom" title="caractères acceptés : a-zA-Z0-9_."  class="design_input" ><br>

			<label for="prenom">Prénom</label>
			<input type="text" id="prenom" name="prenom" value="" placeholder="Prénom" title="caractères acceptés : a-zA-Z0-9_."  class="design_input" ><br>

			<label for="email">Email</label>
			<input type="email" id="email" name="email" placeholder="Email" title="Email"  class="design_input"><br>

			<label for="sexe">Sexe</label>
			<input type="radio" name="sexe" value="m" checked class="radio_input"><span class="design_radioBtn"> Homme</span>
			<input type="radio" name="sexe" value="f" class="radio_input"><span class="design_radioBtn">Femme</span><br><br>

			<label for="ville">Ville</label>
			<input type="text" id="ville" name="ville" placeholder="Ville" title="caractères acceptés : a-zA-Z0-9_." class="design_input" ><br>

			<label for="cp">Code Postal</label>
			<input type="text" id="cp" name="cp" placeholder="Code postal" title="5 chiffres requis : [0-9]" maxlength="5"  class="design_input" d"><br>

			<label for="adresse">Adresse</label>
			<textarea id="adresse" name="adresse" placeholder="Adresse" title="caractères acceptés : a-zA-Z0-9_." class="design_textarea" ></textarea><br><br>
			<input type="submit" name="inscription" value="Valider" class="design_submitBtn inscriptionBtn">
		  </form>
	</div>
<?php
require_once('inc/footer.inc.php');
?>





