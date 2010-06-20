/**
 * Cache ou non le menu de gauche
 */
function showOrHideMenu(image_left, image_right)
{
	// Quel est l'état du menu de gauche
	var bIsHidden = $('#struct_left_panel').is(':hidden');
	if(bIsHidden == true)// On montre le menu de gauche
	{
		// On remontre doucement le menu de gauche
		$('#struct_left_panel').fadeIn("slow");
		// On deplace le menu principal
		$('#struct_main').animate({marginLeft: '210px'}, 'slow');
		// On modifie l'image du lien
		$("#img_arrow").attr("src", image_left);
	} else {// On cache le menu de gauche
		// On cache doucement le menu de gauche
		$('#struct_left_panel').fadeOut("slow");
		// On deplace le menu principal
		$('#struct_main').animate({marginLeft: '0'}, 'slow');
		// On modifie l'image du lien
		$("#img_arrow").attr("src", image_right);
	}
}// fin showOrHideMenu

/**
 * Montre un bloc d'id 'id' s'il est cache,
 * le cache s'il est visible.
 */
function showOrHide(id)
{
	$('#' + id).toggle('slow');
}// fin showOrHide

/**
 * Soumission du formulaire en ajax.
 * @param sIdFormulaire	Id du formulaire
 */
function submitAjaxUpdateCommentaire(sIdFormulaire)
{
	// Soumission d'une requete POST en asynchrone 
	$.ajax
	({
		type: 'POST',
		url: "ajax.php?page=commentaires&mode=add_or_update",
		data:$('#' + sIdFormulaire).serialize()
	});														
}// fin submitAjaxUpdateCommentaire

/**
 * Soumission du formulaire en ajax.
 * @param sIdFormulaire	Id du formulaire
 */
function submitAjaxUpdateConseilMaitres(sIdFormulaire)
{
	// Soumission d'une requete POST en asynchrone 
	$.ajax
	({
		type: 'POST',
		url: "ajax.php?page=conseil_maitres&mode=add_or_update",
		data:$('#' + sIdFormulaire).serialize()
	});
}// fin submitAjaxUpdateConseilMaitres
