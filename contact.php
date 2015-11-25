<?php
require_once('inc/init.inc.php');
require_once('inc/haut_de_site.inc.php');

$url = $_SERVER['REQUEST_URI']; 

echo '<section>';

if(isset($_POST['submit'])){
	if($_POST['choice'] == 'none'){
		echo "<p class='erreur'>Vous devez choisir une option parmis la liste déroulante pour que l'on puisse vous aider plus facilement. Merci</p>";
	}
	else{
		foreach($_POST as $indices => $valeurs)
		{
			$_POST[$indices] = htmlEntities($valeurs);
		}
		
		$to      = 'sisi@yopmail.com';
		$subject = $_POST['choice'] . '- '. $_POST['sujet'];
		$message = 'Message de la part de : ' . $_POST['nom'] . ' ' . $_POST['prenom'] . '<br><br>' 
				 . 'Mail de : ' . $_POST['email'] . '<br><br>'
				 . 'Son message  : ' . $_POST['message'];
					 
		$headers = 'From: ' . $_POST['email'] . "\r\n" . 'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $message, $headers);
		$msg = '<p class="validation">Votre message a bien été envoyé.</p>';
	}
}
else
{
	$msg = "<p class='erreur'>Votre message n'a pas pu être envoyé.</p>";
}	
	

echo"<h1>Contact</h1>";
echo"<p id='p_contact'>Pour tout renseignement au sujet de nos produits, de notre politique de retour, du services après-vente ou sur d'autres sujets auxquelles vous souhaitez des réponses, n'hésitez pas à nous envoyer vos demandes via ce formulaire. Merci.</p>";

echo "<div id='form_contact'>";
if(utilisateurEstConnecte()) //page contact pour l'utilisateur connecté au site
{
?>
	<form method="post" action="<?php $url ?>">
		<select name="choice">
			<option value="none">------------------------------------- Choisir votre demande -------------------------------------</option>
			<option value="sav">SAV</option>
			<option value="conseils">Conseils pour le choix d'un article</option>
			<option value="information">Demande d'informations sur un article</option>
			<option value="autre">Autre ?</option>
		</select>
		<input name="sujet" placeholder="Sujet*" type="text" required="required" class="design_input contact_input"><br>
		<textarea name="message" placeholder="Votre Message*"required="required" class="design_textarea contact_textarea"></textarea><br>	
		<input id="btnEnvoyer" type="submit" name="submit" value="Envoyer" class="design_submitBtn contact_btn">
		<input id="nom" name="nom" placeholder="Nom*" type="hidden" value="<?php echo $_SESSION['utilisateur']['nom']?>"><br>
		<input id="prenom" name="prenom" placeholder="Prénom*" type="hidden" value="<?php echo $_SESSION['utilisateur']['prenom']?>"><br>
		<input id="email" name="email" placeholder="Email*" type="hidden" value="<?php echo $_SESSION['utilisateur']['mail']?>"><br>		
	</form>
<?php
}
else  //page contact pour le simple visiteur
{
?>
	<form method="post" action="<?php $url ?>">
		<input id="nom" name="nom" placeholder="Nom*" type="text" required="required" class="design_input contact_input"><br>
		<input id="prenom" name="prenom" placeholder="Prénom*" type="text" required="required" class="design_input contact_input"><br>
		<input id="email" name="email" placeholder="Email*" type="email" required="required" class="design_input contact_input"><br>
		<select name="choice">
			<option value="none">------------------------------------- Choisir votre demande -------------------------------------</option>
			<option value="sav">SAV</option>
			<option value="conseils">Conseils pour le choix d'un article</option>
			<option value="information">Demande d'informations sur un article</option>
			<option value="autre">Autre ?</option>
		</select>		
		<input name="sujet" placeholder="Sujet*" type="text" required="required" class="design_input contact_input"><br>
		<textarea name="message" placeholder="Votre Message*"required="required" class="design_textarea contact_textarea"></textarea><br>
		<input id="btnEnvoyer" type="submit" name="submit" value="Envoyer" class="design_submitBtn contact_btn">
	</form>
<?php
}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.erreur').delay(3000).fadeOut("slow" );
		$('.validation').delay(3000).fadeOut("slow" );
	});
</script>
<?php
echo "</div>";
	
require_once('inc/footer.inc.php');







