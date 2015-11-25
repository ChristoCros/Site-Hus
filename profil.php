<?php
require_once('inc/init.inc.php');
require_once('inc/haut_de_site.inc.php');

if(!utilisateurEstConnecte()){
  header('location:connexion.php');
  exit();
}

// ------------------------------Mise à jour des données ---------------


if(isset($_POST['maj_infos']))
{
      foreach($_POST as $indices => $valeurs)
		{
		$_POST[$indices] = htmlEntities(addslashes($valeurs));
		}
		
	$modif = executeRequete("UPDATE membre SET nom='$_POST[nom]', prenom='$_POST[prenom]',mail='$_POST[email]', ville='$_POST[ville]', cp='$_POST[cp]', adresse='$_POST[adresse]' WHERE id_membre=" . $_SESSION['utilisateur']['id_membre'] ."");
      
	$msg .= "<div class='validation'>Changements effectués.</div>";
		
	$selection_membre =  executeRequete("SELECT * FROM membre WHERE id_membre=' " . $_SESSION['utilisateur']['id_membre'] . " ' ");
	$membre = $selection_membre->fetch_assoc();
	foreach($membre as $indice => $valeurs)
		{       
            $_SESSION['utilisateur'][$indice] = $valeurs;       
		}
		header('location:profil.php');
		exit;
}

// ------------------ Mise à jour du mot de passe ---------------

if(isset($_POST['maj_mdp'])) // On vérifie si l'ancien mot de passe taper est correcte et ensuite regarder si le nouveau mot de passe et le nouveau mdp resaisit sont identique.
	{
		$nouveau_mdp = $_POST['nouveau_mdp'];
		$resaisir_mdp = $_POST['resaisir_mdp'];
		
		$recup_mdp = $mysqli->query("SELECT mdp FROM membre WHERE id_membre=' " . $_SESSION['utilisateur']['id_membre'] . " ' ");
		$recherche_mdp = $recup_mdp->fetch_assoc();
		// $bdd_mdp = $recherche_mdp['mdp'];
		
			if($nouveau_mdp == $resaisir_mdp){
				$nouveau_mdpHash = passwordHash($nouveau_mdp);
				$msg .="<div class='validation' id='notification'>Votre mot de passe a été modifié</div>";
				$modif_mdp_bdd = executeRequete("UPDATE membre SET mdp='$nouveau_mdpHash' WHERE id_membre=' " . $_SESSION['utilisateur']['id_membre'] . " ' ");
			}
			else{
				$msg .= "<div class='erreur' id='notification'>Les mots de passe ne sont pas identiques.</div>";
			}
		// header('location:profil.php');
	}

// ------------------------------Mise à jour newsletter ---------------	
if(utilisateurEstConnecte()){	
$abonnement_membre = executeRequete("SELECT Checkbox FROM newsletter WHERE id_membre = " . $_SESSION['utilisateur']['id_membre'] ."");
$resultat_abo = $abonnement_membre->fetch_assoc();
$nbLineAbo = $abonnement_membre->num_rows;
 
if(isset($_POST['abonner'])){
	if($nbLineAbo == 0){
		executeRequete("INSERT INTO newsletter VALUES ('','". $_SESSION['utilisateur']['id_membre'] ."', 'oui')");
	}
	if($nbLineAbo >= 1){
		if($_POST['selection_choix'] == "oui"){
			executeRequete("UPDATE newsletter SET checkbox = 'oui'  WHERE id_membre = '" . $_SESSION['utilisateur']['id_membre'] ."'");
		}
		else{
			executeRequete("UPDATE newsletter SET checkbox = 'non'  WHERE id_membre = '" . $_SESSION['utilisateur']['id_membre'] ."'");
		}
	}
	header('location: profil.php');
	exit;
}
}

echo '<section>';
	echo'<div id="profil_div">';

echo $msg;	

