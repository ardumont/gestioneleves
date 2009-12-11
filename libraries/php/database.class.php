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
 * Cette classe permet de dialoguer avec la base de données (Mysql).
 *
 * Elle gère :
 * - La connexion et la déconnexion à la base.
 * - L'éxecution et la récupération des données.
 * - Un gestionnaire d'erreur redirigeable.
 *
 * @author Lionel SAURON
 * @version 2.3
 */
class Database
{
	/**
	 * Identifiant de connexion.
	 */
	private static $s_hConnection = null;

	/**
	 * Methode du gestionnaire d'erreur
	 */
	static $s_sErrorHandler = array("Database", "defaultErrorHandler");

	/**
	 * Constructeur de la classe.
	 *
	 * @author Lionel SAURON
	 */
	private function Database()
	{
	}

	/**
	 * Permet de remplacer le gestionnaire d'erreur par défaut.
	 *
	 * - Le gestionnaire d'erreurs doit être une fonction prenant un tableau en paramètres.
	 * - Pour appeller une méthode statique d'une classe :
	 *   $sErrorHandler = array("Nom de la classe", "Nom de la méthode").
	 *
	 * @author Lionel SAURON
	 * @since 2.0
	 *
	 * @param $sErrorHandler(string) Le nom de la fonction à appeller.
	 */
	public static function setErrorHandler($sErrorHandler)
	{
		self::$s_sErrorHandler = $sErrorHandler;
	}

	/**
	 * Gestionnaire d'erreurs par défaut.
	 *
	 * Le tableau reçu en paramètres :
	 * - $aError['code']    = Le numéro d'erreur Oracle.
	 * - $aError['message'] = Le message d'erreur.
	 * - $aError['sqltext'] = La requête ayant déclanché l'erreur.
	 *
	 * @author Lionel SAURON
	 * @since 2.0
	 *
	 * @param $aError(array) Tableau décrivant l'erreur.
	 */
	private static function defaultErrorHandler($aError)
	{
		print($aError['code']." : ".$aError['message']."\n#".$aError['sqltext']."#<br>");
	}

	/**
	 * Envoie une erreur au gestionnaire d'erreurs.
	 *
	 * @author Lionel SAURON
	 * @since 2.0
	 *
	 * @param $hResource(handle ou null) Handle de la ressource ayant produit l'erreur.
	 */
	private static function sendError($hResource, $sSqlQuery)
	{
		if($hResource === null)
		{
			$nErrorNo  = mysql_errno();
			$sErrorMsg = mysql_error();
		}
		else
		{
			$nErrorNo  = mysql_errno($hResource);
			$sErrorMsg = mysql_error($hResource);
		}

		if($sErrorMsg == "") return;

		$sFunctionName = self::$s_sErrorHandler;

		$aError['code']    = $nErrorNo;
		$aError['message'] = $sErrorMsg;
		$aError['sqltext'] = $sSqlQuery;

		call_user_func($sFunctionName, $aError);
	}

	/**
	 * Fonction de connexion à la base de données.
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sLogin(string) L'identifiant pour se connecter à la base de données.
	 * @param $sPassword(string) Le mot de passe pour se connecter à la base de données.
	 * @param $sDatabaseName(string) Le nom de la base de données.
	 * @param $sHost(string) Le nom ou l'IP du serveur de base de données.
	 * @return <code>true</code> ou <code>false</code>
	 */
	public static function openConnection($sLogin, $sPassword, $sDatabaseName, $sHost)
	{
		// Connexion au serveur
		$hConnection = @mysql_connect($sHost, $sLogin, $sPassword);
		if($hConnection === false)
		{
			self::sendError(null, "mysql_connect");
			return false;
		}

		self::$s_hConnection = $hConnection;

		$bOk = @mysql_select_db($sDatabaseName, $hConnection);
		if($bOk == false)
		{
			self::sendError($hConnection, "mysql_select_db");
			return false;
		}
		return true;
	}

	/**
	 * Ferme la connexion à la base de données.
	 *
	 * @author Lionel SAURON
	 *
	 * @param $hConnection(connection_handle) Connexion à fermer.
	 * @return <code>true</code> ou <code>false</code>.
	 */
	public static function closeConnection($hConnection = null)
	{
		if($hConnection === null)
		{
			$hConnection = self::$s_hConnection;
		}

		$bOk = @mysql_close($hConnection);
		if($bOk == false)
		{
			self::sendError($hConnection, "mysql_close");
			return false;
		}

		return true;
	}

