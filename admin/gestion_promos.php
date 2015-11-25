<?php
require_once("../inc/init.inc.php");
require_once('../inc/haut_de_site.inc.php');

//ici c'est le BACKOFFICE : donc restreindre l'accès à cette partie. Uniquement visible pour l'administrateur : 

echo '<section>';

if(!utilisateurEstConnecteEtAdmin())
{
	header("location:../connexion.php"); //redirection pour tous les membres qui ne sont pas administrateurs (donc tous les autres connectés dont la statut est égal à 0 mais aussi les simples visiteurs !!). Seuls les membres ayant un statut == 1 ont accès à cette page. Cf. BDD
	exit(); //permet de stopper l'éxécution du script
}
if(utilisateurEstConnecteEtAdmin() && isset($_GET['action']) && $_GET['action'] == 'deconnexion')
	{
		session_destroy();
		header("location:" . RACINE_SITE . "connexion.php");
		exit();
	}
####################################################
#ENREGISTREMENT : AJOUT OU MODIFICATION DES PROMOS
####################################################

if(isset($_POST['enregistrement']))
{
  //echo 'test'; //ok !
  //debug($_POST);

	$id_promo = executeRequete("SELECT id_promo FROM promotion WHERE id_promo='$_POST[id_promo]'");
	if($id_promo->num_rows != 0 && isset($_GET['action']) && $_GET['action']=='ajout') //double vérif !!
  {
    $msg .= '<div class="erreur">La référence est déjà attribuée. Veuillez vérifier votre saisie.</div>'; 
  }
	else{
		if(isset($_GET['action']) && $_GET['action'] == 'modification')
		{ 
		
		}
		if(empty($msg)) //si aucun message d'erreurs n'a été généré on passe directement à cette condition : 
		{
			$msg .= '<div class="validation">Enregistrement de la promotion effectué.</div>';
		  //je modifie les données déjà présente par les nouvelles saisies
			executeRequete("REPLACE INTO promotion (id_promo,code_promo,reduction) VALUES ('$_POST[id_promo]', '$_POST[code_promo]', '$_POST[reduction]')"); 
			$_GET['action'] = 'affichage';
		}
		
	}
}

if(isset($_GET['action']) && $_GET['action'] == "suppression")
{

	$resultat = informationSurUnepromotion($_GET['id_promo']);
	$promo_a_supprimer = $resultat->fetch_assoc();
	echo "<div class='validation'>Suppression de la promotion : $_GET[id_promo] </div>";
	executeRequete("DELETE FROM promotion where id_promo=$_GET[id_promo]");
	$_GET['action'] = 'affichage';	
}

	echo '<h1> Gestion des promotions</h1>';

if($_GET['action'] == 'modification'){
	echo '<a href="?action=affichage" class="design5_submitBtn leftButton2">Retourner aux promotions</a>';
}
if($_GET['action'] == 'affichage'){
	echo '<a href="?action=ajout" class="design5_submitBtn add_prom">Ajouter une promotion</a>';
}

echo $msg;  // message d'erreur ou de validation.

if(isset($_GET['action']) && $_GET['action'] == "affichage")
{
	echo '<div>';
	
	$resultat = executeRequete("SELECT * FROM promotion");

echo "<div class='encart_gestion'>";
	echo "<p class='infos_gestionSolo'>Nombre de promotion(s) : <span class='num_infosGestion'>" . $resultat->num_rows . '</span></p>';
echo "</div>";

	$nbcol = $resultat->field_count;
	echo "<table style='border-color:red' border=10> <tr>";
	for ($i=0; $i < $nbcol; $i++)
	{    
		$colonne = $resultat->fetch_field(); 
		echo '<th>' . $colonne->name . '</th>';
	}
	echo "<th>Modification</th>";
	echo "<th>Supression</th>";
	echo "</tr>";
	$i = 0;
	while ($ligne = $resultat->fetch_assoc())
  {  
	//crée-moi autant de lignes <tr> qu'il y a de résultats dans la BDD (utilisation de fecth_assoc() qui nous ressort les informations d'array(). Donc récupération par l'intermédiaire d'une boucle foreach()
	$css_class = ($i % 2 == 0) ? 'clair' : 'sombre';
		echo '<tr class="'.$css_class.'">';
		foreach ($ligne as $indice => $information)
    //on récupère les indices et à les informations. Exemple : $article['id_promo'] = 1
		{
				echo "<td>" . $information . "</td>";
		}
		$i++;
		echo '<td><a href="?action=modification&id_promo=' . $ligne['id_promo'] .'"><img src="'. RACINE_SITE . 'photo/modif.png" alt="" title="Modifier la fiche de la promotion"></a></td>';
		echo '<td><a href="?action=suppression&id_promo=' . $ligne['id_promo'] .'" OnClick="return(confirm(\'En êtes vous certain ?\'));"><img src="'. RACINE_SITE . 'photo/suppr.png" alt="" title="Supprimer ce commentaire"></a></td>';
		echo '</tr>';
	}
	echo '</table>';
	echo "</div>";
}


if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) 
{
		if(isset($_GET['id_promo'])) 
		{
			$resultat = informationSurUnePromotion($_GET['id_promo']); 
			$promotion_actuel = $resultat->fetch_assoc(); 
      //print_r($promotion_actuel);
		}
	?>
	<div  id="prom_form">
		<div style="padding: 10px;" id="div_promForm">
			<form method="post" action="">

			<label for="id_promo" class="gras">Id_promo</label>
				<input type="text" id="id_promo" name="id_promo" value="<?php if(isset($promotion_actuel['id_promo'])) echo $promotion_actuel['id_promo']; elseif(isset($_POST['id_promo'])) echo $_POST['id_promo']; ?>" <?php if(isset($promotion_actuel))?> placeholder="Entrer un numéro"  class="design_input"/> <br />
				
			<label for="code_promo" class="gras">Code promo</label>
				<input type="text" id="code_promo" name="code_promo" value="<?php if(isset($promotion_actuel['code_promo'])) echo $promotion_actuel['code_promo']; elseif(isset($_POST['code_promo'])) echo $_POST['code_promo']; ?>" <?php if(isset($promotion_actuel))?> maxlenght="6" placeholder="Ex: 12IO96" class="design_input" /> <br />

			<label for="reduction"class="gras">Réduction</label>
				<input type="text" id="reduction" name="reduction" value="<?php if(isset($promotion_actuel['reduction']))  echo $promotion_actuel['reduction']; elseif(isset($_POST['reduction'])) echo $_POST['reduction']; ?>" placeholder="% de la remise" class="design_input"/><br />

			<input type="submit" name="enregistrement" value="<?php echo ucfirst($_GET['action']); ?>" class="design2_submitBtn modif_button"/> 
			</form>
		</div>
	</div>
	<?php
} 
require_once('../inc/footer.inc.php');
?>