echo "<h2>Bonjour  " .$_SESSION['utilisateur']['pseudo'] ."</h2>";

echo"<div id='menu_profil'>";
	echo '<p onclick="Objet.d();" id="infosPersoBtn" class="btn_menuProfil">Informations personnels</p>';
	echo '<p onclick="Objet.e();" id="maj_commandesBtn" class="btn_menuProfil">Historiques des commandes</p>';
	echo '<p onclick="Objet.a();" id="maj_infosBtn" class="btn_menuProfil">Mettre à jour mes informations</p>';
	echo '<p onclick="Objet.b();" id="maj_mdpBtn" class="btn_menuProfil">Changer de mot de passe</p>';
	echo '<p onclick="Objet.c();" id="maj_newsBtn" class="btn_menuProfil">S\'abonner à la newsletter</p>';
echo"</div>";

echo '<div id="profil">';	
echo "<p><span class='gras'>Votre ville : </span>" .$_SESSION['utilisateur']['ville'] ."</p>";
echo "<p><span class='gras'>Votre Code postal : </span>" .$_SESSION['utilisateur']['cp'] ."</p>";
echo "<p><span class='gras'>Votre Adresse : </span>" .$_SESSION['utilisateur']['adresse'] ."</p>";
echo "<p><span class='gras'>Votre email : </span>" .$_SESSION['utilisateur']['mail'] ."</p>";
echo '</div>';

echo '<div id="derniere_commande">';

	$resultat = executeRequete("SELECT id_commande,date,montant FROM commande WHERE id_membre=" . $_SESSION['utilisateur']['id_membre'] ."");
	$nbLineCommande = $resultat->num_rows;
	echo "<table style='border-color:red' border=2 id='table_profil'> <tr>";

		echo '<th>Numéro de suivie</th>';
		echo '<th>Date</th>';
		echo '<th>Montant</th>';
	
	echo "</tr>";
	if($nbLineCommande == 0 ){
		echo"<tr><td colspan='3' style='height:50px;'>Votre historique de commande est vide</td></tr>";	
	}
	else{
		$i = 0;
		while ($ligne = $resultat->fetch_assoc())
		{  
			$css_class = ($i % 2 == 0) ? 'clair' : 'sombre';
			echo '<tr class="'.$css_class.'">';
				echo "<td>" . $ligne['id_commande'] . "</td>";
				echo "<td>" . dateLongue($ligne['date'], 'no') . "</td>";
				echo "<td>" . $ligne['montant'] . " €</td>";
			$i++;
			echo '</tr>';
		}
	}
	echo '</table>';
	echo "</div>";
	
