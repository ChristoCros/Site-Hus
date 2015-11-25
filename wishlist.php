<?php

require_once('inc/init.inc.php');
require_once('inc/haut_de_site.inc.php');

if(isset($_POST['ajout_wishlist']))
{	
	$membreSession = $_SESSION['utilisateur']['id_membre'];
	$articleAjouterWishlist = $_POST['id_article'];
	
	$searchInBddId = executeRequete("SELECT id_article FROM favoris WHERE id_article = $articleAjouterWishlist AND id_membre = $membreSession ");
	$nbLineFound = $searchInBddId->num_rows;
	
	if($nbLineFound == 0 ){		
		$ajoutWishlistSecond = executeRequete("INSERT INTO favoris (id_membre,id_article) VALUES ('$membreSession','$articleAjouterWishlist')");
		
		header('location: wishlist.php');
		exit;
	}
}   

// Appel article BDD :

$takeBddWishlist = executeRequete("SELECT id_favoris, f.id_article, a.id_article, id_membre, photo, titre, prix FROM favoris f LEFT JOIN article a ON f.id_article = a.id_article WHERE id_membre = '" . $_SESSION['utilisateur']['id_membre'] ."'" );

$nbLineWishlist = $takeBddWishlist->num_rows;


#----------RETIRER ARTICLE WISHLIST----------

if(isset($_GET['action']) && $_GET['action'] == 'retirer')
{
	executeRequete("DELETE FROM favoris WHERE id_article=$_GET[id_article] AND id_membre = '" . $_SESSION['utilisateur']['id_membre'] . " ' ");
	header("location:wishlist.php");
	exit();
} 


echo '<section>';

//------AFFICHAGE WISHLIST----------------

echo"<h2>Vos articles favoris</h2>";

echo "<table id='tab_wishlist' border='1' style='border-collapse:collapse' cellpadding='4'>";

echo "<tr><th>Photo</th><th>Article</th><th>Prix</th><th>Action</th></tr>";

//Condition : si la liste est vide : 

  if($nbLineWishlist < 1)
  {
      echo '<tr><td colspan="4">Votre liste est vide.<a href="'. RACINE_SITE . 'shop.php">Aller faire un tour sur notre site !</a></td></tr>';
  }
  else
  {
	$i = 0; 
    while($favoris = $takeBddWishlist->fetch_assoc())
    {
	  $css_class = ($i % 2 == 0) ? 'clair' : 'sombre';		
	  echo '<tr class="'.$css_class.'">'; 
		  echo "<td><img class='img_articleWishlist' src='" . $favoris['photo'] . "' alt='". $favoris['titre'] . "' title='". $favoris['titre'] . "' width='100'></td>";	 
		  echo "<td>" . $favoris['titre'] . "</td>";
		  echo "<td>" . $favoris['prix'] . " €</td>";
		  echo "<td><a href='?action=retirer&id_article=" . $favoris['id_article'] . "'><img src='". RACINE_SITE . "photo/suppr.png' alt='' data-tooltip='Retirer de mes favoris'></a></td>";
      echo "</tr>";
	  $i++;
    }
  }
echo "</table>";

require_once('inc/footer.inc.php');





