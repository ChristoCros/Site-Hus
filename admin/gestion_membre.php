<?php
require_once('../inc/init.inc.php');
require_once('../inc/haut_de_site.inc.php');

echo '<section>';

if(!utilisateurEstConnecteEtAdmin())
{
	header("location:../connexion.php"); //redirection pour tous les membres qui ne sont pas administrateurs (donc tous les autres connectés dont la statut est égal à 0 mais aussi les simples visiteurs !!). Seuls les membres ayant un statut == 1 ont accès à cette page. Cf. BDD
	exit(); //permet de stopper l'éxécution du script
}
if(utilisateurEstConnecteEtAdmin() && isset($_GET['action']) && $_GET['action'] == 'deconnexion')
	{
		session_destroy();
		header("location:" . RACINE_SITE . "connexion.php");
		exit();
	}

if(isset($_GET['action']) && $_GET['action'] == "suppression")
{
	executeRequete("DELETE FROM membre WHERE id_membre = $_GET[id_membre]");	
}
	echo '<div id="tab_membre">
	<h1> Liste des Membres</h1>';
	$resultat = executeRequete("SELECT * FROM membre");
	$administrateur = executeRequete("SELECT * FROM membre WHERE statut=1");
	$membre = executeRequete("SELECT * FROM membre WHERE statut=0");
	echo "<div class='encart_gestion'>";
	echo "<p class='infos_gestion'>Nombre de membre(s) : <span class='num_infosGestion'>" . $membre->num_rows . '</span></p>';
	echo "<p class='infos_gestion'>Nombre d'administrateur(s) : <span class='num_infosGestion'>" . $administrateur->num_rows . '</span></p>';
	echo "</div>";	

	$nbcol = $resultat->field_count;
	$colonne = $resultat->fetch_field();	
	echo "<table style='border-color:red' border=10> <tr>";	
		echo '<th>Membre n°</th>';
		echo '<th>Pseudo</th>';
		echo '<th>Nom</th>';
		echo '<th>Prénom</th>';
		echo '<th>Mail</th>';
		echo '<th>Sexe</th>';
		echo '<th>Ville</th>';
		echo '<th>CP</th>';
		echo '<th>Adresse</th>';
		echo '<th>Ville 2</th>';
		echo '<th>CP 2</th>';
		echo '<th>Adresse 2</th>';
		echo '<th>Statut</th>';
		echo "<th>Action</th>";
	echo "</tr>";
	$i = 0;
	while ($ligne = $resultat->fetch_assoc())
  {  
	//crée-moi autant de lignes <tr> qu'il y a de résultats dans la BDD (utilisation de fecth_assoc() qui nous ressort les informations d'array(). Donc récupération par l'intermédiaire d'une boucle foreach()
		$css_class = ($i % 2 == 0) ? 'clair' : 'sombre';
		echo '<tr class="'.$css_class.'">';
				echo "<td>$ligne[id_membre]</td>";
				echo "<td>$ligne[pseudo]</td>";
				echo "<td>$ligne[nom]</td>";
				echo "<td>$ligne[prenom]</td>";
				echo "<td>$ligne[mail]</td>";
				echo "<td>$ligne[sexe]</td>";
				echo "<td>$ligne[ville]</td>";
				echo "<td>$ligne[cp]</td>";
				echo "<td>$ligne[adresse]</td>";
				echo "<td>$ligne[ville_livraison]</td>";
				echo "<td>$ligne[cp_livraison]</td>";
				echo "<td>$ligne[adresse_livraison]</td>";
				echo "<td>$ligne[statut]</td>";
		$i++;
		if($ligne['statut'] ==1){
			echo '<td></td>';
		}
		else{
			echo '<td><a href="?action=suppression&id_membre=' . $ligne['id_membre'] .'" OnClick="return(confirm(\'En êtes vous certain ?\'));"><img src="'. RACINE_SITE . 'photo/suppr.png" alt="" data-tooltip="Supprimer ce membre"></a></td>';
		}
		echo '</tr>';
	}
	echo '</table>';
	
	echo "<p class='design_submitBtn btn_admin' onclick='objetCreationAdmin.a();'>Création d'un nouveau compte administrateur</p>";

	echo "</div>";
	
