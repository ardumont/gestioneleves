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

if(!defined("FORMAT_DATE_FR")) { define("FORMAT_DATE_FR", "%d/%m/%Y %H:%M"); }

/**
 * Cette fonction permet d'afficher une variable de manière lisible en HTML.
 * Elle se base sur la fonction var_export.
 *
 * @author Lionel SAURON
 * @version 1.0
 * @public
 *
 * @param $vVar(mixed) La variable à afficher
 */
function var_dump_ext($vVar)
{
	echo("<pre>" . var_export($vVar, true) . "</pre>");
}// fin var_dump_ext

/**
 * Cette fonction permet d'afficher une variable de manière lisible en HTML.
 * Elle se base sur la fonction var_dump_ext.
 * @param $mVar		Variable à débugger
 * @param $sMsg		Message descriptif de la variable
 */
function debug($mVar, $sMsg = "")
{
	echo("<div style=\"border: 1px solid red;font-size: 0.9em;\">$sMsg<br />");
	var_dump_ext($mVar);
	echo("</div>");
}// fin debug

/**
 * Cette fonction permet d'afficher une variable date (timestamp) de manière lisible en HTML.
 * Elle se base sur les fonctions debug et strftime.
 * @param $nDate	Date timestamp
 * @param $sFormat	Format auquel on veut la voir s'afficher
 * @param $sMsg		Message descriptif
 */
function debug_date($nDate, $sFormat = FORMAT_DATE_FR, $sMsg = "")
{
	// Formatte la date avec le format desire
	$sFormattedDate = strftime($sFormat, $nDate);
	// Debug en html
	debug($sFormattedDate, $sMsg);
}//fin debug_date

/**
 * Cette fonction permet de créer une conjonction de coordination "de" ou "d'"
 * en fonction du mois donné en paramètre.
 * Cette fonction est à utiliser en français.
 *
 * @author Antoine Romain DUMONT
 * @version 1.0
 * @public
 *
 * @param $sMois(string)	le mois
 * @return (string)			la conjonction adéquate
 */
function conjunctionFrench($sMois)
{
	$sMois = strtolower($sMois);

	$sConjonction = "de ";
	switch($sMois)
	{
		case 'avril':
		case 'août':
		case 'octobre':
			$sConjonction = "d'";
			break;

		default:
			break;
	}
	return $sConjonction;
}//fin conjunctionFrench

/**
 * Fonction qui en fonction du mois et de l'annee courante, renvoie la restriction
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
}// fin sql_annee_scolaire_courante

/**
 * Parse a time/date generated with strftime().
 *
 * This function is the same as the original one defined by PHP (Linux/Unix only),
 *  but now you can use it on Windows too.
 *
 * @author Lionel SAURON
 * @version 1.0
 * @public
 *
 * @param $sDate(string)	The string to parse (e.g. returned from strftime()).
 * @param $sFormat(string)	The format used in date  (e.g. the same as used in strftime()).
 * @return (array)			Returns an array with the <code>$sDate</code> parsed, or <code>false</code> on error.
 */
if(function_exists("strptime") == false)
{
	function strptime($sDate, $sFormat)
	{
		$aResult = array
		(
			'tm_sec'   => 0,
			'tm_min'   => 0,
		    'tm_hour'  => 0,
		    'tm_mday'  => 1,
		    'tm_mon'   => 0,
		    'tm_year'  => 0,
		    'tm_wday'  => 0,
		    'tm_yday'  => 0,
		    'unparsed' => $sDate,
		);

		while($sFormat != "")
		{
			// ===== Search a %x element, Check the static string before the %x =====
			$nIdxFound = strpos($sFormat, '%');
			if($nIdxFound === false)
			{

				// There is no more format. Check the last static string.
				$aResult['unparsed'] = ($sFormat == $sDate) ? "" : $sDate;
				break;
			}

			$sFormatBefore = substr($sFormat, 0, $nIdxFound);
			$sDateBefore   = substr($sDate,   0, $nIdxFound);

			if($sFormatBefore != $sDateBefore) break;

			// ===== Read the value of the %x found =====
			$sFormat = substr($sFormat, $nIdxFound);
			$sDate   = substr($sDate,   $nIdxFound);

			$aResult['unparsed'] = $sDate;

			$sFormatCurrent = substr($sFormat, 0, 2);
			$sFormatAfter   = substr($sFormat, 2);

			$nValue = -1;
			$sDateAfter = "";
			switch($sFormatCurrent)
			{
				case '%S': // Seconds after the minute (0-59)

					sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

					if(($nValue < 0) || ($nValue > 59)) return false;

					$aResult['tm_sec']  = $nValue;
					break;

				// ----------
				case '%M': // Minutes after the hour (0-59)
					sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

					if(($nValue < 0) || ($nValue > 59)) return false;

					$aResult['tm_min']  = $nValue;
					break;

				// ----------
				case '%H': // Hour since midnight (0-23)
					sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

					if(($nValue < 0) || ($nValue > 23)) return false;

					$aResult['tm_hour']  = $nValue;
					break;

				// ----------
				case '%d': // Day of the month (1-31)
					sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

					if(($nValue < 1) || ($nValue > 31)) return false;

					$aResult['tm_mday']  = $nValue;
					break;

				// ----------
				case '%m': // Months since January (0-11)
					sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

					if(($nValue < 1) || ($nValue > 12)) return false;

					$aResult['tm_mon']  = ($nValue - 1);
					break;

				// ----------
				case '%Y': // Years since 1900
					sscanf($sDate, "%4d%[^\\n]", $nValue, $sDateAfter);

					if($nValue < 1900) return false;

					$aResult['tm_year']  = ($nValue - 1900);
					break;

				// ----------
				default: break 2; // Break Switch and while
			}

			// ===== Next please =====
			$sFormat = $sFormatAfter;
			$sDate   = $sDateAfter;

			$aResult['unparsed'] = $sDate;

		} // END while($sFormat != "")


		// ===== Create the other value of the result array =====
		$nParsedDateTimestamp = mktime($aResult['tm_hour'], $aResult['tm_min'], $aResult['tm_sec'],
								$aResult['tm_mon'] + 1, $aResult['tm_mday'], $aResult['tm_year'] + 1900);

		// Before PHP 5.1 return -1 when error
		if(($nParsedDateTimestamp === false)
		||($nParsedDateTimestamp === -1)) return false;

		$aResult['tm_wday'] = (int) strftime("%w", $nParsedDateTimestamp); // Days since Sunday (0-6)
		$aResult['tm_yday'] = (strftime("%j", $nParsedDateTimestamp) - 1); // Days since January 1 (0-365)

		return $aResult;
	} // END of function
} // END if(function_exists("strptime") == false)

