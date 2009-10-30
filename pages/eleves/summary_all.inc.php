<?php
//==============================================================================
// Prï¿½paration des donnï¿½es
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objFormActivity = new FormValidation();

$nDate = $objFormActivity->getValue('date', $_GET, 'convert_int', time());

//==============================================================================
// Actions du formulaire
//==============================================================================

//==============================================================================
// Traitement des donnï¿½es
//==============================================================================

// On positionne le dï¿½but de pï¿½riode le 1er du mois.
$nDateStartPeriode = $nDate;

$nDateStartPeriode = strtotime(strftime("%Y-%m-1", $nDateStartPeriode));

// On positionne la fin de pï¿½riode le dernier jour du mois
$nDateEndPeriode = strtotime("next month", $nDateStartPeriode);
$nDateEndPeriode = strtotime("-1 day", $nDateEndPeriode);

$nDayByPeriode = (int)strftime("%d", $nDateEndPeriode);

//==============================================================================
// Prï¿½paration de l'affichage
//==============================================================================

$sMois  = strftime("%B", $nDateStartPeriode);
$sAnnee = strftime("%Y", $nDateStartPeriode);

$sConjonction = conjunctionFrench($sMois);

// Titre
$sGuiTitle = "Activitï¿½ du mois {$sConjonction}{$sMois} {$sAnnee}";

// Mois prï¿½cï¿½dent, mois suivant
$nGuiPreviousPeriode = strtotime("-1 month", $nDateStartPeriode);
$nGuiNextPeriode     = strtotime("+1 month", $nDateStartPeriode);

// ===== La liste complï¿½te des utilisateurs =====

$sQuery =
	"SELECT" .
	"  USER_ID, USER_NAME" .
	" FROM USERS " .
	" WHERE USER_ACTIF = 1 " .
	" ORDER BY USER_NAME ASC";
$aUsers = Database::fetchArray($sQuery);
// $aUsers[COLONNE] = VALEUR

foreach($aUsers as $oUsers)
{
	// ===== La liste des activitï¿½s (applications , projets et tï¿½ches) =====
	$sQuery =
		"SELECT" .
		"  APP_NAME," .
		"  APP_ID," .
		"  round((PROJECT_BUDGET_TIME - PROJECT_ACTIVITY_DURATION) / 8, 2) PROJECT_BUDGET_REMAINS, " .
		"  nullif(round(PROJECT_BUDGET_TIME / 8, 2), 0) PROJECT_BUDGET_TIME," .
		"  round(PROJECT_ACTIVITY_DURATION / 8, 2) PROJECT_ACTIVITY_DURATION," .
		"  PROJECT_SHORT_NAME," .
		"  PROJECT_NAME," .
		"  PROJECT_ID," .
		"  PROJECT_PERCENT_COMPLETE," .
		"  nullif(round(sum(ACTIVITY_DURATION / 8), 2), 0) ACTIVITY_DURATION" .
		" FROM ACTIVITIES, TASKS, PROJECTS, APPLICATIONS" .
		" WHERE PROJECT_APP_ID = APP_ID" .
		"   AND TASK_PROJECT_ID = PROJECT_ID" .
		"   AND TASK_ID = ACTIVITY_TASK_ID" .
		"   AND ACTIVITY_DATE >= ".strftime("'%Y-%m-%d'", $nDateStartPeriode) .
		"   AND ACTIVITY_DATE <= ".strftime("'%Y-%m-%d'", $nDateEndPeriode) .
		"   AND ACTIVITY_USER_ID = ".$oUsers['USER_ID'] .
		" GROUP BY APP_ID, PROJECT_ID" .
		" ORDER BY APP_NAME, PROJECT_SHORT_NAME";
	$aActivities[$oUsers['USER_ID']] = Database::fetchArray($sQuery);
	// $aActivities[COLONNE] = VALEUR

	// ===== Sommes de l'activitï¿½s =====
	$sQuery =
		"SELECT" .
		"  round(sum(ACTIVITY_DURATION / 8), 2)" .
		" FROM ACTIVITIES" .
		" WHERE ACTIVITY_DATE >= ".strftime("'%Y-%m-%d'", $nDateStartPeriode) .
		"   AND ACTIVITY_DATE <= ".strftime("'%Y-%m-%d'", $nDateEndPeriode) .
		"   AND ACTIVITY_USER_ID = ".$oUsers['USER_ID'];
	$fActivitiesSum[$oUsers['USER_ID']] = Database::fetchOneValue($sQuery);
}//endforeach

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1>L'activitï¿½</h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<div style="text-align:center;">
	<a href="?page=activities&amp;mode=summary_all&amp;date=<?php echo($nGuiPreviousPeriode); ?>">
		<img src="<?php echo(URL_ICONS_32X32); ?>/2_left_arrow.png" alt="Mois prï¿½cï¿½dent" />
	</a>
	<?php echo($sGuiTitle); ?>
	<a href="?page=activities&amp;mode=summary_all&amp;date=<?php echo($nGuiNextPeriode); ?>">
		<img src="<?php echo(URL_ICONS_32X32); ?>/2_right_arrow.png" alt="Mois suivant" />
	</a>
	<a href="?page=activities&amp;mode=summary_all">
		<img src="<?php echo(URL_ICONS_32X32); ?>/today.png" alt="Aujourd'hui" />
	</a>
