<?php
/**
 * Importe le contenu du fichier $sNomFichier dans la base.<br />
 * Ceci concerne uniquement l'import des cycles / niveaux / domaines / matieres / competences.<br />
 * Les lignes du fichier sont de la forme :<br />
 * cycle;niveau;domaine;matiere;competence_0;competence_1;...;competence_n<br />
 * La premiere ligne du fichier contient cette ligne descriptive.<br />
 * Il faut donc l'eviter lors du parsing.
 * @param $sNomFichier	Nom du fichier a parser
 */
function import_cndmcs($sNomFichier)
{
	// recupere le contenu du fichier
	$aLigneFichier = file($sNomFichier);
	// parcours le fichier
	foreach($aLigneFichier as $i => $sLigne)
	{
		////////////////
		// CAS IGNORE //
		////////////////

		// on evite la premiere ligne qui est une ligne descriptive du contenu
		if($i === 0)
		{
			continue;
		}

		// ligne vide on continue
		if(strcmp($sLigne, "") === 0 || strcmp($sLigne, "\n") === 0)
		{
			continue;
		}

		//////////////////////////////////////////////
		// RECUPERATION DES DONNEES LIGNE PAR LIGNE //
		//////////////////////////////////////////////

		// sinon on explose cette ligne en tableau suivant les ;
		$aDonnees = explode(";", str_replace("\n", "", utf8_decode($sLigne)));

		////////////////////////////
		// TRAITEMENT DES DONNEES //
		////////////////////////////

		// a partir de la commence le traitement specifique
		// donc pour chacun des types de donnees, on regarde si la donnee existe
		// en base.
		// si oui on recupere son id, sinon on l'integre dans la base
		// puis on recupere son id et on s'en sert pour integrer la donnee suivante

		////////////
		// CYCLES //
		////////////
		// 0 -> cycle
		$nCycleId = ajoute_cycle($aDonnees[0]);

		/////////////
		// NIVEAUX //
		/////////////
		// 1 -> niveau
		$nNiveauId = ajoute_niveau($nCycleId, $aDonnees[1]);

		//////////////
		// DOMAINES //
		//////////////
		// 2 -> domaine
		$nDomaineId = ajoute_domaine($nCycleId, $aDonnees[2]);

		//////////////
		// MATIERES //
		//////////////
		// 3 -> matiere
		if($aDonnees[3] != null)
		{
			$nMatiereId = ajoute_matiere($nDomaineId, $aDonnees[3]);
	
			/////////////////
			// COMPETENCES //
			/////////////////
			// 4 -> competence_1
			// ...
			// n -> competence_n
			$aCompetences = array_slice($aDonnees, 4);
			// pour toutes les competences qui existent, ...
			foreach($aCompetences as $sNomCompetence)
			{
				// ... on ajoute la competence $sNomCompetence pour la matiere d'id 
				// $nMatiereId
				ajoute_competence($nMatiereId, $sNomCompetence);
			}
		}
	}
}

/**
 * Fonction d'ajout d'un cycle dans la table CYCLES.<br />
 * Cette methode renvoie l'id du cycle nouvellement integre si celui n'existait
 * pas. Sinon renvoie l'id du cycle deja existant.
 * @param $sNomCycle	Le nom du cycle a integrer s'il n'existe pas deja
 * @return int			id du cycle existant ou nouvellement integre
 */
function ajoute_cycle($sNomCycle)
{
	// ===== verifie si le cycle existe =====
	$sQuery = "SELECT " .
			  "  CYCLE_ID " .
			  " FROM CYCLES " .
			  " WHERE CYCLE_NOM = " . Database::prepareString($sNomCycle);
	$nCycleId = Database::fetchOneValue($sQuery);
	// $nCycleId = CYCLE_ID or false

	// si le cycle n'existe pas
	if($nCycleId === false)
	{
		// on l'ajoute
		$sQuery = "INSERT INTO CYCLES(CYCLE_NOM) " .
				  " VALUES(" . Database::prepareString($sNomCycle) .")";
		Database::execute($sQuery);					
		// puis on recupere son id
		$sQuery = "SELECT " .
				  "  CYCLE_ID " .
				  " FROM CYCLES " .
				  " WHERE CYCLE_NOM = " . Database::prepareString($sNomCycle);
		$nCycleId = Database::fetchOneValue($sQuery);
	}
	return $nCycleId;
}

/**
 * Ajoute un niveau $sNomNiveau pour le cycle d'id $nIdCycle si celui-ci 
 * n'existe pas puis renvoie l'id du nouveau niveau.<br />
 * S'il existe, renvoie juste l'id de ce niveau.<br />
 * @param $nIdCycle		id du cycle auquel rattache le niveau
 * @param $sNomNiveau	Le nom du niveau
 * @return int			id du niveau
 */
function ajoute_niveau($nIdCycle, $sNomNiveau)
{
	// ===== verifie si le niveau pour le cycle d'id $nIdCycle existe =====
	$sQuery = "SELECT " .
			  "  NIVEAU_ID " .
			  " FROM NIVEAUX " .
			  " WHERE NIVEAU_NOM = " . Database::prepareString($sNomNiveau) .
			  " AND ID_CYCLE = {$nIdCycle}";
	$nNiveauId = Database::fetchOneValue($sQuery);
	// $nNiveauId = NIVEAU_ID or false

	// si le niveau n'existe pas
	if($nNiveauId === false)
	{
		// on l'ajoute en l'attachant au cycle d'id $nIdCycle
		$sQuery = "INSERT INTO NIVEAUX(NIVEAU_NOM, ID_CYCLE) " .
				  " VALUES(" . Database::prepareString($sNomNiveau) .", {$nIdCycle})";
		Database::execute($sQuery);
		// puis on recupere son id
		$sQuery = "SELECT " .
				  "  NIVEAU_ID " .
				  " FROM NIVEAUX " .
				  " WHERE NIVEAU_NOM = " . Database::prepareString($sNomNiveau) .
				  " AND ID_CYCLE = {$nIdCycle}";
		$nNiveauId = Database::fetchOneValue($sQuery);
	}
	return $nNiveauId;
}

