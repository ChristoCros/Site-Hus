<?php
require_once('../inc/init.inc.php');
require_once('../inc/haut_de_site.inc.php');


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

// Requêtes pour comptage de lignes:

$membres = executeRequete("SELECT * FROM membre");
$nbLinesMembre = $membres->num_rows;

$commande = executeRequete("SELECT * FROM commande");
$nbLinesCommande = $commande->num_rows;

$avis = executeRequete("SELECT * FROM avis");
$nbLinesAvis = $avis->num_rows;

$article = executeRequete("SELECT * FROM article");
$nbLinesArticle = $article->num_rows;

$newsletter = executeRequete("SELECT * FROM newsletter");
$nbLinesNewsletter= $newsletter->num_rows;

$promotion = executeRequete("SELECT * FROM promotion");
$nbLinesPromotion = $promotion->num_rows;

// Chiffre d'affaire:

$calcul = executeRequete("SELECT SUM(montant) AS somme FROM commande");
$ChiffreAffaires = $calcul->fetch_array();

// Promotions en cours:

$promo = executeRequete("SELECT * FROM article WHERE id_promo > 1");
$nbArticlesEnPromo = $promo->num_rows;

// Compteur de visite:

if(file_exists('compteur_visites.txt'))
{
        $compteur_f = fopen('compteur_visites.txt', 'r+');
        $compte = fgets($compteur_f);
}
else
{
        $compteur_f = fopen('compteur_visites.txt', 'a+');
        $compte = 0;
}
if(!isset($_SESSION['compteur_de_visite']))
{
        $_SESSION['compteur_de_visite'] = 'visite';
        $compte++;
        fseek($compteur_f, 0);
        fputs($compteur_f, $compte);
}
fclose($compteur_f);
 
// 3 derniers avis :

$commentaire = executeRequete("SELECT id_avis,pseudo, sujet, commentaire, note, date FROM avis a LEFT JOIN membre m ON a.id_membre = m.id_membre ORDER BY date DESC LIMIT 3");
 // $commentaire = executeRequete("SELECT id_membre, sujet, commentaire, note, date FROM avis ORDER BY commentaire DESC LIMIT 3");
 
if(isset($_GET['action']) && $_GET['action'] == "suppression")
{
	executeRequete("DELETE FROM membre WHERE id_membre = $_GET[id_membre]");
}
	echo'<div id="dashboard">';
		echo'<div id="first_line">';
			echo'<div class="div_dashboard membres_info">';
				echo'<div class="infos_cadre">';
					echo'<span>' . $nbLinesMembre . '</span>';
				echo'</div>';	
				echo'<a href="'. RACINE_SITE . 'admin/gestion_membre.php" class="dashboard_title">Membres</a>';
			echo'</div>';
			echo'<div class="div_dashboard commandes_info">';
				echo'<div class="infos_cadre">';
					echo'<span>' . $nbLinesCommande . '</span>';
				echo'</div>';		
				echo'<a href="'. RACINE_SITE . 'admin/gestion_commande.php?action=0&id_commande=0" class="dashboard_title">Commandes</a>';
			echo'</div>';		
			echo'<div class="div_dashboard avis_info">';
				echo'<div class="infos_cadre">';
					echo'<span>' . $nbLinesAvis . '</span>';
				echo'</div>';		
				echo'<a href="'. RACINE_SITE . 'admin/gestion_avis.php?action=0" class="dashboard_title">Avis</a>';
			echo'</div>';
			echo'<div class="div_dashboard articles_info">';
				echo'<div class="infos_cadre">';
					echo'<span>' . $nbLinesArticle . '</span>';
				echo'</div>';		
				echo'<a href="'. RACINE_SITE . 'admin/gestion_articles.php?action=affichage" class="dashboard_title">Articles</a>';
			echo'</div>';
			echo'<div class="div_dashboard newsletter_info">';
				echo'<div class="infos_cadre">';
					echo'<span>' . $nbLinesNewsletter . '</span>';
				echo'</div>';		
				echo'<a href="'. RACINE_SITE . 'admin/envoi_newsletter.php" class="dashboard_title">Newsletter</a>';
			echo'</div>';
			echo'<div class="div_dashboard promotions_info">';
				echo'<div class="infos_cadre">';
					echo'<span>' . $nbLinesPromotion . '</span>';
				echo'</div>';		
				echo'<a href="'. RACINE_SITE . 'admin/gestion_promos.php?action=affichage" class="dashboard_title">Promotions</a>';
			echo'</div>';
			echo'<div class="div_dashboard statistiques_info">';		
				echo'<a href="'. RACINE_SITE . 'admin/statistiques.php" class="dashboard_title">Statistiques</a>';
			echo'</div>';
		echo'</div>';
		
		echo'<div id="derniers_avis">';
			echo "<div id='avisTitle'><h4>Derniers avis</h4></div>";
				echo"<div id='contentThreeComments'>";	
					while($threeLastComments = $commentaire->fetch_assoc()){
					echo '<div class="comment">';
						echo"<div class='infos_comment'>";
							echo "<p class='pseudo_comm'>" . $threeLastComments['pseudo'] . "</p>";
							imageNotation($threeLastComments['note']);
							echo "<p class='date_comm'>" . dateLongue($threeLastComments['date'],'no') . "</p>";
						echo'</div>';
						echo"<div class='affichage_comment'>";							
							echo "<p class='subject_comm'>" . $threeLastComments['sujet'] . "</p>";
							echo "<p class='comm_comm'>" . $threeLastComments['commentaire'] . "</p>";				
							echo "<a href='". RACINE_SITE . "admin/gestion_avis.php?action=0#"  . $threeLastComments['id_avis'] ."' class='link_comm'> >> Voir</a>";				
						echo"</div>";							
					echo '</div>';
					}
			echo '</div>';			
		echo'</div>';
		
		
		echo'<div id="infos_droite">';
			echo'<div id="chiffre_affaire">';
				echo "<table>";
					echo"<tr>";
						echo '<th>Chiffre d\'affaires</th>';
					echo "</tr>";
					echo "<tr>";
						echo "<td>" . virgule(round($ChiffreAffaires['somme'], 2)) . " €</td>";
					echo "</tr>";			
				echo '</table>';			
			echo'</div>';		
		
			echo'<div id="current_promo">';
				echo "<table>";
					echo"<tr>";
						echo '<th>Promotions en cours</th>';
					echo "</tr>";
					echo "<tr>";
						echo "<td>" . $nbArticlesEnPromo . "</td>";
					echo "</tr>";			
				echo '</table>';			
			echo'</div>';		

			echo'<div id="visites">';
				echo "<table>";
					echo"<tr>";
						echo '<th>Visites</th>';
					echo "</tr>";
					echo "<tr>";
						echo "<td>" . $compte . "</td>";
					echo "</tr>";			
				echo '</table>';			
			echo'</div>';		
		echo'</div>';		
	echo '</div>';	
 
require_once('../inc/footer.inc.php');


