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
	<classe cycle="II" niveau="ce1" nom="ce1a" professeur="Christelle Héritier" annee_scolaire="2009-2010">
		<ecole nom="Edouard Vaillant" ville="Blanc-Mesnil" departement="93150" />
		<eleve nom="CARMEL Sally" date_naissance="07/10/2002" />
		<eleve nom="CAUDRON Vivien" date_naissance="12/04/2002" />
		...
	</classe>
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
				Le but est de faciliter la cr&eacute;ation des Classe/Ecole/Eleve.<br />
				Pour cela, vous pouvez
				<ul>
					<li>soit saisir dans chacune des ihms dans cet ordre les classes, écoles, et élèves.</li>
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
<br />

<form method="post" action="?page=imports&amp;mode=imports_xml_classe_do" enctype="multipart/form-data">
	<table class="formulaire">
		<caption>Importer Classe/Ecole/Eleve</caption>
		<tr>
			<td>Fichier &agrave; importer</td>
			<td>
				<!-- taille max que supporte php pour la taille du fichier uploade -->
				<input type="hidden" name="MAX_FILE_SIZE" value="1048576" />
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
