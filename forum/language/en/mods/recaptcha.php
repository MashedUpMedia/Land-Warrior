<?php
/**
*
* groups [English]
*
* @package phpBB3 reCAPTCHA
* @version $Id: recaptcha.php 8 2009-02-11 06:39:14Z xhotshotx $
* @copyright (c) 2009 Michael Williams (mtotheikle) http://startrekguide.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* Security Check
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'NO_RECAP_CONFIRM_CODE'			=> 'Please enter confirmation code',
	'INVALID_RECAP_CONFIRM_CODE'	=> 'Invalid confirmation code',	
));

?>