<?php

/**
 * ProjectSion : Classe pour gérer les profils et les droits s'y rattachant.
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
	 public static $USER_RIGHTS = array
	 (
		'application_list',		'application_add',		'application_edit',		'application_delete',
		'project_list',			'project_view',			'project_add',			'project_edit',
		'project_delete',		'task_add',				'task_edit',			'task_delete',
		'task_change_project', 	'task_change_parent',	'activity_list',		'activity_edit',
		'summary_user', 	 	'summary_team',			'summary_project',		'summary_project_detail',
	 );

	 /**
	 * Liste de tous les droits "Admin" possibles.
	 *
	 * PHP n'autorise pas les tableaux avec le mot clef 'const'.
	 */
	 public static $ADMIN_RIGHTS = array
	 (
		'profil_list',				'profil_add',				'profil_edit',			'profil_delete',
		'user_list',				'user_add',					'user_edit',			'user_active',
		'ttask_list',				'ttask_add',				'ttask_edit',			'ttask_delete',
		'bank_holiday_list', 		'bank_holiday_add',			'bank_holiday_edit',	'bank_holiday_delete',
	 	'admin_calcul_denormalise', 'admin_calcul_arborescence',
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
		if(isset($_SESSION['user_id']) == false) return;

		// Recherche des droits en session.
		if(isset($_SESSION['PROFIL_RIGHTS']) == true)
		{
			// Lecture des droits depuis la session
			self::$s_aRights = $_SESSION['PROFIL_RIGHTS'];
		}
		else
		{
			// Initialisation des variables SQL
			$nUserId = $_SESSION['user_id'];

			// Lecture du profil en BDD
			$sQuery = <<< _EOQ_
				SELECT
					USER_PROFIL_ID
				FROM USERS
				WHERE USER_ID = {$nUserId}
_EOQ_;

			$nProfilId = Database::fetchOneValue($sQuery);

			if($nProfilId != 1)
			{
				// Lecture des droits en BDD
				$sQuery = <<< _EOQ_
					SELECT
						PROFIL_RIGHT
					FROM USERS, PROFILS_REL_RIGHTS
					WHERE USER_PROFIL_ID = PROFIL_ID
					  AND USER_ID = {$nUserId}
_EOQ_;

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
	}

	/**
	 * Vérification des droits
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
	}

	/**
	 * Fonction de test pour savoir si l'utilisateur possède au moins un droit d'administration.
	 * @return <bool>	true, l'utilisateur possède des droits d'administration
	 * 					false, sinon
	 */
	public static function hasAdminRight()
	{
		// Parcours des droits d'administration
		foreach(self::$ADMIN_RIGHTS as $sAdminRight)
		{
			// Dès qu'on trouve un droit d'administration, on s'arrête et on renvoie vrai
			if(self::hasRight($sAdminRight))
			{
				return true;
			}
		}
		// ici, aucun droit d'administration
		return false;
	}
}
?>