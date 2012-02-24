<?php

/*
/**
* CHRONOFORMS version 3.0 
* Copyright (c) 2008 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
You are not allowed to copy or use or rebrand or sell any code at this page under your own name or any other identity!
* See readme.html.
* Visit http://www.ChronoEngine.com for regular update and information.
**/

/* ensure that this file is not called from another file */
defined('_JEXEC') or die('Restricted access'); 

//global $mosConfig_lang, $mosConfig_absolute_path, $chronocontact_params;
// Loading of the database class and the HTML class
require_once( JApplicationHelper::getPath( 'admin_html' ) ); 
require_once( JApplicationHelper::getPath( 'class' ) );

$id 	= JRequest::getVar( 'id', '', 'get', 'int', 0 );
//$task 	= JRequest::getVar( 'task', '', 'post', 'string', '' );
$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array');
if (!is_array( $cid )) {
	$cid = array(0);
}
$cid_del 	= JRequest::getVar( 'cid_del', array(), 'post', 'array');
if (!is_array( $cid_del )) {
	$cid_del = array(0);
}

// case differentiation
switch ($task) {
	case "ajaxfields":
		ajaxfields();
		break;
	case "adminview":
		showadminformChronoContact( '', $cid[0], $option );
		break;
	case "submitadminform":
		submitadminformChronoContact( $option );
		break;
	case "editdata":
		showadminformChronoContact( $cid[0], '', $option );
		break;
	/////////////////////
	case "publish":
		publishChronoContact( $cid, 1, $option );
		break;
	case "unpublish":
		publishChronoContact( $cid, 0, $option );
		break;
	case "add":
		editChronoContact( 0, $option );
		break;
	case "edit":
		editChronoContact( $cid[0], $option );
		break;
	case "applychanges":
		saveChronoContact( $option, $task );
		//editChronoContact( $_POST['id'], $option );
		break;
	case "remove":
		removeChronoContact( $cid, $option );
		break;
	case "save":
		saveChronoContact( $option, $task );
		break;
	case "copy":
		copyChronoContact( $cid[0], $option );
		break;
	case "cancel":
		cancelChronoContact( $option );
		break;
	case "addmenuitem":
		addmenuitem( $option );
		break;
	////////
	case "cancelview":
	case "show":
		showdataChronoContact( $cid[0], $option );
		break;
	case "viewdata":
		viewdataChronoContact( $cid[0], $option );
		break;
	case "createtable":
		maketableChronoContact( $cid[0], $option );
		break;
	case "deletetable":
		deletetableChronoContact( $cid[0], $option);
		break;
	case "finalizetable":
		finalizetableChronoContact( $option );
		break;
	case "deleterecord":
		deleterecordChronoContact( $cid, $option );
		break;
	////// backup
	case "backup":
		backupChronoContact( $cid[0], $option );
		break;
	case "restore1":
		restore1ChronoContact( $cid[0], $option );
		break;
	case "restore2":
		restore2ChronoContact( $cid[0], $option );
		break;
	case "backexcel":
		BackupExcel( $id, $option );
		break;
	case "backcsv":
		BackupCSV( $id, $option );
		break;
	////// config
	case 'config':
		showConfig( $option );
		break;
	case 'saveconfig':
		saveSettings( $option );
		break;
	case 'cancelconfig':
		cancelSettings( $option );
		break;
	case 'save_conf':
		save_conf( $option );
		break;
	////// wizard
	case 'form_wizard':
		form_wizard( $option );
		break;
	case 'save_form_wizard':
		save_form_wizard( $option );
		break;
	///// menu tools
	case 'menu_creator':
		menu_creator( $option );
		break;
	case 'menu_remover':
		menu_remover( $option );
		break;
	case 'menu_save':
		menu_save( $option );
		break;
	case 'menu_delete':
		menu_delete( $cid, $option );
		break;
	///////////////
	default: 
		global $mainframe;
		$database =& JFactory::getDBO();
		$switch = 1;
		if(strpos("x".$task,"plugin_")){
			$directory = JPATH_SITE.'/components/com_chronocontact/plugins/';
			$results = array();
			$handler = opendir($directory);
			while ($file = readdir($handler)) {
				if ( $file != '.' && $file != '..' && substr($file, -4) == '.php' && substr($file, 0, 3) == 'cf_')
					$results[] = str_replace(".php","", $file);
			}
			closedir($handler);
			foreach($results as $result){
				if($task == 'plugin_'.$result){
					require_once(JPATH_SITE."/components/com_chronocontact/plugins/".$result.".php");
					${$result} = new $result();
					$switch = 0;
					
					$database->setQuery( "SELECT id FROM #__chrono_contact_plugins WHERE form_id='".$cid[0]."' AND name='".$result."'" );
					$id = $database->loadResult();
					$row =& JTable::getInstance('chronocontactplugins', 'Table');
					$row->load( $id );
					${$result}->show_conf($row, $id, $cid[0], $option);
					break;
				}
			}
		}
		//echo 'xxx'.$cf_joomla_registration->result_TITLE;
		if($switch == 1){
			showChronoContact( $option );
		}
		break;
}

function save_conf( $option ){
	$plugin = JRequest::getVar('name');
	require_once(JPATH_SITE."/components/com_chronocontact/plugins/".$plugin.".php");
	${$plugin} = new $plugin();
	${$plugin}->save_conf($option);
}
function ajaxfields(){
	global $mainframe;
	$database =& JFactory::getDBO();
	$tablename = JRequest::getVar('table');
	$tables = array( $tablename );
	$result = $database->getTableFields( $tables );
	$table_fields = array_keys($result[$tablename]);
	echo "*#*"."yes"."#999-#";
	?>
	<select name="params[field_name]">
		<?php
		foreach($table_fields as $table_field){
		?>
			<option value="<?php echo $table_field; ?>"><?php echo $table_field; ?></option>
		<?php
		}
		?>
	</select>
	<?php
	echo "*#*";
}
// Publishing of the entries
function publishChronoContact( $cid, $publish, $option ) {
	global $mainframe;
	$database =& JFactory::getDBO();
	if (count( $cid ) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		JError::raiseWarning(100, 'Select a item to '.$action);
		$mainframe->redirect( "index2.php?option=$option" );
	}
    $cids = implode( ',', $cid );
	$database->setQuery( "UPDATE #__chrono_contact SET published=".$publish." WHERE id IN ($cids)");
	if (!$database->query()) {
		JError::raiseWarning(100, $database->getErrorMsg());
		$mainframe->redirect( "index2.php?option=$option" );
	}
 	if (count( $cid ) == 1) {
		$row =& JTable::getInstance('chronocontact', 'Table'); 
		$row->checkin( $cid[0] );
	}
	$mainframe->redirect( "index2.php?option=$option" );
}

