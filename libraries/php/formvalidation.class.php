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
 * Cette classe permet la vérification des formulaires.
 *
 * @author Lionel SAURON
 * @version 2.2.0-beta3
 */
class FormValidation
{

/**
 * Les données lues dans le formulaire et parfois modifiées
 * par les fonctions de test.
 * <code>$aData[Nom du champ] = Valeur du champ</code>
 *
 * @private
 */
var $aData = array();

var $aInformations = array();

var $bStopAll = false;
/**
 * Le nom du dernier champ traité.
 * @private
 */
var $sLastInputName = null;

var $paCurrentInfo = null;

/**
 * Constructeur de la classe.
 *
 * @author Lionel SAURON
 * @public
 */
function FormValidation()
{
}

function setStopAll($bStopAll)
{
	$this->bStopAll = $bStopAll;
}

function initCurrentRef($sInputName, $bCreateData = false)
{
	if($this->bStopAll == true) return;

	// Si null => On reprend les références du dernier champ traité.
	if($sInputName === null) return;

	$sInputName = trim($sInputName);

	// Si vide => On reprend les références du dernier champ traité.
	if($sInputName === "") return;

	// Si on traite le même champ => On reprend les références du dernier champ traité.
	if(($bCreateData == false) && ($sInputName === $this->sLastInputName)) return;

	// Sauvegarde du nom du dernier champ traité.
	$this->sLastInputName = $sInputName;

	// ===== Initialisation du pointeur d'info =====

	$bInitDataRef = false;

	// A t'on déjà ces information ?
	if(array_key_exists($sInputName, $this->aInformations) == false)
	{
		$bInitDataRef = true;

		$this->aInformations[$sInputName] = array
		(
			'DATA'        => null,
			'READ_OK'     => false,
			'TEST_OK'     => true,
			'ERROR_MSG'   => array(),
			'TEST_OPE_OK' => array(),
		);
	}

	$this->paCurrentInfo = &$this->aInformations[$sInputName];

	// ===== Initialisation du pointeur de donnée =====

	if(($bInitDataRef == true) || ($bCreateData == true))
	{
		$bInputNotFound = false;
		$bReadOk = true;

		$aInputName = explode('.', $sInputName);

		// On initialise notre pointeur sur la racine de l'arbre (tableau de donnée)
		$vPointer = &$this->aData;

		foreach($aInputName as $sOneSubInputName)
		{
			if(array_key_exists($sOneSubInputName, $vPointer) == false)
			{
				// On crée l'input dans le tableau ou on le lit ?
				if($bCreateData == true)
				{
					$vPointer[$sOneSubInputName] = null; // On le créé mais vide...

					$bInputNotFound = false;
					$bReadOk = false;

					// On continue
				}
				else
				{
					$bInputNotFound = true;
					$bReadOk = false;
					break;
				}
			}

			// On fait avancer notre pointeur dans l'arbre
			$vPointer = &$vPointer[$sOneSubInputName];
		}

		if($bInputNotFound == false)
		{
			$this->paCurrentInfo['DATA'] = &$vPointer;
			$this->paCurrentInfo['READ_OK'] = $bReadOk;
		}

	} // END if($bInitDataRef == true)
}

/**
 * Enregister le message d'erreur pour un champ et un test donnés.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ en erreur.
 * @param $sTestName(string) Nom du test en erreur.
 * @param $sErrorMsg(string) Message d'erreur à enregister.
 */
function setError($sInputName, $sTestName, $sErrorMsg)
{
	if(trim($sErrorMsg) == "") return;

	$this->initCurrentRef($sInputName);

	$this->paCurrentInfo['ERROR_MSG'][$sTestName] = $sErrorMsg;
}

/**
 * Permet de supprimer des erreurs.
 *
 * - Tous les messages.
 * - Les messages pour un champ.
 * - Les messages pour un champ et un test.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ en erreur.
 * @param $sTestName(string) Nom du test en erreur.
 */
function clearError($sInputName = null, $sTestName = null)
{
	// sInputName = Null ? => Supression de tout les messages
	if($sInputName === null)
	{
		foreach($this->aInformations as $sInputName => $vDummy)
		{
			$this->aInformations[$sInputName]['ERROR_MSG'] = array();
		}

		return;
	}

	// Pour continuer il faut avoir $sInputName valide...
	if(array_key_exists($sInputName, $this->aInformations) == false) return;

	// sTestName = Null ? => Supression de tout les messages de l'input donnée.
	if($sTestName === null)
	{
		$this->aInformations[$sInputName]['ERROR_MSG'] = array();

		return;
	}

	// Pour continuer il faut avoir $sTestName valide...
	if(array_key_exists($sTestName, $this->aInformations[$sInputName]['ERROR_MSG']) == false) return;

	// Supression du message de l'input donnée pour le test donnée.
	unset($this->aInformations[$sInputName]['ERROR_MSG'][$sTestName]);
}

/**
 * Renvoie les messages d'erreurs stockés durant les tests.
 *
 * - Tous les messages.
 * - Les messages pour un champ.
 * - Les messages pour un champ et un test.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ en erreur.
 * @param $sTestName(string) Nom du test en erreur.
 * @return <code>array[Nom du champ][nom du test ayant écrit le message] = Message</code>
 */
function getError($sInputName = null, $sTestName = null)
{
	$aErrorMsg = array();
	$aInputName = array();

	// sInputName = Null ? => Retourne tout les messages
	if($sInputName === null)
	{
		foreach($this->aInformations as $sInputName => $aInfo)
		{
			if(count($aInfo['ERROR_MSG']) > 0)
			{
				$aErrorMsg += array($sInputName => $aInfo['ERROR_MSG']);
			}
		}

		return $aErrorMsg;
	}

	// Pour continuer il faut avoir $sInputName valide...
	if(array_key_exists($sInputName, $this->aInformations) == false) return array();


	// sTestName = Null ? => Retourne tout les messages de l'input donné.
	if($sTestName === null)
	{
		return $this->aInformations[$sInputName]['ERROR_MSG'];
	}

	// Pour continuer il faut avoir $sTestName valide...
	if(array_key_exists($sTestName, $this->aInformations[$sInputName]['ERROR_MSG']) == false) return array();


	// Retourne le message de l'input donné et du test donné.
	return $this->aInformations[$sInputName]['ERROR_MSG'][$sTestName];
}

/**
 * Permet de savoir s'il y a eu des messages d'erreurs.
 *
 * - Tous les messages.
 * - Les messages pour un champ.
 * - Les messages pour un champ et un test.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ en erreur.
 * @param $sTestName(string) Nom du test en erreur.
 * @return <code>true</code> ou <code>false</code>
 */
function hasError($sInputName = null, $sTestName = null)
{
	$aErrorMsg = $this->getError($sInputName, $sTestName);

	$bHasError = (count($aErrorMsg) > 0) ? true : false;

	return $bHasError;
}

/**
 * Lit un champ du formulaire.
 *
 * Cette methode lit un champ dans la source spécifiée, généralement
 * $_GET ou $_POST, mais cela peut être un autre tableau.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ à lire.
 * @param $aSource(array) Tableau contenant les champs ($_GET, $_POST ou autre).
 * @return <code>true</code> ou <code>false</code>.
 */
function read($sInputName, $aSource)
{
	if($this->bStopAll == true) return null;

	// On efface les anciennes données du champ s'il y en avait.
	unset($this->aInformations[$sInputName]);
	$this->sLastInputName = null;

	// On recrée les informations et on force la création de la donnée
	$this->initCurrentRef($sInputName, true);

	// On lit le contenu du champ s'il existe.
	if(array_key_exists($sInputName, $aSource) == true)
	{
		$this->paCurrentInfo['DATA'] = $aSource[$sInputName];
		$this->paCurrentInfo['READ_OK'] = true;
	}

	return $this->paCurrentInfo['READ_OK'];
}

function readVirtual($sInputName)
{
	if($this->bStopAll == true) return null;

	// On efface les anciennes données du champ s'il y en avait.
	unset($this->aInformations[$sInputName]);
	$this->sLastInputName = null;

	// On recrée les informations et on force la création de la donnée
	$this->initCurrentRef($sInputName, true);

	$this->paCurrentInfo['READ_OK'] = true;

	return $this->paCurrentInfo['READ_OK'];
}

function readArray($sInputName, $aSource)
{
	if($this->bStopAll == true) return null;

	// On efface les anciennes données du champ s'il y en avait.
	unset($this->aInformations[$sInputName]);
	$this->sLastInputName = null;

	// On recrée les informations et on force la création de la donnée
	$this->initCurrentRef($sInputName, true);

	$this->paCurrentInfo['DATA'] = $aSource;
	$this->paCurrentInfo['READ_OK'] = true;

	return $this->paCurrentInfo['READ_OK'];
}

/**
 * Renvoie la valeur du champ après avoir passé les tests.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ à lire.
 * @param $DefaultValue(mixed) Valeur par défaut si la lecture ou les tests sont en erreurs.
 * @return Valeur du champ ou <code>null</code> s'il n'existe pas.
 */
function get($sInputName, $DefaultValue = null)
{
	if($this->bStopAll == true) return $DefaultValue;

	// On récupère le vrai nom du champ traité.
	$this->initCurrentRef($sInputName);

	// On retourne la valeur par défaut, si la lecture ou les tests sont en erreurs.
	// On ne retourne pas la valeur par défaut si on a échoué sur un testValue.
	if($this->paCurrentInfo['READ_OK'] == false) return $DefaultValue;

	if(($this->paCurrentInfo['TEST_OK'] == false)
	&& (count($this->paCurrentInfo['ERROR_MSG']) > 0))
	{
		return $DefaultValue;
	}

	return $this->paCurrentInfo['DATA'];
}

/**
 * Lit, teste et renvoie la valeur d'un champ du formulaire.
 *
 * - Effectue un read.
 * - Effectue un testValue0 - exist.
 * - Effectue un testValue0 - empty.
 * - Effectue un testValue0 - <code>$sTestName</code> (un test de type comme is_int, ...).
 * - Effectue un get.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ à lire.
 * @param $aSource(array) Tableau contenant les champs ($_GET, $_POST ou autre).
 * @param $sTestName(string) Nom du test à réaliser.
 * @param $DefaultValue(mixed) Valeur par défaut si le champ n'existe pas.
 * @return Valeur du champ ou <code>null</code> s'il n'existe pas.
 */
function getValue($sInputName, $aSource, $sTestName, $DefaultValue = null)
{
	if($this->bStopAll == true) return $DefaultValue;

	$this->read($sInputName, $aSource);

	$this->testValue0($sInputName, 'exist', $DefaultValue);
	$this->testValue0($sInputName, 'empty', $DefaultValue);
	$this->testValue0($sInputName, $sTestName, $DefaultValue);

	return $this->get($sInputName);
}

/**
 * Détermine si un test réalisé par les fonction test0, test1,
 * est en erreur en fonction de la valeur de sortie.
 *
 * Par défaut, on considère qu'un test est en erreur s'il retourne <code>false</code>
 * sauf pour les cas suivant :
 * - blank : Il y a erreur si le test retourne vrai.
 *
 * @author Lionel SAURON
 * @private
 *
 * @param $sInputName(string) Nom du champ à lire.
 * @param $bTestResult(bool) Résultat de la fonction de test.
 * @return <code>true</code> ou <code>false</code>.
 */
function isTestError($sTestName, $bTestResult)
{
	$bTestError = ($bTestResult == false) ? true : false;

	switch(strtoupper($sTestName))
	{
		case 'BLANK': $bTestError = ($bTestResult == true) ? true : false; break;
	}

	return $bTestError;
}

function testOpeX($sOpeType, $sOpeName = '', $bTestOk = false)
{
	if($this->bStopAll == true) return;

	switch(strtoupper(trim($sOpeType)))
	{
		case '(':
			$this->paCurrentInfo['TEST_OPE_OK'][] = array
			(
				'BLOCK_OPE_TYPE' => 'INIT',
				'TEST_OK'        => true,
			);
		break;

		// ----------
		case ')':
			$aOpeValue = end($this->paCurrentInfo['TEST_OPE_OK']); // Modifie le pointeur interne du tableau
			$nOpeKey   = key($this->paCurrentInfo['TEST_OPE_OK']); // Utilise le pointeur interne du tableau

			// On supprime le block
			unset($this->paCurrentInfo['TEST_OPE_OK'][$nOpeKey]);

			// Est t'on le dernier block ?
			if(count($this->paCurrentInfo['TEST_OPE_OK']) == 0)
			{
				// Plus de block de test => Résultat = résultat du test.
				$this->paCurrentInfo['TEST_OK'] = $aOpeValue['TEST_OK'];
			}
			else
			{
				// On effectue le test avec les résultats du block que l'on vient de fermer
				$sOpeName = $aOpeValue['BLOCK_OPE_TYPE'];
				$bTestOk  = $aOpeValue['TEST_OK'];

				$this->testOpeX('TEST', $sOpeName, $bTestOk);
			}
		break;

		// ----------
		case 'TEST':
			$aOpeValue = end($this->paCurrentInfo['TEST_OPE_OK']); // Modifie le pointeur interne du tableau
			$nOpeKey   = key($this->paCurrentInfo['TEST_OPE_OK']); // Utilise le pointeur interne du tableau

			switch(strtoupper($sOpeName))
			{
				case '':
				case 'INIT':
					$aOpeValue['TEST_OK'] = (boolean)$bTestOk;
				break;

				// ----------
				case 'AND':
					$aOpeValue['TEST_OK'] = (boolean)($aOpeValue['TEST_OK'] & $bTestOk);
				break;

				// ----------
				case 'OR':
					$aOpeValue['TEST_OK'] = (boolean)($aOpeValue['TEST_OK'] | $bTestOk);
				break;

				// ----------
				default:
					$aOpeValue['TEST_OK'] = (boolean)$bTestOk;
			}

			$this->paCurrentInfo['TEST_OPE_OK'][$nOpeKey] = $aOpeValue;
		break;

		// ----------
		default:
	}
}

/**
 * Effectue un test sans paramètres sur un champ du formulaire.
 *
 * - none           = ne teste rien, renvoie <code>true</code> (utlise pour des tests dynamiques).
 * - exist          = teste si le champ existait lors de la lecture.
 * - blank          = teste si le champ est vide (chaine de charactère vide).
 * - is_string      = teste si le type du champ est string.
 * - is_int         = teste si le type du champ est int.
 * - convert_int    = teste si le type du champ est int (stocke la valeur convertie).
 * - is_double      = teste si le type du champ est double.
 * - convert_double = teste si le type du champ est double (stocke la valeur convertie).
 *
 * Renvoie <code>null</code> si les tests ont été stoppés avant
 * ou si le nom du test n'existe pas.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ à lire.
 * @param $sTestName(string) Nom du test à réaliser.
 * @return <code>null</code> ou <code>true</code> ou <code>false</code>.
 */
function test0($sInputName, $sTestName)
{
	if($this->bStopAll == true) return null;

	$this->initCurrentRef($sInputName);

	// Test de l'existence ?
	if(strtoupper($sTestName) == 'EXIST')
	{
		return $this->paCurrentInfo['READ_OK'];
	}

	// Test du résultat de l'opération précedente ?
	if(strtoupper($sTestName) == 'OPERATION')
	{
		return $this->paCurrentInfo['TEST_OK'];
	}

	// On retourne null, si la lecture ou les tests sont en erreurs.
	if($this->paCurrentInfo['READ_OK'] == false) return null;
	if($this->paCurrentInfo['TEST_OK'] == false) return null;

	$bTestOk = false;

	$Value = $this->paCurrentInfo['DATA'];

	switch(strtoupper($sTestName))
	{
		case 'NONE':
			$bTestOk = true;
		break;

		// ----------
		case 'BLANK':
			$bTestOk = ($Value === "") ? true : false;
		break;

		// ----------
		case 'IS_STRING':
			// Est-ce une valeur texte ? Quote simple ou quote double.
			$bTestOk = is_string($Value);
		break;

		case 'IS_INT':
			// On enleve les espace après car sinon il ne peut être considéré comme un entier par is_numeric.
			$Value = trim($Value);

			// Est-ce une valeur numérique ? Format : "+0123.45e6" ou "0xFF".
			if(is_numeric($Value) == false) break;

			// On force un coté du test en int, PHP convertira l'autre en numérique (int ou float).
			$bTestOk = ($Value == (int)$Value) ? true : false;
		break;

		// ----------
		case 'CONVERT_INT':
			// On enleve les espace après car sinon il ne peut être considéré comme un entier par is_numeric.
			$Value = trim($Value);

			// Est-ce une valeur numérique ? Format : "+0123.45e6" ou "0xFF".
			if(is_numeric($Value) == false) break;

			// On force un coté du test en int, PHP convertira l'autre en numérique (int ou float).
			$bTestOk = ($Value == (int)$Value) ? true : false;

			if($bTestOk === true)
				{
				$this->paCurrentInfo['DATA'] = (int)$Value;
				}
		break;

		case 'IS_DOUBLE':
			// On enleve les espace après car sinon il ne peut être considéré comme un double par is_numeric.
			$Value = trim($Value);

			// Est-ce une valeur numérique ? Format : "+0123.45e6" ou "0xFF".
			if(is_numeric($Value) == false) break;

			// On force un coté du test en double, PHP convertira l'autre en numérique (int ou float).
			$bTestOk = ($Value == (double)$Value) ? true : false;
		break;

		// ----------
		case 'CONVERT_DOUBLE':
			// On enleve les espace après car sinon il ne peut être considéré comme un double par is_numeric.
			$Value = trim($Value);

			// Est-ce une valeur numérique ? Format : "+0123.45e6" ou "0xFF".
			if(is_numeric($Value) == false) break;

			// On force un coté du test en double, PHP convertira l'autre en numérique (int ou float).
			$bTestOk = ($Value == (double)$Value) ? true : false;

			if($bTestOk === true)
				{
				$this->paCurrentInfo['DATA'] = (double)$Value;
				}
		break;

		/*case 'CONVERT_BOOL':
			$bNewValue = null;

			if($Value === "true")  $bNewValue = true;
			if($Value === "false") $bNewValue = false;

			if(is_numeric($Value) == true)
			{
				$bNewValue = (boolean)(int)$Value;
			}

			if($bNewValue !== null)
			{
				$this->aData[$sInputName] = $bNewValue;
				$bTestOk = true;
			}
		break;*/

		// ----------
		default:
			return null;
	}

	return $bTestOk;
}

/**
 * Effectue un test sans paramètre sur un champ du formulaire. Affectation d'une valeur par défaut
 * si le test renvoie <code>false</code>.
 *
 * En cas de remplacement par la valeur par défaut, les tests futurs sur ce champ
 * ne seront pas réalisés, sauf si le paramètre <code>$bStopTest</code> vaut <code>false</code>.
 *
 * @warning Le test 'blank' affecte la valeur si le test renvoie <code>true</code>.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ à lire.
 * @param $sTestName(string) Nom du test à réaliser.
 * @param $DefaultValue(mixed) Valeur par défaut si le champ n'existe pas.
 * @param $bStopTest(bool) Permet de ne pas bloquer les tests futurs.
 * @return <code>null</code> ou <code>true</code> ou <code>false</code>.
 */
function testValue0($sInputName, $sTestName, $DefaultValue, $bStopTest = true)
{
	if($this->bStopAll == true) return null;

	$this->initCurrentRef($sInputName);

	$bTestResult = $this->test0($sInputName, $sTestName);
	if($bTestResult === null) return null;

	$bIsError = $this->isTestError($sTestName, $bTestResult);
	if($bIsError == true)
	{
		// On force la création de la donnée
		$this->initCurrentRef($sInputName, true);

		$this->paCurrentInfo['DATA']    = $DefaultValue;
		$this->paCurrentInfo['READ_OK'] = true;
		$this->paCurrentInfo['TEST_OK'] = ($bStopTest == true) ? false : true;
	}

	return $bTestResult;
}

/**
 * Effectue un test sans paramètre sur un champ du formulaire. Stocke un messsage d'erreur pour ce champ.
 * si le test renvoie <code>false</code>.
 *
 * Si le message d'erreur est stocké, les tests futurs sur cette variable
 * ne seront pas réalisés, sauf si le paramètre <code>$bStopTest</code> vaut <code>false</code>.
 *
 * @warning Le test 'blank' stocke le message si le test renvoie <code>true</code>.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ à lire.
 * @param $sTestName(string) Nom du test à réaliser.
 * @param $sErrorMsg(string) Message d'erreur si le champ n'existe pas.
 * @return <code>null</code> ou <code>true</code> ou <code>false</code>.
 */
function testError0($sInputName, $sTestName, $sErrorMsg)
{
	if($this->bStopAll == true) return null;

	$this->initCurrentRef($sInputName);

	$bTestResult = $this->test0($sInputName, $sTestName);
	if($bTestResult === null) return null;

	$bIsError = $this->isTestError($sTestName, $bTestResult);
	if($bIsError == true)
	{
		$this->setError($sInputName, $sTestName, $sErrorMsg);
		$this->paCurrentInfo['TEST_OK'] = false;
	}

	return $bTestResult;
}


function testOpe0($sOpeName, $sInputName, $sTestName)
{
	if($this->bStopAll == true) return null;

	// On conserve les valeurs de l'input relative à l'opération et non au test.
	$sOpeLastInputName = $this->sLastInputName;
	$paOpeCurrentInfo  = &$this->paCurrentInfo;

	$bTestResult = $this->test0($sInputName, $sTestName);
	if($bTestResult === null) return null;

	// On réaffecte les valeurs de l'input relative à l'opération (Le test ayant changé les valeurs).
	$this->sLastInputName = $sOpeLastInputName;
	$this->paCurrentInfo  = &$paOpeCurrentInfo;

	$bIsError = $this->isTestError($sTestName, $bTestResult);
	$bTestOk = (boolean)(!$bIsError);

	$bOpeValue = end($this->paCurrentInfo['TEST_OPE_OK']); // Modifie le pointeur interne du tableau
	$nOpeKey   = key($this->paCurrentInfo['TEST_OPE_OK']); // Utilise le pointeur interne du tableau

	$this->testOpeX('TEST', $sOpeName, $bTestOk);

	return $bTestResult;
}

/**
 * Effectue un test avec 1 paramètre sur un champ du formulaire.
 *
 * - min_value       = teste si la valeur du champ est strictement supérieur à <code>$Param1</code>.
 * - min_value_equal = teste si la valeur du champ est supérieur ou égale à <code>$Param1</code>.
 * - max_value       = teste si la valeur du champ est strictement inférieur à <code>$Param1</code>.
 * - max_value_equal = teste si la valeur du champ est inférieur ou égale à <code>$Param1</code>.
 *
 * Renvoie <code>null</code> si les tests ont été stoppés avant
 * ou si le nom du test n'existe pas.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ à lire.
 * @param $sTestName(string) Nom du test à réaliser.
 * @param $Param1(mixed) Paramètre utilisé lors du test.
 * @return <code>null</code> ou <code>true</code> ou <code>false</code>.
 */
function test1($sInputName, $sTestName, $Param1)
{
	if($this->bStopAll == true) return null;

	$this->initCurrentRef($sInputName);

	// On retourne null, si la lecture ou les tests sont en erreurs.
	if($this->paCurrentInfo['READ_OK'] == false) return null;
	if($this->paCurrentInfo['TEST_OK'] == false) return null;

	$bTestOk = false;

	$Value = $this->paCurrentInfo['DATA'];

	switch(strtoupper($sTestName))
	{
		case 'EQUAL':
			$bTestOk = ($Value == $Param1) ? true : false;
		break;

		// ----------
		case 'EQUAL_STRICT':
			$bTestOk = ($Value === $Param1) ? true : false;
		break;

		// ----------
		case 'MIN_VALUE':
			// On ne compare que des valeurs numériques.
			if(is_numeric($Value)  == false) break;
			if(is_numeric($Param1) == false) break;

			$bTestOk = ($Param1 < $Value) ? true : false;
		break;

		// ----------
		case 'MIN_VALUE_EQUAL':
			// On ne compare que des valeurs numériques.
			if(is_numeric($Value)  == false) break;
			if(is_numeric($Param1) == false) break;

			$bTestOk = ($Param1 <= $Value) ? true : false;
		break;

		// ----------
		case 'MAX_VALUE':
			// On ne compare que des valeurs numériques.
			if(is_numeric($Value)  == false) break;
			if(is_numeric($Param1) == false) break;

			$bTestOk = ($Value < $Param1) ? true : false;
		break;

		// ----------
		case 'MAX_VALUE_EQUAL':
			// On ne compare que des valeurs numériques.
			if(is_numeric($Value)  == false) break;
			if(is_numeric($Param1) == false) break;

			$bTestOk = ($Value <= $Param1) ? true : false;
		break;

		// ----------
		default:
			return null;
	}

	return $bTestOk;
}

/**
 * Effectue un test sans paramètre sur un champ du formulaire. Affectation d'une valeur par défaut
 * si le test renvoie <code>false</code>.
 *
 * En cas de remplacement par la valeur par défaut, les tests futurs sur ce champ
 * ne seront pas réalisés, sauf si le paramètre <code>$bStopTest</code> vaut <code>false</code>.
 *
 * @warning Le test 'blank' affecte la valeur si le test renvoie <code>true</code>.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ à lire.
 * @param $sTestName(string) Nom du test à réaliser.
 * @param $Param1(mixed) Paramètre utilisé lors du test.
 * @param $DefaultValue(mixed) Valeur par défaut si le champ n'existe pas.
 * @param $bStopTest(bool) Permet de ne pas bloquer les tests futurs.
 * @return <code>null</code> ou <code>true</code> ou <code>false</code>.
 */
function testValue1($sInputName, $sTestName, $Param1, $DefaultValue, $bStopTest = true)
{
	if($this->bStopAll == true) return null;

	$this->initCurrentRef($sInputName);

	$bTestResult = $this->test1($sInputName, $sTestName, $Param1);
	if($bTestResult === null) return null;

	$bIsError = $this->isTestError($sTestName, $bTestResult);
	if($bIsError == true)
	{
		// On force la création de la donnée
		$this->initCurrentRef($sInputName, true);

		$this->paCurrentInfo['DATA']    = $DefaultValue;
		$this->paCurrentInfo['READ_OK'] = true;
		$this->paCurrentInfo['TEST_OK'] = ($bStopTest == true) ? false : true;
	}

	return $bTestResult;
}

/**
 * Effectue un test sans paramètre sur un champ du formulaire. Stocke un messsage d'erreur pour ce champ.
 * si le test renvoie <code>false</code>.
 *
 * Si le message d'erreur est stocké, les tests futurs sur cette variable
 * ne seront pas réalisés, sauf si le paramètre <code>$bStopTest</code> vaut <code>false</code>.
 *
 * @warning Le test 'blank' stocke le message si le test renvoie <code>true</code>.
 *
 * @author Lionel SAURON
 * @public
 *
 * @param $sInputName(string) Nom du champ à lire.
 * @param $sTestName(string) Nom du test à réaliser.
 * @param $Param1(mixed) Paramètre utilisé lors du test.
 * @param $sErrorMsg(string) Message d'erreur si le champ n'existe pas.
 * @return <code>null</code> ou <code>true</code> ou <code>false</code>.
 */
function testError1($sInputName, $sTestName, $Param1, $sErrorMsg)
{
	if($this->bStopAll == true) return null;

	$this->initCurrentRef($sInputName);

	$bTestResult = $this->test1($sInputName, $sTestName, $Param1);
	if($bTestResult === null) return null;

	$bIsError = $this->isTestError($sTestName, $bTestResult);
	if($bIsError == true)
	{
		$this->setError($sInputName, $sTestName, $sErrorMsg);
		$this->paCurrentInfo['TEST_OK'] = false;
	}

	return $bTestResult;
}

function testOpe1($sOpeName, $sInputName, $sTestName, $Param1)
{
	if($this->bStopAll == true) return null;

	// On conserve les valeurs de l'input relative à l'opération et non au test.
	$sOpeLastInputName = $this->sLastInputName;
	$paOpeCurrentInfo  = &$this->paCurrentInfo;

	$bTestResult = $this->test1($sInputName, $sTestName, $Param1);
	if($bTestResult === null) return null;

	// On réaffecte les valeurs de l'input relative à l'opération (Le test ayant changé les valeurs).
	$this->sLastInputName = $sOpeLastInputName;
	$this->paCurrentInfo  = &$paOpeCurrentInfo;

	$bIsError = $this->isTestError($sTestName, $bTestResult);
	$bTestOk = (boolean)(!$bIsError);

	$bOpeValue = end($this->paCurrentInfo['TEST_OPE_OK']); // Modifie le pointeur interne du tableau
	$nOpeKey   = key($this->paCurrentInfo['TEST_OPE_OK']); // Utilise le pointeur interne du tableau

	$this->testOpeX('TEST', $sOpeName, $bTestOk);

	return $bTestResult;
}

}
?>
