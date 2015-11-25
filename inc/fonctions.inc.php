<?php

/************************************
		Sécurisation MDP
************************************/
function passwordHash($mdp){
	$salt= "&E;Yhgt74U.?gdrt";
	$hash = sha1($mdp.$salt);
	return $hash;
}


function executeRequete($req)
{
  global $mysqli;//permet d'avoir accès à la variable $mysqli définie dans l'espace global
  
  $resultat = $mysqli->query($req);//ici on exécute la requête reçue en argument
  if(!$resultat)//si renvoie FALSE => ERREUR !!
  {
     die('Erreur sur la requête SQL. <br> Message : ' . $mysqli->error . '<br>Code : ' . $req);//si la requête échoue, on affiche le message correspondant à l'erreur en récupérant la propriété de l'objet $mysqli
  }
  return $resultat; //on retourne un objet issu de la class mysqli_result
}


//----------------------------

function debug($var, $mode = 1)//fonction qui nous évitera de faire des var_dump ou des print_r 
//UNIQUEMENT VALABLE DANS LE CADRE DU DEVELOPPEMENT !!!
{
  //echo '<div style="background: #' . rand(100000,999999). '">';//juste pour styliser notre affichage debug
    if($mode === 1)//si $mode est strictement égal à 1, on fait un print_r
    {
      print '<pre>';print_r($var);print '</pre>';
    }
    else //si $mode est différent de 1, on fait un var_dump
    {
      print '<pre>';var_dump($var);print '</pre>';
    }
    echo '<hr>';
    $trace = debug_backtrace();//fonction prédéfinie retournant un array contenant des informations tel que la ligne et le fichier où est exécutée la fonction
    $trace = array_shift($trace);//extrait la première valeur d'un tableau et la retourne
    //cf. http://php.net/manual/fr/function.array-shift.php
    echo "Debug demandé dans le fichier : $trace[file] à  la ligne $trace[line]";
 // echo '</div>';
  return;
}

//-------FONCTIONS UTILISATEURS

function utilisateurEstConnecte()
{
  //c'est une fonction qui m'indiquera si l'utilisateur est connecté
  if(!isset($_SESSION['utilisateur'])) //si la session utilisateur est non-définie 
  {
    return false;
  }
  else
  {
     return true;   
  }
}
function utilisateurEstConnecteEtAdmin()
{
  if(utilisateurEstConnecte() && $_SESSION['utilisateur']['statut'] == 1)//si le statut est égal à  1, c'est forcément l'administrateur du site cf. statut champ BDD
  {
     return true;  
  }
  return false;
}


//fonction afin de vérifier l'extension des photos uploadées : 

function verificationExtensionPhoto()
{
  //cf. http://php.net/manual/fr/function.strrchr.php
  //ici j'utilise la superglobale $_FILES qui me permet de d'uploader des fichiers. Il s'agit d'un array() : ici un tableau 
  $extension = strrchr($_FILES['photo']['name'],'.'); 
  //fonction me permettant d'obtenir la dernière occurrence d'un caractère dans une chaine
  //cf. http://php.net/manual/fr/reserved.variables.files.php
  $extension = strtolower(substr($extension,1)); //=> nous coupons le point pour transformer par exemple : ".jpg en jpg"
  //cf. strtolower : permet de formater une chaine de caractères en minuscule. => http://php.net/manual/fr/function.strtolower.php  
  
  $tab_extension_valide = array('gif','jpg','jpeg','png'); //liste des extensions autorisées
  //debug($_FILES);
  //A présent nous allons comparer les extensions formatéees (cf. => $extension) et les extensions permises définies dans notre variable : ($tab_extension_valide) en utilisant la fonction prédéfinie in_array() dont le principe est de comparer le contenu de deux tableaux array() : cf. http://php.net/manual/fr/function.in-array.php
  $verif_extension = in_array($extension,$tab_extension_valide);
  return $verif_extension;  //retournera TRUE OU FALSE en fonction de la comparaison
}

//Nous créons ici une fonction qui nous permettra d'obtenir des informations propres à un article
function informationSurUnArticle($id)
{
  $resultat = executeRequete("SELECT * FROM article WHERE id_article=$id");//si l'id_article correspond à l'argument de notre fonction
  return $resultat;
}

function informationSurUnePromotion($idp)
{
  $resultat_promotion = executeRequete("SELECT * FROM promotion prom, article ar WHERE prom.id_promo = ar.id_promo AND id_article = $idp");
  return $resultat_promotion;
}


/********************************************
FONCTION AFFICHAGE NOTATION
********************************************/