if(isset($_POST['creer']))
{
  $verif_caracteres = preg_match('#^[a-zA-Z0-9._-]+$#',$_POST['pseudo']);
  
  if(!$verif_caracteres && !empty($_POST['pseudo']))
  {
    $msg .= "<div class='erreur'>Caractères acceptés : A à Z et de 0 à 9</div>";
  }
  if(strlen($_POST['pseudo']) < 3 || strlen($_POST['pseudo']) > 15)
  {
    $msg .= "<div class='erreur'>Le pseudo doit être compris entre 4 et 14 caractères</div>";
  }
  if(strlen($_POST['mdp']) < 3 || strlen($_POST['mdp']) > 32)
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
      foreach($_POST as $indices => $valeurs)
      {
        $_POST[$indices] = htmlEntities(addslashes($valeurs));
      }
	  
      executeRequete("INSERT INTO membre (pseudo,mdp,nom,prenom,email,sexe,ville,cp,adresse,statut) VALUES ('$_POST[pseudo]','$_POST[mdp]','$_POST[nom]','$_POST[prenom]','$_POST[email]','$_POST[sexe]','$_POST[ville]','$_POST[cp]','$_POST[adresse]','1')");
      
      $msg .="<div class='validation'>Création d'un nouvel administrateur effectué.</div>";
    }
      
  }	

}

echo'<div id="creation_admin" style="display:none;">';

echo $msg; 

?>
	<h1>Création d'un nouvel administrateur</h1>
		
      <form method="post" action="gestion_membre.php" id="form_newAdmin">
        <label for="pseudo" class="gras">Pseudo</label>
        <input type="text" id="pseudo" name="pseudo" value="" maxlength="14" data-tooltip-stickto="right" data-tooltip="caractères acceptés : a-zA-Z0-9_." required="required" class="design_input"><br>

        <label for="mdp" class="gras">Mot de passe</label>
        <input type="text" id="mdp" name="mdp" value="" maxlength="14" data-tooltip-stickto="right" data-tooltip="caractères acceptés : a-zA-Z0-9_." required="required" class="design_input"><br>

        <label for="nom" class="gras">Nom</label>
        <input type="text" id="nom" name="nom" value="" data-tooltip-stickto="right" data-tooltip="caractères acceptés : a-zA-Z0-9_." class="design_input"><br>
        
        <label for="prenom" class="gras">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="" data-tooltip-stickto="right" data-tooltip="caractères acceptés : a-zA-Z0-9_." class="design_input"><br>

        <label for="email" class="gras">Email</label>
        <input type="email" id="email" name="email" value="" data-tooltip-stickto="right" data-tooltip="exemple@monsite.fr" required="required" class="design_input"><br>
        
        <label for="sexe" class="gras">Sexe</label>
        <input type="radio" name="sexe" value="m" checked class="design_radio"><span class="design_radioBtn">Homme</span>
        <input type="radio" name="sexe" value="f" class="design_radio">Femme<br><br>
        
        <label for="ville" class="gras">Ville</label>
        <input type="text" id="ville" name="ville" value="" data-tooltip-stickto="right" data-tooltip="caractères acceptés : a-zA-Z0-9_."class="design_input"><br>
        
        <label for="cp" class="gras">Code Postal</label>
        <input type="text" id="cp" name="cp" value="" data-tooltip-stickto="right" data-tooltip="5 chiffres requis : [0-9]" maxlength="5" class="design_input"><br>  
        
        <label for="adresse" class="gras">Adresse</label>
        <textarea id="adresse" name="adresse" data-tooltip-stickto="right" data-tooltip="caractères acceptés : a-zA-Z0-9_."  class="design_textarea"></textarea><br><br>
        <input type="submit" name="creer" value="Enregistrer" class="design_submitBtn form_admin">
		<a href="<?php RACINE_SITE ?>gestion_membre.php" class="design_submitBtn cancel_btn">Annuler</a>		
      </form>
    </div>
	
	
	<script>
	
	var objetCreationAdmin = {
		a: function(){
			if(document.getElementById("creation_admin").style.display == 'none'){
				document.getElementById("creation_admin").style.display = 'block';
				document.getElementById("tab_membre").style.display = 'none';
				document.getElementById("creation_admin_p").style.display = 'none';
			}
			else{
				document.getElementById("creation_admin").style.display = 'none'
				document.getElementById("creation_admin").style.display = 'block'
				document.getElementById("creation_admin_p").style.display = 'block'
			}
		}
	}
	</script>
<?php 
require_once('../inc/footer.inc.php');
?>

