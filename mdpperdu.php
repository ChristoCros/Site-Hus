<?php
require_once('inc/init.inc.php');
require_once('inc/haut_de_site.inc.php');

echo '<section>';

if(utilisateurEstConnecte())//si l'utilisateur est déjà connecté, il n'a pas à accéder à nouveau à cette page connexion.php
{
   header('location:profil.php');//permet de rediriger le membre connecté vers sa page profil
   exit();
}

if(isset($_POST['reset'])) //si le bouton submit fonction, j'effectue le code suivant : lorsque l'utilisateur se connectera, il puisse accéder à la page profil.php
{
		$emailPost = htmlspecialchars($_POST['email']);		
		$email = executeRequete("SELECT mail FROM membre WHERE mail='$emailPost' ");
			if(!empty($_POST['email'])){
				if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
					if($email->num_rows == 0)
					{
						echo '<p class="erreur">Cette adresse mail n\'existe pas pour notre site.</p>';
					}
					else
					{
						$randomPassword = substr(uniqid(rand(), true), 3 , 10);
						$new_password = passwordHash($randomPassword); // Création d'un nouveau mdp aléatoire grâce au rand
						$reset_mdp_bdd = executeRequete("UPDATE membre SET mdp='$new_password' WHERE mail='$_POST[email]' ");
					
						$body = "Votre mot de passe a bien été réinitialiser pour le site Hus- Design with passion. Votre nouveau mot de passe temporaire est '$randomPassword'. Veuillez changer de mot de passe une fois sur le site en vous dirigeant vers la page profil et en cliquant sur le bouton 'Changer de mot de passe' .";
								
						mail($_POST['email'], 'Votre mot de passe temporaire', $body, 'From: Hus@admin.fr'); // Mise en page du mail
								
						echo "<p class='validation'>Votre mot de passe a bien été changé. Vous allez recevoir un mot de passe temporaire dans votre boîte mail. Regardez également dans vos spams si vous ne le trouvez pas dans votre dossier de réception principal. Une fois connecté avec ce mot de passe, veillez à le changer pour un mot de passe que vous vous souviendrez plus facilement en veillant à ce qu'il soit sécurisé. </p>";
					}
				}
				else{
					echo '<p class="erreur">Vous devez renseigner une adresse email valide : exemple@monsite.fr</p>';
				}
			}
			else{
					echo '<p class="erreur">Vous devez renseigner une adresse mail.</p>';
			}
}

?>
<h1>Réinitialiser votre mot de passe</h1>
<p class="parag_mdpforget">Entrer votre adresse mail et nous vous enverrons un nouveau mot de passe.</p>
<form method="post" action="<?php echo RACINE_SITE ?>mdpperdu.php">
  <input type="email" id="mail" name="email" placeholder="Votre adresse mail : exemple@monsite.fr" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" maxlength="60" size="40" class="design2_input "><br>
  <input type="submit" name="reset" value="Réinitialiser mon mot de passe" id="forgot_password" class="design_submitBtn mdp">
</form>


<script type="text/javascript">
	$(document).ready(function(){
		$('.erreur').delay(3000).fadeOut("slow" );
		$('.validation').delay(5000).fadeOut("slow" );
	});
</script>

<?php
require_once('inc/footer.inc.php');