</div>

<?php foreach($aUsers as $oUsers): ?>
	<h2><?php echo $oUsers['USER_NAME']; ?></h2>
	<?php if(count($aActivities[$oUsers['USER_ID']]) > 0): ?>
		<table>
			<thead>
				<tr>
					<th title="Applications principales auxquelles sont rattachï¿½es les projets">Applications</th>
					<th colspan="2" title="Les projets rattachï¿½s aux applications">Projets</th>
					<th title="Budget saisie pour le projet ou la tï¿½che">Budget total (j)</th>
					<th title="Activitï¿½ totale saisie par tout le monde sur le projet ou la tï¿½che">Activitï¿½ totale (j)</th>
					<th title="Budget restant pour le projet ou la tï¿½che">Budget restant (j)</th>
					<th title="Activitï¿½ saisie par le collaborateur pour le mois en cours">Activitï¿½ du mois (j)</th>
					<th title="Avancement total sur le projet ou la tï¿½che">Avancement</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="6" style="text-align:right;">Total :</th>
					<td><?php echo($fActivitiesSum[$oUsers['USER_ID']]); ?></td>
					<td></td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach($aActivities[$oUsers['USER_ID']] as $nRowNum => $aOneActivity): ?>
				<tr class="ligne<?php echo($nRowNum % 2); ?>">
					<td><a href="?page=applications&amp;mode=edit&amp;app_id=<?php echo($aOneActivity['APP_ID']); ?>"><?php echo($aOneActivity['APP_NAME']); ?></a></td>
					<td><?php echo($aOneActivity['PROJECT_SHORT_NAME']); ?></td>
					<td><a href="?page=projects&amp;mode=view&amp;project_id=<?php echo($aOneActivity['PROJECT_ID']); ?>"><?php echo($aOneActivity['PROJECT_NAME']); ?></a></td>
					<td><?php echo($aOneActivity['PROJECT_BUDGET_TIME']); ?></td>
					<td><?php echo($aOneActivity['PROJECT_ACTIVITY_DURATION']); ?></td>
					<td><?php echo($aOneActivity['PROJECT_BUDGET_REMAINS']); ?></td>
					<td><?php echo($aOneActivity['ACTIVITY_DURATION']); ?></td>
					<td><?php echo($aOneActivity['PROJECT_PERCENT_COMPLETE']); ?> %</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else: ?>
		<p>Aucune activitï¿½ n'a ï¿½tï¿½ saisie pour le moment.</p>
	<?php endif; ?>
<?php endforeach; ?>

<br />
<ul>
	<li><a href="special.php?page=export_all&amp;date=<?php echo($nDateStartPeriode); ?>">Export de l'activitï¿½ de l'ï¿½quipe au format csv.</a></li>
</ul>
