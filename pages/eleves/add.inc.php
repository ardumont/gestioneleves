<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

// ===== Les recherches pour l'ajout d'un eleve mais d'abord la classe a laquelle on affecte cet eleve  =====
$oForm = new FormValidation();

// recupere le nom de l'action du formulaire pour la recherche d'un eleve
$sAction = $oForm->getValue('action', $_POST, 'is_string', "");

// au retour de l'insertion d'un eleve, on recoit la classe de l'eleve
$nClasseId = $oForm->getValue('classe_id', $_GET, 'convert_int');

// au retour de la soumission du formulaire de recherche d'un eleve
$sNomEleve = $oForm->getValue('ELEVE_NOM', $_POST, 'is_string');

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// recherche du nom d'un eleve
	case 'rechercher':
		if($oForm->hasError() == true) break;

		$sQuery =
			"SELECT " .
			" ELEVE_ID, " .
			" ELEVE_NOM " .
			"FROM ELEVES " .
			"WHERE UPPER(ELEVE_NOM) LIKE " .Database::prepareString(strtoupper("{$sNomEleve}%"));
		$aEleveNoms = Database::fetchColumnWithKey($sQuery);
		// $aEleveNoms[ELEVE_ID] = ELEVE_NOM
	break;

	// ----------
	default:// il ne se passe rien au cas ou l'action est vide
		$oForm->clearError();
		break;
}

//==============================================================================
// Traitement des donnees
//==============================================================================

// ===== La liste des classes du professeur connecte pour le select =====
$sQuery = "SELECT" .
		  "  CLASSE_ID, " .
		  "  CLASSE_NOM, " .
		  "  CLASSE_ANNEE_SCOLAIRE, " .
		  "  PROFESSEUR_NOM ".
		  " FROM CLASSES, PROFESSEUR_CLASSE, PROFESSEURS " .
		  " WHERE CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE" .
		  "   AND PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID " .
		  " AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante() .
		  " AND PROFESSEURS.PROFESSEUR_ID = " . $_SESSION['PROFESSEUR_ID'] .
		  " ORDER BY PROFESSEUR_NOM ASC, CLASSE_ANNEE_SCOLAIRE ASC, CLASSE_NOM ASC";
$aClasses = Database::fetchArray($sQuery);
// $aClasses[CLASSE_ID] = CLASSE_NOM

// ===== Affichage des informations =====
// classe par defaut est la premiere de la liste
if ($nClasseId == null && $aClasses != false)
{
	// on affiche les donnees de la premiere classe
	$nClasseId = $aClasses[0]['CLASSE_ID'];
}

// traitement des donnees pour la classe d'id $nClasseId
if ($nClasseId != null)
{
	// ===== Les informations de la classe =====
	$sQuery = "SELECT" .
			  "  CLASSE_NOM, " .
			  "  PROFESSEUR_NOM, " .
			  "  CLASSE_ANNEE_SCOLAIRE, " .
			  "  ECOLE_NOM, " .
			  "  ECOLE_VILLE, " .
			  "  ECOLE_DEPARTEMENT " .
			  " FROM CLASSES, PROFESSEUR_CLASSE, PROFESSEURS, ECOLES " .
			  " WHERE CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE " .
			  " AND PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID " .
			  " AND CLASSES.ID_ECOLE = ECOLES.ECOLE_ID " .
			  " AND CLASSES.CLASSE_ID = {$nClasseId} " .
			  " ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC";
	$aClasseRow = Database::fetchOneRow($sQuery);

	// ===== La liste des eleves de la classe =====
	$sQuery = "SELECT " .
			  "  ELEVE_ID," .
			  "  ELEVE_NOM, " .
			  "  DATE_FORMAT(ELEVE_DATE_NAISSANCE, '%d/%m/%Y') AS ELEVE_DATE_NAISSANCE, " .
			  "  ELEVE_ACTIF " .
			  " FROM ELEVES, ELEVE_CLASSE " .
			  " WHERE ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE " .
			  " AND ELEVE_CLASSE.ID_CLASSE = {$nClasseId}" .
			  " ORDER BY ELEVE_NOM ASC";
	$aEleves = Database::fetchArray($sQuery);
	// $aEleves[][COLONNE] = VALEUR
}

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

