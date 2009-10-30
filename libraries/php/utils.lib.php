<?php
/*
Copyright (C) 2003  Lionel SAURON

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Cette fonction permet d'afficher une variable de maniere lisible en HTML.
 * 
 * Elle se base sur la fonction var_export.
 * 
 * @author Lionel SAURON
 * @version 1.0
 * @public
 * 
 * @param $Var(mixed) La variable e afficher
 */
function var_dump_ext($Var)
{
	echo "<pre>".var_export($Var, true)."</pre>";
}

/**
 * Fonction de debug.
 * @param $sVar	la variable a debugger.
 * @author Antoine Dumont
 */
function debug($sVar){
	echo '<pre style="color:green;font-size:14px;">'.print_r(PrettyPrint::pretty($sVar), true).'</pre>';
}

/**
 * Cette fonction permet de creer une conjonction de coordination "de" ou "d'"
 * en fonction du mois donne en parametre.
 * Cette fonction est e utiliser en franeais.
 * 
 * @author Antoine DUMONT
 * @version 1.0
 * @public
 * 
 * @param $pMois	le mois
 * @return string	la conjonction adequate
 */
function conjunctionFrench($sMois)
{
	$sMois = strtolower($sMois);
	
	$sConjonction = "de ";
	switch($sMois)
	{
		case 'avril':
		case 'aout':
		case 'octobre':
			$sConjonction = "d'";
			break;
	
		default:
			break;
	}
	return $sConjonction;
}

/**
 * Fonction qui en fonction du mois et de l'annee courant, renvoie la restriction
 * sql pour des champs de type annee_scolaire_debut-annee_scolaire_fin
 * Par exemple : 2007-2008 (X-X+1)
 * @return string	restriction sur l'annee scolaire courante 
 */
function sql_annee_scolaire_courante($currentTime = null)
{
	// morceau de requete sql
	$sReq = "";
	// date courante
	if($currentTime == null)
	{
		// si non passe en parametre on l'initialise a la date courante du serveur
		$currentTime = time();
	}
	// mois courant au format numerique (plus facile pour des comparaisons)
	$nMonth = (int) strftime("%m", $currentTime);
	// annee courante au format numerique sur 4 chiffres
	$nYear = (int) strftime("%Y", $currentTime);
	// debut d'annee donc fin d'annee scolaire X-1-X
	if(1 <= $nMonth && $nMonth <= 6)
	{
		// il nous faut l'annee courante -1
		$nYearPrec = $nYear-1;
		// on cree le morceau de requete qui va bien
		$sReq .= "'{$nYearPrec}-{$nYear}'"; 
	} else if(7 <= $nMonth && $nMonth <= 12) {
		// fin d'annee donc debut d'annee scolaire X--X+1
		// il nous faut l'annee courante +1
		$nYearSucc = $nYear+1;
		// on cree le morceau de requete qui va bien
		$sReq .= "'{$nYear}-{$nYearSucc}'"; 
	}
	return $sReq;
}

?>