<?php
require_once('inc/init.inc.php');
require_once('inc/haut_de_site.inc.php');

echo'<section>';

if($_GET['categorie'] == 'all'){
	$donnees = executeRequete("SELECT categorie FROM article GROUP BY categorie");
	
// Affichage menu:

echo"<a class='menu_pages inactiveA' href=' ". RACINE_SITE . "shop.php?p=1'>Tous nos objets</a><a class='menu_pages inactiveA' href=' ". RACINE_SITE . "news.php?p=1'>Nouveautés</a><a class='menu_pages activeA' href=' ". RACINE_SITE . "categories.php?categorie=all'>Catégories </a><a class='menu_pages inactiveA' href=' ". RACINE_SITE . "brands.php?marque=all'>Marques</a>";

echo '<div id="content">';

	while($categories = $donnees->fetch_assoc()){
		
// Affichage des catégories:

$recup_photo = executeRequete("SELECT id_article, categorie, photo FROM article WHERE categorie = '$categories[categorie]' ORDER BY id_article ASC LIMIT 0,1");
$photo = $recup_photo->fetch_assoc();
		
		echo"<a href='?categorie=$categories[categorie]' class='cat_design'>";
			echo"<div class='image_cat' style='background-image:url($photo[photo])'></div>";
			echo'<div class="titre_cat">' . $categories['categorie'] . '</div>';
		echo'</a>';
	}
	
}
if($_GET['categorie'] != 'all'){
	$donnees2 = executeRequete("SELECT ar.id_article, note, categorie, titre, prix, photo, id_promo FROM article ar LEFT JOIN avis av ON ar.id_article = av.id_article  WHERE categorie = '$_GET[categorie]' GROUP BY ar.id_article ");
	// $recupCategories = executeRequete("SELECT categorie FROM article GROUP BY categorie ");
	// $recupMarques = executeRequete("SELECT marque FROM article GROUP BY marque ");	
	
// Affichage de tous les articles de la catégorie:	

// Header dynamique en fonction de la catégorie:	
	echo"<div id='header_categorie'><img src=' ". RACINE_SITE . "photo/$_GET[categorie].jpg' alt='$_GET[categorie]' title='Bandeau catégorie $_GET[categorie]' /></div>";
	
	echo'<a href="categories.php?categorie=all"> < Retour aux catégories </a>';
    while($article = $donnees2->fetch_assoc()) //je récupère les informations
    {
		$recup_promo = informationSurUnePromotion($article['id_article']);
		$promotion = $recup_promo->fetch_assoc();
		$calcul_note = executeRequete("SELECT ROUND(AVG(note)*2)/2 AS moyenne FROM avis WHERE id_article = $article[id_article]");
		$note_final = $calcul_note->fetch_assoc();		
      echo '<div class="articleCat">';
		echo'<div class="note">';
			imageNotation($note_final['moyenne']);
		echo'</div>';
			if($article['id_promo'] == 1){
				echo'<div class="fond_prixCat">';				
						echo"<p>$article[prix] €</p>";
				echo '</div>';						
			}
			else{
				echo'<div class="fond_prixPromoCat">';
					echo"<p><span class='old_price'>$article[prix]€ </span> ". prixAvecPromo($article['id_article']) . "€</p>";
				echo '</div>';						
			}
				echo "<a href='fiche_article.php?id_article=$article[id_article]&categorie=$article[categorie]'><img src='$article[photo]' alt='$article[titre]' class='img_article'></a>";
				echo"<h3>$article[titre]</h3>";
			if(utilisateurEstConnecte())
				{					
					echo "<form method='post' action='panier.php'>";
						echo "<input type='hidden' name='id_article' value='$article[id_article]'>";
						echo "<input type='hidden' name='quantite' value='1'>";
						echo '<input type="submit" name="ajout_panier" value="" class="bouton btn_ajoutPanierFA btn_connect" data-tooltip="Ajouter au panier">';
					echo '</form>';
					echo "<a href='fiche_article.php?id_article=$article[id_article]&categorie=$article[categorie]' class='bouton btn_ficheDetails' data-tooltip='Voir la fiche du produit'></a>";	
					echo "<form method='post' action='wishlist.php'>";
						echo "<input type='hidden' name='id_article' value='$article[id_article]'>";
						echo '<input type="submit" name="ajout_wishlist" value="" class="bouton btn_wishlist" data-tooltip="Ajouter au favoris">';
					echo '</form>';
				}
			else
				{
					echo "<form method='post' action='panier.php'>";
						echo "<input type='hidden' name='id_article' value='$article[id_article]'>";
						echo "<input type='hidden' name='quantite' value='1'>";
						echo '<input type="submit" name="ajout_panier" value="" class="bouton btn_ajoutPanierFA btn_disconnect" data-tooltip="Ajouter au panier">';
					echo '</form>';
					echo "<a href='fiche_article.php?id_article=$article[id_article]&categorie=$article[categorie]' class='bouton btn_ficheDetails' data-tooltip='Voir la fiche du produit'></a>";					
				}			
      echo '</div>';
    }	
	
}

	
echo '</div>';  

require_once('inc/footer.inc.php');







