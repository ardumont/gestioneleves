<?php
/**
 * Classe de génération de livret annuel ou périodique.
 * @author Antoine Romain Dumont aka tony or ToNyX
 */
class Livret
{
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
		$aEvalInds = Database::fetchArrayWithMultiKey($sQuery, array('DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM', 'CLASSE_NOM', 'NIVEAU_NOM', 'PERIODE_NOM'));
		// $aEvalInds[NOM DU DOMAINE][NOM DE LA MATIERE][NOM DE LA COMPETENCE][NOM DE LA CLASSE][NOM DU NIVEAU][NOM DE LA PERIODE][COLONNE] = VALEUR

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
		$aEvalInds = Database::fetchArrayWithMultiKey($sQuery, array('DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM', 'CLASSE_NOM', 'NIVEAU_NOM', 'PERIODE_NOM'));
		// $aEvalInds[NOM DU DOMAINE][NOM DE LA MATIERE][NOM DE LA COMPETENCE][NOM DE LA CLASSE][NOM DU NIVEAU][NOM DE LA PERIODE][COLONNE] = VALEUR

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
	 * Récapitulatif périodique.
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
			AND PERIODE_ID = {$nPeriodeId}
			ORDER BY DOMAINE_NOM ASC, MATIERE_NOM ASC, COMPETENCE_NOM ASC
________EOQ;
		$aEvalInds = Database::fetchArrayWithMultiKey($sQuery, array('DOMAINE_NOM', 'MATIERE_NOM', 'COMPETENCE_NOM', 'CLASSE_NOM', 'NIVEAU_NOM', 'PERIODE_NOM'));
		// $aEvalInds[NOM DU DOMAINE][NOM DE LA MATIERE][NOM DE LA COMPETENCE][NOM DE LA CLASSE][NOM DU NIVEAU][NOM DE LA PERIODE][COLONNE] = VALEUR

		// Calcule le nom et le prénom
		$aNomPrenom = explode(" ", $aEleve['ELEVE_NOM']);

		// ===== Génération du tableau résultat =====

		$aRes['ELEVE'] = $aEleve;
		$aRes['CLASSES_ELEVES'] = $aClassesEleve;
		$aRes['NOTES'] = $aNotes;
//		$aRes['NOTES_VALUES'] = $aNotesValues;
		$aRes['PERIODES'] = $aPeriodes;
		$aRes['CLASSES_NIVEAUX'] = $aClassesNiveaux;
		$aRes['DOMAINES_MATIERES_COMPETENCES'] = $aDomainesMatieresCompetences;
		$aRes['EVAL_INDS'] = $aEvalInds;
		$aRes['NOM_PRENOM'] = $aNomPrenom;

		return $aRes;
	}// fin recap_period
}