	/**
	 * Permet d'enlever tout les caractères non supportés par Oracle.
	 *
	 * Cette fonction <b>doit</b> être appelée pour chaque <code>INSERT</code>
	 * et chaque <code>UPDATE</code> de chaine de caractère en base.
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sText(string) Chaine de caractère à traiter.
	 * @return Chaine échappé.
	 */
	public static function escapeString($sText)
	{
		// Cheater pour les membres static en php 4
		$hConnection = self::$s_hConnection;

		if(get_magic_quotes_gpc() == true)
		{
			$sText = stripslashes($sText);
		}

		$sText = mysql_real_escape_string($sText, $hConnection);

		return $sText;
	}

	/**
	 * Prépare une chaine pour une requête Oracle.
	 * - Ne fait rien si la chaine est égale à "NULL" (case insensitive).
	 * - Permet d'enlever tout les caractères non supporté par Oracle.
	 * - Ajoute les ' si necessaire au début et à la fin (sauf si la chaine vaut "NULL" ).
	 *
	 * Cette fonction <b>doit</b> être appelée pour chaque <code>INSERT</code>
	 * et chaque <code>UPDATE</code> de chaine de caractère en base.
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sText(string) Chaine de caractère à traiter.
	 * @return Chaine préparée.
	 */
	public static function prepareString($sText)
	{
		if($sText === null) return "null";
		if(strtoupper(trim($sText)) == "NULL") return $sText;

		$sText = "'".self::escapeString($sText)."'";

		return $sText;
	}

	/**
	 * Parse et éxecute la requete pour les fetch.
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sSQLQuery(string) La requète à executer.
	 * @param $hConnection(connection_handle) Identifiant de connexion à utiliser pour la requête.
	 * @return <code>false</code> ou la ressource de commande.
	 */
	private static function prepareAndExecuteQuery($sSQLQuery, $hConnection)
	{
		$hStatement = @mysql_query($sSQLQuery, $hConnection);
		if($hStatement === false)
		{
			self::sendError($hConnection, $sSQLQuery);
			return false;
		}

		return $hStatement;
	}

	/**
	 * Execute la requète.
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sSQLQuery(string) La requète à executer.
	 * @param $hConnection(connection_handle) (Optionel) Identifiant de connexion à utiliser pour la requête
	 *        (sinon, utilisation de la connexion par défaut).
	 * @return <code>false</code> ou le nombre de ligne traités.
	 */
	public static function execute($sSQLQuery, $hConnection = null)
	{
		if($hConnection === null)
		{
			$hConnection = self::$s_hConnection;
		}

		$hStatement = @mysql_query($sSQLQuery, $hConnection);
		if($hStatement === false)
		{
			self::sendError($hConnection, $sSQLQuery);
			return false;
		}

		$nNbRow = @mysql_affected_rows($hConnection);
		if($nNbRow === false)
		{
			self::sendError($hConnection, "mysql_affected_rows");
			return false;
		}

		return $nNbRow;
	}

	/**
	 * Execute la requète et retourne toutes les lignes de données.
	 *
	 * Un tableau à 2 dimensions est retourné (format array[][Nom de colonne]) :
	 * - 1ère dimension : Les lignes en base (Incrément automatique).
	 * - 2ème dimension : Le nom des colonnes.
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sSQLQuery(string) La requète à executer.
	 * @param $hConnection(connection_handle) (Optionel) Identifiant de connexion à utiliser pour la requête.
	 *        (sinon, utilisation de la connexion par défaut).
	 * @return <code>false</code> ou le tableau de données lues.
	 */
	public static function fetchArray($sSQLQuery, $hConnection = null)
	{
		if($hConnection === null)
		{
			$hConnection = self::$s_hConnection;
		}

		$hStatement = self::prepareAndExecuteQuery($sSQLQuery, $hConnection);
		if($hStatement === false) return false;

		$aResult = array();
		$aOneRow = array();

		$aOneRow = mysql_fetch_array($hStatement, MYSQL_ASSOC);
		while($aOneRow !== false)
		{
			$aResult[] = $aOneRow;

			$aOneRow = mysql_fetch_array($hStatement, MYSQL_ASSOC);
		}
		return $aResult;
	}

