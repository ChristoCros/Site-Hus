<?php
require_once("../inc/init.inc.php");
require_once('../inc/haut_de_site.inc.php');
//ici c'est le BACKOFFICE : donc restreindre l'accès à cette partie. Uniquement visible pour l'administrateur : 
if(!utilisateurEstConnecteEtAdmin())
{
	header("location:../connexion.php"); //redirection pour tous les membres qui ne sont pas administrateurs (donc tous les autres connectés dont la statut est égal à 0 mais aussi les simples visiteurs !!). Seuls les membres ayant un statut == 1 ont accès à cette page. Cf. BDD
	exit(); //permet de stopper l'éxécution du script
}
if(utilisateurEstConnecteEtAdmin() && isset($_GET['action']) && $_GET['action'] == 'deconnexion'){
		// session_destroy();
		header("location:" . RACINE_SITE . "connexion.php");
		exit();
}

$url = $_SERVER['REQUEST_URI'];

####################################################
#ENREGISTREMENT : AJOUT OU MODIFICATION ARTICLE
####################################################

echo '<section>';

//1- on teste le bouton submit : 
if(isset($_POST['enregistrement']))
{
  //echo 'test'; //ok !
  //debug($_POST);
	$id_article = executeRequete("SELECT id_article FROM article WHERE id_article='$_POST[id_article]'");
	if($id_article->num_rows != 0 && isset($_GET['action']) && $_GET['action']=='ajout') //double vérif !!
  {
    $msg .= '<div class="erreur">La référence est déjà attribuée. Veuillez vérifier votre saisie.</div>'; 
  }
  else {
  //je crée ici une variable vide pour éviter une erreur si l'administrateur ne poste pas de photo : 
	$photo_bdd = ""; 

  //ICI ON S'OCCUPE DE L'UPLOAD DE LA PHOTO : 

		if(isset($_GET['action']) && $_GET['action'] == 'modification')
		{ 
    //en cas de modification, nous récupérons la photo déjà uploadée : 
			$photo_bdd = $_POST['photo_actuelle'];//je place ma photo dans ma variable vide !

		}
		if(!empty($_FILES['photo']['name'])) //s'il y a bien une photo !! c'est-à-dire inverse de vide (!empty)
		{	
			if(verificationExtensionPhoto()) //je vérifie l'extension de la photo : est-ce que l'extension est en minuscule ? si oui : 
			{
				$nom_photo = $_POST['id_article'] . '_' .$_FILES['photo']['name']; //on renomme la photo en nous récupérant la référence UNIQUE de notre article
				$photo_bdd = RACINE_SITE . "photo/$nom_photo"; //ici on pointe le chemin où la photo sera enregistrer
				$photo_dossier = RACINE_SERVEUR . RACINE_SITE . "/photo/$nom_photo";  //on récupère le chemin de la photo placée dans le dossier temporaire
				copy($_FILES['photo']['tmp_name'],$photo_dossier); //on copie la photo du dossier temporaire (=> $_FILES['photo']['tmp_name']) dans le dossier de réception (=> $photo_dossier) 
        //cf. http://php.net/manual/fr/function.copy.php
			}
			else
			{
				$msg .= "<div class='erreur'>L'extension n'est pas valide</div>";
			}
		}
		if(empty($msg)) //si aucun message d'erreurs n'a été généré on passe directement à cette condition : 
		{
			$msg .= '<div class="validation">Enregistrement de l\'article effectué.</div>';
			
			$description = $mysqli->real_escape_string($_POST['description']);
			
			if($_GET['action'] == 'modification'){
				executeRequete("REPLACE INTO article (id_article,id_promo,titre,categorie,sous_categorie,description,photo,marque,reference,materiau,coloris,dimensions,poids,fabrication,garantie,prix,stock,date) VALUES ('$_POST[id_article]', '$_POST[id_promo]', '$_POST[titre]', '$_POST[categorie]', '$_POST[sous_categorie]', '$description','$photo_bdd', '$_POST[marque]', '$_POST[reference]', '$_POST[materiau]',  '$_POST[coloris]',  '$_POST[dimensions]',  '$_POST[poids]',  '$_POST[fabrication]',  '$_POST[garantie]',  '$_POST[prix]',  '$_POST[stock]', '$_POST[date]')");
			}
			if($_GET['action'] == 'ajout'){
				executeRequete("INSERT INTO article (id_article,id_promo,titre,categorie,sous_categorie,description,photo,marque,reference,materiau,coloris,dimensions,poids,fabrication,garantie,prix,stock,date) VALUES ('$_POST[id_article]', '$_POST[id_promo]', '$_POST[titre]', '$_POST[categorie]', '$_POST[sous_categorie]', '$description','$photo_bdd', '$_POST[marque]', '$_POST[reference]', '$_POST[materiau]',  '$_POST[coloris]',  '$_POST[dimensions]',  '$_POST[poids]',  '$_POST[fabrication]',  '$_POST[garantie]',  '$_POST[prix]',  '$_POST[stock]',  NOW())");
			}
				
			$_GET['action'] = 'affichage';
		}
	}
}

