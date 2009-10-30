<?php
/*
Copyright (C) 2003  Antoine Dumont

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

////////////////////////////////////////////////////////////////////////////
// PRETTY PRINT SQL
////////////////////////////////////////////////////////////////////////////

/**
 * Pretty print pour l'affichage des requÃªtes sql
 * @param $req		requete à afficher
 * @return string	chaine de caractÃ¨re en lisible
 * @author adumont
 */
class PrettyPrint
{
	// prototype non utilisee
	function PrettyPrint(){}

	/**
	 * Execution du prettyprint
	 * @param $req		la requete à afficher de faÃ§on lisible
	 * @param $bypass=0	pour ne pas effectuer de prettyprint
	 */
	function pretty($req, $bypass=0)
	{
		if($bypass === 1) { return $req; }
		$tmp = $req;
		//remplace les chaines de caracteres des tableaux ci-dessus
		$tmp = PrettyPrint::replaceSDL($tmp);
		$tmp = PrettyPrint::replaceSDLET($tmp);
		return $tmp;
	}

	/**
	 * Remplacer les strings du tableau t dans la chaine s par le separateur sep
	 * suivi des strings du tableau t.
	 * @param $t		tableau contenant les chaines a remplacer
	 * @param $sep		separateur a ajouter dans la chaine
	 * @param $s		la chaine de caracteres dans laquelle effectuer les changements
	 * @return string	la chaine modifiee
	 */
	function replace($t, $sep, $s)
	{
		$n = count($t);
		for($i=0;$i<$n;$i++)
		{
			$s = preg_replace('/'.$t[$i].'/i', $sep.$t[$i], $s);
		}
		return $s;
	}

	/**
	 * Remplacer toutes les chaines du tableau SAUT_DE_LIGNE.
	 * @param $s		la chaine a modifier
	 * @return string	la chaine modifiee
	 */
	function replaceSDL($s)
	{
		// Tableau de mots clefs sql.
		$SAUT_DE_LIGNE = array(
			"SELECT ",
			"FROM ",
			"WHERE ",
			"GROUP BY ",
			"ORDER BY ",
			"INSERT INTO ",
			"DELETE ",
			"VALUE ",
			"UPDATE ",
			"SET ",
		);
		return PrettyPrint::replace($SAUT_DE_LIGNE,"\n",$s);
	}

	/**
	 * Remplacer toutes les chaines du tableau SAUT_DE_LIGNE_ET_TABULATION.
	 * @param $s		la chaine a modifier.
	 * @return string	la chaine modifiee
	 */
	function replaceSDLET($s)
	{
		// Ponctuation.
		$SAUT_DE_LIGNE_ET_TABULATION = array(
			"AND ",
			"OR ",
		);
		return PrettyPrint::replace($SAUT_DE_LIGNE_ET_TABULATION, "\n\t", $s);
	}
}
?>