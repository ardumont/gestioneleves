<?php
//==============================================================================
// Preparation des donnees
//==============================================================================

//==============================================================================
// Validation du formulaire
//==============================================================================

$objForm = new FormValidation();

$sAction = $objForm->getValue('action', $_POST, 'is_string', "");

// si le champ ELEVE_NOM est specifie alors il s'agit d'un ajout d'un nouvel 
// via la zone de texte de saisie
// pas de test de verification a cause du module de recherche
$sNomEleve = $objForm->getValue('ELEVE_NOM', $_POST, 'is_string');

// recuperation de l'id de l'eleve une fois qu'il a ete trouve
// (via module de recherche)
$nEleveId = $objForm->getValue('ELEVE_ID', $_POST, 'convert_int');

// creer la liaison entre cet eleve et sa nouvelle classe
$objForm->read('CLASSE_ID', $_POST);
$objForm->testError0(null, 'exist',	"Il manque le champ CLASSE_ID !");
$objForm->testError0(null, 'blank',	"Il manque l'id de la classe !");
$objForm->testError0(null, 'is_int',"L'id de la classe doit &ecirc;tre un entier !");
$nClasseId = $objForm->get(null);

// creer la liaison entre cet eleve et sa nouvelle classe
$objForm->read('ELEVE_ID', $_POST);
$nEleveId = $objForm->get(null);

// si l'eleve n'existe pas
if($nEleveId == null)
{
	// creer la liaison entre cet eleve et sa nouvelle classe
	$objForm->read('ELEVE_DATE_NAISSANCE', $_POST);
	$objForm->testError0(null, 'exist',		"Il manque le champ ELEVE_DATE_NAISSANCE !");
	$objForm->testError0(null, 'blank',		"Il manque la date de naissance !");
	$objForm->testError0(null, 'is_string',	"La date de naissance doit &ecirc;tre une cha&icirc;ne de caract&egrave;res !");
	$sEleveDateNaissance = $objForm->get(null);
}

// validation du formulaire
if($sNomEleve == null && $nEleveId == null)
{// s'il manque a la fois l'id (issue de la recherche) et le nom (issu de la saisie)
	Message::addError("Il manque le nom ou l'id de l'&eacute;l&egrave;ve !");
	// Rechargement
	header("Location: ?page=eleves&mode=add&classe_id={$nClasseId}");
	// cas d'erreur on s'arrete
	return;
}

//==============================================================================
// Action du formulaire
//==============================================================================

switch(strtolower($sAction))
{
	// ajoute l'eleve
	case 'ajouter':
		if($objForm->hasError() == true) break;

		// si on a pas envoye l'id d'un eleve, il faut creer ce nouvel eleve
		if($nEleveId == null)
		{
			// on verifie quand meme que le nom de l'eleve n'existe pas deja
			$sQuery =
				"SELECT ELEVE_ID " .
				"FROM ELEVES " .
				"WHERE UPPER(ELEVE_NOM) = " .Database::prepareString(strtoupper($sNomEleve));
			$nEleveId = Database::fetchOneValue($sQuery);
			// $nEleveId = ELEVE_ID

			// l'eleve n'existe pas
			if($nEleveId == false)
			{
				// insertion de l'eleve dans la table ELEVES
				$sQuery =
					"INSERT INTO ELEVES (ELEVE_NOM, ELEVE_DATE_NAISSANCE)" .
					"VALUES(" . 
						 Database::prepareString($sNomEleve) . ", " .
						"STR_TO_DATE(" . Database::prepareString($sEleveDateNaissance) . ", '%d/%m/%Y') " . 
				")";
				Database::execute($sQuery);

				// recupere son id dans la table ELEVES
				$sQuery =
					"SELECT ELEVE_ID " .
					"FROM ELEVES " .
					"WHERE ELEVE_NOM = " .Database::prepareString($sNomEleve);
				$nEleveId = Database::fetchOneValue($sQuery);
			}
		}

		// verification que l'eleve n'existe pas deja dans la classe
		$sQuery =
			" SELECT ID_ELEVE, ID_CLASSE " .
			" FROM ELEVE_CLASSE " .
			" WHERE ID_ELEVE = {$nEleveId}" .
			" AND ID_CLASSE = {$nClasseId} ";
		$aEleves = Database::fetchArray($sQuery);

		// si l'eleve n'appartient pas deja a la classe alors on l'ajoute
		if(count($aEleves) <= 0)
		{
			// ajout de l'eleve dans la classe
			$sQuery =
				"INSERT INTO ELEVE_CLASSE (ID_ELEVE, ID_CLASSE)" .
				"VALUES({$nEleveId}, {$nClasseId})";
			Database::execute($sQuery);
		} else {// sinon il existe deja, alors un petit message pour signaler sa preexistence
			Message::addError("L'&eacute;l&egrave;ve \"{$sNomEleve}\" existe d&eacute;j&agrave; pour cette classe !");
		}

		// rechargement de la liste des eleves
		header("Location: ?page=eleves&mode=add&classe_id={$nClasseId}");
		return;
	break;

	// ----------
	case 'annuler':
		$objForm->clearError();

		// Rechargement
		header("Location: ?page=eleves&mode=add");
		return;
	break;

	// ----------
	default:
		$objForm->clearError();
		Message::addError("L'action \"{$sAction}\" est inconnue !");
}

//==============================================================================
// Traitement des donnees
//==============================================================================

//==============================================================================
// Preparation de l'affichage
//==============================================================================

//==============================================================================
// Affichage de la page
//==============================================================================

// On stocke toutes les erreurs de formulaire.
Message::addErrorFromFormValidation($objForm->getError());

// Rechargement
header("Location: ?page=eleves&mode=add");
return;

?>
