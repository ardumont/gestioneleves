<?php
// liste des titres
$aTitres = array(
	"Nom",
	"Initiateur",
	"Architecture",
	"Développements",
	"Documentations",
	"Design",
	"Rapport de bugs",
	"Demande d'évolutions",
	"Béta testeurs",
);

// tableau des mainteneurs
// Nom => "Initiateur, Archi, Dév, Doc, Design, bugs, évo, testeurs"
$aMainteneurs = array(
	"Antoine Romain Dumont"	=>	"1,1,1,1,1,1,1,1",
);

// tableau des contributeurs
// Nom => "Initiateur, Archi, Dév, Doc, Design, bugs, évo, testeurs"
$aConstributeurs = array(
	"Christelle Héritier"	=>	"0,0,0,1,0,0,0,1",
);
?>
<h1>Liste des contributeurs</h1>
<br />

<table class="list_tree" style="text-align:center;">
    <caption>Tableau des mainteneurs (ordre alphabétique)</caption>
    <thead>
        <tr><!-- Titre -->
        	<?php foreach($aTitres as $sCategorie): ?>
    		<th><?php echo($sCategorie); ?></th>
        	<?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
    	<?php $n = 0; ?>
    	<?php foreach($aMainteneurs as $sNom => $sValue): ?>
		<tr class="level0_row<?php echo(($n++)%2); ?>">
			<th style="text-align:left;"><?php echo $sNom; ?></th>
			<?php $aRes = explode(",", $sValue); ?>
			<?php foreach($aRes as $b): ?>
			<td><?php echo ($b == 1) ? "X": ""; ?></td>
			<?php endforeach; ?>
		</tr>
    	<?php endforeach; ?>
    </tbody>
</table>
<br />
<table class="list_tree" style="text-align:center;">
    <caption>Tableau des contributeurs (ordre alphabétique)</caption>
    <thead>
        <tr><!-- Titre -->
        	<?php foreach($aTitres as $sCategorie): ?>
    		<th><?php echo($sCategorie); ?></th>
        	<?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
    	<?php $n = 0; ?>
    	<?php foreach($aConstributeurs as $sNom => $sValue): ?>
		<tr class="level0_row<?php echo(($n++)%2); ?>">
			<th style="text-align:left;"><?php echo $sNom; ?></th>
			<?php $aRes = explode(",", $sValue); ?>
			<?php foreach($aRes as $b): ?>
			<td><?php echo ($b == 1) ? "X": ""; ?></td>
			<?php endforeach; ?>
		</tr>
		<?php endforeach; ?>
    </tbody>
</table>