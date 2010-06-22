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

/**
 * Page de titre du struct_main.
 * @param $sTitle
 * @param $aObjectsToHide
 */
function h1($sTitle, $aObjectsToHide)
{
	// Nom de la classe en fonction de son statut caché ou non
	$sImg = ($aObjectsToHide == false || in_array('struct_left_panel', $aObjectsToHide) == false) ? "arrow_left.png" : "arrow_right.png";
	$sImg = URL_ICONS_16X16 . "/{$sImg}";

	// Calcule le titre de la page 
	$sTitleToDisplay = <<< ____EOS
		<h1><a href="javascript:void(0)" onclick="toggleMenu();"><img id="img_arrow" src="{$sImg}" /></a>{$sTitle}</h1>
____EOS;
	return $sTitleToDisplay;
}// fin h1

?>