function editChronoContact( $id, $option ) {
	$database =& JFactory::getDBO();
	$row =& JTable::getInstance('chronocontact', 'Table'); 
	$row->load($id);
	HTML_ChronoContact::editChronoContact( $row, $option );
}
// deletion of entries
function removeChronoContact( $cid, $option ) {
	global $mainframe;
	$database =& JFactory::getDBO();
	if (!is_array( $cid ) || count( $cid ) < 1) {
		JError::raiseWarning(100, 'Please select an entry to delete');
		$mainframe->redirect( "index2.php?option=$option" );
	}
	$cids = implode( ',', $cid );
	$database->setQuery( "DELETE FROM #__chrono_contact WHERE id IN ($cids)" );
	if (!$database->query()) {
		JError::raiseWarning(100, $database->getErrorMsg());
		$mainframe->redirect( "index2.php?option=$option" );
	}
	$database->setQuery( "DELETE FROM #__chrono_contact_emails WHERE formid IN ($cids)" );
	if (!$database->query()) {
		JError::raiseWarning(100, $database->getErrorMsg());
		$mainframe->redirect( "index2.php?option=$option" );
	}
	$mainframe->redirect( "index2.php?option=$option" );
}
function copyChronoContact( $id , $option ) {
	global $mainframe;
	$database =& JFactory::getDBO();
	$row =& JTable::getInstance('chronocontact', 'Table'); 
	$row->load($id);
	$row->id = '';
	if (!$row->store()) {
		JError::raiseWarning(100, $row->getError());
		$mainframe->redirect( "index2.php?option=$option" );
	}
	$mainframe->redirect( "index2.php?option=".$option );
}
// save entry
function saveChronoContact( $option, $task ) {
	global $mainframe;
	$database =& JFactory::getDBO();
	$row =& JTable::getInstance('chronocontact', 'Table');
	$post = JRequest::get( 'post' , JREQUEST_ALLOWRAW );
	//$post = JRequest::getVar( 'description', '', 'post','string', _J_ALLOWRAW );
	if (!$row->bind( $post )) {
		JError::raiseWarning(100, $row->getError());
		$mainframe->redirect( "index2.php?option=$option" );
	}
	
	$params = JRequest::getVar( 'params', array(), 'post', 'array');
	if (is_array( $params )) {
		$txt = array();
		foreach ( $params as $k=>$v) {
			$txt[] = "$k=$v";
		}
		//$plugins = mosgetparam($_POST, 'plugins', array(0));
		$plugins 	= JRequest::getVar( 'plugins', array(), 'post', 'array');
		if($plugins){
			$txt[] = "plugins=".implode(",",$plugins);
		}
		$mplugins_order = JRequest::getVar( 'mplugins_order', array(), 'post', 'array');
		if($mplugins_order){
			$txt[] = "mplugins_order=".implode(",",$mplugins_order);
		}
		$tablenames 	= JRequest::getVar( 'tablenames', array(), 'post', 'array');
		if($tablenames){
			$txt[] = "tablenames=".implode(",",$tablenames);
		}
		$row->paramsall = implode( "\n", $txt );
	}
	//$row->titlesall = $_POST['titles'];
	$row->dbclasses = "";
	if($tablenames[0]){
		foreach($tablenames as $tablename){
			//Create Class
			$tables = array();
			$tables[] = $tablename;
			$result = $database->getTableFields( $tables, false );
			//print_r($result[$row->tablenames]);
			$table_fields = $result[$tablename];
			$row->dbclasses .= "<?php";	
			$row->dbclasses .= "\n";
			$row->dbclasses .= "if (!class_exists('Table".str_replace($mainframe->getCfg('dbprefix'), '', $tablename)."')) {";
			$row->dbclasses .= "\n";
			$row->dbclasses .= "class Table".str_replace($mainframe->getCfg('dbprefix'), '', $tablename)." extends JTable {";	
			$primary = 'id';
			foreach($table_fields as $table_field => $field_data){
				$row->dbclasses .= "\n";
				$row->dbclasses .= "var \$".$table_field." = null;";
				if($field_data->Key == 'PRI')$primary = $table_field;
			}
			$row->dbclasses .= "\n";
			$row->dbclasses .= "function __construct( &\$database ) {";
			$row->dbclasses .= "\n";
			$row->dbclasses .= "parent::__construct( '".$tablename."', '".$primary."', \$database );";
			$row->dbclasses .= "\n";
			$row->dbclasses .= "}";
			$row->dbclasses .= "\n";
			$row->dbclasses .= "}";
			$row->dbclasses .= "\n";
			$row->dbclasses .= "}";
			$row->dbclasses .= "\n";
			$row->dbclasses .= "?>";
			$row->dbclasses .= "\n";
		}
	}
	$row->autogenerated = "";
	
	$tables = explode("," , $paramsvalues->tablenames);
	foreach($tablenames as $tablename){
		$row->autogenerated = $row->autogenerated.'<?php
		if($paramsvalues->dbconnection == "Yes"){
			$user = JFactory::getUser();			
			$row =& JTable::getInstance("'.str_replace($mainframe->getCfg("dbprefix"), "", $tablename).'", "Table");
			srand((double)microtime()*10000);
			$inum	=	"I" . substr(base64_encode(md5(rand())), 0, 16);
			JRequest::setVar( "recordtime", JRequest::getVar( "recordtime", date("Y-m-d")." - ".date("H:i:s"), "post", "string", "" ));
			JRequest::setVar( "ipaddress", JRequest::getVar( "ipaddress", $_SERVER["REMOTE_ADDR"], "post", "string", "" ));
			JRequest::setVar( "uid", JRequest::getVar( "uid", $inum, "post", "string", "" ));
			JRequest::setVar( "cf_user_id", JRequest::getVar( "cf_user_id", $user->id, "post", "int", "" ));
			$post = JRequest::get( "post" , JREQUEST_ALLOWRAW );			
			if (!$row->bind( $post )) {
				JError::raiseWarning(100, $row->getError());
			}				
			if (!$row->store()) {
				JError::raiseWarning(100, $row->getError());
			}
			global $row_'.$tablename.';
			$row_'.$tablename.' = $row;
		}
		?>
		';
	}
	
	
	if (!$row->store()) {
		JError::raiseWarning(100, $row->getError());
		$mainframe->redirect( "index2.php?option=$option" );
	}
	
	//Emails
	//Delet old emails and save new clean ones
	$database->setQuery( "DELETE FROM #__chrono_contact_emails WHERE formid = '".$row->id."'" );
	if (!$database->query()) {
		JError::raiseWarning(100, $database->getErrorMsg());
		$mainframe->redirect( "index2.php?option=$option" );
	}
	preg_match_all('/start_email{.*?}end_email/i', $post['emails_temp'], $matches);
	$emails = array();
	$template_count2 = 0;
	$emails_ids =  explode(',', str_replace('email_', '', $post['emails_temp_ids']));
	foreach ( $matches[0] as $email ) {
	$template_count = $emails_ids[$template_count2+1];
	//echo $email;
		$email = preg_replace('/start_email{/i', '', $email);
		$email = preg_replace('/}end_email/i', '', $email);
		$email_elements = explode('||', $email);
		//$emails[] = trim($email);
		$post2 = array();
		//$post2['emailid'] = ;
		$post2['to'] = str_replace('TO=[', '', str_replace(']', '', $email_elements[0]));
		$post2['dto'] = str_replace('DTO=[', '', str_replace(']', '', $email_elements[1]));
		$post2['subject'] = str_replace('SUBJECT=[', '', str_replace(']', '', $email_elements[2]));
		$post2['dsubject'] = str_replace('DSUBJECT=[', '', str_replace(']', '', $email_elements[3]));
		$post2['cc'] = str_replace('CC=[', '', str_replace(']', '', $email_elements[4]));
		$post2['dcc'] = str_replace('DCC=[', '', str_replace(']', '', $email_elements[5]));
		$post2['bcc'] = str_replace('BCC=[', '', str_replace(']', '', $email_elements[6]));
		$post2['dbcc'] = str_replace('DBCC=[', '', str_replace(']', '', $email_elements[7]));
		$post2['fromname'] = str_replace('FROMNAME=[', '', str_replace(']', '', $email_elements[8]));
		$post2['dfromname'] = str_replace('DFROMNAME=[', '', str_replace(']', '', $email_elements[9]));
		$post2['fromemail'] = str_replace('FROMEMAIL=[', '', str_replace(']', '', $email_elements[10]));
		$post2['dfromemail'] = str_replace('DFROMEMAIL=[', '', str_replace(']', '', $email_elements[11]));
		$post2['formid'] = $row->id;
		
		//$post2['params'] = $post['params_email_'.$template_count];
		$params = explode(",", $post['params_email_'.$template_count]);
		$txt = array();
		$txt[0] = "recordip=".$params[0];
		$txt[1] = "emailtype=".$params[1];
		$txt[2] = "enabled=".$params[2];
		$txt[3] = "editor=".$params[3];
		$post2['params'] = implode("\n", $txt);
		$post2['template'] = trim($post['editor_email_'.$template_count]) ? trim($post['editor_email_'.$template_count]) : trim($post['extra_editor_email_'.$template_count]);
		$template_count2++;
		$post2['enabled'] = $params[2];
		
		$row2 =& JTable::getInstance('chronocontactemails', 'Table');
		if (!$row2->bind( $post2 )) {
			JError::raiseWarning(100, $row2->getError());
			$mainframe->redirect( "index2.php?option=$option" );
		}
		if (!$row2->store()) {
			JError::raiseWarning(100, $row2->getError());
			$mainframe->redirect( "index2.php?option=$option" );
		}
	}
	
	//end Emails
	if($task != 'applychanges'){
		$mainframe->redirect( "index2.php?option=".$option );
	}else{
		editChronoContact( $row->id, $option );
	}
}
// abort the current action
function cancelChronoContact( $option ) {
	global $mainframe;
	$database =& JFactory::getDBO();
	//$row =& JTable::getInstance('chronocontact', 'Table');
	//$row->bind( $_POST );
	//$row->checkin();
	$mainframe->redirect( "index2.php?option=$option" );
}
// list entries
function showChronoContact($option) {
	global $mainframe;
	$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit')); 
	$limitstart = JRequest::getVar('limitstart', 0);
	// count entries
	$database =& JFactory::getDBO();
	$database->setQuery( "SELECT count(*) FROM #__chrono_contact" );
	$total = $database->loadResult();
	echo $database->getErrorMsg();
	jimport('joomla.html.pagination'); 		
	$pageNav = new JPagination($total, $limitstart, $limit); 
	# main database query
	$database->setQuery( "SELECT * FROM #__chrono_contact ORDER BY id LIMIT $pageNav->limitstart,$pageNav->limit" );
	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		JError::raiseWarning(100, $database->stderr());
		$mainframe->redirect( "index2.php?option=$option" );
	}
	HTML_ChronoContact::showChronoContact( $rows, $pageNav, $option );
}

