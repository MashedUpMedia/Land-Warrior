<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: mod_events_switchview.php 1117 2008-07-06 17:20:59Z tstahl $
 * @package     JEvents
 * @subpackage  Module JEvents Switch View
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */

defined( '_JEXEC' ) or die( 'Restricted access' );


// CHECK EVENTS COMPONENT 
$file = JPATH_SITE . '/components/com_jevents/mod.defines.php';
if (file_exists($file) ) {
	include_once($file);
	include_once(JEV_LIBS."/modfunctions.php");

} else {
	die ("JEvents Layout Switcher\n<br />This module needs the JEvents component");
}

// load language constants
JEVHelper::loadLanguage('modswitchview');

// existing values
$cfg = & JEVConfig::getInstance();
// priority of view setting is url, cookie, config, 
$jEventsView = $cfg->get('com_calViewName',"default");

$cur_view = JEV_CommonFunctions::getJEventsViewName();

// paramaters
$preview_height = $params->get( 'preview_height', 90 );
$preview_width 	= $params->get( 'preview_width', 140 );
$show_preview 	= $params->get( 'show_preview', 1 );

// get views names from template directory
$darray = array();
foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile) {
	$darray[] = JHTML::_('select.option', $viewfile, $viewfile);
}
sort( $darray );

// Show the preview image
// Set up JavaScript for nd cookie based switching
$onchange = "";
if ($show_preview) {
	$onchange = "showimage();";
}
?>
<img src="<?php echo  JURI::root()."components/com_jevents/views/$cur_view/assets/images/view_thumbnail.png";?>" name="preview" border="1" width="<?php echo $preview_width;?>" height="<?php echo $preview_height;?>" alt="<?php echo $cur_view; ?>" />
<script language='JavaScript1.2' type='text/javascript'>
<!--
	function showimage() {
		//if (!document.images) return;
		document.images.preview.src = '<?php echo  JURI::root();?>components/com_jevents/views/' + getSelectedValue( 'jeventviewform', 'jos_change_view' ) + '/assets/images/view_thumbnail.png';
	}
	function setJViewcookie(index){
		value = getSelectedValue("jeventviewform","jos_change_view");
		document.cookie="jevents_view="+value+";path=/";
		alert("JEvents View changed to "+value);
	}
	function getSelectedValue( frmName, srcListName ) {
		var form = eval( 'document.' + frmName );
		var srcList = eval( 'form.' + srcListName );

		i = srcList.selectedIndex;
		if (i != null && i > -1) {
			return srcList.options[i].value;
		} else {
			return null;
		}
	}
-->
</script>
<?php
$catidsOut = null;
$modcatids = null;
$catidList = null;
global $option, $Itemid;
if ($option == "com_jevents" && $Itemid!=0){
	$myItemid = $Itemid;
}
else $myItemid = JEVHelper::getItemid();
$target = JRoute::_("index.php?option=com_jevents&Itemid=".$myItemid);
?>
<form action="<?php echo $target;?>" name="jeventviewform" method="post">
	<?php
	echo JHTML::_('select.genericlist', $darray, 'jos_change_view', " class=\"button\" onchange=\"$onchange\"",'value', 'text', $cur_view );
	?>
	<input class="button" type="submit" value="<?php echo JText::_('JEV_CMN_SELECT');?>" onclick="setJViewcookie(this.selectedIndex);"/>
</form>
