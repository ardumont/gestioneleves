<?php
/**
 * Classe de calcul des moyennes.
 * @author Antoine Romain Dumont aka tony or ToNyX
 */
class Moyenne
{
	/**
	 * Calcule l'arrondi d'une moyenne.
	 * @param $nMoy
	 * @return int
	 */
	public static function compute($nMoy)
	{
		if(0 <= $nMoy && $nMoy < 2.5)
		{
			$nNote = 0;
		} else if(2.5 <= $nMoy && $nMoy < 7.5) {
			$nNote = 5;
		} else if(7.5 <= $nMoy && $nMoy < 12.5) {
			$nNote = 10;
		} else if(12.5 <= $nMoy && $nMoy < 17.5) {
			$nNote = 15;
		} else if(17.5 <= $nMoy) {
			$nNote = 20;
		}
		return $nNote;
	}// fin compute

	/**
	 * Calcule le libellé de la note.
	 * @param $nNote
	 * @return string
	 */
	public static function label($nNote)
	{
		$sNoteQuery = Database::prepareString($nNote);

		$sQuery = <<< ________EOQ
			SELECT NOTE_LABEL
			FROM NOTES
			WHERE NOTE_NOTE={$sNoteQuery}
________EOQ;
		$sLabel = Database::fetchOneValue($sQuery);
		return $sLabel;
	}// fin note

	/**
	 * Retourne le libelle d'une moyenne apres l'avoir arrondi.
	 * @param $nMoy
	 * @return string
	 */
	public static function compute_and_label($nMoy)
	{
		return Moyenne::label(Moyenne::compute($nMoy));
	}// fin compute_and_label
}// fin Moyenne