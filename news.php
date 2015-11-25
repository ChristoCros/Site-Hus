<?php
require_once('inc/init.inc.php');
require_once('inc/haut_de_site.inc.php');

$url = $_SERVER['REQUEST_URI'];

//Ajout des articles au panier :

creationDuPanier();

if(isset($_POST['ajout_panier']))
{
   
   $result = informationSurUnArticle($_POST['id_article']);
   $result_promo = informationSurUnePromotion($_POST['id_article']);

   $article = $result->fetch_assoc();
   $promo = $result_promo->fetch_assoc();
 
	ajouterArticleDansPanier($article['id_article'],$article['titre'],$article['photo'],$article['prix'],$_POST['quantite'],$promo['id_promo'], $promo['reduction']);
	
	header('location: ' . $url);
}

// Pagination:

$year = executeRequete("SELECT YEAR(NOW()) AS annee");
$annee = $year->fetch_assoc();

$month = executeRequete("SELECT MONTH(NOW())-1 AS mois");
$mois = $month->fetch_assoc();

$compteArticle = executeRequete(" SELECT COUNT(id_article) AS nbArt FROM article  WHERE date BETWEEN  '$annee[annee]-$mois[mois]-01'  AND NOW()");
$nbArticleBDD = $compteArticle->fetch_assoc();

$nbArt = $nbArticleBDD['nbArt'];
$perPage = 16;
$nbPage = ceil($nbArt/$perPage);

if(isset($_GET['p']) && $_GET['p']>0 && $_GET['p'] <= $nbPage){
		$cPage = $_GET['p'];
}
else{
	$cPage = 1;
}

$donnees = executeRequete("SELECT ar.id_article, note, categorie, titre, prix, photo, id_promo FROM article ar LEFT JOIN avis av ON ar.id_article = av.id_article WHERE ar.date BETWEEN  '$annee[annee]-$mois[mois]-01'  AND NOW()  GROUP BY ar.id_article ORDER BY ar.date DESC LIMIT " . (($cPage-1)*$perPage) . ",$perPage");

$recupCategories = executeRequete("SELECT categorie FROM article GROUP BY categorie ");
$recupMarques = executeRequete("SELECT marque FROM article GROUP BY marque ");

echo'<section>';
echo '<div id="content">';
	echo '<div class="affichage_views">';
	
echo"<a class='menu_pages inactiveA' href=' ". RACINE_SITE . "shop.php?p=1'>Tous nos objets</a><a class='menu_pages activeA' href=' ". RACINE_SITE . "news.php?p=1'>Nouveautés</a><a class='menu_pages inactiveA' href=' ". RACINE_SITE . "categories.php?categorie=all'>Catégories </a><a class='menu_pages inactiveA' href=' ". RACINE_SITE . "brands.php?marque=all'>Marques</a>";

if($nbPage > 1){	
		echo'<div class="pagination">';
		if(($_GET['p']-1) < 1 ){
			echo "<a href='news.php?p=1'>&lt;</a>";			
		}
		else{
			echo "<a href='news.php?p=" . ($_GET['p']-1) . "'>&lt;</a>";
		}		
			for($i = 1; $i <= $nbPage; $i++){
				if($i == $cPage){
					echo "<span>$i</span>";
				}
				else{
					echo "<a href='news.php?p=$i'>$i</a>";
				}
			}
		if(($_GET['p']+1) > $nbPage){
			echo "<a href='news.php?p=$nbPage'>&gt;</a>";
		}
		else{
			echo "<a href='news.php?p=" . ($_GET['p']+1) . "'>&gt;</a>";					
		}
		echo'</div>';
}		
	echo'</div>';	
    while($article = $donnees->fetch_assoc()) //je récupère les informations
    {
		$recup_promo = informationSurUnePromotion($article['id_article']);
		$promotion = $recup_promo->fetch_assoc();
		$calcul_note = executeRequete("SELECT ROUND(AVG(note)*2)/2 AS moyenne FROM avis WHERE id_article = $article[id_article]");
		$note_final = $calcul_note->fetch_assoc();		
      echo '<div class="article">';
		echo'<div class="note">';
			imageNotation($note_final['moyenne']);
		echo'</div>';
			if($article['id_promo'] == 1){
				echo'<div class="fond_prix">';				
						echo"<p>$article[prix] €</p>";
				echo '</div>';						
			}
			else{
				echo'<div class="fond_prixPromo">';
					echo"<p><span class='old_price'>$article[prix]€ </span> ". prixAvecPromo($article['id_article']) . "€</p>";
				echo '</div>';						
			}
				echo "<a href='fiche_article.php?id_article=$article[id_article]&categorie=$article[categorie]'><img src='$article[photo]' alt='$article[titre]' class='img_article'></a>";
				echo"<h3>$article[titre]</h3>";
				if(utilisateurEstConnecte())
					{					
						echo "<form method='post' action='" . $url ."'>";
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
						echo "<form method='post' action='" . $url ."'>";
							echo "<input type='hidden' name='id_article' value='$article[id_article]'>";
							echo "<input type='hidden' name='quantite' value='1'>";
							echo '<input type="submit" name="ajout_panier" value="" class="bouton btn_ajoutPanierFA btn_disconnect" data-tooltip="Ajouter au panier">';
						echo '</form>';
						echo "<a href='fiche_article.php?id_article=$article[id_article]&categorie=$article[categorie]' class='bouton btn_ficheDetails' data-tooltip='Voir la fiche du produit'></a>";					
					}			
      echo '</div>';
    }	
    echo '</div>';
	echo '<div class="affichage_views">';

if($nbPage > 1){	
		echo'<div class="pagination">';
		if(($_GET['p']-1) < 1 ){
			echo "<a href='news.php?p=1'>&lt;</a>";			
		}
		else{
			echo "<a href='news.php?p=" . ($_GET['p']-1) . "'>&lt;</a>";
		}		
			for($i = 1; $i <= $nbPage; $i++){
				if($i == $cPage){
					echo "<span>$i</span>";
				}
				else{
					echo "<a href='news.php?p=$i'>$i</a>";
				}
			}
		if(($_GET['p']+1) > $nbPage){
			echo "<a href='news.php?p=$nbPage'>&gt;</a>";
		}
		else{
			echo "<a href='news.php?p=" . ($_GET['p']+1) . "'>&gt;</a>";					
		}
		echo'</div>';
}		
    echo '</div>';
echo '</div>';  


require_once('inc/footer.inc.php');