function imageNotation($note)
{
	if($note == 0.5){
		echo '<img src="'. RACINE_SITE . 'photo/note_0.5.png" alt="0.5" title="0.5" class="img_note">';
	}	
	if($note == 1){
		echo '<img src="'. RACINE_SITE . 'photo/note_1.png" alt="1" title="1" class="img_note">';
	}
	if($note == 1.5){
		echo '<img src="'. RACINE_SITE . 'photo/note_1.5.png" alt="1.5" title="1.5" class="img_note">';
	}		
	if($note == 2){
		echo '<img src="'. RACINE_SITE . 'photo/note_2.png" alt="2" title="2" class="img_note">';
	}	
	if($note == 2.5){
		echo '<img src="'. RACINE_SITE . 'photo/note_2.5.png" alt="2.5" title="2.5" class="img_note">';
	}		
	if($note == 3){
		echo '<img src="'. RACINE_SITE . 'photo/note_3.png" alt="3" title="3" class="img_note">';
	}
	if($note == 3.5){
		echo '<img src="'. RACINE_SITE . 'photo/note_3.5.png" alt="3.5" title="3.5" class="img_note">';
	}	
	if($note == 4){
		echo '<img src="'. RACINE_SITE . 'photo/note_4.png" alt="4" title="4" class="img_note">';
	}
	if($note == 4.5){
		echo '<img src="'. RACINE_SITE . 'photo/note_4.5.png" alt="4.5" title="4.5" class="img_note">';
	}	
	if($note == 5){
		echo '<img src="'. RACINE_SITE . 'photo/note_5.png" alt="5" title="5" class="img_note">';
	}
  	else{

	}
}


/***************************************************
		FONCTION HISTORIQUE COMMANDES
***************************************************/

function creationDeHistorique()
{
    if(!isset($_SESSION['histo'])) //si l'histo de commande (la SESSION) n'existe pas : on le crée
    {
      $_SESSION['histo'] = array();
      $_SESSION['histo']['id_commande'] = array();
      $_SESSION['histo']['date'] = array();
    }
  return true;
  //soit l'histo n'existe pas : on le crée et on retourne true
  //soit l'histo existe déjà, on return true directement
}

/*******************************
	   FONCTION FAVORIS
*******************************/

function creationWishlist()
{
    if(!isset($_SESSION['favoris'])) 
    {
      $_SESSION['favoris'] = array();
      $_SESSION['favoris']['id_article'] = array();
      $_SESSION['favoris']['titre'] = array();
      $_SESSION['favoris']['photo'] = array();
      $_SESSION['favoris']['id_promo'] = array();
      $_SESSION['favoris']['reduction'] = array();
    }
  return true;
}

//Ajouter article à la wishlist:

function ajouterArticleDansWishlist($id_article,$titre,$photo,$prix,$id_prom,$red)
{
#on veut tout d'abord savoir si l'id_article que l'on souhaite ajouter est déjà présent dans notre panier....
  $position_article = array_search($id_article,$_SESSION['favoris']['id_article']); //Recherche dans un tableau la clé associée à une valeur
  //cf. http://php.net/manual/fr/function.array-search.php

#si le article est déjà présent dans le panier : donc true => !== FALSE
  if($position_article !== FALSE)
	{
		$msg = '<div class="erreur">Cet article est déjà dans vos favoris</div>'; // message d'erreur si F5 ou tente de rajouter une seconde fois le même article
	}
  else  //dans le cas contraire : si le article est absent du panier, on ajoute l'id_article du article dans un nouvel indice du tableau. 
  {
		$_SESSION['favoris']['id_article'][] = $id_article;  //on récupère les arguments de notre fonction
		$_SESSION['favoris']['titre'][] = $titre;
		$_SESSION['favoris']['photo'][] = $photo;
		$_SESSION['favoris']['prix'][] = $prix;
		$_SESSION['favoris']['id_promo'][] = $id_prom;
		$_SESSION['favoris']['reduction'][] = $red;
   }
}

//Retirer article de la wishlist:

function retirerArticleWishlist($id_article_a_supprimer)
{
  #on récupère la position de id_article dans notre wishlist donc utilisation de la fonction array_search(). Qui nous retournera un chiffre afin de savoir à quel indice se trouve le article à supprimer. 
  $position_article = array_search($id_article_a_supprimer,$_SESSION['favoris']['id_article']);
  
  #si le article est présent dans la wishlist : on le retire : 
  if($position_article !== FALSE) // == TRUE
  {
    #on utilise un array_splice() qui efface et remplace une portion de tableau => retire un élément et réordonne les indices en conséquence
    //cf. http://php.net/manual/fr/function.array-splice.php

    array_splice($_SESSION['favoris']['id_article'], $position_article,1); 
    array_splice($_SESSION['favoris']['titre'], $position_article,1); 
    array_splice($_SESSION['favoris']['photo'], $position_article,1);  
    array_splice($_SESSION['favoris']['prix'], $position_article,1); 
    array_splice($_SESSION['favoris']['id_promo'], $position_article,1);
    array_splice($_SESSION['favoris']['reduction'], $position_article,1);
    #array_splice() != array_slice()
  } 
}

