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

// ===== Rï¿½cupï¿½re le nom de l'utilisateur en cours ===== 
$sQuery =
	"SELECT USER_NAME " .
	"FROM USERS " .
	"WHERE USER_ID=".$_SESSION['user_id'];

$sUserName = Database::fetchOneValue($sQuery);

// ===== La liste des activitï¿½s (applications , projets et tï¿½ches) =====
$sQuery = "SELECT" .
		  "  APP_NAME," .
		  "  APP_ID," .
		  "  PROJECT_ID,".
		  "  round((PROJECT_BUDGET_TIME - PROJECT_ACTIVITY_DURATION) / 8, 2) PROJECT_BUDGET_REMAINS, " .
		  "  nullif(round(PROJECT_BUDGET_TIME / 8, 2), 0) PROJECT_BUDGET_TIME," .
		  "  round(PROJECT_ACTIVITY_DURATION / 8, 2) PROJECT_ACTIVITY_DURATION," .
		  "  PROJECT_SHORT_NAME," .
		  "  PROJECT_NAME," .
		  "  PROJECT_PERCENT_COMPLETE," .
		  "  nullif(round(sum(ACTIVITY_DURATION / 8), 2), 0) ACTIVITY_DURATION" .
		  " FROM ACTIVITIES, TASKS, PROJECTS, APPLICATIONS" .
		  " WHERE PROJECT_APP_ID = APP_ID" .
		  "   AND TASK_PROJECT_ID = PROJECT_ID" .
		  "   AND TASK_ID = ACTIVITY_TASK_ID" .
		  "   AND ACTIVITY_DATE >= ".strftime("'%Y-%m-%d'", $nDateStartPeriode) .
		  "   AND ACTIVITY_DATE <= ".strftime("'%Y-%m-%d'", $nDateEndPeriode) .
		  "   AND ACTIVITY_USER_ID = ".$_SESSION['user_id'] .
		  " GROUP BY APP_ID, PROJECT_ID" .
		  " ORDER BY APP_NAME, PROJECT_SHORT_NAME";
$aActivities = Database::fetchArray($sQuery);
// $aActivities[][COLONNE] = VALEUR

// ===== Sommes des activitï¿½s pour le mois en cours et pour l'utilisateur $_SESSION['user_id'] =====
$sQuery = "SELECT" .
		  "  round(sum(ACTIVITY_DURATION / 8), 2)" .
		  " FROM ACTIVITIES" .
		  " WHERE ACTIVITY_DATE >= ".strftime("'%Y-%m-%d'", $nDateStartPeriode) .
		  "   AND ACTIVITY_DATE <= ".strftime("'%Y-%m-%d'", $nDateEndPeriode) .
		  "   AND ACTIVITY_USER_ID = ".$_SESSION['user_id'];
// directement la valeur
$fActivitiesSum = Database::fetchOneValue($sQuery);

//==============================================================================
// Prï¿½paration de l'affichage
//==============================================================================

$sMois  = strftime("%B", $nDateStartPeriode);
$sAnnee = strftime("%Y", $nDateStartPeriode);

$sConjonction = conjunctionFrench($sMois);

// Titre
$sGuiTitle = "Activitï¿½ Mensuelle de {$sUserName}";
$sGuiTableTitle = "Activitï¿½ du mois {$sConjonction}{$sMois} {$sAnnee}";

// Mois prï¿½cï¿½dent, mois suivant
$nGuiPreviousPeriode = strtotime("-1 month", $nDateStartPeriode);
$nGuiNextPeriode     = strtotime("+1 month", $nDateStartPeriode);

//==============================================================================
// Affichage de la page
//==============================================================================
?>
<h1><?php echo($sGuiTitle); ?></h1>

<?php if(Message::hasError() == true): ?>
<ul class="form_error">
	<?php foreach(Message::getErrorAndClear() as $sErrorMessage): ?>
	<li><?php echo($sErrorMessage); ?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
<br />
<div style="text-align:center;">
	<a href="?page=activities&amp;mode=summary&amp;date=<?php echo($nGuiPreviousPeriode); ?>">
		<img src="<?php echo(URL_ICONS_32X32); ?>/2_left_arrow.png" alt="Mois prï¿½cï¿½dent" />
	</a>
	<?php echo($sGuiTableTitle); ?>
	<a href="?page=activities&amp;mode=summary&amp;date=<?php echo($nGuiNextPeriode); ?>">
		<img src="<?php echo(URL_ICONS_32X32); ?>/2_right_arrow.png" alt="Mois suivant" />
	</a>
	<a href="?page=activities&amp;mode=summary">
		<img src="<?php echo(URL_ICONS_32X32); ?>/today.png" alt="Aujourd'hui" />
	</a>
