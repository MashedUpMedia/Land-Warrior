<?php
/**
* @version		jce.php 2007-02-21
* @package		Joomla Content Editor (JCE)
* @subpackage	Components
* @copyright	Copyright (C) 2005 - 2007 Ryan Demmer. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$task = JRequest::getCmd( 'task' );

/*
 * Editor or plugin request.
 */
if( $task == 'plugin' || $task == 'help' ){
	require_once( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_jce' .DS. 'editor.php' );
	exit();
}
if( $task == 'popup' ){
	require_once( dirname( __FILE__ ) .DS. 'popup.php' );
}
?>
