<?php
require_once("../inc/init.inc.php");
require_once('../inc/haut_de_site.inc.php');


echo '<section>';

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

if(isset($_POST['envoi'])){
	$to      = $_POST['expediteur'];;
	$subject = $_POST['sujet'];
	$messageBody = 'Nos dernières nouveautés !' . $_POST['message'];		 	 
	$headers = 'De: ' . $to;
	
		mail($to, $subject, $messageBody, $headers);
		$msg = '<p class="validation">La newsletter a bien été envoyé.</p>';
}
else{
		$msg = '<p class="erreur">Une erreur s\'est produite lors de l\'envoie de la newsletter</p>';
}	
	echo '<div>
	<h1>Envoie de la newsletter</h1>';
	$resultat = executeRequete("SELECT * FROM newsletter WHERE checkbox = 'oui' ");
	
echo "<div class='encart_gestion'>";
	echo "<p class='infos_gestionSolo'>Nombre d'abonné(s) à la newsletter : <span class='num_infosGestion'>" . $resultat->num_rows . '</span></p>';
echo "</div>";
?>
	<div id="form_NL">
		<div id="div_formNL">
		  <form method="post" action="">
			<input type="email" id="expediteur" name="expediteur" placeholder="Email de la société" required="required" class="design_input">

			<input type="text" id="sujet_NL" name="sujet" placeholder="Sujet de la newsletter" required="required" class="design_input">
			
			<textarea id="message_NL" name="message" placeholder="Rédaction newsletter" class="design_textarea"></textarea><br>
			<input type="submit" name="envoi" value="Envoi de la newsletter aux membres" class="design_submitBtn btn_NL">
		  </form>
		</div> 
	 </div> 
<?php	
	echo "</div>";
	
require_once('../inc/footer.inc.php');