</div>
<br />
<?php if(count($aActivities) > 0): ?>
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
			<td><?php echo($fActivitiesSum); ?></td>
			<td>&nbsp;</td>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($aActivities as $aOneActivity): ?>
			<tr class="ligne0">
				<td><a href="?page=applications&amp;mode=edit&amp;app_id=<?php echo($aOneActivity['APP_ID']); ?>"><?php echo($aOneActivity['APP_NAME']); ?></a></td>
				<td><?php echo($aOneActivity['PROJECT_SHORT_NAME']); ?></td>
				<td><a href="?page=projects&amp;mode=view&amp;project_id=<?php echo($aOneActivity['PROJECT_ID']); ?>"><?php echo($aOneActivity['PROJECT_NAME']); ?></a></td>
				<td><?php echo($aOneActivity['PROJECT_BUDGET_TIME']); ?></td>
				<td><?php echo($aOneActivity['PROJECT_ACTIVITY_DURATION']); ?></td>
				<td><?php echo($aOneActivity['PROJECT_BUDGET_REMAINS']); ?></td>
				<td><?php echo($aOneActivity['ACTIVITY_DURATION']); ?></td>
				<td><?php echo($aOneActivity['PROJECT_PERCENT_COMPLETE']); ?> %</td>
			</tr>
			<?php
			// ===== La liste des tï¿½ches du projet $aOneActivity['PROJECT_ID'] =====
			$sQuery =
				"SELECT " .
				"  TASKS.TASK_NAME," .
				"  TASKS.TASK_ID," .
				"  TASKS.TASK_PERCENT_COMPLETE," .
				"  round(SUM(ACTIVITY_DURATION) / 8, 2) ACTIVITY_DURATION, " .
				"  round(TASKS.TASK_ACTIVITY_DURATION / 8, 2) TASK_ACTIVITY_DURATION," .				
				"  round((TASKS.TASK_BUDGET_TIME - TASKS.TASK_ACTIVITY_DURATION) / 8, 2) TASK_BUDGET_REMAINS," .				
				"  nullif(round(TASKS.TASK_BUDGET_TIME / 8, 2), 0) TASK_BUDGET_TIME," .
				"  USERS.USER_NAME" .
				" FROM ACTIVITIES, TASKS, USERS" .
				" WHERE ACTIVITY_TASK_ID=TASKS.TASK_ID " .
				"   AND ACTIVITIES.ACTIVITY_USER_ID=USERS.USER_ID" .
				"   AND ACTIVITY_DATE >= ".strftime("'%Y-%m-%d'", $nDateStartPeriode) .
				"   AND ACTIVITY_DATE <= ".strftime("'%Y-%m-%d'", $nDateEndPeriode) .
				"   AND TASKS.TASK_PROJECT_ID=" . $aOneActivity['PROJECT_ID'] .
				"   AND USERS.USER_ID=" . $_SESSION['user_id'] .
				" GROUP BY TASKS.TASK_ID, USERS.USER_ID";
			$aTasks = Database::fetchArray($sQuery);
			//$aTasks[][COLONNE] = VALEUR

			?>
			<?php $i=0; ?>
			<?php foreach($aTasks as $aTask): ?>
				<tr class="ligne1<?php echo (($i++)%2); ?>">
					<td>&nbsp;</td>
					<td style="text-align:left;" colspan="2"><a href="?page=tasks&amp;mode=edit&amp;task_id=<?php echo($aTask['TASK_ID']); ?>"><?php echo($aTask['TASK_NAME']); ?></td>
					<td><?php echo($aTask['TASK_BUDGET_TIME']); ?></td>
					<td><?php echo($aTask['TASK_ACTIVITY_DURATION']); ?></td>
					<td><?php echo($aTask['TASK_BUDGET_REMAINS']); ?></td>
					<td><?php echo($aTask['ACTIVITY_DURATION']); ?></td>
					<td><?php echo($aTask['TASK_PERCENT_COMPLETE']); ?> %</td>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</tbody>
</table>

<br />
<ul>
	<li><a href="special.php?page=export&amp;date=<?php echo($nDateStartPeriode); ?>">Rï¿½capitulatif de l'activitï¿½ mensuelle de l'utilisateur <?php echo($sUserName); ?> au format csv</a></li>
	<li><a href="special.php?page=export_detail&amp;date=<?php echo($nDateStartPeriode); ?>">Rï¿½capitulatif de l'activitï¿½ mensuelle dï¿½taillï¿½e de l'utilisateur <?php echo($sUserName); ?> au format csv</a></li>
</ul>
<?php else: ?>
	<p>Aucune activitï¿½ n'a ï¿½tï¿½ saisie pour le moment.</p>
<?php endif; ?>
