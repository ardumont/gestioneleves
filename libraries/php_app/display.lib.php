<?php

/**
 * Ecrit le code html pour les champs de formulaire obligatoires.
 */
function printHtmlRequiredField()
{
	echo('<abbr class="required_field" title="obligatoire">*</abbr>');
}// fin printHtmlRequiredField

/**
 * Ecrit le code html pour la gestion des objets cachés.
 * @param $aObjectsToHide
 * @param $sNameObject
 * @return string
 */
function manage_display_hidden_objects($aObjectsToHide, $sNameObject)
{
	// Si l'objet n'est pas référencé, on l'affiche
	$sDisplay = ($aObjectsToHide == false || in_array($sNameObject, $aObjectsToHide) == false) ? "" : ' style="display:none;"';
	return $sDisplay;
}// fin manage_display_hidden_objects

?>
