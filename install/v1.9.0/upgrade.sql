-- =============================================================================
-- Base de données          : GESTION_ELEVES
-- Version de l'application : 1.9.0
-- Mode                     : UPDATE
-- =============================================================================

--#STEP(TRANSACTION)
-- Création des profils
INSERT INTO `PROFILS` (`PROFIL_ID`, `PROFIL_NAME`, `PROFIL_COMMENT`) VALUES
(2, 'Professeur', 'Professeur utilisateur de l''application'),
(3, 'Directeur/Directrice', 'Directeur ou directrice d''une école');

--#STEP(TRANSACTION)
-- Ajout des droits pour le profil 'Professeur'
INSERT INTO `PROFILS_REL_RIGHTS`(`PROFIL_ID`, `PROFIL_RIGHT`) VALUES
(2, 'eleve_active'),
(2, 'eleve_add'),
(2, 'eleve_edit'),
(2, 'eleve_list'),
(2, 'consultation_list'),
(2, 'eval_ind_add'),
(2, 'eval_ind_delete'),
(2, 'eval_ind_edit'),
(2, 'eval_ind_list'),
(2, 'eval_col_add'),
(2, 'eval_col_delete'),
(2, 'eval_col_edit'),
(2, 'eval_col_list'),
(2, 'livret_list'),
(2, 'profil_edit');

--#STEP(TRANSACTION)
-- Ajout des droits pour le profil 'Directeur/Directrice'
INSERT INTO `PROFILS_REL_RIGHTS`(`PROFIL_ID`, `PROFIL_RIGHT`) VALUES
(3, 'profil_list'), 
(3, 'profil_add'), 
(3, 'profil_edit'),  
(3, 'profil_delete'), 
(3, 'professeur_list'),  
(3, 'professeur_add'),
(3, 'professeur_edit'), 
(3, 'professeur_delete'),
(3, 'ecole_list'), 
(3, 'ecole_add'), 
(3, 'ecole_edit'), 
(3, 'ecole_delete'),
(3, 'classe_list'), 
(3, 'classe_add'), 
(3, 'classe_edit'), 
(3, 'classe__delete'),
(3, 'eleve_list'),
(3, 'cycle_list'), 
(3, 'cycle_add'), 
(3, 'cycle_edit'),
(3, 'cycle_delete'),
(3, 'niveau_list'), 
(3, 'niveau_add'), 
(3, 'niveau_edit'), 
(3, 'niveau_delete'),
(3, 'domaine_list'), 
(3, 'domaine_add'), 
(3, 'domaine_edit'), 
(3, 'domaine_delete'),
(3, 'matiere_list'), 
(3, 'matiere_add'), 
(3, 'matiere_edit'), 
(3, 'matiere_delete'),
(3, 'competence_list'), 
(3, 'competence_add'), 
(3, 'competence_edit'), 
(3, 'competence_delete'),
(3, 'note_list'),
(3, 'periode_list'), 
(3, 'periode_add'), 
(3, 'periode_edit'), 
(3, 'periode_delete');
