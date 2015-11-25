<?php
require_once('inc/init.inc.php');
require_once('inc/haut_de_site.inc.php');

if(!utilisateurEstConnecte())
{
	header("location:panier.php");
	exit();
}

?>

<!-- Script pour les CGV -->

<script type="text/javascript">
//<![CDATA[
   function checkFormCGV() {
      if(!document.getElementById("cgv_checkbox").checked) {
         document.getElementById("erreur_cgv").innerHTML = "Vous devez accepter les conditions générales de vente";
         return false;
      }
      if(!document.getElementById("letspay").checked) {
         document.getElementById("erreur_radio").innerHTML = "Vous devez cocher votre moyen de paiement";
         return false;
      }	  
   }
//]]>
</script>

<?php

if(isset($_POST['adresseBis'])){
	 
	// $verif_caracteres1 = preg_match('#^[a-zA-Z-]+$#',$_POST['nom_livraison']);
	// $verif_caracteres2 = preg_match('#^[a-zA-Z-]+$#',$_POST['prenom_livraison']);
	// $verif_caracteres3 = preg_match('#^[a-zA-Z0-9.-]+$#',$_POST['ville_livraison']);
	// $verif_caracteres4 = preg_match('#^[0-9]+$#',$_POST['cp_livraison']);
	// $verif_caracteres5 = preg_match('#^[a-zA-Z-]+$#',$_POST['adresse_livraison']);
	
	// if(!$verif_caracteres1 || !$verif_caracteres2 || !$verif_caracteres3 || !$verif_caracteres4 || !$verif_caracteres5){
	  
	if(!empty($_POST['nom_livraison']) && !empty($_POST['prenom_livraison']) && !empty($_POST['ville_livraison']) && !empty($_POST['cp_livraison']) && !empty($_POST['adresse_livraison'])){
		
	    // Protection faille XSS:
	  
	    $nomBis = htmlspecialchars($_POST['nom_livraison']);
	    $prenomBis = htmlspecialchars($_POST['prenom_livraison']);
	    $villeBis = $mysqli->real_escape_string(htmlspecialchars($_POST['ville_livraison']));
	    $cpBis = htmlspecialchars($_POST['cp_livraison']);
	    $adresseBis = $mysqli->real_escape_string(htmlspecialchars($_POST['adresse_livraison']));
	  	  
        executeRequete("UPDATE membre SET nom_livraison = '$nomBis',prenom_livraison = '$prenomBis',ville_livraison = '$villeBis' ,cp_livraison = '$cpBis' ,adresse_livraison = '$adresseBis' WHERE id_membre =' " . $_SESSION['utilisateur']['id_membre'] . " ' ");
	  
	}
	// else{
		// $msg .= "<div class='erreur'>L'un des champs de votre adresse de livraison bis est incorrect</div>";
	// }
	// }
}

#----------VERIFICATION CODE PROMO----------

if( isset($_POST['validation_codePromo'])){
	
	for($i = 0; $i < count($_SESSION['panier']['id_article']) ; $i++){
		$query = executeRequete("SELECT * FROM article ar, promotion prom WHERE prom.id_promo = ar.id_promo AND '$_POST[code_promoPanier]' = prom.code_promo AND '".$_SESSION['panier']['id_article'][$i]."' = ar.id_article");
	}
	
	$verif_promo = $query->fetch_assoc();
}
if(!empty($_POST['code_promoPanier']) && isset($_POST['validation_codePromo'])){
	$query_code = executeRequete("SELECT id_article, code_promo, ar.id_promo, reduction FROM promotion prom, article ar WHERE '$_POST[code_promoPanier]' = prom.code_promo AND ar.id_article IN (".implode(",", $_SESSION['panier']['id_article'] )." ) AND ar.id_promo = prom.id_promo");
	$resultat_queryCode = $query_code->fetch_assoc();
}

// Récupération adresses en BDD:

$recupBis = executeRequete("SELECT * FROM membre WHERE id_membre =' " . $_SESSION['utilisateur']['id_membre'] . " ' ");

$adresseBisBDD = $recupBis->fetch_assoc();

echo '<section>';

//------AFFICHAGE DU PANIER------

echo "<div id='check3'>";
	echo"<div class='checkout_road'>
		 <img src='". RACINE_SITE ."photo/line3.png' alt='' title=''>
		 <h2>3. Paiement</h2>
		 </div>";

echo"<div id='left_cart'>";		 
// Méthode de paiement

echo"<h3>Méthode de paiement</h3>";

echo"<div id='payment_method'>";
	echo"<div class='cadre_method'>";
		echo"<input type='radio' name='disabled' disabled>";
		echo"<label>Carte de crédit (indisponible)</label>";
		// echo"<img src='' title='' alt=''>";
	echo"</div>";
	echo"<div class='cadre_method'>";
		echo"<input type='radio' name='disabled' disabled>";
		echo"<img src='". RACINE_SITE ."photo/paypal.png' title='' alt=''>";
		echo"<label> (indisponible)</label>";		
	echo"</div>";
	echo"<div class='cadre_method'>";
		echo"<input type='radio' name='paiement' id='letspay'>";
		echo"<label>Paiement test</label>";		
	echo"</div>";
