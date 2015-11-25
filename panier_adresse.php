<?php

require_once('inc/init.inc.php');
require_once('inc/haut_de_site.inc.php');

if(!utilisateurEstConnecte())
{
	header("location:panier.php");
	exit();
}

// Récupération adresse en BDD:

$recupBis = executeRequete("SELECT * FROM membre WHERE id_membre =' " . $_SESSION['utilisateur']['id_membre'] . " ' ");

$adresseBisBDD = $recupBis->fetch_assoc();

echo '<section>';

echo $msg;

//------AFFICHAGE DU PANIER------
?>
<div id='check2'>
	<div class="checkout_road">
		<img src="<?php echo RACINE_SITE ?>photo/line2.png" alt="" title="">
		<h2>2. Adresse de livraison</h2>
	</div>

	<div id="content_adress">
		<h3>Où se faire livrer :</h3>
			<div id="adresse_livraisonChoix">
<?php

// Adresse de livraison

echo"<div id='shipping_adress'>";
	echo"<div class='cadre_adress'>";
		echo"<form action='panier_paiement.php' method='POST'>";
		echo"<input type='radio' name='adresse' value='profil' onClick='cacher();'>";
		echo"<label class='first_child'>" . $adresseBisBDD['nom'] .' '. $adresseBisBDD['prenom'] ."</label>";
		echo"<label>" . $adresseBisBDD['ville'] . "</label>";
		echo"<label>" . $adresseBisBDD['cp'] . "</label>";
		echo"<label>" . $adresseBisBDD['adresse'] . "</label>";
	echo"</div>";
	if(!empty($adresseBisBDD['nom_livraison'])){
		echo"<div class='cadre_adress'>";
			echo"<input type='radio' name='adresse' value='bis' onClick='cacher();'>";
			echo"<label class='first_child'>" . $adresseBisBDD['nom_livraison'] .' '. $adresseBisBDD['prenom_livraison'] ."</label>";
			echo"<label>" . $adresseBisBDD['ville_livraison'] . "</label>";
			echo"<label>" . $adresseBisBDD['cp_livraison'] . "</label>";
			echo"<label>" . $adresseBisBDD['adresse_livraison'] . "</label>";		
		echo"</div>";		
	}
	echo"<div class='cadre_adressDeux'>";
		echo"<input type='radio' name='adresse' value='bis2' onClick='afficher();'>";
		echo"<label class='where'>Se faire livrer ailleurs ?</label>";
	echo"</div>";	
echo"</div>";
echo"</div>";

?>
	<div id="form_adresse">
			<label>Nom</label>
			<input type="text" id="nom_cart" name="nom_livraison" placeholder="Nom" data-tooltip="Caractères acceptés : a-z A-Z" data-tooltip-stickto="left" class="design_input" ><br>

			<label>Prénom</label>
			<input type="text" id="prenom_cart" name="prenom_livraison" placeholder="Prénom" data-tooltip="Caractères acceptés : a-z A-Z" data-tooltip-stickto="left"  class="design_input" ><br>

			<label>Pays</label>
			<input type="text" id="pays_cart" name="pays" placeholder="France" class="design_input" readonly><br>			
			
			<label>Code Postal</label>
			<input type="text" id="cp_cart" name="cp_livraison" placeholder="Code postal" data-tooltip="5 chiffres requis : 0-9" data-tooltip-stickto="left" maxlength="5"  class="design_input" ><br> 			
			
			<label>Ville</label>
			<input type="text" id="ville_cart" name="ville_livraison" placeholder="Ville" data-tooltip="Caractères acceptés : a-z A-Z . -" data-tooltip-stickto="left" class="design_input" ><br> 			
			
			<label>Adresse</label>
			<input id="adresse_cart" name="adresse_livraison" placeholder="Adresse" data-tooltip="Caractères acceptés : a-z A-Z 0-9" data-tooltip-stickto="left" class="design_input" ><br><br>
	</div>
	<div id="formAdress_submitBtn">
			<input type="submit" name="adresseBis" value="Valider et passer au paiement" class="design_submitBtn">			
		</form>	
	</div>
</div>
<?php
	echo"<div id='recap_panier'>";
		echo"<h3>Récapitulatif de votre panier</h3>";
		echo"<div id='titre_recap'><p class='tReca'>Titre</p><p class='tReca'>Quantité</p><p class='tReca'>Prix</p></div>";		
		for($w = 0; $w < count($_SESSION['panier']['id_article']); $w++) //boucle qui tournera autant de fois qu'il y a d'articles dans notre panier
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
			echo"<div id='shipping'><p>Frais de port :</p><p>Offert</p></div>";			
		}
		else{
			echo"<div id='shipping'><p>Frais de port :</p><p>4,50 €</p></div>";			
		}		
		echo"<div id='montant_panier'><p>Sous total :</p><p>" . montantSousTotal() . " €</p></div>";
		
		echo"<div id='montant_panierTotal'><p>Prix total :</p><p>" . montantTotal() . " €</p></div>";
	echo"</div>";
?>	
</div>

<div id="first_Btn"><a class="btn_paiementFirst" href="<?php echo RACINE_SITE ?>panier.php">Étape précédente</a></div>

<script type="text/javascript">
	document.getElementById("form_adresse").style.display = "none";
	document.getElementById("formAdress_submitBtn").style.display = "none";
	 
	function afficher()
	{
		document.getElementById("form_adresse").style.display = "block";
		document.getElementById("formAdress_submitBtn").style.display = "block";
	}
	 
	function cacher()
	{
		document.getElementById("form_adresse").style.display = "none";
		document.getElementById("formAdress_submitBtn").style.display = "block";		
	}
</script>


<?php

require_once('inc/footer.inc.php');





