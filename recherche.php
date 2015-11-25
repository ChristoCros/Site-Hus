<?php
require_once('inc/init.inc.php');
require_once('inc/haut_de_site.inc.php');

//Ajout des articles au panier :

creationDuPanier();

if(isset($_POST['ajout_panier']))
{
   
   $result = informationSurUnArticle($_POST['id_article']);
   $result_promo = informationSurUnePromotion($_POST['id_article']);

   $article = $result->fetch_assoc();
   $promo = $result_promo->fetch_assoc();
 
	ajouterArticleDansPanier($article['id_article'],$article['titre'],$article['photo'],$article['prix'],$_POST['quantite'],$promo['id_promo'], $promo['reduction']);
	
	header('location: panier.php');
}

echo '<section>';
echo '<div id="content_site">';

if(isset($_POST['requete']) && $_POST['requete'] != NULL)
	{
		$requete = $mysqli->real_escape_string($_POST['requete']);
		
		$queryArticle = executeRequete("SELECT * FROM article WHERE titre LIKE '%$requete%' ORDER BY id_article DESC") or die (mysql_error());
		$queryCatégorie = executeRequete("SELECT * FROM article WHERE categorie LIKE '%$requete%' GROUP BY categorie ORDER BY id_article DESC") or die (mysql_error());
		$queryMarque = executeRequete("SELECT * FROM article WHERE marque LIKE '%$requete%' GROUP BY marque ORDER BY id_article DESC") or die (mysql_error());		
		
		$nb_articles = $queryArticle->num_rows;
		$nb_categories = $queryCatégorie->num_rows;
		$nb_marques = $queryMarque->num_rows;
		if($nb_articles != 0 || $nb_categories != 0 || $nb_marques != 0)
		{	
				echo '<div id="div-recherche">';
					echo "<p class='resultat_recherche'>Nous avons trouvé ";
						if($nb_categories > 1) 
							{
								echo "<a href='#cat_found'>$nb_categories catégories </a>";
							} 
						if($nb_categories == 1) 
							{ 
								echo "<a href='#cat_found'>$nb_categories catégorie </a>";
							}
						if($nb_articles > 1) 
							{
								echo "<a href='#articles_found'>$nb_articles articles </a>";
							} 
						if($nb_articles == 1) 
							{ 
								echo "<a href='#articles_found'>$nb_articles article </a>"; 
							}							
						if($nb_marques > 1) 
							{
								echo "<a href='#brands_found'>$nb_marques marques </a>";
							} 
						if($nb_marques == 1) 
							{ 
								echo "<a href='#brands_found'>$nb_marques marque </a>";
							}
					echo ".</p>";
				echo'</div>';
			if($nb_categories >= 1){			
				echo '<div id="cat_found">';
					echo"<h4>Catégories</h4>";				
					while($categorie = $queryCatégorie->fetch_assoc()) //je récupère les informations
					{
						echo"<a href='" . RACINE_SITE . "categories.php?categorie=$categorie[categorie]'>$categorie[categorie]</a>";
						echo"<div class='content_bar'>";
						
						$articleBrand = executeRequete("SELECT * FROM article WHERE categorie = '$categorie[categorie]' GROUP BY id_article LIMIT 5");
							while($resultBrand = $articleBrand->fetch_assoc()){
								echo"<div class='article_brand'>";
									echo"<div class='result_brand'>"
									. "<a href='" . RACINE_SITE ."fiche_article.php?id_article=$resultBrand[id_article]&categorie=$resultBrand[categorie]'>"
										. "<img src='$resultBrand[photo]' height='120' alt='$resultBrand[titre]'>"
									. "</a>"
									. "</div>";
								echo"</div>";
							}
						echo"</div>";
					}
				echo '</div>';
			}				
			if($nb_marques >= 1){			
				echo '<div id="brands_found">';
					echo"<h4>Marques</h4>";				
					while($marque = $queryMarque->fetch_assoc()) //je récupère les informations
					{
						echo"<a href='" . RACINE_SITE . "brands.php?marque=$marque[marque]'>$marque[marque]</a>";
						echo"<div class='content_bar'>";
						
						$articleBrand = executeRequete("SELECT * FROM article WHERE marque = '$marque[marque]' GROUP BY id_article LIMIT 5");
							while($resultBrand = $articleBrand->fetch_assoc()){
								echo"<div class='article_brand'>";
									echo"<div class='result_brand'>"
									. "<a href='" . RACINE_SITE ."fiche_article.php?id_article=$resultBrand[id_article]&categorie=$resultBrand[categorie]'>"
										. "<img src='$resultBrand[photo]' height='120' alt='$resultBrand[titre]'>"
									. "</a>"
									. "</div>";
								echo"</div>";
							}
						echo"</div>";
					}
				echo '</div>';
			}
			if($nb_articles >= 1){			
				echo '<div id="articles_found">';
					echo"<h4>Articles</h4>";
					while($article = $queryArticle->fetch_assoc()) //je récupère les informations
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
								echo "<a href='fiche_article.php?id_article=$article[id_article]&categorie=$article[categorie]'><img src='$article[photo]' height='50%'  alt='$article[titre]' class='img_article'></a>";
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
				echo '</div>';
			}
		} 
		else
			{
				echo '<div id="div-recherche">';
					echo "<p class='resultat_recherche'>Nous n'avons trouvé aucun résultat pour votre recherche : '' <span class='no_result'>". $_POST['requete'] . "</span> ''.</p>";
				echo'</div>';
			}

	}
echo '</div>';
				
		
require_once('inc/footer.inc.php');





