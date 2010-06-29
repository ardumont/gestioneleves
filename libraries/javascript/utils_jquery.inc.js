/**
 * Si le menu est affiché, cache le menu de gauche sinon l'affiche.
 * Le statut est enregistré en bdd pour conserver l'état d'affichage au prochain réaffichage de la page. 
 */
function toggleMenu()
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
		$("#img_arrow").attr("src", 'images/icons/16x16/arrow_left.png');
//		// On stocke le résultat en bdd
//		updateMenu('struct_left_panel', true);
	} else {// On cache le menu de gauche
		// On cache doucement le menu de gauche
		$('#struct_left_panel').fadeOut("slow");
		// On deplace le menu principal
		$('#struct_main').animate({marginLeft: '0'}, 'slow');
		// On modifie l'image du lien
		$("#img_arrow").attr("src", 'images/icons/16x16/arrow_right.png');
//		// On stocke le résultat en bdd
//		updateMenu('struct_left_panel', false);
	}
}// fin toggleMenu

/**
 * Fonction de mise à jour du menu en bdd.
 * @param sId
 */
function updateMenu(sId, bIsHidden)
{
	// Soumission d'une requete POST en asynchrone pour mettre à jour ou non en bdd son statut 
	$.ajax
	({
		type: 'POST'
		,url: "ajax.php?page=menu&mode=add_or_update"
		,data: "statut_hidden=" + ((bIsHidden == true) ? 'false' : 'true') + '&cle=' + sId
//		,success: function(data) {alert(data);}
	});
}// fin updateMenu

/**
 * Montre un bloc d'id 'sId' s'il est cache,
 * le cache s'il est visible.
 * + Mise à jour en bdd des champs.
 */
function showOrHide(sId)
{
	// Quel est l'état du menu de gauche
	var bIsHidden = $('#' + sId).is(':hidden');
	// Cache ou non le menu
	$('#' + sId).toggle('slow');
	// Met à jour les champs en bdd
	updateMenu(sId, bIsHidden);
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
