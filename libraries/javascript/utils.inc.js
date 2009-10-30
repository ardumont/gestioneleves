
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