?>
<h1><a href="javascript:void(0)" onclick="showOrHideMenu('<?php echo(URL_ICONS_16X16); ?>/arrow_left.png', '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png');"><img id="img_arrow" src="<?php echo(URL_ICONS_16X16); ?>/arrow_left.png" /></a>Ajout d'un &eacute;l&egrave;ve</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>

<br />
<table class="formulaire">
	<caption>Fonctionnement</caption>
	<tr>
		<td>
			Pour &eacute;viter de saisir un &eacute;l&egrave;ve qui existe d&eacute;j&agrave;
			dans la base.<br />
			Vous pouvez toujours saisir le d&eacute;but de son nom dans la zone de texte
			ci-dessous puis cliquer sur le bouton <i>Rechercher</i>.<br />
			La page va alors se recharger.<br />
			S'il existe des noms qui correspondent &agrave; vos crit&egrave;res de
			recherche, vous pourrez alors s&eacute;lectionner un &eacute;l&egrave;ve dans la
			 liste d'&eacute;l&egrave;ves trouv&eacute;s pour l'ajouter dans la classe voulue.
			 <br />
			Si vous ne le trouvez pas, vous pouvez toujours saisir un nouveau nom dans la
			zone de texte <i>Nom de l'&eacute;l&egrave;ve</i> situ&eacute;e dans
			l'encadr&eacute; intitul&eacute; <i>Ajouter un &eacute;l&egrave;ve</i>.<br />
			La date de naissance est obligatoire car cela permet de distinguer les homonymes.
			Si vous n'avez pas de date de naissance, laisser le champ initialis&eacute; &agrave; <i>jj/mm/aaaa</i> et penser &agrave; r&eacute;&eacute;diter les dates de naissance plus-tard.
		</td>
	</tr>
</table>

<form method="post" action="?page=eleves&amp;mode=add">
	<table class="formulaire">
		<caption>Rechercher un &eacute;l&egrave;ve</caption>
		<tbody>
			<tr>
				<td>Nom &agrave; rechercher (Nom Pr&eacute;nom)</td>
				<td><input type="text" size="30" maxlength="<?php echo(ELEVE_NOM); ?>" name="ELEVE_NOM" /></td>
			</tr>
			<tr>
				<td><input type="submit" value="Rechercher" name="action" /></td>
			</tr>
		</tbody>
	</table>
</form>

