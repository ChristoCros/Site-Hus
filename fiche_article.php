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

// Ajout d'un commentaire :

if(isset($_POST['envoyer'])){
if( $_POST['note'] <= 0 || $_POST['note'] > 5 || empty($_POST['sujet']) || empty($_POST['commentaire'])){
		$msg .= '<p id="erreur">Veuillez remplir tous les champs et choisir une note valide.</p>';
	}
	else
	{
	$sujet = $mysqli->real_escape_string($_POST['sujet']);
	$commentaire = $mysqli->real_escape_string($_POST['commentaire']);
	$note = $_POST['note'];
    executeRequete("INSERT INTO avis (id_membre,id_article,sujet,commentaire,note,date) VALUES ('". $_SESSION['utilisateur']['id_membre']. "','$_GET[id_article]','$sujet','$commentaire','$note',NOW())");
	header('location:' . $url);
	}
}

if(isset($_GET['id_article']))
{
	$resultat = informationSurUnArticle($_GET['id_article']);
}

if(isset($_GET['id_article']))
{
	$resultat2 = informationSurUnePromotion($_GET['id_article']);
}

if($resultat->num_rows <= 0)
{
  header("location:shop.php");
  exit();
}

$article = $resultat->fetch_assoc();
$promo = $resultat2->fetch_assoc();

$donnees = executeRequete("SELECT note FROM article ar LEFT JOIN avis av ON ar.id_article = av.id_article AND ar.id_article = '$article[id_article]'");
$recupNote = $donnees->fetch_assoc();

$donnees2 = executeRequete("SELECT ar.id_article, titre, photo, prix, note, categorie,sous_categorie, stock, id_promo FROM article ar LEFT JOIN avis av ON ar.id_article = av.id_article WHERE ar.id_article != '$_GET[id_article]' AND sous_categorie = '$article[sous_categorie]' GROUP BY ar.id_article LIMIT 4");

$calcul_note = executeRequete("SELECT ROUND(AVG(note)*2)/2  AS moyenne FROM avis WHERE id_article = $_GET[id_article]");
$note_final = $calcul_note->fetch_assoc();

$historique_message = executeRequete("SELECT * FROM avis WHERE id_article= '$_GET[id_article]'");

// Redirection du formulaire :

$pageArticleEnCours = $_GET['id_article'];
$pageCategorieEnCours = $_GET['categorie'];

// echo $pageArticleEnCours;
// echo $pageCategorieEnCours;

echo "<section>";

echo "<div id='conteneur'>";

// Header dynamique en fonction de la catégorie:	
	echo"<div id='header_categorie'><img src=' ". RACINE_SITE . "photo/$_GET[categorie].jpg' alt='$_GET[categorie]' title='Bandeau catégorie $_GET[categorie]' /></div>";

// Fil d'ariane :	
	echo "<p id='filAriane'><a href='index.php'>Home </a>&gt; <a href='". RACINE_SITE . "categories.php?categorie=$_GET[categorie]'>$_GET[categorie]</a> &gt; <span id='filAriane_article'>$article[titre]</span></p>";

