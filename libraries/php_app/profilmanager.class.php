<?php
/**
 * Classe pour gérer les profils et les droits s'y rattachant.
 *
 * @author Lionel SAURON
 * @version 1.0.0
 */
class ProfilManager
{
	/**
	 * Liste de tous les droits "Utilisateur" possibles.
	 *
	 * PHP n'autorise pas les tableaux avec le mot clef 'const'.
	 */
	 public static $USER_RIGHTS = array(
		'eleve_list', 'eleve_add', 'eleve_edit', 'eleve_active',
		'consultation_list',
		'eval_ind_list', 'eval_ind_add', 'eval_ind_edit', 'eval_ind_delete',
 		'eval_col_list', 'eval_col_add', 'eval_col_edit', 'eval_col_delete',
		'livret_list', 'livret_add', 'livret_edit', 'livret_delete',
 		'profil_list', 'profil_add', 'profil_edit', 'profil_delete',
	);

	 /**
	 * Liste de tous les droits "Admin" possibles.
	 *
	 * PHP n'autorise pas les tableaux avec le mot clef 'const'.
	 */
	 public static $ADMIN_RIGHTS = array(
		'admin_profil_list', 'admin_profil_add', 'admin_profil_edit', 'admin_profil_delete',
		'professeur_list', 'professeur_add', 'professeur_edit', 'professeur_delete',
		'ecole_list', 'ecole_add', 'ecole_edit', 'ecole_delete',
		'classe_list', 'classe_add', 'classe_edit', 'classe_delete',
		'admin_eleve_list',
		'cycle_list', 'cycle_add', 'cycle_edit', 'cycle_delete',
 		'niveau_list', 'niveau_add', 'niveau_edit', 'niveau_delete',
 		'domaine_list', 'domaine_add', 'domaine_edit', 'domaine_delete',
 		'matiere_list', 'matiere_add', 'matiere_edit', 'matiere_delete',
	 	'competence_list', 'competence_add', 'competence_edit', 'competence_delete',
 		'note_list',
 		'periode_list', 'periode_add', 'periode_edit', 'periode_delete',
	 	'import_csv_cycle',
	    'import_xml_cycle',
	    'import_xml_classe'
    );

	/**
	 * Liste des droits possédés par la personne.
	 */
	private static $s_aRights = array();

	/**
	 * Lecture des droits depuis la session ou la BDD.
	 *
	 * ProjectSion n'étant pas une application militaire, ni une application
	 * destinée à gérer tout les projets d'une entreprise (en une seule fois),
	 * on stocke les droits en session pour éviter un accès en BDD.
	 */
	public static function loadRights()
	{
		// A t'on un utilisateur ? Non => On sort
		if(isset($_SESSION['PROFESSEUR_ID']) == false) return;

		// Recherche des droits en session.
		if(isset($_SESSION['PROFIL_RIGHTS']) == true)
		{
			// Lecture des droits depuis la session
			self::$s_aRights = $_SESSION['PROFIL_RIGHTS'];
		}
		else
		{
			// Initialisation des variables SQL
			$nUserId = $_SESSION['PROFESSEUR_ID'];

			// Lecture du profil en BDD
			$sQuery = <<< ____________EOQ
				SELECT
					PROFESSEUR_PROFIL_ID
				FROM PROFESSEURS
				WHERE PROFESSEUR_ID = {$nUserId}
____________EOQ;

			$nProfilId = Database::fetchOneValue($sQuery);

			if($nProfilId != 1)
			{
				// Lecture des droits en BDD
				$sQuery = <<< ________________EOQ
					SELECT
						PROFIL_RIGHT
					FROM PROFESSEURS
						INNER JOIN PROFILS_REL_RIGHTS
							ON PROFESSEUR_PROFIL_ID = PROFIL_ID
					WHERE PROFESSEUR_ID = {$nUserId}
________________EOQ;

				self::$s_aRights = Database::fetchColumn($sQuery);
			}
			else
			{
				// On est administrateur. On a tout les droits.
				self::$s_aRights = array_merge(self::$USER_RIGHTS, self::$ADMIN_RIGHTS);
			}

			// Sauvegarde des droits en session
			$_SESSION['PROFIL_RIGHTS'] = self::$s_aRights;
		}
	}// end loadRights

	/**
	 * Vérification des droits.
	 *
	 * @param $sRightNeed(string) Nom du droit à vérifier.
	 * @return <code>true</code> ou <code>false</code>
	 */
	public static function hasRight($sRightNeed)
	{
		$bHasRight = false;

		if(in_array($sRightNeed, self::$s_aRights) == true)
		{
			$bHasRight = true;
		}

		return $bHasRight;
	}// end hasRight

	/**
	 * Teste si l'utilisateur possède au moins un droit d'administration.
	 * @return <code>true</code> ou <code>false</code>
	 */
	public static function hasAdminRight()
	{
		// Comparaison des droits admin et des droits possédés
		$aCommonRights = array_intersect(self::$s_aRights, self::$ADMIN_RIGHTS);

		// A t'on un droit d'administration ?
		$bAdminRight = (count($aCommonRights) > 0);

		return $bAdminRight;
	}// end hasAdminRight
}// end class ProfilManager