	/**
	 * Execute la requète et retourne toutes les lignes de données en utilisant
	 * une clef.
	 *
	 * Un tableau à 2 dimensions est retourné (format array[Valeur de la colonne $sKey][Nom de colonne]) :
	 * - 1ère dimension : Les lignes en base indexées par le paramètre sKey
	 * - 2ème dimension : Le nom des colonnes autres que la colonne servant de clef.
	 *
	 * Si la clef n'est pas considérée comme unique, le format de sortie devient :
	 * array[Valeur de la colonne $sKey][][Nom de colonne] = Valeur
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sSQLQuery(string) La requète à executer.
	 * @param $sKey(string) Nom du champ de la requête devant servir de clef de ligne.
	 * @param $bUnique(boolean) (defaut = true) La clef est considérée comme unique.
	 * @param $hConnection(connection_handle) (Optionel) Identifiant de connexion à utiliser pour la requête.
	 *        (sinon, utilisation de la connexion par défaut).
	 * @return <code>false</code> ou le tableau de données lues.
	 */
	public static function fetchArrayWithKey($sSQLQuery, $sKey, $bUnique = true, $hConnection = null)
	{
		if($hConnection === null)
		{
			$hConnection = self::$s_hConnection;
		}

		$hStatement = self::prepareAndExecuteQuery($sSQLQuery, $hConnection);
		if($hStatement === false) return false;

		$aResult = array();
		$aOneRow = array();

		$aOneRow = mysql_fetch_array($hStatement, MYSQL_ASSOC);
		while($aOneRow !== false)
		{
			$RowKey = $aOneRow[$sKey];
			unset($aOneRow[$sKey]);

			if($bUnique == true)
			{
				$aResult[$RowKey] = $aOneRow;
			}
			else
			{
				$aResult[$RowKey][] = $aOneRow;
			}

			$aOneRow = mysql_fetch_array($hStatement, MYSQL_ASSOC);
		}

		return $aResult;
	}

	/**
	 * Execute la requète et retourne toutes les lignes de données en utilisant
	 * plusieurs colonnes comme clefs.
	 *
	 * Un tableau à X dimension est retourné (format array[...Valeur colonne $aKeys...][Nom de colonne] = Valeur) :
	 * - 1ère dimension : La valeur de la colonne données par le 1er élément de $aKeys.
	 * - 2ère dimension : La valeur de la colonne données par le 2ème élément de $aKeys.
	 * - etc ...
	 * - Dernière dimension : Le nom des colonnes autres que la colonne servant de clef.
	 *
	 * Si la clef n'est pas considérée comme unique, le format de sortie devient :
	 * array[...Valeur colonne $aKeys...][][Nom de colonne] = Valeur
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sSQLQuery(string) La requète à executer.
	 * @param $aKeys(array) Listes des noms des champs de la requête devant servir de clefs.
	 * @param $bUnique(boolean) (defaut = true) La clef est considérée comme unique.
	 * @param $hConnection(connection_handle) (Optionel) Identifiant de connexion à utiliser pour la requête.
	 *        (sinon, utilisation de la connexion par défaut).
	 * @return <code>false</code> ou le tableau de données lues.
	 */
	public static function fetchArrayWithMultiKey($sSQLQuery, $aKeys, $bUnique = true, $hConnection = null)
	{
		if($hConnection === null)
		{
			$hConnection = self::$s_hConnection;
		}

		$hStatement = self::prepareAndExecuteQuery($sSQLQuery, $hConnection);
		if($hStatement === false) return false;

		$aResult = array();
		$aOneRow = array();

		$aOneRow = mysql_fetch_array($hStatement, MYSQL_ASSOC);
		while($aOneRow !== false)
		{
			// On sépare les colonnes qui doivent être considérés comme clef ou comme données
			// Attention à l'ordre, c'est $aKeys qui donne l'ordre, donc en 1er dans la fonction
			$aQueryKeys = array_intersect($aKeys, array_keys($aOneRow));

			$aKeyValue = array();
			$aDataValue = array();
			foreach($aOneRow as $sKey => $sValue)
			{
				$nPosition = array_search($sKey, $aQueryKeys, true); // Renvoie la clef dans le tableau

				if($nPosition !== false)
				{
					$aKeyValue[$nPosition] = $sValue;
				}
				else
				{
					$aDataValue[$sKey] = $sValue;
				}
			}

			// On initialise notre pointeur sur la racine de l'arbre (tableau de résultat)
			$Pointeur = &$aResult;

			// On parcours nos indexs (on trie les clefs ayant été crée à la main)
			ksort($aKeyValue);
			foreach($aKeyValue as $sValue)
			{
				// Si notre index n'est pas dans le tableau, on l'ajoute
				if(isset($Pointeur[$sValue]) == false) $Pointeur[$sValue] = array();

				// On fait avancer notre pointeur dans l'arbre (notre tableau résultat)
				$Pointeur = &$Pointeur[$sValue];
			}

			// On a fait tout les index, donc maintenant on place la feuille (nos valeurs)
			if($bUnique == true)
			{
				$Pointeur = $aDataValue;
			}
			else
			{
				$Pointeur[] = $aDataValue;
			}

			$aOneRow = mysql_fetch_array($hStatement, MYSQL_ASSOC);
		}

		return $aResult;
	}