/**
 * Transforme la date $sDate au format $sFormat en timestamp.
 * @param $sDate	date à partir de laquelle construire notre timestamp
 * @param $sFormat	format de la date (par défaut "%d/%m/%Y")
 * @return <int>	le timestamp correspondant
 */
function strptime_timestamp($sDate, $sFormat = "%d/%m/%Y")
{
	$aParsedDate = strptime($sDate, $sFormat);

	if( ($aParsedDate === false) || ($aParsedDate['unparsed'] != "") ) return false;

	$nParsedDateTimestamp = mktime($aParsedDate['tm_hour'], $aParsedDate['tm_min'], $aParsedDate['tm_sec'],
								   $aParsedDate['tm_mon'] + 1, $aParsedDate['tm_mday'], $aParsedDate['tm_year'] + 1900);
	return $nParsedDateTimestamp;
}//fin strptime_timestamp

/**
 * Test si le jour est un week end.
 * @return <boolean>
 */
function is_week_end($dDateStart)
{
	return in_array(date('w', $dDateStart), array(0, 6));
}// fin is_week_end

/**
 * Fonction de tests d'un jour.
 * @param $dDate
 * @param $aJoursFeries
 * @return <boolean>
 */
function is_bank_holiday($dDate, $aJoursFeries)
{
	return in_array(date('d/m/' . date('Y', $dDate), $dDate), $aJoursFeries);
}// fin is_bank_holiday

/**
 * Retourne le tableau des jours fériés compris dans la période [$dDateStart, $dDateEnd] pour la France.
 * Attention :
 * - pas de vérification sur les dates
 * - les jours fériés sont calculés directement par ce code.
 * @param $dDateStart
 * @param $dDateEnd
 * @return <array>
 */
function get_bank_holidays_with_computations($dDateStart, $dDateEnd)
{
	$aBankHolidays = array(); // Tableau des jours feriés
	// On boucle dans le cas où l'année de départ serait différente de l'année d'arrivée
	$diff_year = date('Y', $dDateEnd) - date('Y', $dDateStart);
	for ($i = 0; $i <= $diff_year; $i++)
	{
		$year = (int) date('Y', $dDateStart) + $i;
		// Liste des jours fériés
		$aBankHolidays[] = '01/01/' . $year; // Jour de l'an
		$aBankHolidays[] = '01/05/' . $year; // Fete du travail
		$aBankHolidays[] = '08/05/' . $year; // Victoire 1945
		$aBankHolidays[] = '14/07/' . $year; // Fete nationale
		$aBankHolidays[] = '15/08/' . $year; // Assomption
		$aBankHolidays[] = '01/11/' . $year; // Toussaint
		$aBankHolidays[] = '11/11/' . $year; // Armistice 1918
		$aBankHolidays[] = '25/12/' . $year; // Noel
		// Récupération de Pâques .  Permet ensuite d'obtenir le jour de l'ascension et celui de la Pentecôte
		$easter = easter_date($year);
		$aBankHolidays[] = date('d/m/' . $year, $easter + 86400); // Pâques
		$aBankHolidays[] = date('d/m/' . $year, $easter + (86400*39)); // Ascension
		$aBankHolidays[] = date('d/m/' . $year, $easter + (86400*50)); // Pentecôte
	}
	return $aBankHolidays;
}// fin get_bank_holidays_with_computations