// Div présentation article:
	echo"<div id='presentation_article'>";
		echo"<div id='div_photo'>";
			echo"<img src='$article[photo]' alt='' height='300' id='image_article'/>";
		echo"</div>";
			echo"<div id='description_article'>";
				echo"<h2 id='title_article'>$article[titre]</h2>";
				echo"<p id='text_brand'>by <a href='". RACINE_SITE . "brands.php?marque=$article[marque]' id='span_brand'>$article[marque]</a><p>";
				imageNotation($note_final['moyenne']);
				echo"<p id='text_description'>$article[description]</p>";
			echo"</div>";
			echo"<div id='verticale_barre'></div>";
			echo"<div id='achat_article'>";
				if($article['id_promo'] == 1){			
						echo"<span id='prix'>$article[prix] €</span>";				
				}
				else{
						echo"<span id='ancientPrix'>$article[prix] €</span>";						
						echo"<span id='prix'>". prixAvecPromo($article['id_article']) . "€</span>";			
				}
				echo"<div id='horizontale_barre'></div>";
				echo"<span id='share_it'>Partager sur :</span>";				
				echo"<div id='social_barre'>";
					echo"<a href='' id='facebook'></a>";
					echo"<a href='' id='pinterest'></a>";
					echo"<a href='' id='twitter'></a>";
				echo"</div>";
				if(utilisateurEstConnecte()){
					echo"<div id='nombre_wishlist'>";
						echo "<form method='post' action='wishlist.php'>";
							echo "<input type='hidden' name='id_article' value='$article[id_article]'>";
							echo "<input type='hidden' name='id_promo' value='$promo[id_promo]'>";
						echo '<input type="submit" name="ajout_wishlist" value="" class="bouton btn_wishlist" data-tooltip="Ajouter au favoris" >';
						echo '</form>';	
					echo"</div>";
					echo"<div id='add_basket'>";
						echo'<form method="post" action="' . $url .'" onsubmit="return Objet.pasNegatif();">';
						echo "<input type='hidden' name='id_article' value='$article[id_article]'>";
							echo "<input type='hidden' name='id_promo' value='$promo[id_promo]'>";
							echo "<input type='number' name='quantite' value='1' min='1' max='30' id='quantite'>";
							echo "<span onclick='Objet.plus();' class='plus'>+</span>";
							echo "<span onclick='Objet.moins();' class='moins'>-</span><br>";
							echo '<input type="submit" name="ajout_panier" id="add_basket_btn" value="" class="btn_ajoutPanierFA bouton" data-tooltip="Ajouter au panier">';
							echo '</form>';
					echo"</div>";
				}
				else{
					echo"<div id='add_basket'>";
						echo'<form method="post" action="' . $url .'" onsubmit="return Objet.pasNegatif();">';
						echo "<input type='hidden' name='id_article' value='$article[id_article]'>";
							echo "<input type='hidden' name='id_promo' value='$promo[id_promo]'>";
							echo "<input type='number' name='quantite' value='1' min='1' max='30' id='quantite'>";
							echo "<span onclick='Objet.plus();' class='plus plus_disc'>+</span>";
							echo "<span onclick='Objet.moins();' class='moins moins_disc'>-</span><br>";
							echo '<input type="submit" name="ajout_panier" id="add_basket_btn" value="" class="btn_ajoutPanierFA bouton" data-tooltip="Ajouter au panier">';
							echo '</form>';
					echo"</div>";					
				}	
			echo"</div>";		
	echo "</div>";
	
// Div détails article:
	echo"<div id='details_article'>";
		echo"<div id='menu_vertical'>";
			echo"<ul>";
				echo"<li onclick='Objet.a();' id='details_Btn' class='menuLi active'>Détails</li>";
				// echo"<li onclick='Objet.b();' id='video_Btn' class='menuLi inactive'>Vidéo</li>";
				echo"<li onclick='Objet.c();' id='avis_Btn' class='menuLi inactive'>Avis (" . $historique_message->num_rows .")</li>";
			echo"</ul>";
		echo"</div>";
		echo"<div id='contenu'>";
			echo"<div id='contenu_details'>";
				echo"<div class='clair'>";
					echo"<p><span>Marque </span>$article[marque]</p>";
				echo"</div>";
				echo"<div class='sombre'>";
					echo"<p><span>Référence </span>$article[reference]</p>";
				echo"</div>";
				echo"<div class='clair'>";
					echo"<p><span>Matériau </span>$article[materiau]</p>";
				echo"</div>";
				echo"<div class='sombre'>";
					echo"<p><span>Coloris </span>$article[coloris]</p>";
				echo"</div>";
				echo"<div class='clair'>";
					echo"<p><span>Dimensions(Lxlxh) </span>$article[dimensions]</p>";
				echo"</div>";
				echo"<div class='sombre'>";
					echo"<p><span>Poids </span>$article[poids] kg</p>";				
				echo"</div>";
				echo"<div class='clair'>";
					echo"<p><span>Fabrication </span>$article[fabrication]</p>";
				echo"</div>";
				echo"<div class='sombre'>";
					if($article['garantie'] == 1){
						echo"<p><span>Garantie </span>$article[garantie] an</p>";
					}
					else{
						echo"<p><span>Garantie </span>$article[garantie] ans</p>";						
					}
				echo"</div>";
				echo"<div class='clair'>";
					if($article['stock'] > 10){
						echo"<p><span>Disponible </span><span id='enstock'>En stock</span><span class='livraison'> (Livré chez vous en 1 semaine)</span></p>";
					}
					if($article['stock'] >= 1 && $article['stock'] <= 10){
						echo"<p><span>Disponible </span><span id='nbstock'>$article[stock] articles restants</span><span class='livraison'> (Livré chez vous en 1 semaine)</span></p>";
					}
					if($article['stock'] == 0){
						echo"<p><span>Disponible </span><span id='rupturestock'>Rupture de stock</span class='livraison'> (Livré chez vous sous 2 à 3 semaines)</p>";						
					}
				echo"</div>";				
			echo"</div>";
			
//DIV vidéo:			
			// echo"<div id='contenu_video'>";
			?>
			<!--	<iframe src="https://player.vimeo.com/video/24448358?color=ffffff&title=0&byline=0&portrait=0" width="600" height="334" frameborder="0"></iframe> -->
			<?php	
			// echo"</div>";

