<?php
require_once('../inc/init.inc.php');
require_once('../inc/haut_de_site.inc.php');

if(!utilisateurEstConnecteEtAdmin())
{
	header("location:../connexion.php");
	exit();
}
if(utilisateurEstConnecteEtAdmin() && isset($_GET['action']) && $_GET['action'] == 'deconnexion')
	{
		session_destroy();
		header("location:" . RACINE_SITE . "connexion.php");
		exit();
	}
			
if(isset($_GET['action']) && $_GET['action'] == "suppression")
{
	echo "<div class='validation'>Suppression de la commande : $_GET[id_commande] </div>";
	executeRequete("DELETE FROM commande where id_commande=$_GET[id_commande]");
	$_GET['action'] = '0';
	header('location:gestion_commande.php?action=0&id_commande=0');
}

if(isset($_GET['action']) && $_GET['action'] == "0")
{
	$resultat = executeRequete("SELECT * FROM commande");
}

// if(isset($_GET['action']) && $_GET['action'] == "croissant")
// {
	// $resultat = executeRequete("SELECT * FROM commande ORDER BY montant ASC");
// }

// if(isset($_GET['action']) && $_GET['action'] == "decroissant")
// {
	// $resultat =	executeRequete("SELECT * FROM commande ORDER BY montant DESC");
// }

echo '<section>';

echo '<h1> Liste des Commandes</h1>';

$commandes = executeRequete("SELECT SUM(montant) AS somme FROM commande");
$totalCommande = $commandes->fetch_array();

echo "<div class='encart_gestion'>";
	echo "<p class='infos_gestion'>Nombre de commande(s) : <span class='num_infosGestion'>". $resultat->num_rows . "</span></p>";
	echo "<p class='infos_gestion'>Chiffre d'affaires (CA) : <span class='num_infosGestion'>" . virgule(round($totalCommande['somme'], 2)) . "€</span></p>";
echo "</div>";		

$commande = executeRequete("SELECT * FROM commande c, details_commande d WHERE c.id_commande = d.id_commande AND c.id_commande = '$_GET[id_commande]'");

// $id_details_commande = executeRequete("SELECT * FROM commande c, details_commande d WHERE c.id_commande = d.id_commande ");
// $id_details = $id_details_commande->fetch_assoc();


if($_GET['id_commande'] > 0){
	
	echo "<div id='infos_commande'>";
	echo "<table style='border-color:red;'> <tr>";
 
	echo '<th>N° de commande</th>';
	echo '<th>Montant</th>';
	echo "<th>Nom Prénom du membre</th>";
	echo "<th>Date</th>";
	echo "<th>N° détails commande</th>";
	echo "<th>N° de l'article</th>";
	echo "<th>Quantité</th>";
	echo "<th>Prix unitaire</th>";
	echo "</tr>";	
		$j = 0;	
		while ($details = $commande->fetch_assoc())
	  {  
		$membre_id = executeRequete("SELECT nom, prenom FROM membre WHERE id_membre = '" . $details['id_membre'] . "'");
		$nom_membre = $membre_id->fetch_assoc();
		
		$article = executeRequete("SELECT titre FROM article WHERE id_article = $details[id_article]");
		$titre_article = $article->fetch_assoc();

			$css_class = ($j % 2 == 0) ? 'clair' : 'sombre';
			echo '<tr class="'.$css_class.'">';
						echo "<td style='width: 150px;'>" . $details['id_commande'] . "</td>";			
						echo "<td style='width: 150px;'>" . virgule($details['montant']) . "</td>";					
						echo "<td style='width: 150px;' data-tooltip='Membre n°$details[id_membre]'>" . $nom_membre['nom'] . ' ' . $nom_membre['prenom'] . "</td>";
						echo "<td style='width: 150px;'>" . dateLongue($details['date'], 'no') . "</td>";				
						echo "<td style='width: 150px;'>" . $details['id_details_commande'] . "</td>";				
						echo "<td style='width: 150px;' data-tooltip='Article n°$details[id_article]'>" . $titre_article['titre'] . "</td>";				
						echo "<td style='width: 150px;'>" . $details['quantite'] . "</td>";
						echo "<td style='width: 150px;'>" . virgule($details['prix']) . "</td>";
			$j++;
		}
			echo '</tr>';
	}
	echo '</table>';
echo "</div>";

	$nbcol = $resultat->field_count;
	echo "<table style='border-color:red;' border=10> <tr>";
 
	echo '<th>N° de commande</th>';
	echo '<th>Montant</th>';
	echo '<th>Nom Prénom du membre</th>';
	echo '<th>Date</th>';
	echo "<th>Supression</th>";
	echo "</tr>";
	$i = 0;	
	while ($ligne = $resultat->fetch_assoc())
  {  
		$membre_id2 = executeRequete("SELECT nom, prenom FROM membre WHERE id_membre = '$ligne[id_membre]'");
		$nom_membre2 = $membre_id2->fetch_assoc();

	    $css_class = ($i % 2 == 0) ? 'clair' : 'sombre';
		echo '<tr class="'.$css_class.'">';
					echo "<td style='width: 150px;'><a href='". RACINE_SITE . "admin/gestion_commande.php?action=0&id_commande=$ligne[id_commande]'>" . $ligne['id_commande'] . "</a></td>";
					echo "<td style='width: 150px;'>" . virgule($ligne['montant']) . "</td>";
						echo "<td style='width: 150px;' data-tooltip='Membre n°$ligne[id_membre]'>" . $nom_membre2['nom'] . ' ' . $nom_membre2['prenom'] . "</td>";
					echo "<td style='width: 150px;'>" . dateLongue($ligne['date'], 'no') . "</td>";					
		$i++;
		echo '<td style="width: 150px;"><a href="?action=suppression&id_commande=' . $ligne['id_commande'] .'" OnClick="return(confirm(\'En êtes vous certain ?\'));"><img src="'. RACINE_SITE . 'photo/suppr.png" alt="" title="Supprimer cette commande"></a></td>';
		echo '</tr>';
	}
	echo '</table>';

	
	
require_once('../inc/footer.inc.php');	
	
	
	
	