if(isset($_GET['action']) && $_GET['action'] == "suppression")
{

	$resultat = informationSurUnArticle($_GET['id_article']);
	$article_a_supprimer = $resultat->fetch_assoc();
	$chemin_photo_a_supprimer = RACINE_SERVEUR . $article_a_supprimer['photo'];
	if(!empty($article_a_supprimer['photo']) && file_exists($chemin_photo_a_supprimer))
	{
		unlink($chemin_photo_a_supprimer);
    //cf. http://php.net/manual/fr/function.unlink.php
	}
	echo "<div class='validation'>Suppression de l'article : $_GET[id_article] </div>";
	executeRequete("DELETE FROM article WHERE id_article = '$_GET[id_article]'");
	$_GET['action'] = 'affichage';

	header('location: gestion_articles.php?action=affichage');
	
}

	echo'<h1> Gestion des articles</h1>';

echo $msg; 

if($_GET['action'] == 'modification'){
	echo '<a id="btn_modifArticle" href="?action=affichage" class="design6_submitBtn leftButton">Retourner aux articles</a>';	
}
if($_GET['action'] == 'affichage'){
	echo '<a id="btn_ajoutArticle" href="?action=ajout" class="design6_submitBtn ">Ajouter un article</a><br><br>';
}

if(isset($_GET['action']) && $_GET['action'] == "affichage")
{
	echo '<div>';
	$resultat = executeRequete("SELECT * FROM article");

echo "<div class='encart_gestion'>";
	echo "<p class='infos_gestionSolo'>Nombre d'article(s) : <span class='num_infosGestion'>" . $resultat->num_rows . '</span></p>';
echo "</div>";

	$nbcol = $resultat->field_count;
	
	$i = 0;
	while ($ligne = $resultat->fetch_assoc())
  {
	$css_class = ($i % 2 == 0) ? 'clair' : 'sombre';
		echo '<div class="admin_article">';
		
		echo "<div class='id_article'><p>$ligne[id_article]</p><p class='date_article'>Créé le : " . dateLongue($ligne['date'], 'no') . "</p></div>";
		
		echo"<div class='article_left'>";		
			echo "<div class='ref'><p>$ligne[reference]</p></div>";		
			echo "<div class='admin_img'><img src='" . $ligne['photo'] . "' width='170' /></div>";		
			echo "<div class='action_article'>";		
				echo '<a class="btn_modif" href="?action=modification&id_article=' . $ligne['id_article'] .'"><img src="'. RACINE_SITE . 'photo/modif.png" alt="" title="Modifier la fiche de l\'article"></a>';
				echo '<a class="btn_suppr" href="?action=suppression&id_article=' . $ligne['id_article'] .'" OnClick="return(confirm(\'En êtes vous certain ?\'));"><img src="'. RACINE_SITE . 'photo/suppr.png" alt="" title="Supprimer ce commentaire"></a>';
			echo"</div>";
		echo"</div>";
			
		echo "<div class='separateur1'></div>";
		
		echo"<div class='content_infos'>";
			echo "<h3>$ligne[titre]</h3>";
			echo "<p>by $ligne[marque]</p>";
			echo "<p class='description_admin'>$ligne[description]</p>";
			echo "<div class='sombre2'><p><span>Catégorie</span>$ligne[categorie]</p></div>";
			echo "<div class='clair2'><p><span>Sous-catégorie</span>$ligne[sous_categorie]</p></div>";			
			echo "<div class='sombre2'><p><span>Matériaux</span>$ligne[materiau]</p></div>";
			echo "<div class='clair2'><p><span>Coloris</span>$ligne[coloris]</p></div>";
			echo "<div class='sombre2'><p><span>Dimensions</span>$ligne[dimensions]</p></div>";
			echo "<div class='clair2'><p><span>Poids</span>$ligne[poids] kg</p></div>";
		echo"</div>";

		echo "<div class='separateur1'></div>";
		
		echo "<div id='infosArticle_right'>";
			echo "<div class=''>";
				echo "<p class='infosArticle_design1'>Stock :</p>";
				echo "<p class='infosArticle_design2'>$ligne[stock]</p>";
			echo"</div>";
			echo"<div class='separateur_hori'></div>";			
			echo "<div class=''>";
				echo "<p class='infosArticle_design1'>Prix unitaire :</p>";
				echo "<p class='infosArticle_design2'>$ligne[prix] €</p>";
			echo"</div>";			
			echo"<div class='separateur_hori'></div>";
			echo "<div class=''>";
				echo "<p class='infosArticle_design1'>Prix avec promo :</p>";
				if($ligne['prix'] == prixAvecPromo($ligne['id_article']) ){
					echo "<p class='infosArticle_design2'>--</p>";
				}
				else{
					echo "<p class='infosArticle_design2'>". prixAvecPromo($ligne['id_article']) . " €</p>";
				}
			echo"</div>";
			echo"<div class='separateur_hori'></div>";			
			echo "<div class=''>";
				echo "<p class='infosArticle_design1'>Fabrication :</p>";
				echo "<p class='infosArticle_design3'>$ligne[fabrication]</p>";
			echo"</div>";
			echo"<div class='separateur_hori'></div>";			
			echo "<div class=''>";
				echo "<p class='infosArticle_design1'>Durée de garantie :</p>";
				if($ligne['garantie'] == 1){
					echo "<p class='infosArticle_design2'>$ligne[garantie] an</p>";
				}
				else{
					echo "<p class='infosArticle_design2'>$ligne[garantie] ans</p>";					
				}
			echo"</div>";		
		echo "</div>";
		
		$i++;
		echo '</div>';
	}
	echo "</div>";
}


