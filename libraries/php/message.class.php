<?php

class Message
{
private static $s_aErrorMsg = array();

public static function addError($sErrorMsg)
{
	self::$s_aErrorMsg[] = $sErrorMsg;
}

public static function addErrorFromFormValidation($aErrorMsg)
{
	foreach($aErrorMsg as $aErrorByInput)
	{
		foreach($aErrorByInput as $sErrorMessage)
		{
			self::addError($sErrorMessage);
		}
	}
}

public static function hasError()
{
	$bHasError = (count(self::$s_aErrorMsg) > 0) ? true : false;
	
	return $bHasError;
}

public static function getErrorAndClear()
{
	$aErrorMsg = self::$s_aErrorMsg;
	
	self::$s_aErrorMsg = array();
	
	return $aErrorMsg;
}

public static function loadFromSession($aErrorMsg)
{
	 self::$s_aErrorMsg = $aErrorMsg;
}

public static function saveToSession()
{
	return self::$s_aErrorMsg;
}

}
?>