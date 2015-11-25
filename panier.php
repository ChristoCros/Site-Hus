<?php

require_once('inc/init.inc.php');

require_once('inc/haut_de_site.inc.php');

#---------- AJOUT AU PANIER ------------------

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

#----------DEBUT VIDER LE PANIER----------

if(isset($_GET['action']) && $_GET['action'] == 'vider')
{
  unset($_SESSION['panier']);
  
  header('location: panier.php');
  exit;
}

#----------DEBUT RETIRER ARTICLE DU PANIER----------

if(isset($_GET['action']) && $_GET['action'] == 'retirer')
{
	retirerArticleDuPanier($_GET['id_article']);
	header("location:panier.php");
	exit();
}    

#----------VERIFICATION CODE PROMO----------

if( isset($_POST['validation_codePromo'])){
	
	for($i = 0; $i < count($_SESSION['panier']['id_article']) ; $i++){
		$query = executeRequete("SELECT * FROM article ar, promotion prom WHERE prom.id_promo = ar.id_promo AND '$_POST[code_promoPanier]' = prom.code_promo AND '".$_SESSION['panier']['id_article'][$i]."' = ar.id_article");
	}
	
	$verif_promo = $query->fetch_assoc();
}
if(!empty($_POST['code_promoPanier']) && isset($_POST['validation_codePromo'])){
	$query_code = executeRequete("SELECT id_article, code_promo, ar.id_promo, reduction FROM promotion prom, article ar WHERE '$_POST[code_promoPanier]' = prom.code_promo AND ar.id_article IN (".implode(",", $_SESSION['panier']['id_article'] )." ) AND ar.id_promo = prom.id_promo");
	$resultat_queryCode = $query_code->fetch_assoc();
}

#----------PARTIE PAIEMENT DU PANIER----------


echo '<section>';

  if(isset($_POST['payer']) && $_POST['payer'])
  {
    //boucle qui tourne autant de fois qu'il y a d'articles différents dans le panier : 
    for($i = 0; $i < count($_SESSION['panier']['id_article']) ; $i++)
      //cf. count() équivalent du sizeof() => http://php.net/manual/fr/function.sizeof.php
    {
      $resultat = informationSurUnArticle($_SESSION['panier']['id_article'][$i]);
      
      $article = $resultat->fetch_assoc();
      
      #verification du stock : (on est toujours dans la boucle dont le but est de nous retourner le contenu du panier)
      if($article['stock'] < $_SESSION['panier']['quantite'][$i]) //si le stock actuel est strictement inférieur à la quantité que l'on souhaite commander...=>PROBLEME !!!
      {
        echo '<hr><div class="erreur">Stock restant : ' . $article['stock'] . '</div>';
        echo '<div class="erreur">Quantité demandée : ' .$_SESSION['panier']['quantite'][$i] . '</div>';
      
        if($article['stock'] > 0)
        {
          echo '<div class="erreur">la quantité de l\'article ' . $_SESSION['panier']['id_article'][$i] . ' a été réduite car notre stock était insuffisant. Veuillez vérifier vos achats</div>';
          $_SESSION['panier']['quantite'][$i] = $article['stock'];
        }
        else //rupture de stock : on retire carrément les articles du panier. 
        {
          echo '<div class="erreur">l\'article ' . $_SESSION['panier']['id_article'][$i] . ' a été retiré de votre panier car nous sommes en rupture de stock, veuilliez vérifier vos achats.</div>';
          retirerArticleDuPanier($_SESSION['panier']['id_article'][$i]); //on retire l'article. 
          $i--; //on décrémente pour retirer un article. Lorsque l'on souhaite rajouter une valeur à notre variable on incrémente, ici on souhaite enlever une valeur du coup on décrémente. 
        }
        $erreur = TRUE;
      }
    }
	  if(!isset($erreur)) //si $erreur = FALSE => on enregistre le panier. 
	  {
			
		executeRequete("INSERT INTO commande(id_membre,montant,date) VALUES (" . $_SESSION['utilisateur']['id_membre'] . "," . point(montantTotal()) . ", NOW())");
		
		$id_commande = $mysqli->insert_id;
		
		for($j = 0 ; $j < count($_SESSION['panier']['id_article']); $j++)
		{
		executeRequete("INSERT INTO details_commande (id_commande,id_article,quantite,prix) VALUES ('$id_commande', '". $_SESSION['panier']['id_article'][$j] . "','" . $_SESSION['panier']['quantite'][$j] . "','". $_SESSION['panier']['prix'][$j]. "')");
		  
		executeRequete("UPDATE article SET stock=stock-".$_SESSION['panier']['quantite'][$j] . " WHERE id_article=" . $_SESSION['panier']['id_article'][$j]);
		}
		unset($_SESSION['panier']);
		
		$body = "Merci d'avoir commandé sur Hus - Design with passion. Votre numéro de suivi de commande est le $id_commande.";
		
		mail($_SESSION['utilisateur']['mail'],"Hus - Confirmation de votre commande", $body,"From:confirmation@hus.com");
		echo "<div class='validation'>Merci pour votre commande. Un mail vous a été envoyé. Votre numéro de suivi est le $id_commande</div>";
	  }  
	}
	