/**
 * Retourne le tableau des jours fériés compris dans la période [$dDateStart, $dDateEnd] pour le pays $sPays.
 * Attention :
 * - pas de vérification sur les dates
 * @param $dDateStart	Date de début
 * @param $dDateEnd		Date de fin
 * @param $sPays		Les jours fériés du pays désiré (FR, UK, etc...)
 * @return <array>
 */
function get_bank_holidays($dDateStart, $dDateEnd, $sPays = "FR")
{
	// En route vers la récupération des jours fériés à partir de la bdd
	$sPaysQuery = Database::prepareString($sPays);

	// ===== La liste des jours fériés =====
	$sQuery = <<< ____EOQ
		SELECT PARAMS_BH_ID, PARAMS_BH_DATE, PARAMS_BH_NAME, PARAMS_BH_PAYS
		FROM PARAMS_BANK_HOLIDAYS
		WHERE PARAMS_BH_PAYS = {$sPaysQuery}
		ORDER BY PARAMS_BH_DATE
____EOQ;
	$aBankHolidaysBDD = Database::fetchArray($sQuery);
	// $aBankHolidays[][Nom de colonne] = Valeur

	$nDiffYear = date('Y', $dDateEnd) - date('Y', $dDateStart);
	// On boucle dans le cas où l'année de départ serait différente de l'année d'arrivée
	for ($i=0; $i<=$nDiffYear; $i++)
	{
		$nYear = (int) date('Y', $dDateStart) + $i;
		foreach($aBankHolidaysBDD as $oBankHoliday)
		{
			// Liste des jours fériés
			$aBankHolidays[] = "{$oBankHoliday['PARAMS_BH_DATE']}/{$nYear}"; // Jour de l'an
		}

		// Pour la france, gestion des dates variables
		if($sPays == "FR")
		{
			// Pâques...
			$dEaster = easter_date($nYear);
			$aBankHolidays[] = date('d/m/' . $nYear, $dEaster + 86400); // Pâques
			// ... qui ensuite permet d'obtenir le jour de l'ascension et celui de la Pentecôte
			$aBankHolidays[] = date('d/m/' . $nYear, $dEaster + (86400*39)); // Ascension
			$aBankHolidays[] = date('d/m/' . $nYear, $dEaster + (86400*50)); // Pentecôte
		}
	}

	return $aBankHolidays;
}// fin get_bank_holidays

/**
 * Retourne le nombre de jours ouvrés entre 2 dates.
 * Prend en compte les jours fériés français.
 * @param $dDateStart	Date de début (timestamp)
 * @param $dDateEnd		Date de fin (timestamp)
 * @return <int>	Nombre de jours ouvrés
 */
function nb_working_days($dDateStart, $dDateEnd)
{
	// Récupère la liste des jours fériés
	$aBankHolidays = get_bank_holidays($dDateStart, $dDateEnd);

	$nNbOpenDays = 0;
	while ($dDateStart <= $dDateEnd)
	{
		// Si le jour suivant n'est ni un dimanche (0) ou un samedi (6), ni un jour férié, on incrémente les jours ouvrés
		if (!is_week_end($dDateStart) && !is_bank_holiday($dDateStart, $aBankHolidays))
		{
			$nNbOpenDays++;
		}
		$dDateStart += 86400;
	}
	return $nNbOpenDays;
}// fin nb_working_days

/**
 * Retourne le nombre de jours ouvrés entre 2 dates.
 * Ne prend pas en compte les jours fériés français.
 * S'il est nécessaire de prendre en compte les jours fériés, cf.  méthode get_nb_open_days.
 * @param $dDateStart	Date de début (timestamp)
 * @param $dDateEnd		Date de fin (timestamp)
 * @return <int>	Nombre de jours ouvrés
 */
function nb_days_without_week_end($dDateStart, $dDateEnd)
{
	$nNbOpenDays = 0;
	while ($dDateStart <= $dDateEnd)
	{
		// Si le jour suivant n'est ni un dimanche (0) ou un samedi (6)
		if (!is_week_end($dDateStart))
		{
			$nNbOpenDays++;
		}
		$dDateStart += 86400;
	}
	return $nNbOpenDays;
}// fin nb_days_without_week_end

/**
 * Fonction de remplacement du caractère SEPARATOR_DECIMAL_DOT par le caractère SEPARATOR_DECIMAL_COMMA.
 * @param $sChaine
 * @return string
 */
function replace_separator_decimal($sChaine)
{
	$sRes = $sChaine;
	if(is_numeric($sRes))
	{
		$sRes = str_replace(SEPARATOR_DECIMAL_DOT, SEPARATOR_DECIMAL_COMMA, $sChaine);
	}
	return $sRes;
}// fin replace_separator_decimal