//Div commentaires:			
			echo"<div id='contenu_avis'>";			
if(utilisateurEstConnecte())
{
$limitationCommentaire = executeRequete("SELECT commentaire FROM avis WHERE id_membre=" .$_SESSION['utilisateur']['id_membre'] . " AND id_article=" . $article['id_article'] . " ");
	

	if($limitationCommentaire->num_rows > 0){

	}
	else{
		echo '<div class="all_comments">';
		echo'<h2>Laissez un commentaire :</h2>';
		echo '<form method="POST" action="" class="form_comment">';
		echo "<input type='hidden' name='id_article' value='$article[id_article]'>";
		echo "<input type='hidden' name='note' id='note' value='0'>";
		echo '<input type="text" name="sujet" id="sujet" placeholder="Sujet du commentaire"><br >';
		echo '<textarea cols="40" rows="5" name="commentaire" id="message" placeholder="Votre message" ></textarea><br >';
		echo'<div class="rating">';
		   echo'<a href="#5" onclick="Objet.note5();">H</a>';
		   echo'<a href="#4" onclick="Objet.note4();">H</a>';
		   echo'<a href="#3" onclick="Objet.note3();">H</a>';
		   echo'<a href="#2" onclick="Objet.note2();">H</a>';
		   echo'<a href="#1" onclick="Objet.note1();">H</a>';
		echo'</div>';			
		echo '<input type="submit" name="envoyer" value="Envoyer" id="send" class="bouton">';
		echo $msg;
		echo '</form>';
		echo '</div>';		
	}
}
else
{	
	echo '<div id="seConnecter">';
		echo '<div class="all_comments">';	
			echo '<p id="phrase_connexion">Connectez-vous ou inscrivez-vous pour pouvoir ajouter un commentaire à cet article.</p>';
			echo '<a href="'. RACINE_SITE . 'connexion.php">Se connecter</a>' . ' | '. '<a href="'. RACINE_SITE . 'inscription.php">S\'inscrire</a>';
		echo '</div>';
	echo '</div>';
}

if($historique_message->num_rows == 0){
 
}
else{	
	echo '<div class="all_comments">';
	while ($ligne = $historique_message->fetch_assoc())
		{  
			$membre = executeRequete("SELECT pseudo FROM membre WHERE id_membre=' ". $ligne['id_membre']. " ' ");
			$nb_commMembre = executeRequete("SELECT id_membre FROM avis WHERE id_membre=' ". $ligne['id_membre']. " ' ");
			$pseudo = $membre->fetch_assoc();
			$affichage_commentaire = htmlspecialchars(stripslashes($ligne['commentaire'])); // Permet de rendre le HTML inoffensif => htmlspecialchars
			$affichage_sujet = htmlspecialchars(stripslashes($ligne['sujet']));		
			$affichage_note = htmlspecialchars(stripslashes($ligne['note'])); // Stripslashes = enlève les slash ou antislash ajouté par le $mysqli->escape_string pour se protéger et empêcher les erreurs dû au ' ou " et rend tout ça lisible correctement.
			
			echo "<div class='comm'>";
				echo"<div class='infos_comm'>";
					imageNotation($affichage_note);					
					echo "<p class='pseudo'>$pseudo[pseudo]</p>";
					echo "<p class='date_comm_fp'>" . dateLongue($ligne['date'], 'no') . "</p>";
				echo"</div>";
				echo"<div class='affichage_comm'>";				
					echo "<p class='text_sujet'>$affichage_sujet</p>";
					echo "<p class='text_comm'>$affichage_commentaire</p>";
				echo"</div>";
			echo "</div>";
		}
	echo '</div>';
}			
			echo"</div>";	
		echo"</div>";
	echo "</div>";