	/**
	 * Execute la requète et retourne toutes les lignes de données de la première colonne seulement.
	 *
	 * Un tableau à 1 dimension est retourné (format array[]) :
	 * - 1ère dimension : Les lignes en base.
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sSQLQuery(string) La requète à executer.
	 * @param $hConnection(connection_handle) (Optionel) Identifiant de connexion à utiliser pour la requête.
	 *        (sinon, utilisation de la connexion par défaut).
	 * @return <code>false</code> ou le tableau de donnée lue.
	 */
	public static function fetchColumn($sSQLQuery, $hConnection = null)
	{
		if($hConnection === null)
		{
			$hConnection = self::$s_hConnection;
		}

		$hStatement = self::prepareAndExecuteQuery($sSQLQuery, $hConnection);
		if($hStatement === false) return false;

		$aResult = array();
		$aOneRow = array();

		$aOneRow = mysql_fetch_array($hStatement, MYSQL_NUM);
		while($aOneRow !== false)
		{
			$aResult[] = $aOneRow[0];

			$aOneRow = mysql_fetch_array($hStatement, MYSQL_NUM);
		}

		return $aResult;
	}

	/**
	 * Execute la requète et retourne toutes les lignes de données en utilisant
	 * le premier champ de la requête comme clef, le deuxième champ comme valeur.
	 *
	 * Un tableau à 1 dimension est retourné (format array[valeur 1ère colonne] = valeur 2ème colonne) :
	 * - 1ère dimension : La valeur de la 1ère colonne.
	 *
	 * Si la clef n'est pas considérée comme unique, le format de sortie devient :
	 * array[valeur 1ère colonne][] = valeur 2ème colonne
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sSQLQuery(string) La requête à éxecuter.
	 * @param $bUnique(boolean) (defaut = true) La clef est considérée comme unique.
	 * @param $hConnection(connection_handle) (Optionel) Identifiant de connexion à utiliser pour la requête.
	 *        (sinon, utilisation de la connexion par défaut).
	 * @return <code>false</code> ou le tableau de donnée lue.
	 */
	public static function fetchColumnWithKey($sSQLQuery, $bUnique = true, $hConnection = null)
	{
		if($hConnection === null)
		{
			$hConnection = self::$s_hConnection;
		}

		$hStatement = self::prepareAndExecuteQuery($sSQLQuery, $hConnection);
		if($hStatement === false) return false;

		$aResult = array();
		$aOneRow = array();

		$aOneRow = mysql_fetch_array($hStatement, MYSQL_NUM);
		while($aOneRow !== false)
		{
			$sKey = $aOneRow[0];

			if($bUnique == true)
			{
				$aResult[$sKey] = $aOneRow[1];
			}
			else
			{
				$aResult[$sKey][] = $aOneRow[1];
			}

			$aOneRow = mysql_fetch_array($hStatement, MYSQL_NUM);
		}

		return $aResult;
	}