?>
	<div id="maj_infos">	
			
		<div id="messages"><?php echo $msg; ?></div>
		
			<form method="post" action="<?php echo RACINE_SITE ?>profil.php">
				<label for="pseudo" class='gras'>Pseudo</label>
				<p id="pseudo_profil"><?php echo $_SESSION['utilisateur']['pseudo']; ?></p><br>

				<label for="nom" class='gras'>Nom</label>
				<input type="text" id="nom" name="nom" value="<?php echo $_SESSION['utilisateur']['nom']; ?>"  placeholder="nom" title="caractères acceptés : a-zA-Z0-9_."  class="design_input"><br>
				
				<label for="prenom" class='gras'>Prénom</label>
				<input type="text" id="prenom" name="prenom" value="<?php echo $_SESSION['utilisateur']['prenom']; ?>" placeholder="prenom" title="caractères acceptés : a-zA-Z0-9_."  class="design_input"><br>

				<label for="email" class='gras'>Email</label>
				<input type="email" id="email" name="email" value="<?php echo $_SESSION['utilisateur']['mail']; ?>" placeholder="email" title="votre email"  class="design_input"><br>
				
				<label for="ville"class='gras'>Ville</label>
				<input type="text" id="ville" name="ville" value="<?php echo $_SESSION['utilisateur']['ville']; ?>" placeholder="ville" title="caractères acceptés : a-zA-Z0-9_." class="design_input"><br>
				
				<label for="cp" class='gras'>Code Postal</label>
				<input type="text" id="cp" name="cp" value="<?php echo $_SESSION['utilisateur']['cp']; ?>" placeholder="Code postal" title="5 chiffres requis : [0-9]" maxlength="5" class="design_input"><br>  
				
				<label for="adresse" class='gras'>Adresse</label>
				<textarea id="adresse" name="adresse" placeholder="adresse" title="caractères acceptés : a-zA-Z0-9_." class="design_textarea"><?php echo $_SESSION['utilisateur']['adresse']; ?></textarea><br><br>
				<input type="submit" name="maj_infos" value="Mettre à jour" class="maj design_submitBtn">
			</form>
    </div>
	<div id="maj_mdp">
			<form method="POST" action="profil.php">

				<label for="nouveau_mdp" class="gras">Nouveau mot de passe</label>
				<input type="password" name="nouveau_mdp" class="design_input"><br><br>
				
				<label for="resaisir_mdp" class="gras">Resaisir le mot de passe</label>
				<input type="password" name="resaisir_mdp" class="design_input"><br><br>
							
				<input type="submit" name="maj_mdp" value="Enregistrer" class="maj_mdp design_submitBtn">
			</form>	
	</div>
	<div id="newsletterProfil">
			<form method="POST" action="profil.php">
				
				<p class="message_news">Souhaitez-vous recevoir la newsletter de notre site ( une fois par mois en moyenne) ?</p>
<?php

if($resultat_abo['Checkbox'] == 'oui'){
?>
				<select name="selection_choix" class="select_option">
					<option value="oui">Oui</option>
					<option value="non" selected>Non</option>
				</select>
				
				<input type="submit" name="abonner" value="Valider" class="design_submitBtn abo_btn">
			</form> 				
<?php
	echo '<p class="message_news abo">Vous êtes abonné à notre newsletter mensuel, merci.</p>';
}
else{
?>
				<select name="selection_choix" class="select_option">
					<option value="oui" selected>Oui</option>
					<option value="non">Non</option>
				</select>
				
				<input type="submit" name="abonner" value="Valider" class="design_submitBtn abo_btn">
			</form> 				
<?php
	echo '<p class="message_news non_abo">Vous n\'êtes pas abonné à la newsletter.</p>';
	echo '<p class="message_news mini_message">Si vous souhaitez vous abonner à notre newsletter mensuel sélectionner "oui" et valider. Merci !</p>';
}
?>		
	</div>
	
<script>



