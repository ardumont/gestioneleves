<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

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

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>Liste des &eacute;coles</h1>

<br />
<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="?page=imports&amp;mode=imp_do" enctype="multipart/form-data">
	<table class="formulaire">
		<caption>Importer Niveau/Cycle/Domaine/Mati&egrave;re/Comp&eacute;tence</caption>
		<tr>
			<td>Fichier &agrave; importer</td>
			<td>
				<!-- taille max que supporte php pour la taille du fichier uploade -->
				<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
				<input type="file" name="nom_fichier" accept="text/text" />
			</td>
		</tr>
		<tr>
			<td><input type="submit" name="action" value="Importer"></td>
		</tr>
	</table>
</form>