// function ajoutAuFavoris($membreSession,$articleAjouterWishlist){
	// NOTE : Faire en sorte que l'id_favoris soit le même pour chaque article ajouté en wishlist et soit unique pour chaque membre. Ex: l'id_membre 1 a un id_favoris 1, il le gardera pour toujours. Donc regarder si le membre a déjà une liste avec une requete sql et un num_rows < 1 pour définir que non sinon num_rows() >1 il a déjà une liste et donc on reprend l'id_favoris pour tous les articles qu'il aimera. 
	// id_favoris -> auto incrémentation pour la première fois et ensuite récupération.	
	//id_membre -> $membreSession = $_SESSION['utilisateur']['id_membre']
	//id_article -> $articleAjouterWishlist = $article['id_article']
	
	// $askBddForWishlist = executeRequete("SELECT id_favoris FROM favoris WHERE id_membre = '$membreSession' ");
	// $nbLineInBdd = $askBddForWishlist->num_rows();
	
	// if($nbLineInBdd == 0){
		// $ajoutWishlistFirst= executeRequete("INSERT INTO favoris VALUES ('$membreSession','$articleAjouterWishlist')");
	// }
	// if($nbLineInBdd >= 1){
	// $searchListWishlistNumber = executeRequete("SELECT id_favoris FROM favoris WHERE id_membre = '$membreSession' ");
	// $listWishlistNumber = $searchListWishlistNumber->fetch_assoc();
	
	// $ajoutWishlistSecond= executeRequete("INSERT INTO favoris VALUES ('$listWishlistNumber','$membreSession','$articleAjouterWishlist')");
	// }
// }

/*************************
	FONCTION PANIER
*************************/
  
  
function creationDuPanier()
{
    if(!isset($_SESSION['panier'])) //si le panier (la SESSION) n'existe pas : on le crée
    {
      $_SESSION['panier'] = array();
      $_SESSION['panier']['id_article'] = array();
      $_SESSION['panier']['titre'] = array();
      $_SESSION['panier']['photo'] = array();
      $_SESSION['panier']['prix'] = array();
      $_SESSION['panier']['quantite'] = array();
      $_SESSION['panier']['id_promo'] = array();
      $_SESSION['panier']['reduction'] = array();
    }
  return true;
  //soit le panier n'existe pas : on le crée et on retourne true
  //soit le panier existe déjà, on return true directement
}

/******************************************************
FONCTION AJOUTER UN ARTICLE AU PANIER
******************************************************/
function ajouterArticleDansPanier($id_article,$titre,$photo,$prix,$quantite,$id_prom,$red)
{
#on veut tout d'abord savoir si l'id_article que l'on souhaite ajouter est déjà présent dans notre panier....
  $position_article = array_search($id_article,$_SESSION['panier']['id_article']); //Recherche dans un tableau la clé associée à une valeur
  //cf. http://php.net/manual/fr/function.array-search.php

#si le article est déjà présent dans le panier : donc true => !== FALSE
  if($position_article !== FALSE)
  {
  $_SESSION['panier']['quantite'][$position_article] += $quantite;
  }
  else  //dans le cas contraire : si le article est absent du panier, on ajoute l'id_article du article dans un nouvel indice du tableau. 
  {
		$_SESSION['panier']['id_article'][] = $id_article;  //on récupère les arguments de notre fonction
		$_SESSION['panier']['titre'][] = $titre;
		$_SESSION['panier']['photo'][] = $photo;
		$_SESSION['panier']['prix'][] = $prix;
		$_SESSION['panier']['quantite'][] = $quantite;
		$_SESSION['panier']['id_promo'][] = $id_prom;
		$_SESSION['panier']['reduction'][] = $red;
   }
}
/***********************
FONCTION MONTANT PANIER
***********************/
function montantSousTotal()
{
	if(empty($_SESSION['panier']['id_article'])){
		return 0;
	}
	else{
		$total = 0;
		for($i=0; $i < count($_SESSION['panier']['id_article']); $i++)
		{
			if($_SESSION['panier']['id_promo'] > 1){
				$total +=  ($_SESSION['panier']['prix'][$i] - ($_SESSION['panier']['prix'][$i]*($_SESSION['panier']['reduction'][$i]/100)))*$_SESSION['panier']['quantite'][$i];				
			}
			else{
				$total +=  $_SESSION['panier']['prix'][$i]*$_SESSION['panier']['quantite'][$i];
			}
		}
		return virgule(round($total,2));
	}
}