/**
 * Ajoute un domaine $sNomDomaine pour le cycle d'id $nIdCycle si celui-ci 
 * n'existe pas puis renvoie l'id du nouveau domaine.<br />
 * S'il existe, renvoie juste l'id de ce domaine.<br />
 * @param $nIdCycle		id du cycle auquel rattache le niveau
 * @param $sNomDomaine	Le nom du domaine
 * @return int			id du domaine
 */
function ajoute_domaine($nIdCycle, $sNomDomaine)
{
	// ===== verifie si le domaine pour le cycle d'id $nIdCycle existe =====
	$sQuery = "SELECT " .
			  "  DOMAINE_ID " .
			  " FROM DOMAINES " .
			  " WHERE DOMAINE_NOM = " . Database::prepareString($sNomDomaine) .
			  " AND ID_CYCLE = {$nIdCycle}";
	$nDomaineId = Database::fetchOneValue($sQuery);
	// $nDomaineId = DOMAINE_ID or false

	// si le domaine n'existe pas
	if($nDomaineId == false)
	{
		// on l'ajoute en l'attachant au cycle d'id $nIdCycle
		$sQuery = "INSERT INTO DOMAINES(DOMAINE_NOM, ID_CYCLE) " .
				  " VALUES(" . Database::prepareString($sNomDomaine) .", {$nIdCycle})";
		Database::execute($sQuery);
		// puis on recupere son id
		$sQuery = "SELECT " .
				  "  DOMAINE_ID " .
				  " FROM DOMAINES " .
				  " WHERE DOMAINE_NOM = " . Database::prepareString($sNomDomaine) .
				  " AND ID_CYCLE = {$nIdCycle}";
		$nDomaineId = Database::fetchOneValue($sQuery);
	}
	return $nDomaineId;
}

/**
 * Ajoute une matiere $sNomMatiere pour le domaine d'id $nIdDomaine si celui-ci 
 * n'existe pas puis renvoie l'id de la nouvelle matiere.<br />
 * Si elle existe, renvoie juste l'id de cette matiere.<br />
 * @param $nIdDomaine	id du domaine auquel rattache lla matiere
 * @param $sNomMatiere	Le nom de la matiere
 * @return int			id de la matiere
 */
function ajoute_matiere($nIdDomaine, $sNomMatiere)
{
	// ===== verifie si la matiere pour le domaine d'id $nIdDomaine existe =====
	$sQuery = "SELECT " .
			  "  MATIERE_ID " .
			  " FROM MATIERES " .
			  " WHERE MATIERE_NOM = " . Database::prepareString($sNomMatiere) .
			  " AND ID_DOMAINE = {$nIdDomaine}";
	$nMatiereId = Database::fetchOneValue($sQuery);
	// $nMatiereId = MATIERE_ID or false

	// si la matiere n'existe pas
	if($nMatiereId === false)
	{
		// on l'ajoute en l'attachant au domaine d'id $nIdDomaine
		$sQuery = "INSERT INTO MATIERES(MATIERE_NOM, ID_DOMAINE) " .
				  " VALUES(" . Database::prepareString($sNomMatiere) .", {$nIdDomaine})";
		Database::execute($sQuery);
		// puis on recupere son id
		$sQuery = "SELECT " .
				  "  MATIERE_ID " .
				  " FROM MATIERES " .
				  " WHERE MATIERE_NOM = " . Database::prepareString($sNomMatiere) .
				  " AND ID_DOMAINE = {$nIdDomaine}";
		$nMatiereId = Database::fetchOneValue($sQuery);
	}
	return $nMatiereId;
}

/**
 * Ajoute une competence $sNomCompetence pour la matiere d'id $nIdMatiere si 
 * celle-ci n'existe pas puis renvoie l'id de la nouvelle competence.<br />
 * Si elle existe, renvoie juste l'id de cette competence.<br />
 * @param $nIdMatiere		id de la matiere a laquel rattacher la competence
 * @param $sNomCompetence	Le nom de la competence
 * @return int				id de la competence
 */
function ajoute_competence($nIdMatiere, $sNomCompetence)
{
	// ===== verifie si la competence pour la matiere d'id $nIdMatiere existe =====
	$sQuery = "SELECT " .
			  "  COMPETENCE_ID " .
			  " FROM COMPETENCES " .
			  " WHERE COMPETENCE_NOM = " . Database::prepareString($sNomCompetence) .
			  " AND ID_MATIERE = {$nIdMatiere}";
	$nCompetenceId = Database::fetchOneValue($sQuery);
	// $nCompetenceId = COMPETENCE_ID or false

	// si la competence n'existe pas
	if($nCompetenceId === false)
	{
		// on l'ajoute en l'attachant a la matiere d'id $nIdMatiere
		$sQuery = "INSERT INTO COMPETENCES(COMPETENCE_NOM, ID_MATIERE) " .
				  " VALUES(" . Database::prepareString($sNomCompetence) .", {$nIdMatiere})";
		Database::execute($sQuery);
		$sQuery = "SELECT " .
				  "  COMPETENCE_ID " .
				  " FROM COMPETENCES " .
				  " WHERE COMPETENCE_NOM = " . Database::prepareString($sNomCompetence) .
				  " AND ID_MATIERE = {$nIdMatiere}";
		$nCompetenceId = Database::fetchOneValue($sQuery);
	}
	return $nCompetenceId;
}

?>