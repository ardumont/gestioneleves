<?php
/**
 * Classe de génération de livret annuel ou périodique.
 * @author Antoine Romain Dumont aka tony or ToNyX
 */
class Livret
{
	/**
	 * Fonction d'affichage du libellé du cycle en fonction du cycle
	 * @param $sCycleNom
	 * @return string
	 */
	public static function display_libelle_cycle($sCycleNom)
	{
		$sLib = "";
		switch($sCycleNom)
		{
			case "I": case "i":
				$sLib = "Cycle des apprentissages personnalisés";
				break;
			case "II": case "ii":
				$sLib = "Cycle des apprentissages fondamentaux";
				break;
			case "III": case "iii":
				$sLib = "Cycle des approfondissements";
				break;
		}
		return $sLib;
	}// fin display_libelle_cycle

	/**
	 * Récapitulatif annuel.
	 * @param $nEleveId	Elève concerné par le livret
	 * @return array	Résultat
	 * ['ELEVE']
	 * ['CLASSES_ELEVES']
	 * ['NOTES']
	 * ['NOTES_VALUES']
	 * ['PERIODES']
	 * ['CLASSES_NIVEAUX']
	 * ['DOMAINES_MATIERES_COMPETENCES']
	 * ['EVAL_INDS']
	 * ['NOM_PRENOM']
	 */
	public static function recap_annuel($nEleveId)
	{
		//restriction sur l'annee scolaire courante
		$sRestrictionAnneeScolaire =
			" AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();

		// ===== Les informations sur l'élève =====
		$sQuery = <<< ________EOQ
			SELECT DISTINCT
				ELEVE_NOM,
				CLASSE_ANNEE_SCOLAIRE,
				CLASSE_ID,
				CLASSE_NOM,
				ECOLE_NOM,
				ECOLE_VILLE,
				NIVEAU_NOM,
				CYCLE_NOM
			FROM ELEVES
				INNER JOIN ELEVE_CLASSE
					ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
				INNER JOIN CLASSES
					ON ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
				INNER JOIN ECOLES
					ON CLASSES.ID_ECOLE = ECOLES.ECOLE_ID
				INNER JOIN NIVEAU_CLASSE
					ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
				INNER JOIN NIVEAUX
					ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
				INNER JOIN CYCLES
					ON NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID
			WHERE ELEVE_ID = {$nEleveId}
			{$sRestrictionAnneeScolaire}
			ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC, ELEVE_NOM ASC
________EOQ;
		$aEleve = Database::fetchOneRow($sQuery);
		// $aEleve[COLONNE] = VALEUR

		// ===== Les informations sur l'élève =====
		$sQuery = <<< ________EOQ
			SELECT DISTINCT
				PROFESSEUR_NOM,
				CLASSE_NOM,
				CLASSE_ANNEE_SCOLAIRE
			FROM ELEVES
				INNER JOIN ELEVE_CLASSE
					ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
				INNER JOIN CLASSES
					ON ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
				INNER JOIN PROFESSEUR_CLASSE
					ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
				INNER JOIN PROFESSEURS
					ON PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID
			WHERE ELEVE_ID = {$nEleveId}
			{$sRestrictionAnneeScolaire}
			ORDER BY CLASSE_ANNEE_SCOLAIRE ASC
________EOQ;
		$aClassesEleve = Database::fetchArray($sQuery);
		// $aClassesEleve[][COLONNE] = VALEUR

		// ===== Les informations sur les notes =====
		$sQuery = <<< ________EOQ
			SELECT
				NOTE_NOM,
				NOTE_LABEL,
				NOTE_NOTE
			FROM NOTES
			ORDER BY NOTE_NOTE DESC
________EOQ;
		$aNotes = Database::fetchArray($sQuery);
		// $aNotes[][COLONNE] = VALEUR
		$aNotesValues = Database::fetchArrayWithKey($sQuery, 'NOTE_LABEL');
		// $aNotesValues[VALEUR DE NOTE_LABEL][COLONNE] = VALEUR

		// ===== La liste des periodes =====
		$sQuery = <<< ________EOQ
			SELECT
				PERIODE_ID,
				PERIODE_NOM
			FROM PERIODES
			ORDER BY PERIODE_NOM ASC
________EOQ;
		$aPeriodes = Database::fetchArray($sQuery);
		// $aPeriodes[][COLONNE] = VALEUR

		// ===== La liste des classes =====
		$sQuery = <<< ________EOQ
			SELECT
				CLASSE_NOM,
				NIVEAU_NOM
			FROM CLASSES
				INNER JOIN NIVEAU_CLASSE
					ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
				INNER JOIN NIVEAUX
					ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
				INNER JOIN CYCLES
					ON NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID
			WHERE CYCLE_NOM = '{$aEleve['CYCLE_NOM']}'
			{$sRestrictionAnneeScolaire}
			ORDER BY NIVEAU_ID ASC
________EOQ;
		$aClassesNiveaux = Database::fetchArray($sQuery);
		// $aClassesNiveaux[][COLONNE] = VALEUR

		// ===== La liste des compétences (filtre sur le cycle et sur l'élève) =====
		$sQuery = <<< ________EOQ
			SELECT
				COMPETENCE_NOM,
				MATIERE_NOM,
				DOMAINE_NOM
			FROM COMPETENCES
				INNER JOIN MATIERES
					ON COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID
				INNER JOIN DOMAINES
					ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
				INNER JOIN CYCLES
					ON DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID
			WHERE CYCLE_NOM = '{$aEleve['CYCLE_NOM']}'
			ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
________EOQ;
		$aDomainesMatieresCompetences = Database::fetchArrayWithMultiKey($sQuery, array('DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM'));
		// $aDomainesMatieresCompetences[NOM DU DOMAINE][NOM DE LA MATIERE][NOM DE LA COMPETENCE][COLONNE] = VALEUR

		// ===== La liste des evaluations individuelles a ce jour pour l'élève =====
		$sQuery = <<< ________EOQ
			SELECT
				MATIERE_NOM,
				DOMAINE_NOM,
				COMPETENCE_NOM,
				NIVEAU_NOM,
				CLASSE_NOM,
				PERIODE_NOM,
				NOTE_LABEL,
				NOTE_NOTE
			FROM EVALUATIONS_INDIVIDUELLES
				INNER JOIN NOTES
					ON EVALUATIONS_INDIVIDUELLES.ID_NOTE = NOTES.NOTE_ID
				INNER JOIN ELEVES
					ON EVALUATIONS_INDIVIDUELLES.ID_ELEVE = ELEVES.ELEVE_ID
				INNER JOIN EVALUATIONS_COLLECTIVES
					ON EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL = EVALUATIONS_COLLECTIVES.EVAL_COL_ID
				INNER JOIN CLASSES
					ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
				INNER JOIN NIVEAU_CLASSE
					ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
				INNER JOIN NIVEAUX
					ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
				INNER JOIN COMPETENCES
					ON EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID
				INNER JOIN MATIERES
					ON COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID
				INNER JOIN DOMAINES
					ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
				INNER JOIN PERIODES
					ON EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID
			WHERE ELEVE_ID = {$nEleveId}
			ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
________EOQ;
		$aEvalInds = Database::fetchArrayWithMultiKey($sQuery, array('DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM', 'CLASSE_NOM', 'NIVEAU_NOM', 'PERIODE_NOM'), false);
		// $aEvalInds[NOM DU DOMAINE][NOM DE LA MATIERE][NOM DE LA COMPETENCE][NOM DE LA CLASSE][NOM DU NIVEAU][NOM DE LA PERIODE][COLONNE] = VALEUR

		// Lancement du calcul de la moyenne pour la période
		// Parcours de tous les domaines
		foreach($aDomainesMatieresCompetences as $sDomaine => $aMatieres)
		{
			// Parcours de toutes les matieres
			foreach($aMatieres as $sMatiere => $aCompetences)
			{
				// Parcours de chaque classe de l'élève
				foreach($aClassesNiveaux as $i => $aClasseNiveau)
				{
					$sNiveauNom = $aClasseNiveau['NIVEAU_NOM'];
					$sClasseNom = $aClasseNiveau['CLASSE_NOM'];

					// Parcours de chaque période de l'élève
					foreach($aPeriodes as $aPeriode)
					{
						$sPeriodeNom = $aPeriode['PERIODE_NOM'];

						// Parcours de toutes les compétences
						foreach($aCompetences as $sCompetence => $aCompetence)
						{
							if(isset($aEvalInds[$sDomaine]) &&
							   isset($aEvalInds[$sDomaine][$sMatiere]) &&
							   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence]) &&
							   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom]) &&
							   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom]) &&
							   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]))
							{
								// Récupère la liste des compétences évaluées
								$aRes = $aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom];

								// Nombre de compétences
								$nNbComp = 0;
								// Somme des notes des compétences
								$nSommeComp = 0;
								// Pour chaque compétence évaluée
								foreach($aRes as $oRes)
								{
									// Les competences à "Non Evaluees" ne comptent pas dans la moyenne
									if($oRes['NOTE_NOTE'] == 0)
									{
										continue;
									}
									// Somme
									$nSommeComp += $oRes['NOTE_NOTE'];
									// Incrémente le nombre de compétences évaluées
									$nNbComp++;
								}
								// Calcul de la moyenne
								$nMoy = ($nNbComp != 0) ? $nSommeComp / $nNbComp : 0;
								// On stocke enfin la moyenne de ces compétences
								$aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]['NOTE_LABEL'] = Moyenne::compute_and_label($nMoy);
							}
						}// fin parcours des compétences
					}// fin parcours des périodes
				}// fin parcours des classes
			}// fin parcours des matieres
		}// fin parcours des domaines
		// fin calcul de la moyenne

		// Calcule le nom et le prénom
		$aNomPrenom = explode(" ", $aEleve['ELEVE_NOM']);

		// ===== Génération du tableau résultat =====

		$aRes['ELEVE'] = $aEleve;
		$aRes['CLASSES_ELEVES'] = $aClassesEleve;
		$aRes['NOTES'] = $aNotes;
		$aRes['NOTES_VALUES'] = $aNotesValues;
		$aRes['PERIODES'] = $aPeriodes;
		$aRes['CLASSES_NIVEAUX'] = $aClassesNiveaux;
		$aRes['DOMAINES_MATIERES_COMPETENCES'] = $aDomainesMatieresCompetences;
		$aRes['EVAL_INDS'] = $aEvalInds;
		$aRes['NOM_PRENOM'] = $aNomPrenom;

		return $aRes;
	}// fin recap_annuel

	/**
	 * Récapitulatif du cycle pour un élève.
	 * @param $nEleveId	Elève concerné par le livret
	 * @return array	Résultat
	 * ['ELEVE']
	 * ['CLASSES_ELEVES']
	 * ['NOTES']
	 * ['NOTES_VALUES']
	 * ['PERIODES']
	 * ['CLASSES_NIVEAUX']
	 * ['DOMAINES_MATIERES_COMPETENCES']
	 * ['EVAL_INDS']
	 * ['NOM_PRENOM']
	 */
	public static function recap_cycle($nEleveId)
	{
		//restriction sur l'annee scolaire courante
		$sRestrictionAnneeScolaire =
			" AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();

		// ===== Les informations sur l'élève =====
		$sQuery = <<< ________EOQ
			SELECT DISTINCT
				ELEVE_NOM,
				CLASSE_ANNEE_SCOLAIRE,
				CLASSE_NOM,
				ECOLE_NOM,
				ECOLE_VILLE,
				NIVEAU_NOM,
				CYCLE_NOM
			FROM ELEVES
				INNER JOIN ELEVE_CLASSE
					ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
				INNER JOIN CLASSES
					ON ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
				INNER JOIN ECOLES
					ON CLASSES.ID_ECOLE = ECOLES.ECOLE_ID
				INNER JOIN NIVEAU_CLASSE
					ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
				INNER JOIN NIVEAUX
					ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
				INNER JOIN CYCLES
					ON NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID
			WHERE ELEVE_ID = {$nEleveId}
			ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC, ELEVE_NOM ASC
________EOQ;
		$aEleve = Database::fetchOneRow($sQuery);
		// $aEleve[COLONNE] = VALEUR

		// ===== Les informations sur l'élève =====
		$sQuery = <<< ________EOQ
			SELECT DISTINCT
				PROFESSEUR_NOM,
				CLASSE_NOM,
				CLASSE_ANNEE_SCOLAIRE
			FROM ELEVES
				INNER JOIN ELEVE_CLASSE
					ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
				INNER JOIN CLASSES
					ON ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
				INNER JOIN PROFESSEUR_CLASSE
					ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
				INNER JOIN PROFESSEURS
					ON PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID
			WHERE ELEVE_ID = {$nEleveId}
			ORDER BY CLASSE_ANNEE_SCOLAIRE ASC
________EOQ;
		$aClassesEleve = Database::fetchArray($sQuery);
		// $aClassesEleve[][COLONNE] = VALEUR

		// ===== Les informations sur les notes =====
		$sQuery = <<< ________EOQ
			SELECT
				NOTE_NOM,
				NOTE_LABEL,
				NOTE_NOTE
			FROM NOTES
			ORDER BY NOTE_NOTE DESC
________EOQ;
		$aNotes = Database::fetchArray($sQuery);
		// $aNotes[][COLONNE] = VALEUR
		$aNotesValues = Database::fetchArrayWithKey($sQuery, 'NOTE_LABEL');
		// $aNotesValues[VALEUR DE NOTE_LABEL][COLONNE] = VALEUR

		// ===== La liste des periodes =====
		$sQuery = <<< ________EOQ
			SELECT
				PERIODE_NOM
			FROM PERIODES
			ORDER BY PERIODE_NOM ASC
________EOQ;
		$aPeriodes = Database::fetchArray($sQuery);
		// $aPeriodes[][COLONNE] = VALEUR

		// ===== La liste des classes =====
		$sQuery = <<< ________EOQ
			SELECT
				CLASSE_NOM,
				NIVEAU_NOM
			FROM CLASSES
				INNER JOIN NIVEAU_CLASSE
					ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
				INNER JOIN NIVEAUX
					ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
				INNER JOIN CYCLES
					ON NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID
			WHERE CYCLE_NOM = '{$aEleve['CYCLE_NOM']}'
			ORDER BY NIVEAU_ID ASC
________EOQ;
		$aClassesNiveaux = Database::fetchArray($sQuery);
		// $aClassesNiveaux[][COLONNE] = VALEUR

		// ===== La liste des compétences (filtre sur le cycle et sur l'élève) =====
		$sQuery = <<< ________EOQ
			SELECT
				COMPETENCE_NOM,
				MATIERE_NOM,
				DOMAINE_NOM
			FROM COMPETENCES
				INNER JOIN MATIERES
					ON COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID
				INNER JOIN DOMAINES
					ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
				INNER JOIN CYCLES
					ON DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID
			WHERE CYCLE_NOM = '{$aEleve['CYCLE_NOM']}'
			ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
________EOQ;
		$aDomainesMatieresCompetences = Database::fetchArrayWithMultiKey($sQuery, array('DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM'));
		// $aDomainesMatieresCompetences[NOM DU DOMAINE][NOM DE LA MATIERE][NOM DE LA COMPETENCE][COLONNE] = VALEUR

		// ===== La liste des evaluations individuelles a ce jour pour l'élève =====
		$sQuery = <<< ________EOQ
			SELECT
				MATIERE_NOM,
				DOMAINE_NOM,
				COMPETENCE_NOM,
				NIVEAU_NOM,
				CLASSE_NOM,
				PERIODE_NOM,
				NOTE_LABEL,
				NOTE_NOTE
			FROM EVALUATIONS_INDIVIDUELLES
				INNER JOIN NOTES
					ON EVALUATIONS_INDIVIDUELLES.ID_NOTE = NOTES.NOTE_ID
				INNER JOIN ELEVES
					ON EVALUATIONS_INDIVIDUELLES.ID_ELEVE = ELEVES.ELEVE_ID
				INNER JOIN EVALUATIONS_COLLECTIVES
					ON EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL = EVALUATIONS_COLLECTIVES.EVAL_COL_ID
				INNER JOIN CLASSES
					ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
				INNER JOIN NIVEAU_CLASSE
					ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
				INNER JOIN NIVEAUX
					ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
				INNER JOIN COMPETENCES
					ON EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID
				INNER JOIN MATIERES
					ON COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID
				INNER JOIN DOMAINES
					ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
				INNER JOIN PERIODES
					ON EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID
			WHERE ELEVE_ID = {$nEleveId}
			ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
________EOQ;
		$aEvalInds = Database::fetchArrayWithMultiKey($sQuery, array('DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM', 'CLASSE_NOM', 'NIVEAU_NOM', 'PERIODE_NOM'), false);
		// $aEvalInds[NOM DU DOMAINE][NOM DE LA MATIERE][NOM DE LA COMPETENCE][NOM DE LA CLASSE][NOM DU NIVEAU][NOM DE LA PERIODE][COLONNE] = VALEUR

		// Lancement du calcul de la moyenne pour la période
		// Parcours de tous les domaines
		foreach($aDomainesMatieresCompetences as $sDomaine => $aMatieres)
		{
			// Parcours de toutes les matieres
			foreach($aMatieres as $sMatiere => $aCompetences)
			{
				// Parcours de chaque classe de l'élève
				foreach($aClassesNiveaux as $i => $aClasseNiveau)
				{
					$sNiveauNom = $aClasseNiveau['NIVEAU_NOM'];
					$sClasseNom = $aClasseNiveau['CLASSE_NOM'];

					// Parcours de chaque période de l'élève
					foreach($aPeriodes as $aPeriode)
					{
						$sPeriodeNom = $aPeriode['PERIODE_NOM'];

						// Parcours de toutes les compétences
						foreach($aCompetences as $sCompetence => $aCompetence)
						{
							if(isset($aEvalInds[$sDomaine]) &&
							   isset($aEvalInds[$sDomaine][$sMatiere]) &&
							   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence]) &&
							   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom]) &&
							   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom]) &&
							   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]))
							{
								// Récupère la liste des compétences évaluées
								$aRes = $aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom];

								// Nombre de compétences
								$nNbComp = 0;
								// Somme des notes des compétences
								$nSommeComp = 0;
								// Pour chaque compétence évaluée
								foreach($aRes as $oRes)
								{
									// Les competences à "Non Evaluees" ne comptent pas dans la moyenne
									if($oRes['NOTE_NOTE'] == 0)
									{
										continue;
									}
									// Somme
									$nSommeComp += $oRes['NOTE_NOTE'];
									// Incrémente le nombre de compétences évaluées
									$nNbComp++;
								}
								// Calcul de la moyenne
								$nMoy = ($nNbComp != 0) ? $nSommeComp / $nNbComp : 0;
								// On stocke enfin la moyenne de ces compétences
								$aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]['NOTE_LABEL'] = Moyenne::compute_and_label($nMoy);
							}
						}// fin parcours des compétences
					}// fin parcours des périodes
				}// fin parcours des classes
			}// fin parcours des matieres
		}// fin parcours des domaines
		// fin calcul de la moyenne

		// Calcule le nom et le prénom
		$aNomPrenom = explode(" ", $aEleve['ELEVE_NOM']);

		// ===== Génération du tableau résultat =====

		$aRes['ELEVE'] = $aEleve;
		$aRes['CLASSES_ELEVES'] = $aClassesEleve;
		$aRes['NOTES'] = $aNotes;
		$aRes['NOTES_VALUES'] = $aNotesValues;
		$aRes['PERIODES'] = $aPeriodes;
		$aRes['CLASSES_NIVEAUX'] = $aClassesNiveaux;
		$aRes['DOMAINES_MATIERES_COMPETENCES'] = $aDomainesMatieresCompetences;
		$aRes['EVAL_INDS'] = $aEvalInds;
		$aRes['NOM_PRENOM'] = $aNomPrenom;

		return $aRes;
	}// fin recap_cycle

	/**
	 * Récapitulatif périodique pour un élève.
	 * @param $nEleveId	  Elève concerné par le livret
	 * @param $nPeriodeId Période concernée par le livret
	 * @return array	  Résultat
	 * ['ELEVE']
	 * ['CLASSES_ELEVES']
	 * ['NOTES']
	 * ['NOTES_VALUES']
	 * ['PERIODES']
	 * ['CLASSES_NIVEAUX']
	 * ['DOMAINES_MATIERES_COMPETENCES']
	 * ['EVAL_INDS']
	 * ['NOM_PRENOM']
	 */
	public static function recap_period($nEleveId, $nPeriodeId)
	{
		//restriction sur l'annee scolaire courante
		$sRestrictionAnneeScolaire =
			" AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();

		// ===== Les informations sur l'élève =====
		$sQuery = <<< ________EOQ
			SELECT DISTINCT
				ELEVE_NOM,
				CLASSE_ANNEE_SCOLAIRE,
				CLASSE_NOM,
				ECOLE_NOM,
				ECOLE_VILLE,
				NIVEAU_NOM,
				CYCLE_NOM
			FROM ELEVES
				INNER JOIN ELEVE_CLASSE
					ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
				INNER JOIN CLASSES
					ON ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
				INNER JOIN ECOLES
					ON CLASSES.ID_ECOLE = ECOLES.ECOLE_ID
				INNER JOIN NIVEAU_CLASSE
					ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
				INNER JOIN NIVEAUX
					ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
				INNER JOIN CYCLES
					ON NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID
			WHERE ELEVE_ID = {$nEleveId}
			ORDER BY CLASSE_ANNEE_SCOLAIRE DESC, CLASSE_NOM ASC, ELEVE_NOM ASC
________EOQ;
		$aEleve = Database::fetchOneRow($sQuery);
		// $aEleve[COLONNE] = VALEUR

		// ===== Les informations sur l'élève =====
		$sQuery = <<< ________EOQ
			SELECT DISTINCT
				PROFESSEUR_NOM,
				CLASSE_NOM,
				CLASSE_ANNEE_SCOLAIRE
			FROM ELEVES
				INNER JOIN ELEVE_CLASSE
					ON ELEVES.ELEVE_ID = ELEVE_CLASSE.ID_ELEVE
				INNER JOIN CLASSES
					ON ELEVE_CLASSE.ID_CLASSE = CLASSES.CLASSE_ID
				INNER JOIN PROFESSEUR_CLASSE
					ON CLASSES.CLASSE_ID = PROFESSEUR_CLASSE.ID_CLASSE
				INNER JOIN PROFESSEURS
					ON PROFESSEUR_CLASSE.ID_PROFESSEUR = PROFESSEURS.PROFESSEUR_ID
			WHERE ELEVE_ID = {$nEleveId}
			{$sRestrictionAnneeScolaire}
			ORDER BY CLASSE_ANNEE_SCOLAIRE ASC
________EOQ;
		$aClassesEleve = Database::fetchOneRow($sQuery);
		// $aClassesEleve[COLONNE] = VALEUR

		// ===== Les informations sur les notes =====
		$sQuery = <<< ________EOQ
			SELECT
				NOTE_NOM,
				NOTE_LABEL
			FROM NOTES
			ORDER BY NOTE_NOTE DESC
________EOQ;
		$aNotes = Database::fetchArray($sQuery);

		// ===== La liste des periodes =====
		$sQuery = <<< ________EOQ
			SELECT
				PERIODE_ID,
				PERIODE_NOM
			FROM PERIODES
			WHERE PERIODE_ID = {$nPeriodeId}
			ORDER BY PERIODE_NOM ASC
________EOQ;
		$aPeriodes = Database::fetchOneRow($sQuery);
		// $aPeriodes[COLONNE] = VALEUR

		// ===== La liste des classes =====
		$sQuery = <<< ________EOQ
			SELECT
			    CLASSE_ID,
				CLASSE_NOM,
				NIVEAU_NOM
			FROM CLASSES
				INNER JOIN NIVEAU_CLASSE
					ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
				INNER JOIN NIVEAUX
					ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
				INNER JOIN CYCLES
					ON NIVEAUX.ID_CYCLE = CYCLES.CYCLE_ID
			WHERE CYCLE_NOM = '{$aEleve['CYCLE_NOM']}'
			{$sRestrictionAnneeScolaire}
			ORDER BY NIVEAU_ID ASC
________EOQ;
		$aClassesNiveaux = Database::fetchOneRow($sQuery);
		// $aClassesNiveaux[COLONNE] = VALEUR

		// ===== La liste des compétences (filtre sur le cycle et sur l'élève) =====
		$sQuery = <<< ________EOQ
			SELECT
				COMPETENCE_NOM,
				MATIERE_NOM,
				DOMAINE_NOM
			FROM COMPETENCES
				INNER JOIN MATIERES
					ON COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID
				INNER JOIN DOMAINES
					ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
				INNER JOIN CYCLES
					ON DOMAINES.ID_CYCLE = CYCLES.CYCLE_ID
				INNER JOIN EVALUATIONS_INDIVIDUELLES
					ON COMPETENCES.COMPETENCE_ID = EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE
				INNER JOIN EVALUATIONS_COLLECTIVES
					ON EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL = EVALUATIONS_COLLECTIVES.EVAL_COL_ID
				INNER JOIN PERIODES
					ON EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID
			WHERE CYCLE_NOM = '{$aEleve['CYCLE_NOM']}'
			AND EVALUATIONS_INDIVIDUELLES.ID_ELEVE = {$nEleveId}
			AND PERIODE_ID = {$nPeriodeId}
			ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
________EOQ;
		$aDomainesMatieresCompetences = Database::fetchArrayWithMultiKey($sQuery, array('DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM'));
		// $aDomainesMatieresCompetences[NOM DU DOMAINE][NOM DE LA MATIERE][NOM DE LA COMPETENCE][COLONNE] = VALEUR

		// ===== La liste des evaluations individuelles a ce jour pour l'élève =====
		$sQuery = <<< ________EOQ
			SELECT
				DOMAINE_NOM,
				MATIERE_NOM,
				COMPETENCE_NOM,
				CLASSE_NOM,
				NIVEAU_NOM,
				PERIODE_NOM,
				NOTE_LABEL,
				NOTE_NOTE
			FROM EVALUATIONS_INDIVIDUELLES
				INNER JOIN NOTES
					ON EVALUATIONS_INDIVIDUELLES.ID_NOTE = NOTES.NOTE_ID
				INNER JOIN ELEVES
					ON EVALUATIONS_INDIVIDUELLES.ID_ELEVE = ELEVES.ELEVE_ID
				INNER JOIN EVALUATIONS_COLLECTIVES
					ON EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL = EVALUATIONS_COLLECTIVES.EVAL_COL_ID
				INNER JOIN CLASSES
					ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
				INNER JOIN NIVEAU_CLASSE
					ON CLASSES.CLASSE_ID = NIVEAU_CLASSE.ID_CLASSE
				INNER JOIN NIVEAUX
					ON NIVEAU_CLASSE.ID_NIVEAU = NIVEAUX.NIVEAU_ID
				INNER JOIN COMPETENCES
					ON EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID
				INNER JOIN MATIERES
					ON COMPETENCES.ID_MATIERE = MATIERES.MATIERE_ID
				INNER JOIN DOMAINES
					ON MATIERES.ID_DOMAINE = DOMAINES.DOMAINE_ID
				INNER JOIN PERIODES
					ON EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID
			WHERE ELEVE_ID = {$nEleveId}
			AND PERIODE_ID = {$nPeriodeId}
			ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
________EOQ;
		$aEvalInds = Database::fetchArrayWithMultiKey($sQuery, array('DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM', 'CLASSE_NOM', 'NIVEAU_NOM', 'PERIODE_NOM'), false);
		// $aEvalInds[NOM DU DOMAINE][NOM DE LA MATIERE][NOM DE LA COMPETENCE][NOM DE LA CLASSE][NOM DU NIVEAU][NOM DE LA PERIODE][COLONNE] = VALEUR

		// Lancement du calcul de la moyenne pour la période
		// Parcours de tous les domaines
		foreach($aDomainesMatieresCompetences as $sDomaine => $aMatieres)
		{
			// Parcours de toutes les matieres
			foreach($aMatieres as $sMatiere => $aCompetences)
			{
				$sNiveauNom = $aClassesNiveaux['NIVEAU_NOM'];
				$sClasseNom = $aClassesNiveaux['CLASSE_NOM'];
				$sPeriodeNom = $aPeriodes['PERIODE_NOM'];

				// Parcours de toutes les compétences
				foreach($aCompetences as $sCompetence => $aCompetence)
				{
					if(isset($aEvalInds[$sDomaine]) &&
					   isset($aEvalInds[$sDomaine][$sMatiere]) &&
					   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence]) &&
					   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom]) &&
					   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom]) &&
					   isset($aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]))
					{
						// Récupère la liste des compétences évaluées
						$aRes = $aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom];

						// Nombre de compétences
						$nNbComp = 0;
						// Somme des notes des compétences
						$nSommeComp = 0;
						// Pour chaque compétence évaluée
						foreach($aRes as $oRes)
						{
							// Les competences à "Non Evaluees" ne comptent pas dans la moyenne
							if($oRes['NOTE_NOTE'] == 0)
							{
								continue;
							}
							// Somme
							$nSommeComp += $oRes['NOTE_NOTE'];
							// Incrémente le nombre de compétences évaluées
							$nNbComp++;
						}
						// Calcul de la moyenne
						$nMoy = ($nNbComp != 0) ? $nSommeComp / $nNbComp : 0;
						// On stocke enfin la moyenne de ces compétences
						$aEvalInds[$sDomaine][$sMatiere][$sCompetence][$sClasseNom][$sNiveauNom][$sPeriodeNom]['NOTE_LABEL'] = Moyenne::compute_and_label($nMoy);
					}
				}// fin parcours des compétences
			}// fin parcours des matieres
		}// fin parcours des domaines
		// fin calcul de la moyenne

		// Calcule le nom et le prénom
		$aNomPrenom = explode(" ", $aEleve['ELEVE_NOM']);

		// ===== Génération du tableau résultat =====

		$aRes['ELEVE'] = $aEleve;
		$aRes['CLASSES_ELEVES'] = $aClassesEleve;
		$aRes['NOTES'] = $aNotes;
		$aRes['PERIODES'] = $aPeriodes;
		$aRes['CLASSES_NIVEAUX'] = $aClassesNiveaux;
		$aRes['DOMAINES_MATIERES_COMPETENCES'] = $aDomainesMatieresCompetences;
		$aRes['EVAL_INDS'] = $aEvalInds;
		$aRes['NOM_PRENOM'] = $aNomPrenom;

		return $aRes;
	}// fin recap_period

	/**
	 * Récapitulatif périodique pour une classe sur une période.
	 * @param $nClasseId		Classe
	 * @param $nPeriodeId		Période concernée par le livret
	 * @param $nCompetenceId	Compétence
	 * @return array	Résultat
	 * ['NOTES']
	 * ['PERIODE_NOM']
	 * ['COMPETENCE_NOM']
	 * ['CLASSE_NOM']
	 * ['EVAL_INDS']
	 */
	public static function recap_period_competence($nClasseId, $nPeriodeId, $nCompetenceId)
	{
		//restriction sur l'annee scolaire courante
		$sRestrictionAnneeScolaire =
			" AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();

		// ===== Les informations sur les classes =====
		$sQuery = <<< ________EOQ
			SELECT
				CLASSE_NOM
			FROM CLASSES
			WHERE CLASSE_ID = {$nClasseId}
________EOQ;
		$sClasseNom = Database::fetchOneValue($sQuery);

		// ===== Les informations sur les notes =====
		$sQuery = <<< ________EOQ
			SELECT
				NOTE_NOM,
				NOTE_LABEL
			FROM NOTES
			ORDER BY NOTE_NOTE DESC
________EOQ;
		$aNotes = Database::fetchArray($sQuery);

		// ===== La liste des periodes =====
		$sQuery = <<< ________EOQ
			SELECT
				PERIODE_NOM
			FROM PERIODES
			WHERE PERIODE_ID = {$nPeriodeId}
________EOQ;
		$sPeriodeNom = Database::fetchOneValue($sQuery);

		// ===== La liste des compétences (filtre sur le cycle et sur l'élève) =====
		$sQuery = <<< ________EOQ
			SELECT
				COMPETENCE_NOM
			FROM COMPETENCES
			WHERE COMPETENCE_ID = {$nCompetenceId}
________EOQ;
		$sCompetenceNom = Database::fetchOneValue($sQuery);

		// ===== La liste des evaluations individuelles a ce jour pour l'élève =====
		$sQuery = <<< ________EOQ
			SELECT
				ELEVE_NOM,
				EVAL_IND_ID,
				COMPETENCE_NOM,
				CLASSE_NOM,
				PERIODE_NOM,
				NOTE_LABEL,
				NOTE_NOTE,
				COMPETENCE_ID,
				PERIODE_ID
			FROM EVALUATIONS_INDIVIDUELLES
				INNER JOIN COMPETENCES
					ON EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID
				INNER JOIN NOTES
					ON EVALUATIONS_INDIVIDUELLES.ID_NOTE = NOTES.NOTE_ID
				INNER JOIN ELEVES
					ON EVALUATIONS_INDIVIDUELLES.ID_ELEVE = ELEVES.ELEVE_ID
				INNER JOIN EVALUATIONS_COLLECTIVES
					ON EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL = EVALUATIONS_COLLECTIVES.EVAL_COL_ID
				INNER JOIN CLASSES
					ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
				INNER JOIN PERIODES
					ON EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID
			WHERE PERIODE_ID = {$nPeriodeId}
			AND COMPETENCE_ID = {$nCompetenceId}
			AND CLASSE_ID = {$nClasseId}
			AND ELEVE_ACTIF=1
			ORDER BY ELEVE_NOM ASC, COMPETENCE_NOM ASC
________EOQ;
		$aEvalInds = Database::fetchArrayWithMultiKey($sQuery, array('ELEVE_NOM', 'EVAL_IND_ID'));
		// $aEvalInds[NOM DE L'ELEVE][COLONNE] = VALEUR

		// Parcours de toutes les evaluations individuelles pour la compétence
		foreach($aEvalInds as $sEleveNom => $aEvalInd)
		{
			// Nombre d'évaluations sur l'élève pour la compétence
			$nNbEval = 0;
			// Somme des notes des évaluations
			$nSommeEval = 0;
			// Pour chaque évaluation
			foreach($aEvalInd as $nEvalIndId => $aRes)
			{
				// Stocke la note pour informations
				$aEvalIndsMoy[$sEleveNom]["NOTE_LABEL_{$nEvalIndId}"] = $aRes['NOTE_LABEL'];

				// Les competences évaluées à "Non Evaluees" ne comptent pas dans la moyenne
				if($aRes['NOTE_NOTE'] == 0)
				{
					continue;
				}
				// Somme des évaluations
				$nSommeEval += $aRes['NOTE_NOTE'];
				// Incrémente le nombre d'évaluations
				$nNbEval++;
			}
			// Calcul de la moyenne
			$nMoy = ($nNbEval != 0) ? $nSommeEval / $nNbEval : 0;
			// On stocke enfin la moyenne de ces compétences
			$aEvalIndsMoy[$sEleveNom]['NOTE_LABEL'] = Moyenne::compute_and_label($nMoy);
		}
		// fin calcul de la moyenne

		// ===== Génération du tableau résultat =====

		$aRes['NOTES'] = $aNotes;
		$aRes['PERIODE_NOM'] = $sPeriodeNom;
		$aRes['COMPETENCE_NOM'] = $sCompetenceNom;
		$aRes['CLASSE_NOM'] = $sClasseNom;
		$aRes['EVAL_INDS'] = isset($aEvalIndsMoy) ? $aEvalIndsMoy : array();

		return $aRes;
	}// fin recap_period_competence

	/**
	 * Récapitulatif annuel pour une classe sur une période.
	 * @param $nClasseId		Classe
	 * @param $nCompetenceId	Compétence
	 * @return array	Résultat
	 * ['NOTES']
	 * ['PERIODES']
	 * ['COMPETENCE_NOM']
	 * ['CLASSE_NOM']
	 * ['EVAL_INDS']
	 */
	public static function recap_annuel_competence($nClasseId, $nCompetenceId)
	{
		//restriction sur l'annee scolaire courante
		$sRestrictionAnneeScolaire =
			" AND CLASSE_ANNEE_SCOLAIRE = " . sql_annee_scolaire_courante();

		// ===== Les informations sur les classes =====
		$sQuery = <<< ________EOQ
			SELECT
				CLASSE_NOM
			FROM CLASSES
			WHERE CLASSE_ID = {$nClasseId}
________EOQ;
		$sClasseNom = Database::fetchOneValue($sQuery);

		// ===== Les informations sur les notes =====
		$sQuery = <<< ________EOQ
			SELECT
				NOTE_NOM,
				NOTE_LABEL
			FROM NOTES
			ORDER BY NOTE_NOTE DESC
________EOQ;
		$aNotes = Database::fetchArray($sQuery);

		// ===== La liste des periodes =====
		$sQuery = <<< ________EOQ
			SELECT
				PERIODE_NOM
			FROM PERIODES
			ORDER BY PERIODE_NOM ASC
________EOQ;
		$aPeriodes = Database::fetchColumn($sQuery);

		// ===== La compétence =====
		$sQuery = <<< ________EOQ
			SELECT
				COMPETENCE_NOM
			FROM COMPETENCES
			WHERE COMPETENCE_ID = {$nCompetenceId}
________EOQ;
		$sCompetenceNom = Database::fetchOneValue($sQuery);

		// ===== La liste des evaluations individuelles a ce jour pour l'élève =====
		$sQuery = <<< ________EOQ
			SELECT
				ELEVE_NOM,
				EVAL_IND_ID,
				COMPETENCE_NOM,
				CLASSE_NOM,
				PERIODE_NOM,
				NOTE_LABEL,
				NOTE_NOTE,
				COMPETENCE_ID,
				PERIODE_ID
			FROM EVALUATIONS_INDIVIDUELLES
				INNER JOIN COMPETENCES
					ON EVALUATIONS_INDIVIDUELLES.ID_COMPETENCE = COMPETENCES.COMPETENCE_ID
				INNER JOIN NOTES
					ON EVALUATIONS_INDIVIDUELLES.ID_NOTE = NOTES.NOTE_ID
				INNER JOIN ELEVES
					ON EVALUATIONS_INDIVIDUELLES.ID_ELEVE = ELEVES.ELEVE_ID
				INNER JOIN EVALUATIONS_COLLECTIVES
					ON EVALUATIONS_INDIVIDUELLES.ID_EVAL_COL = EVALUATIONS_COLLECTIVES.EVAL_COL_ID
				INNER JOIN CLASSES
					ON EVALUATIONS_COLLECTIVES.ID_CLASSE = CLASSES.CLASSE_ID
				INNER JOIN PERIODES
					ON EVALUATIONS_COLLECTIVES.ID_PERIODE = PERIODES.PERIODE_ID
			WHERE COMPETENCE_ID = {$nCompetenceId}
			AND CLASSE_ID = {$nClasseId}
			AND ELEVE_ACTIF=1
			ORDER BY ELEVE_NOM ASC, COMPETENCE_NOM ASC
________EOQ;
		$aEvalInds = Database::fetchArrayWithMultiKey($sQuery, array('ELEVE_NOM', 'PERIODE_NOM', 'EVAL_IND_ID'));
		// $aEvalInds[NOM DE LA PERIODE][NOM DE L'ELEVE][COLONNE] = VALEUR

		// Parcours de tous les élèves pour lesquels la compétence a été évalué
		foreach($aEvalInds as $sEleveNom => $aEvalsInds)
		{
			// Parcours de toutes les périodes
			foreach($aPeriodes as $sPeriodeNom)
			{
				// Si sur cette periode, on a évaluée la compétence
				if(isset($aEvalsInds[$sPeriodeNom]))
				{
					// Nombre d'évaluations sur l'élève pour la compétence
					$nNbEval = 0;
					// Somme des notes des évaluations
					$nSommeEval = 0;
					// Pour chaque évaluation
					foreach($aEvalsInds[$sPeriodeNom] as $nEvalIndId => $aRes)
					{
						// Stocke la note pour informations
						$aEvalIndsMoy[$sEleveNom][$sPeriodeNom]["NOTE_LABEL_{$nEvalIndId}"] = $aRes['NOTE_LABEL'];

						// Les competences évaluées à "Non Evaluees" ne comptent pas dans la moyenne
						if($aRes['NOTE_NOTE'] == 0)
						{
							continue;
						}
						// Somme des évaluations
						$nSommeEval += $aRes['NOTE_NOTE'];
						// Incrémente le nombre d'évaluations
						$nNbEval++;
					}
					// Calcul de la moyenne
					$nMoy = ($nNbEval != 0) ? $nSommeEval / $nNbEval : 0;
					// On stocke enfin la moyenne de ces compétences
					$aEvalIndsMoy[$sEleveNom][$sPeriodeNom]['NOTE_LABEL'] = Moyenne::compute_and_label($nMoy);
				} else {// sinon, on initialise la valeur à vide
					$aEvalIndsMoy[$sEleveNom][$sPeriodeNom]['NOTE_LABEL'] = null;
				}
			} // Fin itération sur les périodes renseignées
		}// Fin itération sur les périodes
		// fin calcul de la moyenne

		// ===== Génération du tableau résultat =====

		$aRes['NOTES'] = $aNotes;
		$aRes['PERIODES'] = $aPeriodes;
		$aRes['COMPETENCE_NOM'] = $sCompetenceNom;
		$aRes['CLASSE_NOM'] = $sClasseNom;
		$aRes['EVAL_INDS'] = isset($aEvalIndsMoy) ? $aEvalIndsMoy : array();

		return $aRes;
	}// fin recap_annuel_competence
}// fin class Livret