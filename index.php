<?php
require_once('inc/init.inc.php');
//j'inclus les parties de mon site : 
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

$donnees = executeRequete("SELECT id_article FROM avis WHERE id_article != '' GROUP BY id_article ORDER BY AVG(note) DESC LIMIT 4");

?>

<section>

<div id="header_index">
	<div id="text_button">
		<div id="text_index">
			<h1>HUS</h1>
			<div class="underline_title"></div>			
			<p>C'est ici que tout commence.</p>
			<p>Asseyez-vous, détendez-vous et admirez nos objets de décoration qui rendront vos amis jaloux une fois chez vous !</p>
			<p>Alors n'hésitez plus et foncer !</p>
		</div>
		<div>
			<a id="button_index" href="<?php echo RACINE_SITE ?>shop.php?p=1">Commencer mes achats</a>
		</div>		
	</div>
</div>

<?php
	echo"<div id='bestNoteIndex'>";
		echo"<h3 class='titre_indexProducts'>Nos articles les mieux notés :</h3>";
		echo"<div class='underline_title'></div>";
    while($resultat = $donnees->fetch_assoc())
    {
		foreach($resultat as $indice => $information){
			$recupInfos = executeRequete("SELECT * FROM article ar, avis av WHERE ar.id_article = av.id_article AND ar.id_article='" . $information . "'" );
			$similars = $recupInfos->fetch_assoc();				
			$calcul_note = executeRequete("SELECT ROUND(AVG(note)*2)/2 AS moyenne FROM avis WHERE id_article = '$similars[id_article]'");
			$note_final = $calcul_note->fetch_assoc();
		  echo '<div class="articleNote">';
			echo'<div class="note">';
				imageNotation($note_final['moyenne']);
			echo'</div>';
			if($similars['id_promo'] == 1){
				echo'<div class="fond_prixSimilar">';				
						echo"<p>$similars[prix] €</p>";
				echo '</div>';						
			}
			else{
				echo'<div class="fond_prixPromoSimilar">';
					echo"<p><span class='old_price'>$similars[prix]€</span> ". prixAvecPromo($similars['id_article']) . "€</p>";
				echo '</div>';						
			}
					echo "<a href='fiche_article.php?id_article=$similars[id_article]&categorie=$similars[categorie]'><img src='$similars[photo]' alt='$similars[titre]' class='img_article'></a>";
					echo"<h3>$similars[titre]</h3>";
			if(utilisateurEstConnecte())
				{					
					echo "<form method='post' action='" . $url ."'>";
						echo "<input type='hidden' name='id_article' value='$similars[id_article]'>";
						echo "<input type='hidden' name='quantite' value='1'>";
						echo '<input type="submit" name="ajout_panier" value="" class="bouton btn_ajoutPanierFA btn_connect" data-tooltip="Ajouter au panier">';
					echo '</form>';
					echo "<a href='fiche_article.php?id_article=$similars[id_article]&categorie=$similars[categorie]' class='bouton btn_ficheDetails'></a>";	
					echo "<form method='post' action='wishlist.php'>";
						echo "<input type='hidden' name='id_article' value='$similars[id_article]'>";
						echo '<input type="submit" name="ajout_wishlist" value="" class="bouton btn_wishlist">';
					echo '</form>';
				}
			else
				{
					echo "<form method='post' action='" . $url ."'>";
						echo "<input type='hidden' name='id_article' value='$similars[id_article]'>";
						echo "<input type='hidden' name='quantite' value='1'>";
						echo '<input type="submit" name="ajout_panier" value="" class="bouton btn_ajoutPanierFA btn_disconnect" data-tooltip="Ajouter au panier">';
					echo '</form>';
					echo "<a href='fiche_article.php?id_article=$similars[id_article]&categorie=$similars[categorie]' class='bouton btn_ficheDetails' data-tooltip='Voir la fiche du produit'></a>";					
				}
		  echo '</div>';
		} 
    }	
	echo"</div>";
echo"</div>";

?>

<!-- <div id="content_services">
	<div id="Nos_services">
		<p class="titre_sP">Nos services</p>
		<div class="infos_service">
			<div class="">Paiement sécurisé</div>
		</div>
		<div class="infos_service">
			<div class="">Livraison gratuite</div>
		</div>
		<div class="infos_service">
			<div class="">Suivi de commande</div>
		</div>
		<div class="infos_service">
			<div class="">Satisfait ou remboursé</div>
		</div>
	</div>
	<div id="Need_help">
		<p class="titre_sP">Besoin d'aide ?</p>
		<p>Nous sommes joignable du Lundi au Samedi de 8H à 22H pour répondre à toutes vos questions.</p>
		<h3>0809 101 112**</h3>
		<p>**0.34cts/min + surcoût éventuel de votre opérateur</p>
		<div id="line"></div>
		<p> > Nous sommes injoignable ?</p>
		</p>Contacter-nous par <a class="a_Inline" href="<?php RACINE_SITE ?>contact.php" >mail</a></p>
	</dv>
</div> -->

<?php
require_once('inc/footer.inc.php');