///////////////////////////////
function showdataChronoContact($id, $option) {
	global $mainframe;	
	$database =& JFactory::getDBO();
	if(!$id){
		if(is_array(JRequest::getVar('formid', '', 'post', 'array', array(0)))){
			$id_arr = JRequest::getVar('formid', '', 'post', 'array', array(0));
			$id = $id_arr[0];
		}else{
			$id = JRequest::getVar('formid', '', 'post', 'int', 0);
		}
	}
	if(!$id){
		$id = JRequest::getVar('formid', '', 'get', 'int', 0);
	}
	
	$query = "SELECT * FROM #__chrono_contact WHERE id = '$id'";
	$database->setQuery( $query );
	$rows = $database->loadObjectList();
	$registry = new JRegistry();
	$registry->loadINI( $rows[0]->paramsall );
	$paramsvalues = $registry->toObject( );
	$formtables = explode(",", $paramsvalues->tablenames);
	
	$table = JRequest::getVar('table', '', 'post', 'string', 0) ? JRequest::getVar('table', '', 'post', 'string', 0) : $formtables[0];
	
	$result = $database->getTableList();
	if (!in_array($table, $result)) {
	
		echo "<form action=\"index2.php\" method=\"post\" name=\"adminForm\">Table Doesn't Exist for this form, Please create a table first to store your form data
		<input type=\"hidden\" name=\"task\" value=\"\" /><input type=\"hidden\" name=\"option\" value=\"$option\" />
		</form>";
	} else {
		$limit = JRequest::getVar('limit', $mainframe->getCfg('list_limit')); 
		$limitstart = JRequest::getVar('limitstart', 0);
		// count entries
		$database->setQuery( "SELECT count(*) FROM ".$table );
		$total = $database->loadResult();
		echo $database->getErrorMsg();
		jimport('joomla.html.pagination'); 		
		$pageNav = new JPagination($total, $limitstart, $limit);
		# main database query
		# get primary key
		$tables = array();
		$tables[] = $table;
		$result = $database->getTableFields( $tables, false );
		$table_fields = $result[$table];
		$primary = 'cf_id';
		foreach($table_fields as $table_field => $field_data){
			if($field_data->Key == 'PRI')$primary = $table_field;
		}
			
		$database->setQuery( "SELECT * FROM ".$table." ORDER BY ".$primary." LIMIT $pageNav->limitstart,$pageNav->limit" );
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			JError::raiseWarning(100, $database->stderr());
			$mainframe->redirect( "index2.php?option=$option" );
		}
		$formid = $id;
		HTML_ChronoContact::showdataChronoContact( $rows, $pageNav, $option, $formid, $table );
	}
}
function viewdataChronoContact( $ids, $option ) {
	global $mainframe;	
	$database =& JFactory::getDBO();
	$fids = explode("_",$ids);
	$table = JRequest::getVar('table', '', 'post', 'string', 0);
	# get primary key
	$tables = array();
	$tables[] = $table;
	$result = $database->getTableFields( $tables, false );
	$table_fields = $result[$table];
	$primary = 'cf_id';
	foreach($table_fields as $table_field => $field_data){
		if($field_data->Key == 'PRI')$primary = $table_field;
	}
	$database->setQuery( "SELECT * FROM ".$table." WHERE ".$primary."=".$fids[0] );
	$rows = $database->loadObjectList();
	$row = $rows[0];
	$tablename = $table;
	//echo "SELECT * FROM ".$table." WHERE ".$primary."=".$fids[0];
	HTML_ChronoContact::viewdataChronoContact( $row, $option, $tablename, $fids[1] );
}
function maketableChronoContact( $id, $option ) {
	global $mainframe;
	
	$database =& JFactory::getDBO();
	$result = $database->getTableList();
	if (0) {
	//echo $mosConfig_dbprefix."_chronoforms_".$id;
		//echo "A table has already been created for this form";
		HTML_ChronoContact::maketableChronoContact( $id, $option, "exists" );
	} else {
		$row =& JTable::getInstance('chronocontact', 'Table');
		$row->load( $id );
		$typelist = '<option value="VARCHAR(255)">VARCHAR(255)</option>
                <option value="TINYINT">TINYINT</option>
                <option value="TEXT">TEXT</option>
                <option value="DATE">DATE</option>
                <option value="SMALLINT">SMALLINT</option>
                <option value="MEDIUMINT">MEDIUMINT</option>
                <option value="INT(11)">INT(11)</option>
                <option value="BIGINT">BIGINT</option>
                <option value="FLOAT">FLOAT</option>
                <option value="DOUBLE">DOUBLE</option>
                <option value="DECIMAL">DECIMAL</option>
                <option value="DATETIME">DATETIME</option>
                <option value="TIMESTAMP">TIMESTAMP</option>
                <option value="TIME">TIME</option>
                <option value="YEAR">YEAR</option>
                <option value="CHAR">CHAR</option>
                <option value="TINYBLOB">TINYBLOB</option>
                <option value="TINYTEXT">TINYTEXT</option>
                <option value="BLOB">BLOB</option>
                <option value="MEDIUMBLOB">MEDIUMBLOB</option>
                <option value="MEDIUMTEXT">MEDIUMTEXT</option>
                <option value="LONGBLOB">LONGBLOB</option>
                <option value="LONGTEXT">LONGTEXT</option>
                <option value="ENUM">ENUM</option>
                <option value="SET">SET</option>
                <option value="BIT">BIT</option>
                <option value="BOOL">BOOL</option>
                <option value="BINARY">BINARY</option>
                <option value="VARBINARY">VARBINARY</option>
    </select>';
		//$htmlstring = $row->html;
		$html_message = "";
		
		$j = 0;
		$type = "TEXT";
		$defaults = array('cf_id', 'uid', 'recordtime', 'ipaddress', 'cf_user_id');
		foreach($defaults as $default){
			$type = "TEXT";
			$checked = "";
			if($j == 0) $checked = "checked";
			if($j == 0) $type = "INT(11)";
			if($j == 1) $type = "VARCHAR(255)";
			$html_message .= '<tr height="10"><td width="30"><input type="checkbox" value="1" checked id="cf_data2_'.$j.'" name="cf_'.$default.'"/></td><td width="30"/><td width="50" class="tablecell1">'.$default.'</td><td width="30"/><td class="tablecell2">
			<input type="hidden" name="cf_type_'.$default.'" value="'.$type.'">'.$type.'
			</td><td class="tablecell2"><input type="checkbox" value="1" '.$checked.' id="cf_auto_'.$j.'" name="cf_auto_'.$default.'"/></td><td class="tablecell2"><input type="checkbox" value="1" '.$checked.' id="cf_pri_'.$j.'" name="cf_pri_'.$default.'"/></td>
			<td class="tablecell2"><strong style="color:#ff0000">Dont Change unless you know what you are doing</strong></td></tr>';
			$j++;
		}
		
		$names = explode(",", $row->fieldsnames);
		$fieldstypes = explode(",", $row->fieldstypes);
		$i = 4;
		foreach($names as $name){
			$html_message.= "<tr height='10'><td width='30'><input type='checkbox' name='cf_".$name."' id='cf_data_".$i."' value='1'></td><td width='30'></td><td width='50' class='tablecell1'>";
			$html_message.= $name."</td><td width='30'></td><td class='tablecell2'><select id='cf_type_".$name."' name='cf_type_".$name."'>".$typelist."</td>";
			$html_message.= "<td class='tablecell2'><input type='checkbox' name='cf_auto_".$name."' id='cf_auto_".$i."' value='1'></td><td class='tablecell2'><input type='checkbox' name='cf_pri_".$name."' id='cf_pri_".$i."' value='1'></td>
			<td class='tablecell2'>Form Field - Type:<strong>(".$fieldstypes[$i-4].")</strong></td></tr>";
			$i++;
		}
		$html_message.= "</table>";
		
		
		
		$html_message = "<table class=\"adminlist\" id='create_table_table'>
		<tr><td colspan='2'>Table Name:</td><td colspan='6' align='left'><input type='text' name='cf_table_name' size='50' value='".$mainframe->getCfg('dbprefix')."chronoforms_".$row->name."'></td></tr>
		<tr height='10'>
			<td width='30'>
				<strong>Create column?<br><input type='checkbox' onclick='checkAll(".($i+1).",\"cf_data_\");' value='' name='toggle'/></strong>
			</td>
			<td width='30'></td>
			<td width='50' class='tablecell1'><strong>Column name</strong></td>
			<td width='30'></td>
			<td class='tablecell2'><strong>Column type</strong></td>
			<td class='tablecell2'><strong>Auto_increment</strong></td>
			<td class='tablecell2'><strong>Primary Key</strong></td>
			<td class='tablecell2'><strong>Comments</strong></td>
		</tr>".$html_message;
		
	
		HTML_ChronoContact::maketableChronoContact( $row, $option, $html_message );
	}
}
function finalizetableChronoContact( $option ) {
	global $mainframe;	
	$database =& JFactory::getDBO();
	$id = JRequest::getVar('formid');
	$row =& JTable::getInstance('chronocontact', 'Table');
	$row->load( $id );
	
	$defaults = array('cf_id', 'uid', 'recordtime', 'ipaddress', 'cf_user_id');
	$names = array_merge($defaults, explode(",", $row->fieldsnames));
	$table_sql_arr = array();
	$primarykey = "";
	$cform_names 	= JRequest::getVar( 'cform_name', array(), 'post', 'array');
	//$cf_new 	= JRequest::getVar( 'cf_new', '', 'post', 'array', array(0) );
	$cf_type 	= JRequest::getVar( 'cf_type', array(), 'post', 'array');
	$cf_auto 	= JRequest::getVar( 'cf_auto', array(), 'post', 'array');
	$cf_pri 	= JRequest::getVar( 'cf_pri', array(), 'post', 'array');
	
	foreach($cform_names as $index => $cform_name){
		if($cform_name){
			$names[] = $cform_name;
			JRequest::setVar('cf_'.$cform_name, $cform_name);
			JRequest::setVar('cf_type_'.$cform_name, $cf_type[$index]);
			JRequest::setVar('cf_auto_'.$cform_name, $cf_auto[$index]);
			JRequest::setVar('cf_pri_'.$cform_name, $cf_pri[$index]);
		}
	}
	
	foreach($names as $name){
		if(JRequest::getVar('cf_'.$name)){
			$sqlpiece = '`'.$name.'` '.JRequest::getVar('cf_type_'.$name).' NOT NULL';
			if(JRequest::getVar('cf_auto_'.$name)) $sqlpiece .= " auto_increment";
			if(JRequest::getVar('cf_pri_'.$name)) $primarykey = "PRIMARY KEY  (`".$name."`)";
			$table_sql_arr[] = $sqlpiece;
		}
	}
	
	//echo implode(",", $names); return;
	
	$table_sql_arr[] = $primarykey;
	$table_sql_body = implode(", ", $table_sql_arr);
	//return;
	$registry = new JRegistry();
	$registry->loadINI( $row->paramsall );
	$paramsvalues = $registry->toObject( );
	if ( count($names) > 0){
		$table_sql = "CREATE TABLE `".JRequest::getVar('cf_table_name')."` (";
		$table_sql .= $table_sql_body ."";
		if ($paramsvalues->mysql_type == 2){
			$table_sql .= ") TYPE = MYISAM ;";
		} else{ 
			$table_sql .= ") ENGINE = MYISAM ;";
		}
	}
	//echo $table_sql; return;
	$database->setQuery( $table_sql );
	if (!$database->query()) {
		$mainframe->redirect( 'index2.php?option='.$option, "Error while creating table :".$database->getErrorMsg() );
	}else{	
		$mainframe->redirect( 'index2.php?option='.$option, "Table has been created successfully" );
	}
			
}