var Objet = {
	
	a : function(){
		var switchMajInfos = document.getElementById("maj_infos").style.display = "block";
		var switchMajInfos = document.getElementById("maj_infosBtn").style.backgroundColor = "#2C4752";
		var switchMajMdp = document.getElementById("maj_mdp").style.display = "none";
		var switchMajMdp = document.getElementById("maj_mdpBtn").style.backgroundColor = "#1BB09F";
		var switchMajNews = document.getElementById("newsletterProfil").style.display = "none";	
		var switchMajNews = document.getElementById("maj_newsBtn").style.backgroundColor = "#1BB09F";	
		var switchMajProfil = document.getElementById("profil").style.display = "none";	
		var switchMajProfil = document.getElementById("infosPersoBtn").style.backgroundColor = "#1BB09F";
		var switchMajCommande = document.getElementById("derniere_commande").style.display = "none";		
		var switchMajCommande = document.getElementById("maj_commandesBtn").style.backgroundColor = "#1BB09F";
	},
	b : function(){
		var switchMajinfos = document.getElementById("maj_infos").style.display = "none";
		var switchMajInfos = document.getElementById("maj_infosBtn").style.backgroundColor = "#1BB09F";
		var switchMajMdp = document.getElementById("maj_mdp").style.display = "block";
		var switchMajMdp = document.getElementById("maj_mdpBtn").style.backgroundColor = "#2C4752";		
		var switchMajNews = document.getElementById("newsletterProfil").style.display = "none";
		var switchMajNews = document.getElementById("maj_newsBtn").style.backgroundColor = "#1BB09F";			
		var switchMajProfil = document.getElementById("profil").style.display = "none";
		var switchMajProfil = document.getElementById("infosPersoBtn").style.backgroundColor = "#1BB09F";		
		var switchMajCommande = document.getElementById("derniere_commande").style.display = "none";
		var switchMajCommande = document.getElementById("maj_commandesBtn").style.backgroundColor = "#1BB09F";		
	},
	c : function(){
		var switchMajinfos = document.getElementById("maj_infos").style.display = "none";
		var switchMajInfos = document.getElementById("maj_infosBtn").style.backgroundColor = "#1BB09F";
		var switchMajMdp = document.getElementById("maj_mdp").style.display = "none";
		var switchMajMdp = document.getElementById("maj_mdpBtn").style.backgroundColor = "#1BB09F";		
		var switchMajNews = document.getElementById("newsletterProfil").style.display = "block";
		var switchMajNews = document.getElementById("maj_newsBtn").style.backgroundColor = "#2C4752";			
		var switchMajProfil = document.getElementById("profil").style.display = "none";
		var switchMajProfil = document.getElementById("infosPersoBtn").style.backgroundColor = "#1BB09F";		
		var switchMajCommande = document.getElementById("derniere_commande").style.display = "none";
		var switchMajCommande = document.getElementById("maj_commandesBtn").style.backgroundColor = "#1BB09F";		
	},
	d : function(){
		var switchMajinfos = document.getElementById("maj_infos").style.display = "none";
		var switchMajInfos = document.getElementById("maj_infosBtn").style.backgroundColor = "#1BB09F";
		var switchMajMdp = document.getElementById("maj_mdp").style.display = "none";
		var switchMajMdp = document.getElementById("maj_mdpBtn").style.backgroundColor = "#1BB09F";		
		var switchMajNews = document.getElementById("newsletterProfil").style.display = "none";
		var switchMajNews = document.getElementById("maj_newsBtn").style.backgroundColor = "#1BB09F";			
		var switchMajProfil = document.getElementById("profil").style.display = "block";
		var switchMajProfil = document.getElementById("infosPersoBtn").style.backgroundColor = "#2C4752";		
		var switchMajCommande = document.getElementById("derniere_commande").style.display = "none";
		var switchMajCommande = document.getElementById("maj_commandesBtn").style.backgroundColor = "#1BB09F";		
	},
	e : function(){
		var switchMajinfos = document.getElementById("maj_infos").style.display = "none";
		var switchMajInfos = document.getElementById("maj_infosBtn").style.backgroundColor = "#1BB09F";
		var switchMajMdp = document.getElementById("maj_mdp").style.display = "none";
		var switchMajMdp = document.getElementById("maj_mdpBtn").style.backgroundColor = "#1BB09F";		
		var switchMajNews = document.getElementById("newsletterProfil").style.display = "none";
		var switchMajNews = document.getElementById("maj_newsBtn").style.backgroundColor = "#1BB09F";			
		var switchMajProfil = document.getElementById("profil").style.display = "none";
		var switchMajProfil = document.getElementById("infosPersoBtn").style.backgroundColor = "#1BB09F";		
		var switchMajCommande = document.getElementById("derniere_commande").style.display = "block";
		var switchMajCommande = document.getElementById("maj_commandesBtn").style.backgroundColor = "#2C4752";	
	},
}	

$(document).ready(function(){
	$('.erreur').delay(3000).fadeOut("slow" );
	$('.validation').delay(5000).fadeOut("slow" );
});
	
</script>

</div>
<?php
require_once('inc/footer.inc.php');


