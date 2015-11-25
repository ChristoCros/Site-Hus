<!DOCTYPE html>
<html>
  <head>
  
    <title>Hus | Design with passion</title>
	<meta content="Hus - Design with passion vous permet de trouver de beaux objets design et de qualité pour votre intérieur et pour épater vos amis !" name="description">
	<meta charset="utf-8">
	<link REL="SHORTCUT ICON" href="<?php echo RACINE_SITE;?>photo/favicon.png">	
    <link rel="stylesheet" href="<?php echo RACINE_SITE;?>inc/style.css" media="screen">
    <link rel="stylesheet" href="<?php echo RACINE_SITE;?>inc/dashboard.css" media="screen">
    <link rel="stylesheet" href="<?php echo RACINE_SITE;?>inc/recherche.css" media="screen">
	<link rel="stylesheet" type="text/css" href="<?php echo RACINE_SITE;?>inc/html5tooltips.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo RACINE_SITE;?>inc/html5tooltips.animation.css" />
	
  </head>
  <body>
  
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo RACINE_SITE;?>inc/js/html5tooltips.js"></script>
	<script src="<?php echo RACINE_SITE;?>inc/js/jquery.ui.js"></script>
	<script src="<?php echo RACINE_SITE;?>inc/js/main.js"></script>
	
	
  <!-- Partie connexion, incription, voir mon profil -->
		<div id="back-log">
		  <div id ="log">
		  		<?php
			if(utilisateurEstConnecte())
			{
				echo '<a href="'. RACINE_SITE . 'profil.php" class="button-log compte">Mon compte</a>';
				echo '<a href="'. RACINE_SITE . 'wishlist.php" class="button-log fav">Favoris ('. favorisNbLoveArticle($_SESSION['utilisateur']['id_membre']) .')</a>';
				if(utilisateurEstConnecteEtAdmin())
				{
					echo '<ul id="menu-deroulant">';					
						echo '<li class="menu-horizontal gestion"><a href="'. RACINE_SITE . 'admin/dashboard.php" class="admin">Administration</a>';
							echo '<ul>';
								echo '<li class="sous-menu"><a href="'. RACINE_SITE . 'admin/gestion_membre.php">Gestion des membres</a></li>';
								echo '<li class="sous-menu"><a href="'. RACINE_SITE . 'admin/gestion_commande.php?action=0&id_commande=0">Gestion des commandes</a></li>';
								echo '<li class="sous-menu"><a href="'. RACINE_SITE . 'admin/gestion_avis.php?action=0">Gestion des avis</a></li>';
								echo '<li class="sous-menu"><a href="'. RACINE_SITE . 'admin/gestion_articles.php?action=affichage">Gestion des articles</a></li>';
								echo '<li class="sous-menu"><a href="'. RACINE_SITE . 'admin/envoi_newsletter.php">Gestion newsletter</a></li>';
								echo '<li class="sous-menu"><a href="'. RACINE_SITE . 'admin/gestion_promos.php?action=affichage">Gestion des promotions</a></li>';
								echo '<li class="sous-menu"><a href="'. RACINE_SITE . 'admin/statistiques.php">Statistiques</a></li>';
							echo '</ul>';
						echo '</li>';					
					echo '</ul>';					
				}
				if(utilisateurEstConnecte() && !utilisateurEstConnecteEtAdmin()){
					echo '<a href="?action=deconnexion" class="button-log decMembre">Déconnexion</a>';					
				}
				else{
					echo '<a href="?action=deconnexion" class="button-log dec">Déconnexion</a>';
				}					
			}
			else  //menu pour le simple visiteur
			{		
				//echo '<a href="'. RACINE_SITE . '" class="button-log fav">Favoris ( ... )</a>';
				echo '<a href="'. RACINE_SITE . 'inscription.php" class="button-log subs disconnect">Inscription</a>';			
				echo '<a href="'. RACINE_SITE . 'connexion.php" class="button-log connex">Connexion</a>';				
			}
			if(utilisateurEstConnecte() && isset($_GET['action']) && $_GET['action'] == 'deconnexion')
			{
			  session_destroy();
			  header("location:" . RACINE_SITE . "index.php");
			  exit();
			}
			if(isset($_POST['search']))
			{
			  $requete = $_POST['requete'];
			  header("location:" . RACINE_SITE . "recherche.php?search=$requete");
			  exit();
			}			
			?>

			</div>
		</div>	
		<header>
			<div id="header">
				<nav>
					<ul>
						<li><a href="<?php echo RACINE_SITE;?>index.php" class="home">Accueil</a></li>
						<li><a href="<?php echo RACINE_SITE;?>shop.php?p=1"  class="shop">Shop</a></li>
					</ul>
				</nav>
				<div id="logo">
					<a href="<?php echo RACINE_SITE;?>index.php"><img src="<?php echo RACINE_SITE;?>photo/logo.png" alt="logo" title=""></a>
				</div>
				<div class="searchbar">
					<form method="post" action="<?php echo RACINE_SITE;?>recherche.php" >
						<input type="search" name="requete" id="recherche" placeholder="Rechercher...">
					</form>	
				</div>
				<div id="panier">		
					<?php compterArticles();
						if(compterArticles()< 2)
							{
								echo '<a href=" ' .  RACINE_SITE . 'panier.php" class="nb_resa">' . compterArticles() .  ' article <br/>' . virgule(montantSousTotal()) .' €</a>';
							}
						else{
								echo '<a href=" ' .  RACINE_SITE . 'panier.php" class="nb_resa">' . compterArticles() .  ' articles <br/>' . virgule(montantSousTotal()) .' €</a>';
							}?>
				</div>				
			</div>
			<div id="livraison_gratuite">
				<div id="text_livraison">
					<img src="<?php echo RACINE_SITE;?>/photo/free_del.png" alt="" title="">
					<p>Livraison gratuite à partir de 50€ d'achat ! C'est le moment d'en profiter !<p>
				</div>
			</div>
		</header>	
		
   <!-- <div id="conteneur"> -->
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	