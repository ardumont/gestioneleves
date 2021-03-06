<?php
//==============================================================================
// Vérification des droits d'accès
//==============================================================================

$bHasRight = ProfilManager::hasRight('import_csv_cycle');
if($bHasRight == false)
{
	// Redirection
	header("Location: ?page=no_rights");
	return;
}

//==============================================================================
// Preparation des donnees
//==============================================================================

$oForm = new FormValidation();

// Récupère le résultat de l'import
$bResImport = $oForm->getValue('res', $_GET, 'convert_string', -1);

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

$sCSVExemple = <<< EOCSV
	cycle;niveau;domaine;matiere;competence_0;competence_1;...;competence_n
	II;ce1;Instruction civique et morale;Instruction civique et morale;Prendre des responsabilités et être autonome;Respecter les adultes et les obéir;...;
	II;ce1;Français;Lecture Ecriture;Repérer un son auditivement;Repérer la graphie d'un son;...;
EOCSV;

$sCSVExemple = utf8_decode($sCSVExemple);

//==============================================================================
// Affichage de la page
//==============================================================================

echo h1("Import de cycles/niveaux/domaines/matières/compétences à partir d'un fichier CSV", $aObjectsToHide);

if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<br />
<a href="javascript:void(0);" onclick="showOrHide('help')">Aide</a>
<div id="help" class="messagebox_info" style="display: none;">
	Le but est de faciliter la cr&eacute;ation des Cycle/Domaine/Mati&egrave;re/Comp&eacute;tence.<br />
	Pour cela, vous pouvez
	<ul>
		<li>soit saisir dans chacune des ihms dans cet ordre les cycles, domaines, mati&egrave;res et comp&eacute;tences.</li>
		<li>soit importer un fichier csv &eacute;dit&eacute; &agrave; la main avec une structure similaire :
			<pre style="font-size: 1.1em;"><?php echo htmlentities($sCSVExemple); ?></pre>
			L'ordre dans le fichier est important, il montre la dépendance des cycles, niveaux, domaines, matières et compétences.
			Attention à l'encodage du fichier qui doit être en UTF-8.
		</li>
	</ul>
</div>

<form method="post" action="?page=imports&amp;mode=imports_csv_do" enctype="multipart/form-data">
	<table class="formulaire">
		<caption>Importer Niveau/Cycle/Domaine/Mati&egrave;re/Comp&eacute;tence</caption>
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