	/**
	 * Execute la requète et retourne toutes les lignes de données en utilisant
	 * les premiers champs de la requête comme clef, le dernier champ comme valeur.
	 *
	 * Un tableau à X dimension est retourné (format array[valeur 1ère colonne][...] = valeur dernière colonne) :
	 * - 1ère dimension : La valeur de la 1ère colonne.
	 * - 2ère dimension : La valeur de la 2ème colonne.
	 * - etc ...
	 *
	 * Si la clef n'est pas considérée comme unique, le format de sortie devient :
	 * array[valeur 1ère colonne][...valeur Xème colonne...][] = valeur dernière colonne
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sSQLQuery(string) La requête à éxecuter.
	 * @param $bUnique(boolean) (defaut = true) La clef est considérée comme unique.
	 * @param $hConnection(connection_handle) (Optionel) Identifiant de connexion à utiliser pour la requête.
	 *        (sinon, utilisation de la connexion par défaut).
	 * @return <code>false</code> ou le tableau de donnée lue.
	 */
	public static function fetchColumnWithMultiKey($sSQLQuery, $bUnique = true, $hConnection = null)
	{
		if($hConnection === null)
		{
			$hConnection = self::$s_hConnection;
		}

		$hStatement = self::prepareAndExecuteQuery($sSQLQuery, $hConnection);
		if($hStatement === false) return false;

		$aResult = array();
		$aOneRow = array();

		$aOneRow = mysql_fetch_array($hStatement, MYSQL_NUM);
		while($aOneRow !== false)
		{
			// On recupère le dernier élément que l'on enleve à la ligne lu en base
			$aResultOneRow = array_pop($aOneRow);

			// On initialise notre pointeur sur la racine de l'arbre (tableau de résultat)
			$Pointeur = &$aResult;

			// On parcours notre ligne lu en base, il ne reste que les indexs
			foreach($aOneRow as $sValue)
			{
				// Si notre index n'est pas dans le tableau, on l'ajoute
				if(isset($Pointeur[$sValue]) == false) $Pointeur[$sValue] = array();

				// On fait avancer notre pointeur dans l'arbre (notre tableau résultat)
				$Pointeur = &$Pointeur[$sValue];
			}

			// On a fait tout les index, donc maintenant on place la feuille (notre valeur)
			if($bUnique == true)
			{
				$Pointeur = $aResultOneRow;
			}
			else
			{
				$Pointeur[] = $aResultOneRow;
			}

			$aOneRow = mysql_fetch_array($hStatement, MYSQL_NUM);
		}

		return $aResult;
	}

	/**
	 * Execute la requète et retourne la 1ère ligne des données.
	 *
	 * Un tableau est retourné, le nom des colonnes sert de clef (format array[Nom de colonne] = Valeur) :
	 * - 1ère dimension : Le nom des colonnes.
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sSQLQuery(string) La requête à éxecuter.
	 * @param $hConnection(connection_handle) (Optionel) Identifiant de connexion à utiliser pour la requête.
	 *        (sinon, utilisation de la connexion par défaut).
	 * @return <code>false</code> ou un tableau représentant la 1ère ligne de donnée lue.
	 */
	public static function fetchOneRow($sSQLQuery, $hConnection = null)
	{
		if($hConnection === null)
		{
			$hConnection = self::$s_hConnection;
		}

		$hStatement = self::prepareAndExecuteQuery($sSQLQuery, $hConnection);
		if($hStatement === false) return false;

		$aOneRow = array();

		$aOneRow = mysql_fetch_array($hStatement, MYSQL_ASSOC);
		if($aOneRow === false) return false;

		return $aOneRow;
	}

	/**
	 * Execute la requète et retourne la 1ère valeur de la 1ère colonne.
	 *
	 * @author Lionel SAURON
	 *
	 * @param $sSQLQuery(string) La requête à executer.
	 * @param $hConnection(connection_handle) (Optionel) Identifiant de connexion à utiliser pour la requête.
	 *        (sinon, utilisation de la connexion par défaut).
	 * @return <code>false</code> ou la données lue.
	 */
	public static function fetchOneValue($sSQLQuery, $hConnection = null)
	{
		if($hConnection === null)
		{
			$hConnection = self::$s_hConnection;
		}

		$hStatement = self::prepareAndExecuteQuery($sSQLQuery, $hConnection);
		if($hStatement === false) return false;

		$aOneRow = array();

		$aOneRow = mysql_fetch_array($hStatement, MYSQL_NUM);
		if($aOneRow === false) return false;

		return $aOneRow[0];
	}

	/**
	 * Retourne le dernier identifiant inséré en bdd dans la connexion ouverte à la bdd
	 *
	 * @author Antoine Romain DUMONT
	 *
	 * @param $hConnection
	 * @return <code>int</code>
	 */
	public static function lastInsertId($hConnection = null)
	{
		if($hConnection === null)
		{
			$hConnection = self::$s_hConnection;
		}
		return mysql_insert_id($hConnection);
	}// end lastInsertId
}
?>