<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */
 
/**
 * Smarty truncate modifier plugin
 * 
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *               optionally splitting in the middle of a word, and
 *               appending the $etc string or inserting $etc into the middle.
 * 
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php truncate (Smarty online manual)
 * @param string  $string      input string
 * @param integer $length      length of truncated text
 * @param string  $etc         end string
 * @param boolean $break_words truncate at word boundary
 * @param boolean $middle      truncate in the middle of text
 * @return string truncated string
 */
function smarty_modifier_truncate($string, $length = 80, $etc = '', $break_words = false, $middle = false) {
    $l = strlen($string);
	if ( $l <= $length )
		return $string;

	$tmp = '';

	for ($i = 0; $i < $length; ++$i)
	{
		$t = mb_substr($string, $i, 1);
		if (strlen($t) != 1)
		{
			--$length;
			if ($i == $length)
			{
				break;
			}
		}
		$tmp .= $t;
	}
	(mb_strlen($string) > $length) && $tmp .= $etc;
	return $tmp;
} 

?>