<form method="post" action="?page=eleves&amp;mode=add_do">
	<table class="formulaire">
		<caption>Ajouter un &eacute;l&egrave;ve</caption>
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<td>Classe de l'&eacute;l&egrave;ve</td>
				<td>
					<select name="CLASSE_ID">
						<?php foreach($aClasses as $aClasse): ?>
							<option value="<?php echo($aClasse['CLASSE_ID']); ?>"<?php echo($aClasse['CLASSE_ID'] == $nClasseId ? ' selected="selected"' : ''); ?>><?php echo($aClasse['PROFESSEUR_NOM'] . " - " . $aClasse['CLASSE_ANNEE_SCOLAIRE'] . " - " . $aClasse['CLASSE_NOM']); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<?php if($sNomEleve != null && count($aEleveNoms) > 0): ?>
			<tr>
				<td>Nom de l'&eacute;l&egrave;ve recherch&eacute;</td>
				<td>
					<select id="select_eleve_id" name="ELEVE_ID" onchange="showOrHideSelect('select_eleve_id', 'input_eleve_nom');showOrHideSelect('select_eleve_id', 'input_date_naissance');">
						<option value="0">-- Commence par '<?php echo($sNomEleve); ?>' --</option>
						<?php foreach($aEleveNoms as $nKey => $sValue): ?>
							<option value="<?php echo($nKey); ?>"><?php echo($sValue); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<?php endif; ?>
			<tr id="input_eleve_nom">
				<td>Nom de l'&eacute;l&egrave;ve</td>
				<td><input type="text" size="<?php echo(ELEVE_NOM); ?>" maxlength="<?php echo(ELEVE_NOM); ?>" name="ELEVE_NOM" /></td>
			</tr>
			<tr id="input_date_naissance">
				<td>Date de naissance (jj/mm/aaaa)</td>
				<td>
					<input type="text" size="<?php echo(ELEVE_DATE_NAISSANCE); ?>" maxlength="<?php echo(ELEVE_DATE_NAISSANCE); ?>" id="ELEVE_DATE_NAISSANCE" name="ELEVE_DATE_NAISSANCE" value="jj/mm/aaaa" onfocus="document.getElementById('ELEVE_DATE_NAISSANCE').value='';"/>
					<button id="f_trigger_b1" type="reset">...</button>
					<script type="text/javascript">
					    Calendar.setup({
					        inputField     :    "ELEVE_DATE_NAISSANCE",	// id of the input field
					        ifFormat       :    "%d/%m/%Y",      		// format of the input field
					        showsTime      :    false,           		// will display a time selector
					        button         :    "f_trigger_b1",  		// trigger for the calendar (button ID)
					        singleClick    :    true,           		// single-click mode
					        step           :    1                		// show all years in drop-down boxes (instead of every other year as default)
					    });
					</script>
				</td>
			</tr>
			<tr>
				<td><input type="submit" value="Ajouter" name="action" /></td>
			</tr>
		</tbody>
	</table>
</form>

<?php if($nClasseId != null && $aClasseRow != false): ?>
<table class="list_tree">
	<caption>D&eacute;tails de la classe</caption>
	<thead></thead>
	<tfoot></tfoot>
	<tbody>
		<tr class="level0_row0">
			<th>Classe</th>
			<td><?php echo($aClasseRow['CLASSE_NOM']); ?></td>
		</tr>
		<tr class="level0_row1">
			<th>Ann&eacute;e scolaire</th>
			<td><?php echo($aClasseRow['CLASSE_ANNEE_SCOLAIRE']); ?></td>
		</tr>
		<tr class="level0_row0">
			<th>Professeur</th>
			<td><?php echo($aClasseRow['PROFESSEUR_NOM']); ?></td>
		</tr>
		<tr class="level0_row1">
			<th>Ecole</th>
			<td><?php echo($aClasseRow['ECOLE_NOM']); ?></td>
		</tr>
		<tr class="level0_row0">
			<th>Ville</th>
			<td><?php echo($aClasseRow['ECOLE_VILLE']); ?></td>
		</tr>
		<tr class="level0_row1">
			<th>D&eacute;partement</th>
			<td><?php echo($aClasseRow['ECOLE_DEPARTEMENT']); ?></td>
		</tr>
	</tbody>
</table>
<br />
<table class="list_tree">
	<thead>
		<tr>
			<th>El&egrave;ves</th>
			<th>Dates de naissance</th>
			<th>Activit&eacute;s</th>
		</tr>
	</thead>
	<tfoot>
	</tfoot>
	<tbody>
		<?php foreach($aEleves as $nRowNum => $aEleve): ?>
		<tr class="level0_row<?php echo(($nRowNum)%2); ?>">
			<td><?php echo($aEleve['ELEVE_NOM']); ?></td>
			<td><?php echo($aEleve['ELEVE_DATE_NAISSANCE']); ?></td>
			<td><?php echo($aEleve['ELEVE_ACTIF']); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<table class="formulaire">
	<caption>Informations</caption>
	<tr>
		<td>
			Aucune classe ne correspondant aux critères de sélection n'a été trouvée.
		</td>
	</tr>
</table>
<?php endif; ?>