if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) 
{
		if(isset($_GET['id_article'])) 
		{
			$resultat = informationSurUnArticle($_GET['id_article']); 
			$article_actuel = $resultat->fetch_assoc();
		}
	?>
	<div id="contentModif_article">
		<div id="modif_article" style="padding: 10px;">
			<form method="post" enctype="multipart/form-data" action="<?php $url ?>">

			<label for="photo" class="gras">Photo</label>
			<input type="file" id="photo" name="photo" class="design_file"/><br />				
<?php
			if(isset($article_actuel))
			{
				echo "<label for='photo_actuelle' class='gras' >Photo actuelle : </label><img src=\"$article_actuel[photo]\"  height=\"140\" /><br />";
				echo "<input type=\"hidden\" name=\"photo_actuelle\" value=\"$article_actuel[photo]\" /><br />";
			}
?>

			<input type="hidden" id="id_article" name="id_article" value="<?php if(isset($article_actuel['id_article'])) echo $article_actuel['id_article']; ?>" class="design_input"/>
			<input type="hidden" id="date" name="date" value="<?php if(isset($article_actuel['date'])) echo $article_actuel['date']; ?>" class="design_input"/>
			<br><br>
			<label for="id_article" class="gras">Id_article</label>
				<input type="text" id="id_article" name="id_article" value="<?php if(isset($article_actuel['id_article'])) echo $article_actuel['id_article']; elseif(isset($_POST['id_article'])) echo $_POST['id_article']; ?>" <?php if(isset($article_actuel)) echo 'readonly'; ?> class="design_input"  placeholder="Ex: vide = AI, ou 1, 2, 3, ..."/> <br />

			<label for="id_promo" class="gras">Id_promo</label>
				<input type="text" id="id_promo" name="id_promo" value="<?php if(isset($article_actuel['id_promo'])) echo $article_actuel['id_promo']; elseif(isset($_POST['id_promo'])) echo $_POST['id_promo']; ?>" <?php if(isset($article_actuel)) ?> class="design_input"  placeholder="Ex: 1, 2, 3, ..."/> <br />				

			<label for="titre" class="gras">Titre</label>
				<input type="text" id="titre" name="titre" value="<?php if(isset($article_actuel['titre']))  echo $article_actuel['titre']; elseif(isset($_POST['titre'])) echo $_POST['titre']; ?>" class="design_input" placeholder="Ex: Bouchon Wouf (28 ch. max) "/><br />				

			<label for="categorie" class="gras">Catégorie</label>
				<input type="text" id="categorie" name="categorie" value="<?php if(isset($article_actuel['categorie'])) echo $article_actuel['categorie']; elseif(isset($_POST['categorie'])) echo $_POST['categorie'];  ?>" class="design_input" placeholder="Ex: Maison"/> <br /> <br />

			<label for="sous_categorie" class="gras">Sous-catégorie</label>
				<input type="text" id="sous_categorie" name="sous_categorie" placeholder="Ex: lampe, ventilateur, verre, ...." value="<?php if(isset($article_actuel['sous_categorie'])) echo $article_actuel['sous_categorie']; elseif(isset($_POST['sous_categorie'])) echo $_POST['sous_categorie'];  ?>" class="design_input"/> <br /> <br />
				
			<label for="description" class="gras">Description</label><br>
				<textarea rows="10" cols="2" name="description" id="description" class="design_textarea" placeholder="Description de l'article. Le plus synthétique possible."><?php if(isset($article_actuel['description'])) echo $article_actuel['description']; elseif(isset($_POST['description'])) echo $_POST['description']; ?></textarea><br />
				
			<label for="marque" class="gras">Marque</label>
				<input type="text" id="marque" name="marque" value="<?php if(isset($article_actuel['marque'])) echo $article_actuel['marque']; elseif(isset($_POST['marque'])) echo $_POST['marque'];  ?>" class="design_input" placeholder="Sans espace si possible."/><br/>

			<label for="reference" class="gras">Référence</label>
				<input type="text" id="reference" name="reference" value="<?php if(isset($article_actuel['reference'])) echo $article_actuel['reference']; elseif(isset($_POST['reference'])) echo $_POST['reference'];  ?>" class="design_input" placeholder="Ex: 125ABC456"/><br/>

			<label for="materiau" class="gras">Matériau</label>
				<input type="text" id="materiau" name="materiau" value="<?php if(isset($article_actuel['materiau'])) echo $article_actuel['materiau']; elseif(isset($_POST['materiau'])) echo $_POST['materiau'];  ?>" class="design_input" placeholder="Ex: Bois, aluminium, ..."/><br/>

			<label for="coloris" class="gras">Coloris</label>
				<input type="text" id="coloris" name="coloris" value="<?php if(isset($article_actuel['coloris'])) echo $article_actuel['coloris']; elseif(isset($_POST['coloris'])) echo $_POST['coloris'];  ?>" class="design_input" placeholder="Ex: Rouge, beige,..."/><br/>
				
			<label for="dimensions" class="gras">Dimensions</label>			
				<input type="text" id="dimensions" name="dimensions" value="<?php if(isset($article_actuel['dimensions'])) echo $article_actuel['dimensions']; elseif(isset($_POST['dimensions'])) echo $_POST['dimensions'];  ?>" class="design_input" placeholder="Longueur x largeur x hauteur en cm."/><br/>				
				
			<label for="poids" class="gras">Poids</label>			
				<input type="text" id="poids" name="poids" value="<?php if(isset($article_actuel['poids'])) echo $article_actuel['poids']; elseif(isset($_POST['poids'])) echo $_POST['poids'];  ?>" class="design_input" placeholder="Ex: 0.3, 1.2, ..."/><br/>

			<label for="fabrication" class="gras">Fabrication</label>			
				<input type="text" id="fabrication" name="fabrication" value="<?php if(isset($article_actuel['fabrication'])) echo $article_actuel['fabrication']; elseif(isset($_POST['fabrication'])) echo $_POST['fabrication'];  ?>" class="design_input" placeholder="Ex: Fabriqué en France"/><br/>

			<label for="garantie" class="gras">Garantie</label>			
				<input type="text" id="garantie" name="garantie" value="<?php if(isset($article_actuel['garantie'])) echo $article_actuel['garantie']; elseif(isset($_POST['garantie'])) echo $_POST['garantie'];  ?>" class="design_input" placeholder="Ex: 1, 2, 3, ..."/><br/>

			<label for="prix" class="gras">Prix</label>			
				<input type="text" id="prixAdmin" name="prix" value="<?php if(isset($article_actuel['prix'])) echo $article_actuel['prix']; elseif(isset($_POST['prix'])) echo $_POST['prix'];  ?>" class="design_input" placeholder="Ex: 45, 89, 250, ..."/><br/>

			<label for="stock" class="gras">Stock</label>			
				<input type="text" id="stock" name="stock" value="<?php if(isset($article_actuel['stock'])) echo $article_actuel['stock']; elseif(isset($_POST['stock'])) echo $_POST['stock'];  ?>" class="design_input" placeholder="Ex: 4500, 10000, 25000, ..."/><br/>

			<label for="date" class="gras">Date</label>			
				<input type="text" id="date" name="date" value="<?php if(isset($article_actuel['date'])) echo $article_actuel['date']; elseif(isset($_POST['date'])) echo $_POST['date'];  ?>" class="design_input" placeholder="Laisser vide ou AAAA-MM-DD"/><br/>				
				
			<input type="submit" name="enregistrement" value="<?php echo ucfirst($_GET['action']); ?>" class="design5_submitBtn"/> 
			</form>
		</div>
	</div>
			
	<?php
} 

?>
<script>

$(document).ready(function(){
	$('.erreur').delay(3000).fadeOut("slow" );
	$('.validation').delay(5000).fadeOut("slow" );
});

</script>

<?php

require_once('../inc/footer.inc.php');