function montantTotal()
{
	if(montantSousTotal() > 50){
		$totalWithShipping = montantSousTotal();		
	}
	else{
		$totalWithShipping = montantSousTotal() + 4.50;
	}
	return virgule($totalWithShipping);
}

function montantTotalAvecPromotion()
{
  $total = 0;
  for($i=0; $i < count($_SESSION['panier']['id_article']); $i++)
  {
    $total +=  $_SESSION['panier']['prix'][$i] - ($_SESSION['panier']['prix'][$i] * ($_SESSION['panier']['reduction'][$i]/100));
  }
  return virgule(round($total,2));
}

function remise()
{
	$reduction = montantTotalAvecPromotion() - montantTotal();
	return round($reduction,2);
}

/****************************************************************
					FONCTION PRIX AVEC PROMO
****************************************************************/

function prixAvecPromo($idArticleEnProm)
{
	$requeteProm = executeRequete("SELECT prix, reduction FROM article ar, promotion prom WHERE ar.id_promo = prom.id_promo AND ar.id_article = $idArticleEnProm");
	$resultatProm = $requeteProm->fetch_assoc();
	
	$prixPromotionFinal = $resultatProm['prix'] - ($resultatProm['prix'] * ( $resultatProm['reduction'] / 100));
	return virgule(round($prixPromotionFinal,2));
}


/************************************
FONCTION RETIRER UN ARTICLE DU PANIER
*************************************/
function retirerArticleDuPanier($id_article_a_supprimer)
{
  #on récupère la position de id_article dans notre panier donc utilisation de la fonction array_search(). Qui nous retournera un chiffre afin de savoir à quel indice se trouve le article à supprimer. 
  $position_article = array_search($id_article_a_supprimer,$_SESSION['panier']['id_article']);
  
  #si le article est présent dans le panier : on le retire : 
  if($position_article !== FALSE) // == TRUE
  {
    #on utilise un array_splice() qui efface et remplace une portion de tableau => retire un élément et réordonne les indices en conséquence
    //cf. http://php.net/manual/fr/function.array-splice.php

    array_splice($_SESSION['panier']['id_article'], $position_article,1); 
    array_splice($_SESSION['panier']['titre'], $position_article,1); 
    array_splice($_SESSION['panier']['photo'], $position_article,1);  
    array_splice($_SESSION['panier']['prix'], $position_article,1); 
    array_splice($_SESSION['panier']['quantite'], $position_article,1); 
    array_splice($_SESSION['panier']['id_promo'], $position_article,1);
    array_splice($_SESSION['panier']['reduction'], $position_article,1);
    #array_splice() != array_slice()
    
  } 
}


/***************************************
	NOMBRES ARTICLES PANIER
***************************************/

$commandes = executeRequete("SELECT SUM(montant) FROM commande");


function compterArticles()
{
	if(isset($_SESSION['panier'])){
		
		$totalArticle = 0;
		
		for($k=0; $k < count($_SESSION['panier']['id_article']); $k++)
		{
			$totalArticle +=  $_SESSION['panier']['quantite'][$k];
		}
		return $totalArticle;
	}
	else{
		return 0; // Si aucune ligne dans mon panier retourne 0.
	}

}



//Fonction pour la date en FR:

/* Configure le script en français */
setlocale (LC_TIME, 'fr_FR','fra');
//Définit le décalage horaire par défaut de toutes les fonctions date/heure  
date_default_timezone_set("Europe/Paris");
//Definit l'encodage interne
mb_internal_encoding("UTF-8");

//Convertir une date US vers une date en français affichant le jour de la semaine
function dateLongue($date,$heure = 'yes'){
    if($heure == 'yes')
    {
		$strDate = mb_convert_encoding('%d %B %Y à %Hh%M','ISO-8859-9','UTF-8');  
    }
    else
    {
		$strDate = mb_convert_encoding('%d %B %Y','ISO-8859-9','UTF-8');    
    }
    return iconv("ISO-8859-9","UTF-8",strftime($strDate ,strtotime($date))); 
}

// Ajout d'une virgule au lieu d'un point :

function virgule($prix){
	$prix = str_replace('.', ',', $prix);
	return $prix;
}

function point($prix){
	$prix = str_replace(',', '.', $prix);
	return $prix;
}

/*********************************************************************************
	Fonction deu calcul du nombre de ligne en BDD pour les favoris 
*********************************************************************************/

function favorisNbLoveArticle($id_membre){
	$query = executeRequete("SELECT * FROM favoris WHERE id_membre = '$id_membre' ");
	$compteLigne = $query->num_rows;
	
	return $compteLigne;
}
























