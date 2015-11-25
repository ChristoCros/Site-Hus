<?php
require_once("../inc/init.inc.php");
require_once('../inc/haut_de_site.inc.php');

if(!utilisateurEstConnecteEtAdmin())
{
	header("location:../connexion.php");
	exit();
}
if(utilisateurEstConnecteEtAdmin() && isset($_GET['action']) && $_GET['action'] == 'deconnexion')
	{
		session_destroy();
		header("location:" . RACINE_SITE . "connexion.php");
		exit();
	}
	 
if(isset($_GET['action']) && $_GET['action'] == "suppression")
{
	executeRequete("DELETE FROM avis WHERE id_avis = $_GET[id_avis]");
	$_GET['action'] = '0';
	
	$msg .= "<div class='validation'>Le commentaire a été effacé avec succés.</div>";
}

if(isset($_POST['modifier'])){
	$commentaire = $mysqli->real_escape_string($_POST['commentaire']);
	executeRequete("UPDATE avis SET sujet = '$_POST[sujet]', commentaire = '$commentaire', note = '$_POST[note]', date = '$_POST[date]' WHERE id_avis = '$_POST[id_avis]' ");
	
	header('location: gestion_avis.php?action=0');
	exit;
}

// ----------------- Organisation par défaut ------------//

$resultat = executeRequete("SELECT * FROM avis a, membre m, article ar WHERE a.id_membre = m.id_membre AND a.id_article = ar.id_article ORDER BY id_avis ASC");

echo '<section>';

echo $msg;

	echo '<div>
	<h1>Gestion des commentaires</h1>';

if(isset($_GET['action']) && $_GET['action'] == '0'){	
	echo "<div class='encart_gestion'>";
		echo "<p class='infos_gestionSolo'>Nombre de commentaire(s) : <span class='num_infosGestion'>" . $resultat->num_rows . '</span></p>';
	echo "</div>";
		while ($ligne = $resultat->fetch_assoc()){
			echo "<div class='comm_div' id=' ". $ligne['id_avis'] ." ' >".
				"<div class='infos_membre'>".
					"<div class='infosMembre_text'>" .
						"<p>" . $ligne['prenom']  . ' ' . $ligne['nom'] . ' ( Membre n° '.$ligne['id_membre'].')' . ' - '.  $ligne['titre'] . ' ( Article n° '.$ligne['id_article'].')'  .  ' - ' . dateLongue($ligne['date'], 'no') . "</p>" .
					"</div>" .
					"<div class='infosMembre_btnModif'>" .
						"<a href='?action=modification&id_avis=" . $ligne['id_avis'] ." '><img src='". RACINE_SITE . "photo/modif.png' alt='' data-tooltip='Modifier le commentaire'></a>" .
					"</div>" .
					"<div class='infosMembre_btnSuppr'>" .
						"<a href='?action=suppression&id_avis=" . $ligne['id_avis'] ." ' OnClick='return(confirm(\"En êtes vous certain ?\"));'><img src='". RACINE_SITE . "photo/suppr.png' alt='' data-tooltip='Supprimer ce commentaire'></a>" .
					"</div>" .
				"</div>" .
				"<div class='texts_comm'>" .
					"<p class='gest_sujet'>" . $ligne['sujet'] . "</p>" .
					"<p class='gest_comm'>" . $ligne['commentaire'] . "</p>" .
				"</div>" .
			"</div>";
		}
		
}
if(isset($_GET['action']) && $_GET['action'] == 'modification'){
	
	$recupComm = executeRequete("SELECT id_avis,sujet, commentaire,note,date FROM avis WHERE id_avis = $_GET[id_avis] ");
	$afficheComm = $recupComm->fetch_assoc();
	
?>
<div id="modifAvis_div">
		<form method="post" action="gestion_avis.php?action=0" id="modifAvis_form">
		
			<input type="hidden" name="id_avis" value="<?php if(isset($afficheComm['id_avis'])) echo $afficheComm['id_avis']; ?>">
			
			<label for="sujet" class="gras">Sujet</label>
				<input type="text" id="sujet_avis" name="sujet" value="<?php if(isset($afficheComm['sujet']))  echo $afficheComm['sujet']; elseif(isset($_POST['sujet'])) echo $_POST['sujet']; ?>" class="design_input" placeholder="Correction du sujet"/><br />				
				
			<label for="commentaire" class="gras">Commentaire</label><br>
				<textarea name="commentaire" id="commentaire" class="design_textarea" placeholder="Correction du commentaire"><?php if(isset($afficheComm['commentaire'])) echo $afficheComm['commentaire']; elseif(isset($_POST['commentaire'])) echo $_POST['commentaire']; ?></textarea><br />

			<input type="hidden" name="note" value="<?php if(isset($afficheComm['note'])) echo $afficheComm['note']; ?>">
			<input type="hidden" name="date" value="<?php if(isset($afficheComm['date'])) echo $afficheComm['date']; ?>">
				
			<input type="submit" name="modifier" value="Modifier" class="design_submitBtn modifAvis_btn"/> 
		</form>
</div>
<?php			
}	
	echo "</div>";

?>
<script>
$(document).ready(function(){
	$('.erreur').delay(3000).fadeOut("slow" );
	$('.validation').delay(5000).fadeOut("slow" );
});
	
</script>

<?php

require_once('../inc/footer.inc.php');








