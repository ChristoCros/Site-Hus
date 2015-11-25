<?php
require_once("../inc/init.inc.php");
require_once('../inc/haut_de_site.inc.php');


echo '<section>';

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

$cinqarticlesMieuxNotes = executeRequete("SELECT id_article FROM avis GROUP BY id_article ORDER BY AVG(note) DESC LIMIT 5");

echo "<h1>Statistiques</h1>";

echo '<div id="articleStats">';
	echo '<h4>Top 5 des articles les mieux notées</h4>';			
		while($resultat1 = $cinqarticlesMieuxNotes->fetch_assoc())
		{
			$recupInfosArticle = executeRequete("SELECT titre, photo, s.id_article, categorie, ROUND(AVG(note)*2)/2 AS moyenne FROM article s, avis a WHERE s.id_article = a.id_article AND a.id_article='" . $resultat1['id_article'] . " '  GROUP BY id_article" );
				while($infos = $recupInfosArticle->fetch_assoc())
				{
					echo"<div class='article_div'>";
					echo "<p class='titreStats'>$infos[titre]</p>";
						echo"<div class='imageArticle'>"
							. "<img src='$infos[photo]' height='130' alt='$infos[titre]' data-tooltip='Article n° $infos[id_article]'>"
						. "</div>";
					echo imageNotation($infos['moyenne']);
					echo"</div>";
				}
		}
echo '</div><br>';

// $donnees = executeRequete("SELECT ar.id_article, note, categorie, titre, prix, photo, id_promo FROM article ar LEFT JOIN avis av ON ar.id_article = av.id_article  GROUP BY ar.id_article ORDER BY ar.id_article DESC LIMIT " . (($cPage-1)*$perPage) . ",$perPage");
				
$cinqarticlesPlusAchetes = executeRequete("SELECT id_commande, d.id_article, a .id_article, titre, photo, a.prix FROM details_commande d LEFT JOIN article a ON d.id_article = a.id_article GROUP BY d.id_article ORDER BY COUNT(*) DESC LIMIT 5");

echo '<div id="articleStats">';
	echo '<h4>Top 5 des articles les plus achetés</h4>';			
		while($resultat2 = $cinqarticlesPlusAchetes->fetch_assoc())
		{
			echo"<div class='article_div'>";
			echo "<p class='titreStats'>$resultat2[titre]</p>";
				echo"<div class='imageArticle'>"
					. "<img src='$resultat2[photo]' height='120' alt='$resultat2[titre]' data-tooltip='Article n° $resultat2[id_article]''>"
				. "</div>";
			echo "<p class='prixStats'>$resultat2[prix] €</p>";
			echo"</div>";
		}
echo '</div><br>';

$cinqMembresAchetentPlus = executeRequete("SELECT sexe, m.id_membre, montant FROM membre m, commande c WHERE c.id_membre = m.id_membre GROUP BY m.id_membre ORDER BY SUM(montant) DESC LIMIT 5");

echo '<div id="articleStats">';
	echo '<h4>Top 5 des membres qui achètent le plus</h4>';
		while($resultat3 = $cinqMembresAchetentPlus->fetch_assoc())
		{
			$recupPseudoMembre = executeRequete("SELECT pseudo,  ROUND(SUM(montant),2) AS total FROM membre m, commande c WHERE m.id_membre = c.id_membre AND c.id_membre='" . $resultat3['id_membre'] . "'" );
			if($resultat3['sexe'] == 'f'){
				while($infos3 = $recupPseudoMembre->fetch_assoc()){
					echo"<div class='femme'>";
						echo "<p class='pseudo_stats'>$infos3[pseudo]</p>";
						echo "<p class='p_stats'>Somme totale dépensée :</p>";
						echo"<p class='total_stats'>" . virgule($infos3['total']) . " €</p>";						
					echo"</div>";
				}
			}
			if($resultat3['sexe'] == 'm'){
				while($infos3 = $recupPseudoMembre->fetch_assoc()){
					echo"<div class='homme'>";				
						echo"<p class='pseudo_stats'>$infos3[pseudo]</p>";
						echo "<p class='p_stats'>Somme totale dépensée :</p>";
						echo"<p class='total_stats'>" . virgule($infos3['total']) . " €</p>";						
					echo"</div>";
				}				
			}
		}
echo '</div>';

require_once('../inc/footer.inc.php');