/* backup ****************************************************/
function backupChronoContact( $id, $option ){
global $mainframe;	
	$database =& JFactory::getDBO();
	$database->setQuery( "SELECT * FROM #__chrono_contact WHERE id='".$id."'" );
	$rows = $database->loadObjectList();

	$tablename = $mainframe->getCfg('dbprefix')."chrono_contact";
	$tables = array( $tablename );
 	$result = $database->getTableFields( $tables );
	$table_fields = array_keys($result[$tablename]);
	$string = '';
	foreach($table_fields as $table_field){
		$string .= '<++-++-++'.$table_field.'++-++-++>';
		$string .= $rows[0]->$table_field;
		$string .= '<endendend>';		
	}
	
	$database->setQuery( "SELECT * FROM #__chrono_contact_emails WHERE formid='".$id."' ORDER BY emailid" );
	$emails = $database->loadObjectList();
	$tablename = $mainframe->getCfg('dbprefix')."chrono_contact_emails";
	$tables = array( $tablename );
 	$result = $database->getTableFields( $tables );
	$table_fields = array_keys($result[$tablename]);
	$string2 = '';
	foreach($emails as $email){
		foreach($table_fields as $table_field){
			$string2 .= '<2++-++-++'.$table_field.'++-++-++>';
			$string2 .= $email->$table_field;
			$string2 .= '<endendend2>';		
		}
		$string2 .= '<cf_email_separator>';
	}
	
	
	if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {
		$UserBrowser = "Opera";
	}
	elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {
		$UserBrowser = "IE";
	} else {
		$UserBrowser = '';
	}
	$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
	@ob_end_clean();
	ob_start();

	header('Content-Type: ' . $mime_type);
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

	if ($UserBrowser == 'IE') {
		header('Content-Disposition: inline; filename="' . $rows[0]->name.'.cfbak"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	}
	else {
		header('Content-Disposition: attachment; filename="' . $rows[0]->name.'.cfbak"');
		header('Pragma: no-cache');
	}
	print $string.'
	{cfbak_start_emails}
	'.$string2.'
	{cfbak_end_emails}';
	exit();
	
}
function restore1ChronoContact( $id, $option ){
	HTML_ChronoContact::restoreChronoContact( $id, $option );
}
function restore2ChronoContact( $id, $option ){
global $mainframe;	
	$database =& JFactory::getDBO();
	$id = JRequest::getVar('formid');
	//echo $_FILES['file']['type'];
	if(!empty($_FILES['file']['name'])){
		$filename = $_FILES['file']['name'];
		$exten = explode(".",$filename);
		if($exten[count($exten)-1] == 'cfbak'){
		//if($_FILES['file']['type'] == "application/octet-stream"){
			//$filename = $_FILES['file']['name'];
			
			$path = JPATH_BASE.DS.'cache' . '/';
			if( is_writable($path) ) {
	
				if(!move_uploaded_file($_FILES['file']['tmp_name'], $path . $filename)) {
					print "<font class=\"error\">"."UPLAOD FAILED".": " . $_FILES['file']['error'] . "</font><br>\n";
				} else {
					$data = file_get_contents( $path . $filename );					
					$data = str_replace( '&amp;', '&', $data );
					$values = '(';
					$values2 = '(';
					//print $data;
					preg_match_all('/\<++(.*?)\<endendend>/s', $data, $matches);
					$i = 0;
					foreach ( $matches[0] as $match ) {
						if($i != 0){$values .= ',';$values2 .= ',';}
						//echo $match.'x';
						preg_match_all('/\<++(.*?)\++>/s', $match, $match2es);
						$fieldvalue = str_replace($match2es[0][0],'',$match);
						$match2es[0][0] = str_replace('<++-++-++','',$match2es[0][0]);
						$match2es[0][0] = str_replace('++-++-++>','',$match2es[0][0]);
						$values .= $match2es[0][0];
						if($i == 0){
							$values2 .= "''";
						}else{
							$match = str_replace('<++-++-++'.$match2es[0][0].'++-++-++>','',$match);
							$match = str_replace('<endendend>','',$match);
							$match = trim($match," \t.");
							//if(!trim($match)){
								//$match = "''";
							//}
							$values2 .= "'".addslashes($match)."'";
						}
						$i++;
					}
					$values .= ')';
					$values2 .= ')';
					$database->setQuery( "INSERT INTO #__chrono_contact ".$values." VALUES ".$values2 );
					if (!$database->query()) {
						JError::raiseWarning(100, "Restoring the whole form failed Failed, error : ".$database->getErrorMsg());
						$mainframe->redirect( "index2.php?option=$option" );
					}else{
						//$mainframe->redirect( 'index2.php?option='.$option , "Restored successfully");
					}
					$lastformid = $database->insertid();
					
					// Restore Emails
					$values = '(`';
					$values2 = '(';
					$emails_data = array();
					$emails_count = explode('<cf_email_separator>', $data);
					$fields_count_1 = explode('{cfbak_start_emails}', $emails_count[0]);
					$fields_count_2 = explode('<endendend2>', $fields_count_1[1]);
					preg_match_all('/\<2++(.*?)\<endendend2>/s', $data, $matches);
					$i = 0;
					$i_v = 0;
					$counter = 0;
					foreach ( $matches[0] as $match ) {
						//if($i != 0){$values .= ',';$values2 .= ',';}
						//echo $match.'x';
						preg_match_all('/\<2++(.*?)\++>/s', $match, $match2es);
						$fieldvalue = str_replace($match2es[0][0],'',$match);
						$match2es[0][0] = str_replace('<2++-++-++','',$match2es[0][0]);
						$match2es[0][0] = str_replace('++-++-++>','',$match2es[0][0]);
						if($i_v < (count($fields_count_2) - 1)){
							if($i_v != 0){$values .= '`,`';}
							$values .= $match2es[0][0];
						}
						if($i != 0){$values2 .= ',';}
						if($i == 0){
							$values2 .= "''";
						}else if($i == 1){
							$values2 .= "'".$lastformid."'";
						}else{
							$match = str_replace('<2++-++-++'.$match2es[0][0].'++-++-++>','',$match);
							$match = str_replace('<endendend2>','',$match);
							$match = trim($match," \t.");
							$values2 .= "'".addslashes($match)."'";
						}
						$counter++;
						$i++;
						$i_v++;
						
						if($counter == (count($fields_count_2) - 1)){
							$values2 .= ')';
							$emails_data[] = $values2;
							$values2 = '(';
							$counter = 0;
							$i = 0;
						}
					}
					$values .= '`)';
					//echo $emails_data[1];
					//$values2 .= ')';
					foreach($emails_data as $email_data){
						$database->setQuery( "INSERT INTO #__chrono_contact_emails ".$values." VALUES ".$email_data );
						if (!$database->query()) {
							JError::raiseWarning(100, "Restoring Emails Setup Failed, error : ".$database->getErrorMsg());
							$mainframe->redirect( "index2.php?option=$option" );
						}else{
							//$mainframe->redirect( 'index2.php?option='.$option , "Restored successfully");
						}
					}
					$mainframe->redirect( 'index2.php?option='.$option , "Restored successfully");
					
				}
			}
		}else{
			echo "Sorry, But this is not a valid ChronoForms backup file, Backup files should end with .cfbak";
		}
	}
}

function BackupExcel( $id, $option ) {
	global $mainframe;
	$database =& JFactory::getDBO();

	include_once JPATH_BASE.DS.'/components/com_chronocontact/excelwriter/'."Writer.php";
	//echo $_POST['formid'];
	$formid = JRequest::getVar( 'formid', array(), 'post', 'array');
	$database->setQuery( "SELECT name FROM #__chrono_contact WHERE id='".$formid[0]."'" );
	$formname = $database->loadResult();
	
	$tablename = JRequest::getVar('table');
	$tables = array( $tablename );
 	$result = $database->getTableFields( $tables );
	$table_fields = array_keys($result[$tablename]);
	
	$database->setQuery( "SELECT * FROM ".$tablename."" );
	$datarows = $database->loadObjectList();
	
	$xls =& new Spreadsheet_Excel_Writer();
	$xls->setVersion(8); // this fixes the 255 limit issue! :)
	$xls->send("ChronoForms - ".$formname." - ".date("j_n_Y").".xls");
	$format =& $xls->addFormat();
	$format->setBold();
	$format->setColor("blue");
	if (strlen($formname) > 10){$formname = substr($formname,0,10);};
	$sheet =& $xls->addWorksheet($formname.' at '.date("m-d-Y"));
	$sheet->setInputEncoding('utf-8');

	$titcol = 0;
	foreach($table_fields as $table_field){
		$sheet->writeString(0, $titcol, $table_field, $format);
		$titcol++;
	}
			
			
	$datacol = 0;
	$rowcount = 1;
	foreach($datarows as $datarow){
		foreach($table_fields as $table_field){
			$sheet->writeString($rowcount, $datacol, $datarow->$table_field, 0);
			$datacol++;
		}
		$datacol = 0;
		$rowcount++;
	}
			
	$xls->close();
	exit;
	
}
function deleterecordChronoContact( $cid_del, $option ) {
	global $mainframe;
	$database =& JFactory::getDBO();
	//echo $cid_del[0];	
	$cid_del_arr = array();
	foreach($cid_del as $cid_del_1){
		$fids = explode("_",$cid_del_1);
		$cid_del_arr[] = $fids[0];
		$formid = $fids[1];
	}
	$cid_dels = implode( ',', $cid_del_arr );
	
	if (!is_array( $cid_del ) || count( $cid_del ) < 1) {
		JError::raiseWarning(100, 'Please select an entry to delete');
		showdataChronoContact( $formid, $option );
	}
	# get primary key
	$tables = array();
	$table = JRequest::getVar('table');
	$tables[] = $table;
	$result = $database->getTableFields( $tables, false );
	$table_fields = $result[$table];
	$primary = 'cf_id';
	foreach($table_fields as $table_field => $field_data){
		if($field_data->Key == 'PRI')$primary = $table_field;
	}
	$database->setQuery( "DELETE FROM ".$table." WHERE ".$primary." IN ($cid_dels)" );
	if (!$database->query()) {
		JError::raiseWarning(100, $database->getErrorMsg());
		showdataChronoContact( $formid, $option );
	}
	showdataChronoContact( $formid, $option );
	
}

function BackupCSV( $id, $option ) {
global $mainframe;
	$database =& JFactory::getDBO();

	include_once JPATH_BASE.'/components/com_chronocontact/excelwriter/'."Writer.php";
	//echo $_POST['formid'];
	$formid = JRequest::getVar( 'formid', array(), 'post', 'array');
	$database->setQuery( "SELECT name FROM #__chrono_contact WHERE id='".$formid[0]."'" );
	$formname = $database->loadResult();
	
	$tablename = JRequest::getVar('table');
	$tables = array( $tablename );
 	$result = $database->getTableFields( $tables );
	$table_fields = array_keys($result[$tablename]);
	
	$database->setQuery( "SELECT * FROM ".$tablename."" );
	$datarows = $database->loadObjectList();
	
	$titcol = 0;
	foreach($table_fields as $table_field){
		if($titcol){$csvline .=",";}
		$csvline .= $table_field;
		$titcol++;
	}
	$csvline .="\n";
			
	$datacol = 0;
	$rowcount = 1;
	foreach($datarows as $datarow){
		foreach($table_fields as $table_field){
			if($datacol){$csvline .=",";}
			$csvline .= '"'.addslashes($datarow->$table_field).'"';
			$datacol++;
		}
		$csvline .="\n";
		$datacol = 0;
		$rowcount++;
	}
	
	if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {
		$UserBrowser = "Opera";
	}
	elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {
		$UserBrowser = "IE";
	} else {
		$UserBrowser = '';
	}
	$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
	@ob_end_clean();
	ob_start();

	header('Content-Type: ' . $mime_type);
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

	if ($UserBrowser == 'IE') {
		header('Content-Disposition: inline; filename="' . "ChronoForms - ".$formname." - ".date("j_n_Y").'.csv"');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	}
	else {
		header('Content-Disposition: attachment; filename="' . "ChronoForms - ".$formname." - ".date("j_n_Y").'.csv"');
		header('Pragma: no-cache');
	}
	print $csvline;
	exit();
	
}
function deletetableChronoContact(  $id, $option ){
global $mainframe;
	$database =& JFactory::getDBO();
	$result = $database->getTableList();
	if (!in_array($mainframe->getCfg('dbprefix')."chronoforms_".$id, $result)) {
		JError::raiseWarning(100, "There is no table for this form to delete");
		$mainframe->redirect( "index2.php?option=$option" );
	}else{
		$database->setQuery( "DROP TABLE IF EXISTS `#__chronoforms_".$id."`;" );
		if (!$database->query()) {
			JError::raiseWarning(100, $database->getErrorMsg());
			$mainframe->redirect( "index2.php?option=$option" );
		}else{
			$mainframe->redirect( 'index2.php?option='.$option , "Table Deleted successfully");
		}
	}
}
function menu_creator($option){
	HTML_ChronoContact::menu_creator( $option );
}
function menu_save($option){
	global $mainframe;
	$database =& JFactory::getDBO();
	JTable::addIncludePath(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table');
	$row =& JTable::getInstance('component', 'JTable');
	//$row->load(1);
	$row->name = JRequest::getVar('linktext');
	$row->parent = JRequest::getVar('parent');
	$row->ordering = JRequest::getVar('ordering');
	$row->admin_menu_link = "option=com_chronocontact&task=show&formid=".JRequest::getVar('form_id');
	$row->admin_menu_img = "js/ThemeOffice/".JRequest::getVar('icon');
	$row->option = "com_chronocontact";
	$row->link = "option=com_chronocontact&task=show&formid=1";
	$row->admin_menu_alt = JRequest::getVar('linktext');
	if (!$row->store()) {
		JError::raiseWarning(100, $row->getError());
		$mainframe->redirect( "index2.php?option=$option" );
	}
	$mainframe->redirect( 'index2.php?option='.$option , "Menu Item Created");
}

function menu_remover($option){
	HTML_ChronoContact::menu_remover( $option );
}
function menu_delete( $cid, $option ){
	global $mainframe;
	$database =& JFactory::getDBO();
	if (!is_array( $cid ) || count( $cid ) < 1) {
		JError::raiseWarning(100, 'Please select an entry to delete');
		$mainframe->redirect( "index2.php?option=$option" );
	}
	$cids = implode( ',', $cid );
	$database->setQuery( "DELETE FROM #__components WHERE id IN ($cids)" );
	if (!$database->query()) {
		JError::raiseWarning(100, $database->getErrorMsg());
		$mainframe->redirect( "index2.php?option=$option" );
	}
	$mainframe->redirect( 'index2.php?option='.$option.'&task=menu_remover' , "Menu Item Deleted");
}



function form_wizard($option){
	HTML_ChronoContact::form_wizard( $option );
}

function save_form_wizard( $option ){
	$post = JRequest::get( 'post' , JREQUEST_ALLOWRAW );
	$post['html'] = $post['form_code_temp'];
	$post['name'] = $post['form_title_temp'];
	$post['redirecturl'] = $post['redirecturl'];
	$post['onsubmitcode'] = $post['onsubmitcode'];
	$post['paramsall'] = "formmethod=post
omittedfields=
LoadFiles=Yes
debug=0
mysql_type=1
enmambots=No
uploads=No
uploadfields=".$post['uploadfields']."
uploadmax=
uploadmin=
dvfields=
dvrecord=Record #n
imagever=No
imtype=0
captcha_dataload=0
validate=No
validatetype=prototype
val_required=
val_validate_number=
val_validate_digits=
val_validate_alpha=
val_validate_alphanum=
val_validate_date=
val_validate_email=
val_validate_url=
val_validate_date_au=
val_validate_currency_dollar=
val_validate_selection=
val_validate_one_required=
plugins=
plugins_order=1
onsubmitcode_order=2
autogenerated_order=3
mplugins_order=";
	
	//print_r($post);
	global $mainframe;
	$database =& JFactory::getDBO();
	$row =& JTable::getInstance('chronocontact', 'Table');
	if (!$row->bind( $post )) {
		JError::raiseWarning(100, $row->getError());
		$mainframe->redirect( "index2.php?option=$option" );
	}
	if (!$row->store()) {
		JError::raiseWarning(100, $row->getError());
		$mainframe->redirect( "index2.php?option=$option" );
	}
	//$mainframe->redirect( "index2.php?option=$option", 'Form Saved, you can Open form to edit other settings now!' );
	
	preg_match_all('/start_email{.*?}end_email/i', $post['emails_temp'], $matches);
	$emails = array();
	$template_count = 0;
	foreach ( $matches[0] as $email ) {
	echo $email;
		$email = preg_replace('/start_email{/i', '', $email);
		$email = preg_replace('/}end_email/i', '', $email);
		$email_elements = explode('||', $email);
		//$emails[] = trim($email);
		$post2 = array();
		$post2['to'] = str_replace('TO=[', '', str_replace(']', '', $email_elements[0]));
		$post2['dto'] = str_replace('DTO=[', '', str_replace(']', '', $email_elements[1]));
		$post2['subject'] = str_replace('SUBJECT=[', '', str_replace(']', '', $email_elements[2]));
		$post2['dsubject'] = str_replace('DSUBJECT=[', '', str_replace(']', '', $email_elements[3]));
		$post2['cc'] = str_replace('CC=[', '', str_replace(']', '', $email_elements[4]));
		$post2['dcc'] = str_replace('DCC=[', '', str_replace(']', '', $email_elements[5]));
		$post2['bcc'] = str_replace('BCC=[', '', str_replace(']', '', $email_elements[6]));
		$post2['dbcc'] = str_replace('DBCC=[', '', str_replace(']', '', $email_elements[7]));
		$post2['fromname'] = str_replace('FROMNAME=[', '', str_replace(']', '', $email_elements[8]));
		$post2['dfromname'] = str_replace('DFROMNAME=[', '', str_replace(']', '', $email_elements[9]));
		$post2['fromemail'] = str_replace('FROMEMAIL=[', '', str_replace(']', '', $email_elements[10]));
		$post2['dfromemail'] = str_replace('DFROMEMAIL=[', '', str_replace(']', '', $email_elements[11]));
		$post2['formid'] = $row->id;
		
		//$post2['params'] = $post['params_email_'.$template_count];
		$params = explode(",", $post['params_email_'.$template_count]);
		$txt = array();
		$txt[0] = "recordip=".$params[0];
		$txt[1] = "emailtype=".$params[1];
		$txt[2] = "enabled=".$params[2];
		$txt[3] = "editor=".'1';
		$post2['params'] = implode("\n", $txt);
		$post2['enabled'] = $params[2];
		$post2['template'] = trim($post['editor_email_'.$template_count]) ? trim($post['editor_email_'.$template_count]) : trim($post['extra_editor_email_'.$template_count]);
		$template_count++;
		
		$row2 =& JTable::getInstance('chronocontactemails', 'Table');
		if (!$row2->bind( $post2 )) {
			JError::raiseWarning(100, $row2->getError());
		}
		if (!$row2->store()) {
			JError::raiseWarning(100, $row2->getError());
		}
	}
	$mainframe->redirect( "index2.php?option=$option", 'Form Saved, you can Open form to edit other settings now!' );
	//$emails = array_unique($emails);
}



?>