//------AFFICHAGE DU PANIER------

echo $msg;
echo"<div id='container'>";
echo"<div id='check1'>";
echo"<div class='checkout_road'>
	<img src='". RACINE_SITE ."photo/line1.png' alt='' title=''>
	<h2>1. Panier</h2>
	</div>";

echo "<table id='tab_panier' border='1' style='border-collapse:collapse' cellpadding='5'>";

//Proposer au visteur de vider son panier : 

echo '<tr><th colspan="4"  id="titre_tableau">VOTRE PANIER</th><td colspan="1"><a href="?action=vider">Vider le panier</a></td></tr>';
echo "<tr><th>Photo</th><th>Article</th><th>Quantité</th><th>Prix TTC</th><th>Action</th></tr>";

//Condition : si le panier est vide : 

  if(empty($_SESSION['panier']['id_article']))
  {
      echo '<tr><td colspan="10" id="empty_cart">Votre panier est vide</td></tr>';
  }
  else
  {
	$i = 0; 
    for($w = 0; $w < count($_SESSION['panier']['id_article']); $w++) //boucle qui tournera autant de fois qu'il y a d'articles dans notre panier
    {
	  $css_class = ($i % 2 == 0) ? 'clair' : 'sombre';		
	  echo '<tr class="'.$css_class.'">'; 
      echo "<td><img class='img_articlePanier' src='" . $_SESSION['panier']['photo'][$w] . "' alt='". $_SESSION['panier']['titre'][$w] . "' title='". $_SESSION['panier']['titre'][$w] . "' ></td>";	 
      echo "<td>" . $_SESSION['panier']['titre'][$w] . "</td>";	 	  
      echo "<td>" . $_SESSION['panier']['quantite'][$w] . "</td>";
	  if($_SESSION['panier']['id_promo'][$w] > 1){
		echo "<td>" . virgule($_SESSION['panier']['prix'][$w] - ($_SESSION['panier']['prix'][$w] *($_SESSION['panier']['reduction'][$w]/100)))  . " €</td>";  
	  }
	  else{
		echo "<td>" . $_SESSION['panier']['prix'][$w] . " €</td>";		  
	  }
      echo "<td><a href='?action=retirer&id_article=" . $_SESSION['panier']['id_article'][$w] . "'><img src='". RACINE_SITE . "photo/suppr.png' alt='' title='Supprimer cet article'></a></td>";	  
      echo "</tr>";
	  $i++;
    }
    echo "<tr class='border_stop'><td colspan='3'></td><th colspan='1' class='border_prix'>Sous total:</th><td colspan='1'>" . montantSousTotal() . " €</td></tr>";
}
echo "</table>";

if(utilisateurEstConnecte())
{
	if(!empty($_SESSION['panier']['id_article'])){
		echo "<div><a id='return_btn' href='" . RACINE_SITE . "panier_adresse.php'>Passer à l'étape suivante</a></div>";
	}
}
else
{
	if(!empty($_SESSION['panier']['id_article'])){
		echo "<div><a id='connect_btn' href='" . RACINE_SITE . "connexion.php'>Se connecter pour commander</a></div>";
	}
}



echo "</div>";
echo "</div>";

?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.erreur').delay(3000).fadeOut("slow" );
		$('.validation').delay(5000).fadeOut("slow" );
	});
</script>
<?php

require_once('inc/footer.inc.php');