echo"</div>"; 


// Méthode de paiement

echo"<h3 class='h3_titleCart'>Votre adresse de livraison :</h3>";

echo"<div id='shipping_adress'>";
	if($_POST['adresse'] == 'profil'){
		echo"<div class='cadre_adressPayment'>";
			echo"<p>" . $adresseBisBDD['nom'] .' '. $adresseBisBDD['prenom'] ."</p>";
			echo"<p>" . $adresseBisBDD['ville'] . "</p>";
			echo"<p>" . $adresseBisBDD['cp'] . "</p>";
			echo"<p>" . $adresseBisBDD['adresse'] . "</p>";
		echo"</div>";
	}
	if($_POST['adresse'] == 'bis' || $_POST['adresse'] == 'bis2'){
		echo"<div class='cadre_adressPayment'>";
			echo"<p>" . $adresseBisBDD['nom_livraison'] .' '. $adresseBisBDD['prenom_livraison'] ."</p>";
			echo"<p>" . $adresseBisBDD['ville_livraison'] . "</p>";
			echo"<p>" . $adresseBisBDD['cp_livraison'] . "</p>";
			echo"<p>" . $adresseBisBDD['adresse_livraison'] . "</p>";		
		echo"</div>";		
	}
echo"</div>"; 

// Email de réception

echo"<h3 class='h3_titleCart'>Votre email :</h3>";

echo"<div id='mail_adress'>";
		echo"<div class='cadre_mail'>";
			echo"<p>" . $adresseBisBDD['mail'] . "</p>";
		echo"</div>";
		echo"<div class='cadre_mail'>";
			echo"<a href='". RACINE_SITE ."profil.php'> >> Changer d'email ?</a>";
		echo"</div>";
echo"</div>"; 

echo"</div>";// Fin div #left_cart

// Div droite :

echo"<div id='right_cart'>";
	echo"<div id='recap_panier'>";
		echo"<h3>Récapitulatif de votre panier</h3>";
		echo"<div id='titre_recap'><p class='tReca'>Titre</p><p class='tReca'>Quantité</p><p class='tReca'>Prix</p></div>";		
		for($w = 0; $w < count($_SESSION['panier']['id_article']); $w++)
		{	
			echo '<div class="article_recap">'; 
			echo "<img class='img_articlePanier' src='" . $_SESSION['panier']['photo'][$w] . "' alt='". $_SESSION['panier']['titre'][$w] . "' title='". $_SESSION['panier']['titre'][$w] . "' >";	 
			echo "<p class='titreA_recap'>" . $_SESSION['panier']['titre'][$w] . "</p>";	 	  
			echo "<p class='quantite_recap'>" . $_SESSION['panier']['quantite'][$w] . "</p>";
			if($_SESSION['panier']['id_promo'] == 1){
				echo "<p class='prix_recap'>" . $_SESSION['panier']['prix'][$w] . " €</p>";					
			}
			else{
				echo"<p class='prix_recap'>". prixAvecPromo($_SESSION['panier']['id_article'][$w]) . "€</p>";
			}			
			echo "</div>";
		}
		if(montantTotal() > 50){
			echo"<div id='free_shipping'><p>Frais de port :</p><p>Offert</p></div>";			
		}
		else{
			echo"<div id='shipping'><p>Frais de port :</p><p>4,50 €</p></div>";			
		}		
		echo"<div id='montant_panier'><p>Sous total :</p><p>" . montantSousTotal() . " €</p></div>";
			
		echo"<div id='montant_panierTotal'><p>Prix total :</p><p>" . montantTotal() . " €</p></div>";
	echo"</div>";	
?>

<!-- Validation de la checkbox -->

	<form id="form_payment" action="panier.php" method="POST" onsubmit="return checkFormCGV()">
	   <label for="cgv-checkbox" id="checkText">J'accepte les conditions g&eacute;n&eacute;rales de vente (<a href="<?php echo RACINE_SITE;?>cgv.php"> Voir les C.G.V</a> )</label>
	   <input type="checkbox" name="cgv-checkbox" id="cgv_checkbox" />
	   <div id="erreur_cgv" style="color: red;"></div>
	   <div id="erreur_radio" style="color: red;"></div>

<?php
 
  if(utilisateurEstConnecte())
  {
    echo '<input id="payment_Btn" type="submit" name="payer" value="Payer">';
    echo '</form>';
  }
  else
  {
    echo '<p>Veuillez-vous <a href="connexion.php">connecter</a> afin de pouvoir payer</p>';
  }
echo"</div>"; // fin div #right_cart
echo"</div>"; // fin div #check3

echo "<div id='first_Btn'><a class='return_BtnAdresse' href='" . RACINE_SITE . "panier_adresse.php'>Étape précédente</a></div>";

require_once('inc/footer.inc.php');