// Div produits similaires:
if($donnees2->num_rows >= 1){
	echo"<div id='similar_products'>";
		echo"<p class='titre_sP'>Produits similaires</p>";
		
		while($similars = $donnees2->fetch_assoc())
		{
			$calcul_note = executeRequete("SELECT ROUND(AVG(note)*2)/2 AS moyenne FROM avis WHERE id_article = $similars[id_article]");
			$note_final = $calcul_note->fetch_assoc();		
			echo '<div class="articleSimilar">';
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
					echo "<form method='post' action='" . $url . "'>";
						echo "<input type='hidden' name='id_article' value='$similars[id_article]'>";
						echo "<input type='hidden' name='quantite' value='1'>";
						echo '<input type="submit" name="ajout_panier" value="" class="bouton btn_ajoutPanierFA btn_connect" data-tooltip="Ajouter au panier">';
					echo '</form>';
					echo "<a href='fiche_article.php?id_article=$similars[id_article]&categorie=$similars[categorie]' class='bouton btn_ficheDetails' data-tooltip='Voir la fiche du produit'></a>";	
					echo "<form method='post' action='wishlist.php'>";
						echo "<input type='hidden' name='id_article' value='$similars[id_article]'>";
						echo '<input type="submit" name="ajout_wishlist" value="" class="bouton btn_wishlist" data-tooltip="Ajouter au favoris">';
					echo '</form>';
				}
			else
				{
					echo "<form method='post' action='" . $url . "'>";
						echo "<input type='hidden' name='id_article' value='$similars[id_article]'>";
						echo "<input type='hidden' name='quantite' value='1'>";
						echo '<input type="submit" name="ajout_panier" value="" class="bouton btn_ajoutPanierFA btn_disconnect" data-tooltip="Ajouter au panier">';
					echo '</form>';
					echo "<a href='fiche_article.php?id_article=$similars[id_article]&categorie=$similars[categorie]' class='bouton btn_ficheDetails' data-tooltip='Voir la fiche du produit'></a>";					
				}
			echo '</div>';
		}	
		echo"</div>";
	echo"</div>";
}

?>
	
<script type="text/javascript">

var Objet = {
	
	a : function(){
		var switchBtn = document.getElementById("details_Btn").className = "menuLi active";
		// var switchBtn2 = document.getElementById("video_Btn").className = "menuLi inactive";
		var switchBtn3 = document.getElementById("avis_Btn").className = "menuLi inactive";
		var switchMajDiv1 = document.getElementById("contenu_details").style.display = "block";
		// var switchMajDiv2 = document.getElementById("contenu_video").style.display = "none";
		var switchMajDiv3 = document.getElementById("contenu_avis").style.display = "none";
		var resetHeight = document.getElementById("menu_vertical").style.height = "410px";		
		var resetHeight2 = document.getElementById("details_article").style.height = "410px";	
	},
	// b : function(){
		// var switchBtn = document.getElementById("details_Btn").className = "menuLi inactive";
		// var switchBtn2 = document.getElementById("video_Btn").className = "menuLi active";
		// var switchBtn3 = document.getElementById("avis_Btn").className = "menuLi inactive";
		// var switchMajDiv1 = document.getElementById("contenu_details").style.display = "none";
		// var switchMajDiv2 = document.getElementById("contenu_video").style.display = "block";
		// var switchMajDiv3 = document.getElementById("contenu_avis").style.display = "none";
		// var resetHeight = document.getElementById("menu_vertical").style.height = "370px";		
		// var resetHeight2 = document.getElementById("details_article").style.height = "370px";		
	// },
	c : function(){
		var switchBtn = document.getElementById("details_Btn").className = "menuLi inactive";
		// var switchBtn2 = document.getElementById("video_Btn").className = "menuLi inactive";
		var switchBtn3 = document.getElementById("avis_Btn").className = "menuLi active";
		var switchMajDiv1 = document.getElementById("contenu_details").style.display = "none";
		// var switchMajDiv2 = document.getElementById("contenu_video").style.display = "none";
		var switchMajDiv3 = document.getElementById("contenu_avis").style.display = "block";
		var recupereHauteur = document.getElementById("details_article").offsetHeight;
		var attributionHauteur = document.getElementById("menu_vertical").style.height = recupereHauteur+"px";
	},
	note1 : function(){
		var noteUn = document.getElementById('note').value = 1;
	},
	note2 : function(){
		var noteDeux = document.getElementById('note').value = 2;
	},
	note3 : function(){
		var noteTrois = document.getElementById('note').value = 3;
	},
	note4 : function(){
		var noteQuatre = document.getElementById('note').value = 4;
	},
	note5 : function(){
		var noteCinq = document.getElementById('note').value = 5;
	},
	plus : function(){
		var ajoutQuantite = document.getElementById('quantite');
		ajoutQuantite.value = parseInt(ajoutQuantite.value)+1;
	},
	moins : function(){
		var soustraireQuantite = document.getElementById('quantite');
		soustraireQuantite.value = parseInt(soustraireQuantite.value)-1;
		// if(parseInt(document.getElementById('quantite')) < 1){
			// var nePlusSoustraire = document.getElementById('moins').onclick= '';
		// }
		// if(parseInt(document.getElementById('quantite')) > 0){
			// var nePlusSoustraire = document.getElementById('moins').onclick= 'Objet.moins();';			
		// }
	},
	pasNegatif : function(){
	  if(parseInt(document.getElementById('quantite').value) > 0)
		  return true;
	   else
	   {
		  alert("Quantité nulle ou négative impossible.");
		  return false;
	   }
	}
}	
		
</script>


<?php	
	
// echo '</div>';

require_once('inc/footer.inc.php');




