<?php
require_once('inc/init.inc.php');
//j'inclus les parties de mon site : 
require_once('inc/haut_de_site.inc.php');

echo '<section>';

echo "<div class='cadre_legals'>";
	echo "<div class='legals'>";
?>

<h2>Demande de remboursement</h2>

	<p>Ce site a été créé dans le cadre d'une formation à IFOCOP et donc toutes ces informations sont fictives.</p>

<p><span>Vous bénéficiez de la garantie satisfait ou remboursé sur les produits vendus et expédiés par Hus - design with passion</span></br>
Vous avez changé d'avis ? Les articles que vous avez reçus ou offerts ne vous apportent pas une entière satisfaction ? <span>Vous souhaitez vous rétracter sur un ou plusieurs produit(s) de votre commande ?</span></p>

<p><span>Vous avez 14 jours, à compter du jour de réception de vos produits, pour nous retourner les articles qui ne vous conviennent pas.</span></br></br>
Seuls les produits retournés complets, dans leur emballage d'origine et en parfait état de revente seront acceptés. Ainsi, les produits doivent impérativement être sur-emballés pour le transport.</br>
Dans le cas des logiciels, disques, CD Rom, jeux vidéo et cartes téléphoniques prépayées, la procédure de rétractation ne s'applique que dans la mesure où leur film plastique d'origine est intact conformément aux dispositions de l'article 8.3 de nos Conditions Générales de Vente.</br>
Les produits issus du montage PC ne peuvent faire l'objet d'une rétractation car ils sont considérés comme une commande personnalisée.</p>

<h3>Comment faire ?</h3>

<p>Télécharger le formulaire de rétractation en cliquant <a href="<?php RACINE_SITE ?>formulaire_retractation.pdf" target="_blank">ici.</a></p>

<p><i>1 - Imprimez le formulaire</br>
2 - Complétez les informations concernant le produit pour lequel vous souhaitez vous rétracter (un formulaire par produit)</br>
3 - Collez sur le dessus du colis la partie haute du formulaire complété</br>
4 - Glissez le formulaire complété dans le colis</i></p>

<p>NB : En utilisant ce formulaire, nous ne pourrons prendre en compte votre demande de rétractation que lors de la réception de votre colis par notre SAV, ce qui peut générer un délai supplémentaire.</p>

<p>Lors de l'envoi de votre colis, nous vous conseillons d'assurer votre envoi à hauteur de la valeur de la marchandise qu'il contient, pour éviter tout problème lié à une avarie durant l'acheminement de votre colis.</p>

<p>Nous vous enverrons un email dès la réception de votre colis par notre SAV. </p>

<?php
	echo"</div>";
echo"</div>";

require_once('inc/footer.inc.php');







