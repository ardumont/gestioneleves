
/**
 * Montre un bloc d'id 'id' s'il est cache,
 * le cache s'il est visible.
 */
function showOrHide(id)
{
	var objElement = document.getElementById(id);
	
	objElement.style.display = (objElement.style.display != 'none') ? 'none' : '';
}

/**
 * Montre l'objet d'id 'id'.
 */
function showId(id)
{
	var objElement = document.getElementById(id);
	objElement.style.display = '';	
}

/**
 * Cache l'objet d'id id.
 */
function hideId(id)
{
	var objElement = document.getElementById(id);
	objElement.style.display = 'none';	
}

/**
 * Affiche les ids du tableau aIds.
 */
function showIds(aIds)
{
	for(var i=0; i<aIds.length; i++)
	{
		showId(aIds[i]);
	}
}

/**
 * Cache les ids du tableau aIds.
 */
function hideIds(aIds)
{
	for(var i=0; i<aIds.length; i++)
	{
		hideId(aIds[i]);
	}
}

/**
 * Montre ou cache qqch en fonction du champ du select.
 */
function showOrHideSelect(select_id, to_hide_id)
{
	// si le champ du select n'est pas a 0, on cache le champ to_hide_id
	if(document.getElementById(select_id).value != 0)
	{
		hideId(to_hide_id);
	} else {// sinon on le montre
		showId(to_hide_id);
	}
}

/**
 * Fonction permettant l'affichage le bloc de gauche.
 * @param idLeft
 * @param idRight	 
 */
function showtab(idLeft, idRight) {
	//FIXME voir si on ne peut pas avoir ces valeurs à la volée
	document.getElementById(idLeft).style.display='block';
	document.getElementById(idRight).style.marginLeft='230px';
	document.getElementById(idRight).style.width='80%';
}

/**
 * Fonction permettant de masquer le bloc de gauche.
 * @param idLeft
 * @param idRight	 
 */
function hidetab(idLeft, idRight) {
	//FIXME voir si on ne peut pas avoir ces valeurs à la volée
	document.getElementById(idLeft).style.display='none'; 
	document.getElementById(idRight).style.margin=0;
	document.getElementById(idRight).style.width='99%';
}

///**
// * Fonction pour cacher le menu de gauche lors du clic sur la flèche.
// * @param idLeft
// * @param idRight
// */
//function showOrHideMenuBlock(idLeft, idRight)
//{
//	// le menu à cacher ou à montrer
//	var objElement = document.getElementById(idLeft);
//	// etat de cet objet
//	var etat = (objElement.style.display != 'none') ? '1' : '0';
//	if(etat == 1)
//	{
//		// modifie l'image pour déplier le menu
//		document.getElementById('img_arrow').src = '<?php echo(URL_ICONS_16X16); ?>/arrow_right.png';
//		hidetab(idLeft, idRight);
//	} else {
//		// modifie l'image pour cacher le menu
//		document.getElementById('img_arrow').src = '<?php echo(URL_ICONS_16X16); ?>/arrow_left.png';
//		showtab(idLeft, idRight);
//	}
//}
