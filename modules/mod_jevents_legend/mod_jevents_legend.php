<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: mod_jevents_cal.php 1057 2008-04-21 18:06:33Z tstahl $
 * @package     JEvents
 * @subpackage  Module JEvents Calendar
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */


defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (dirname(__FILE__).DS.'helper.php');

$jevhelper = new modJeventsLegendHelper();

$theme = JEV_CommonFunctions::getJEventsViewName();
require_once(JModuleHelper::getLayoutPath('mod_jevents_legend',$theme.DS."legend"));

$viewclass = ucfirst($theme)."ModLegendView";
$modview = new $viewclass($params, $module->id);
echo $modview->displayCalendarLegend();