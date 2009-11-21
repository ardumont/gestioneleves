<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

$oForm = new FormValidation();

// Récupère le résultat de l'import
$bResImport =  $oForm->getValue('res', $_GET, 'convert_string', -1);

//==============================================================================
// Validation du formulaire
//==============================================================================

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnees
//==============================================================================

//==============================================================================
// Preparation de l'affichage
//==============================================================================

$sXMLExemple = <<< EOXML
	<?xml version="1.0" encoding="UTF-8"?>
	<cycle xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="cycle.xsd" name="II">
		<domaine name="Instruction civique et morale">
			<matiere name="Instruction civique et morale">
				<competence name="Prendre des responsabilités et être autonome" />
				<competence name="Respecter les adultes et les obéir" />
				<competence name="Respecter les règles de la vie collective" />
				<competence name="Connaître les règles élémentaires de politesse" />
				<competence name="Connaître des règles simples de comportement en société" />
				<competence name="Coopérer à la vie de la classe et rendre service" />
				<competence name="Connaître les principaux symboles de la nation et de la République et les respecter" />
			</matiere>
		</domaine>
		<domaine name="Français">
			<matiere name="Lecture écriture">
				<competence name="Repérer un son auditivement" />
				<competence name="Repérer la graphie d'un son" />
				...
			</matiere>
		</domaine>
	</cycle>
EOXML;

$sXMLExemple = htmlentities(utf8_decode($sXMLExemple));

//==============================================================================
// Affichage de la page
//==============================================================================

?>
<h1>Imports XML</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<br />
<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<div id="help" style="display: none;">
	<table class="formulaire">
		<caption>Fonctionnement</caption>
		<tr>
			<td>
				Le but est de faciliter la cr&eacute;ation des Cycle/Domaine/Mati&egrave;re/Comp&eacute;tence.<br />
				Pour cela, vous pouvez
				<ul>
					<li>soit saisir dans chacune des ihms dans cet ordre les cycles, domaines, mati&egrave;res et comp&eacute;tences.</li>
					<li>soit importer un fichier xml &eacute;dit&eacute; &agrave; la main avec une structure similaire :
						<pre style="font-size: 1.1em;"><?php echo $sXMLExemple; ?></pre>
						L'imbrication des tags montre bien la d&eacute;pendance de chacun des &eacute;l&eacute;ments.
						Attention &agrave; l'encodage du fichier qui doit &ecirc;tre en UTF-8.
					</li>
				</ul>
			</td>
		</tr>
	</table>
</div>

<form method="post" action="?page=imports&amp;mode=imports_xml_do" enctype="multipart/form-data">
	<table class="formulaire">
		<caption>Importer Cycle/Domaine/Mati&egrave;re/Comp&eacute;tence</caption>
		<tr>
			<td>Fichier &agrave; importer</td>
			<td>
				<!-- taille max que supporte php pour la taille du fichier uploadé -->
				<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
				<input type="file" name="nom_fichier" accept="text/text" />
			</td>
		</tr>
		<tr>
			<td><input type="submit" name="action" value="Importer"></td>
		</tr>
	</table>
</form>

<?php if($bResImport != -1): ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			L'import est un <?php echo ($bResImport == "ok") ? ' <span style="color:green;font-weight:bold;">succès</span>' : ' <span style="color:red;font-weight:bold;">échec</span>'; ?>.<br />
		</td>
	</tr>
</table>
<?php endif; ?>
