<?php
require_once('inc/init.inc.php');
require_once('inc/haut_de_site.inc.php');

echo '<section id="plan_section">';

$requestBrands = executeRequete("SELECT marque FROM article GROUP BY marque ORDER BY marque ASC");

$requestCategories = executeRequete("SELECT categorie FROM article GROUP BY categorie ORDER BY categorie ASC");


?>

<h1>Plan du site Hus - Design with passion</h1>

<div id="plan_gauche">
	<div>
		<h2>NAVIGATION</h2>
		<ul>
			<li><a href="<?php echo RACINE_SITE;?>index.php">Accueil</a></li>
			<li><a href="<?php echo RACINE_SITE;?>shop.php?p=1">Shop</a></li>
			<li><a href="<?php echo RACINE_SITE;?>panier.php">Panier</a></li>
			<li><a href="<?php echo RACINE_SITE;?>wishlist.php">Favoris</a></li>
		</ul>
	</div>
	<div>
		<h2>INFORMATIONS</h2>
		<ul>
			<li><a href="<?php echo RACINE_SITE;?>cgv.php">Conditions générales de ventes</a></li>
			<li><a href="<?php echo RACINE_SITE;?>mentions.php">Mentions légales</a></li>
			<li><a href="<?php echo RACINE_SITE;?>contact.php">Contactez-nous / SAV</a></li>
			<li><a href="<?php echo RACINE_SITE;?>retractation.php">Demande de remboursement</a></li>
		</ul>
	</div>	
	<div>
		<h2>NOS MARQUES</h2>
		<ul>
<?php		
			while($resultBrands = $requestBrands->fetch_assoc()){
				echo"<li><a href='" . RACINE_SITE . "brands.php?marque=$resultBrands[marque]'>$resultBrands[marque]</a></li>";		
			}
?>
		</ul>
	</div>		
</div>
<div id="plan_droite">
	<div>
		<h2>COMPTE</h2>
		<ul>
			<li><a href="<?php echo RACINE_SITE;?>connexion.php">Se connecter </a></li>
			<li><a href="<?php echo RACINE_SITE;?>mdpperdu.php"> Mot de passe oublié ?</a></li>
			<li><a href="<?php echo RACINE_SITE;?>inscription.php">Inscription</a></li>
			<li><a href="<?php echo RACINE_SITE;?>profil.php">Mon compte</a></li>
		</ul>	
	</div>
	<div>
		<h2>SHOPPING</h2>
		<ul>
			<li><a href="<?php echo RACINE_SITE;?>shop.php?p=1">Tous nos articles</a></li>
			<li><a href="<?php echo RACINE_SITE;?>news.php">Nouveautés</a></li>
			<li><a href="<?php echo RACINE_SITE;?>brands.php">Marques</a></li>
			<li><a href="<?php echo RACINE_SITE;?>cetegories.php">Catégories</a></li>
		</ul>	
	</div>
	<div>
		<h2>NOS CATÉGORIES</h2>
		<ul>
<?php		
			while($resultCategories = $requestCategories->fetch_assoc()){
				echo"<li><a href='" . RACINE_SITE . "categories.php?categorie=$resultCategories[categorie]'>$resultCategories[categorie]</a></li>";		
			}
?>
		</ul>
	</div>	
</div>

<?php

require_once('inc/footer.inc.php');







