<?php
defined('_JEXEC') or die('Restricted access'); 
global $mainframe;
require_once( $mainframe->getPath( 'class' ) );
// the class name must be the same as the file name without the .php at the end
class cf_profile  {
	//the next 3 fields must be defined for every plugin
	var $result_TITLE = "Profile Page";
	var $result_TOOLTIP = "Load data from some table to be shown on the form page using a very simple method! all you need to do is to put the field name between { and } , then it will be replaced by the same field value from the choosed table";
	var $plugin_name = "cf_profile"; // must be the same as the class name
	var $event = "ONLOAD"; // must be defined and in Uppercase, should be ONSUBMIT or ONLOAD
	// the next function must exist and will have the backend config code
	function show_conf($row, $id, $form_id, $option){
	global $mainframe;
	$database =& JFactory::getDBO();
	if(!$row->params){
			$row->params = "table_name=
			field_name=
			parameter=";
		}
	$registry = new JRegistry();
	$registry->loadINI( $row->params );
	$paramsvalues = $registry->toObject( );
	$tables = $database->getTableList();
	?>
	 <script type="text/javascript">		   
			function getHTTPObject() {
			  var xmlhttp;
			
			  if(window.XMLHttpRequest){
				xmlhttp = new XMLHttpRequest();
			  }
			  else if (window.ActiveXObject){
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				if (!xmlhttp){
					xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
				}
			   
			}
			  return xmlhttp;			 
			}
			var http = getHTTPObject(); // We create the HTTP Object
	</script>
	<script type="text/javascript">
	function loadfields(){
		var table_name = document.getElementById("table_name").value;
		http.open("POST", "index3.php?option=com_chronocontact&task=ajaxfields" , true);
		http.onreadystatechange = function () 
		{   if (http.readyState == 1) {
				document.getElementById('ajax_fields').innerHTML = 'LOADING';
			}
			else if (http.readyState == 4) {
				if(http.status==200) {
					document.getElementById('ajax_fields').innerHTML = ''; 
					var results=http.responseText.split("*#*");
					var results=results[1].split("#999-#");
					if(results[0] == 'yes'){
						document.getElementById('ajax_fields').innerHTML = results[1];
					} 
				}
			}
		};
		//http.send(null);		
		http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 	
		http.send("table=" + escape(table_name));	
	}
	</script>
	<form action="index2.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">
		<tr style="background-color:#c9c9c9 ">
			<td><?php echo JHTML::_('tooltip', "The table which will be used to get the data from" ); ?></td>
			<td><strong><?php echo "Table name"; ?>:</strong> </td>
			<td></td>
			<td>
			<select name="params[table_name]" onChange="loadfields()" id="table_name">
			<?php foreach($tables as $table){ ?>
			<option <?php if($paramsvalues->table_name == $table) echo "selected"; ?> value="<?php echo $table; ?>"><?php echo $table; ?></option>
			<?php } ?>
			</select>
			</td>
		</tr>
		<tr style="background-color:#c9c9c9 ">
			<td><?php echo JHTML::_('tooltip', "This is the name of the parameter coming in the page request url, like userid=128, so when you add here userid, the plugin will check for this parameter and loads results based on this value" ); ?></td>
			<td><strong><?php echo "REQUEST Parameter name"; ?>:</strong> </td>
			<td></td>
			<td><input type="text" class="inputbox" size="50" maxlength="50" name="params[parameter]" value="<?php echo $paramsvalues->parameter; ?>" /></td>
		</tr>
		<tr style="background-color:#c9c9c9 ">
			<td><?php echo JHTML::_('tooltip', "This is the name of table field which will be used at the SELECT statement, for best results, this field must be UNIQUE" ); ?></td>
			<td><strong><?php echo "Target field name"; ?>:</strong> </td>
			<td></td>
			<td>
			<?php if($id == 0){ ?>
				<div id="ajax_fields">SELECT Table First</div>
			<?php }else{ ?>
			<div id="ajax_fields">
			<?php $tablename = $paramsvalues->table_name;
				$tables = array( $tablename );
				$result = $database->getTableFields( $tables );
				$table_fields = array_keys($result[$tablename]);
				?>
				<select name="params[field_name]">
					<?php
					foreach($table_fields as $table_field){
					?>
						<option <?php if($paramsvalues->field_name == $table_field) echo "selected"; ?> value="<?php echo $table_field; ?>"><?php echo $table_field; ?></option>
					<?php
					}
					?>
				</select>
			<?php } ?>
			</div>
			<!--<input type="text" class="inputbox" size="50" maxlength="50" name="params[field_name]" value="<?php echo $paramsvalues->field_name; ?>" />--></td>
		</tr>
		</tr>
	</table>
	<input type="hidden" name="id" value="<?php echo $id; ?>" />
	<input type="hidden" name="form_id" value="<?php echo $form_id; ?>" />
	<input type="hidden" name="name" value="<?php echo $this->plugin_name; ?>" />
	<input type="hidden" name="event" value="<?php echo $this->event; ?>" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="save_conf" />
	
	</form>
	<?php
	}
	// this function must exist and may not be changed unless you need to customize something
	function save_conf( $option ) {
		global $mainframe;
		$database =& JFactory::getDBO();		
		$post = JRequest::get( 'post' , JREQUEST_ALLOWRAW );
		
		$row =& JTable::getInstance('chronocontactplugins', 'Table'); 
		if (!$row->bind( $post )) {
			JError::raiseWarning(100, $row->getError());
			$mainframe->redirect( "index2.php?option=$option" );
		}
		
		///$params = mosGetParam( $_POST, 'params', '' );
		$params 	= JRequest::getVar( 'params', '', 'post', 'array', array(0) );
		if (is_array( $params )) {
			$txt = array();
			foreach ( $params as $k=>$v) {
				$txt[] = "$k=$v";
			}
			$row->params = implode( "\n", $txt );
		}
		if (!$row->store()) {
			JError::raiseWarning(100, $row->getError());
			$mainframe->redirect( "index2.php?option=$option" );
		}
		$mainframe->redirect( "index2.php?option=".$option, "Config Saved" );
	}
	
	function onload( $option, $params, $html_string ) {
		global $mainframe;
		$my 		= JFactory::getUser();
		$database =& JFactory::getDBO();
	
		$parid 	= JRequest::getVar( $params->parameter, '', 'request', 'int', 0 );
		if($parid){
			$record_id = $parid;
		}else{
			$record_id = $my->id;
		}
		
		if($record_id){
			$database->setQuery( "SELECT * FROM ".$params->table_name." WHERE ".$params->field_name." = '".$record_id."'" );
			$rows = $database->loadObjectList();
			$row = $rows[0];
			$tables = array( $params->table_name );
			$result = $database->getTableFields( $tables );
			$table_fields = array_keys($result[$params->table_name]);
			foreach($table_fields as $table_field){
				$html_string = str_replace("{".$table_field."}", $row->$table_field, $html_string);
			}
		}
		
		return $html_string ;
		
	}

}
?>