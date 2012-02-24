<?php
/**
 * CHRONOFORMS version 3.0 stable
 * Copyright (c) 2006 Chrono_Man, ChronoEngine.com. All rights reserved.
 * Author: Chrono_Man (ChronoEngine.com)
 * See readme.html.
 * Visit http://www.ChronoEngine.com for regular update and information.
 **/

/* ensure that this file is called by another file */
defined('_JEXEC') or die('Restricted access');



/**
 * Load the HTML class
 */
require_once( JApplicationHelper::getPath( 'front_html' ) ); 
require_once( JApplicationHelper::getPath( 'class' ) );

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();

jimport( 'joomla.application.component.controller' );
global $mainframe;
$CFDBO =& JFactory::getDBO();
$formname = JRequest::getVar( 'chronoformname');//, '', 'get', 'string', '' ) ? JRequest::getVar( 'chronoformname', '', 'get', 'string', '' ) : JRequest::getVar( 'chronoformname', '', 'post', 'string', '' );
	if ( !$formname ) {
		$params =& $mainframe->getPageParameters('com_chronocontact');
		$formname = $params->get('formname');
	}
	$query = "SELECT * FROM #__chrono_contact WHERE name = '$formname'";
	$CFDBO->setQuery( $query );
	$rows = $CFDBO->loadObjectList();
	$registry_cf = new JRegistry();
	$registry_cf->loadINI( $rows[0]->paramsall );
	$paramsvalues = $registry_cf->toObject( );
	if($paramsvalues->dbconnection == "Yes"){
		eval ("?>".$rows[0]->dbclasses);
	}
	


$posted = JRequest::get( 'post' , JREQUEST_ALLOWRAW );
/**
 * Main switch statement
 */
switch( $task ) {
	case 'send':
		uploadandmail();
		break;
	default:
		showform($posted);
		break;
}
/**
 * End of main page
 *
 */

/**
 * Display the form for entry
 *
 */
function showform($posted)
{
    global $mainframe;
	$database =& JFactory::getDBO();
	
	//clear any stored sessions
	$session =& JFactory::getSession();
	if(!$posted){
		$session->set("chrono_verification_msg", '', md5('chrono'));
	}

    $formname = JRequest::getVar( 'chronoformname');//, '', 'get', 'string', '' ) ? JRequest::getVar( 'chronoformname', '', 'get', 'string', '' ) : JRequest::getVar( 'chronoformname', '', 'post', 'string', '' );
    //$formname = $_GET['chronoformname'];
	if ( !$formname ) {
		$params =& $mainframe->getPageParameters('com_chronocontact');
		$formname = $params->get('formname');
	}
	$imver = "";
    $query = "
	   SELECT *
	       FROM #__chrono_contact
	       WHERE name = '$formname'";
    $database->setQuery( $query );
    $rows = $database->loadObjectList();
    $registry = new JRegistry();
	$registry->loadINI( $rows[0]->paramsall );
	$paramsvalues = $registry->toObject( );
    if ( trim($paramsvalues->imagever) == 'Yes' ) {
        $imver = '<input name="chrono_verification" style="vertical-align:top;" type="text" id="chrono_verification" value="">
            &nbsp;&nbsp;<img src="'.JURI::Base()
            .'components/com_chronocontact/chrono_verification.php?imtype='.$paramsvalues->imtype.'">';
    }
	
	$htmlstring = $rows[0]->html;	
	
    HTML_ChronoContact::showform( $rows , $imver, $posted);
}

/**
 * Respond to a submitted form
 *
 */
function uploadandmail()
{
    global $mainframe;
	$database =& JFactory::getDBO();
		$posted = JRequest::get( 'post' , JREQUEST_ALLOWRAW );
		// Block SPAM through the submit URL
		if(!JRequest::checkToken()){
			echo "You are not allowed to access this URL";
			return;
		}
		if ( empty($posted) ) {
			echo "You are not allowed to access this URL directly, POST array is empty";
			return;
		}    
	
    
    /**
     * Retrieve form data from the database
     */
    $formname = JRequest::getVar( 'chronoformname');//, '', 'get', 'string', '' ) ? JRequest::getVar( 'chronoformname', '', 'get', 'string', '' ) : JRequest::getVar( 'chronoformname', '', 'post', 'string', '' );
    //$formname = $_GET['chronoformname'];
    $query     = "SELECT * FROM #__chrono_contact WHERE name='$formname'";
    $database->setQuery( $query );
    $rows = $database->loadObjectList();
	$registry = new JRegistry();
	$registry->loadINI( $rows[0]->titlesall );
	$titlesvalues = $registry->toObject( );
	$registry = new JRegistry();
	$registry->loadINI( $rows[0]->paramsall );
	$paramsvalues = $registry->toObject( );
	
	$debug = $paramsvalues->debug;
	if ( $debug ) {
		$mainframe->enqueueMessage('Form passed first SPAM check OK');
	}
	
    $error_found = false;
	$session =& JFactory::getSession();
	
	// Check how soon was the last submission
	if(trim($paramsvalues->submissions_limit)){
		if(!$session->get('chrono__submissions_limit', 'default', md5('chrono'))){
			$session->set("chrono__submissions_limit", mktime(date("H"), date("i"), date("s"), date("m")  , date("d")+1, date("Y")), md5('chrono'));
		}else{
			if(($session->get('chrono__submissions_limit', 'default', md5('chrono')) + ((int)trim($paramsvalues->submissions_limit))) > mktime(date("H"), date("i"), date("s"), date("m")  , date("d")+1, date("Y"))){
				$session->set("chrono_verification_msg", 'Sorry but you can not submit the form again very soon like this!', md5('chrono'));
				showform($posted);
				return;
			}else{
				$session->set("chrono__submissions_limit", mktime(date("H"), date("i"), date("s"), date("m")  , date("d")+1, date("Y")), md5('chrono'));
			}
		}
	}
	if ( $debug ) {
		$mainframe->enqueueMessage('Form passed the submissions limit (if enabled) OK');
	}
    /**
     * If imageversification is on check the code
     */
    if ( trim($paramsvalues->imagever) == 'Yes' ) {
		//session_start();
		//$session =& JFactory::getSession();
		$sessionvar = $session->get('chrono_verification', 'default', md5('chrono'));
		$chrono_verification = strtolower(JRequest::getVar('chrono_verification'));
		if ( md5($chrono_verification ) != $sessionvar ) {
			//showErrorMessage('Sorry, You have entered a wrong verification code, Please try again!!');
			JRequest::setVar('cf_wrong_security_code', 1);
			trim($paramsvalues->imgver_error_msg) ? $session->set("chrono_verification_msg", trim($paramsvalues->imgver_error_msg), md5('chrono')) : $session->set("chrono_verification_msg", 'You have entered an incorrect verification code at the bottom of the form.', md5('chrono'));
			showform($posted);
			return;
        }else{
			$session->clear('chrono_verification');
			$session->clear('chrono_verification_msg');
		}
    }
	if ( $debug ) {
		$mainframe->enqueueMessage('Form passed the Image verification (if enabled) OK');
	}
	//Server side validation
	if ( trim($paramsvalues->servervalidate) == 'Yes' ) {
		//$session =& JFactory::getSession();
		if ($returnval = eval( "?>".$rows[0]->server_validation )){
			//showErrorMessage('Sorry, You have entered a wrong verification code, Please try again!!');
			$session->set("chrono_verification_msg", $returnval, md5('chrono'));
			showform($posted);
			return;
        }
    }
	if ( $debug ) {
		$mainframe->enqueueMessage('Form passed the server side validation (if enabled) OK');
	}
	/**
     * if $debug is true then ChronoForms will show diagnostic output
     */
    
    if ( $debug ) {
        echo "_POST: ";
        print_r($posted);
        echo "<br />";
    }

    /**
     * Upload attachments
     */
	$attachments = array();
	if ( trim($paramsvalues->uploads == 'Yes' ) && trim($paramsvalues->uploadfields) ) {
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		if(!JFile::exists(JPATH_COMPONENT.DS.'uploads'.DS.$formname.DS.'index.html')){
			if(!JFolder::create(JPATH_COMPONENT.DS.'uploads'.DS.$formname)){
				JError::raiseWarning(100, 'Couldn\'t create upload directroy 1');
			}
			if(!JFile::write(JPATH_COMPONENT.DS.'uploads'.DS.$formname.DS.'index.html', 'NULL')){
				JError::raiseWarning(100, 'Couldn\'t create upload directroy 2');
			}
		}
		//$allowed_s1 = explode(",", trim($paramsvalues->uploadfields));
			if ( is_array($paramsvalues->uploadfields) ) {
				$allowed_s1 = implode('|', $paramsvalues->uploadfields);
			} else {
				$allowed_s1 = $paramsvalues->uploadfields;
			}
			$allowed_s1 = explode(",", trim($allowed_s1));
		
		foreach ( $allowed_s1 as $allowed_1 ) {
			$allowed_s2      = explode(":", trim($allowed_1));
			$allowed_s3      = explode("|", trim($allowed_s2[1]));
			$allowed_s4      = explode("{", trim($allowed_s3[count($allowed_s3) - 1]));
			$allowed_s3[count($allowed_s3) - 1]	= $allowed_s4[0];
			$allowed_s5      = explode("-", str_replace('}', '', trim($allowed_s4[1])));
			$chronofile 	= JRequest::getVar( $allowed_s2[0], '', 'files', 'array' );
			$chronofile['name']	= JFile::makeSafe($chronofile['name']);
			$original_name   = $chronofile['tmp_name'];
			$filename        = date('YmdHis').'_'.preg_replace('`[^a-z0-9-_.]`i','',$chronofile['name']);
			$fileok          = true;
			if ( $original_name ) {
				if ( ($chronofile["size"] / 1024) > trim($allowed_s5[0]) ) {
					$fileok = false;
					$session->set("chrono_verification_msg", 'Sorry, Your uploaded file size exceeds the allowed limit.', md5('chrono'));
					showform($posted);
					return;
				}
				if ( ($chronofile["size"] / 1024) < trim($allowed_s5[1]) ) {
					$fileok = false;
					$session->set("chrono_verification_msg", 'Sorry, Your uploaded file size is less than the allowed limit', md5('chrono'));
					showform($posted);
					return;
				}
				$fn     = $chronofile['name'];
				$fext   = substr($fn, strrpos($fn, '.') + 1);
				if ( !in_array(strtolower($fext), $allowed_s3) ) {
					$fileok = false;
					$session->set("chrono_verification_msg", 'Sorry, Your uploaded file type is not allowed', md5('chrono'));
					showform($posted);
					return;
				}
				if ( $fileok ) {					
					$uploadedfile = JFile::upload($original_name, JPATH_COMPONENT.DS.'uploads'.DS.$formname.DS.$filename);//handle_uploaded_files($original_name, $filename);
					$posted[$allowed_s2[0]] = $filename;
					JRequest::setVar($allowed_s2[0], $filename);
					if ( $uploadedfile ) {
                        $attachments[$allowed_s2[0]] = JPATH_COMPONENT.DS.'uploads'.DS.$formname.DS.$filename;
						if ( $debug ) {
							$mainframe->enqueueMessage($filename.' has been uploaded OK');
						}
					}else{
						if ( $debug ) {
							$mainframe->enqueueMessage($filename.' has NOT been uploaded!!');
						}
					}
				}
			}
		}
	}

	/* Do Onsubmit before_email plugins*/
	
	$ava_plugins = explode(",",$paramsvalues->plugins);
	$ava_plugins_order = explode(",",$paramsvalues->mplugins_order);
	//$ava_plugins_array = array();
	array_multisort($ava_plugins_order, $ava_plugins);
	foreach($ava_plugins as $ava_plugin){
		$query     = "SELECT * FROM #__chrono_contact_plugins WHERE form_id='".$rows[0]->id."' AND event='ONSUBMIT' AND name='".$ava_plugin."'";
		$database->setQuery( $query );
		$plugins = $database->loadObjectList();
		if(count($plugins)){
			require_once(JPATH_SITE."/components/com_chronocontact/plugins/".$ava_plugin.".php");
			${$ava_plugin} = new $ava_plugin();
			//$params = mosParseParams($plugins[0]->params);
			$registry3 = new JRegistry();
			$registry3->loadINI( $plugins[0]->params );
			$params = $registry3->toObject( );
			if($params->onsubmit == 'before_email'){
				${$ava_plugin}->onsubmit( 'com_chronocontact', $params, $plugins[0] );
			}
		}
	}
	if ( $debug ) {
		$mainframe->enqueueMessage('Form passed the plugins step (if enabled) OK');
	}
	/**
	 * If there are no errors and e-mail is required then build and send it.
	 */
	if ( ($rows[0]->emailresults != 0) && !$error_found ) {
	    /**
         * Clean the list of fields to be omitted from the results email
         */
	    /*if ( trim($paramsvalues->omittedfields ) != '' ) {
	       $omittedlist = explode(",", $paramsvalues->omittedfields);
        }*/
	    $htmlstring = $rows[0]->html;
	    /**
	     * Find all the 'name's in the html-string and add to the $matches array
	     */
	    /*
		preg_match_all('/name=("|\').*?("|\')/i', $htmlstring, $matches);
	    // clean the matches array
	    
	    $names = array();
	    foreach ( $matches[0] as $name ) {
	        $name = preg_replace('/name=("|\')/i', '', $name);
	        $name = preg_replace('/("|\')/', '', $name);
	        $name = preg_replace('/name=("|\')/', '', $name);
	        if ( strpos($name, '[]') ) {
	            $name = str_replace('[]', '', $name);
	        }
	        $names[] = trim($name);
	    }
	    $names = array_unique($names);
		*/
	    /**
	     * Associate field values with names and implode arrays
	     */
	    $fields = array();
		$names = explode(",", $rows[0]->fieldsnames);
	    foreach ( $names as $name ) {
			if($paramsvalues->handlepostedarrays == 'Yes'){
				if ( is_array($posted[$name])) {
					$fields[$name] = implode(", ", $posted[$name]);
				} else {
					$fields[$name] = $posted[$name];
				}
			}else{
				$fields[$name] = $posted[$name];
			}
	    }
	    /**
	     * Loop the Emails
	     */
		$database =& JFactory::getDBO();
		$query = "SELECT * FROM #__chrono_contact_emails WHERE formid = '".$rows[0]->id."'";
		$database->setQuery( $query );
		$emails = $database->loadObjectList();
		$emailscounter = 0; 
		
		if ( $debug ) {
			$mainframe->enqueueMessage('Emails data loaded OK');
		}
		/**
	     * Run the On-submit 'pre e-mail' code if there is any
	     */
	    if ( !empty($rows[0]->onsubmitcodeb4) ) {
			eval( "?>".$rows[0]->onsubmitcodeb4 );
		}
		if($paramsvalues->savedataorder == 'before_email'){
			if ( !empty($rows[0]->autogenerated) ) {
				eval( "?>".$rows[0]->autogenerated );
			}
		}
		
		if ( $debug ) {
			$mainframe->enqueueMessage('Form passed all before email code evaluation OK');
		}
		
		$posted = JRequest::get( 'post' , JREQUEST_ALLOWRAW );
		foreach($emails as $email){
			$registry_email = new JRegistry();
			$registry_email->loadINI( $email->params );
			$email_params = $registry_email->toObject( );
			if ( $email->enabled == "1" ) {
				//if ( $debug ) { echo "Use template<br />"; }
				$email_body = $email->template;
				ob_start();
				eval( "?>".$email_body );
				$email_body = ob_get_clean();
				//ob_end_clean();
				//build emial template from defined fields and posted fields
				foreach ( $fields as $name => $post) {
					$email_body = str_replace("{".$name."}", $post, $email_body);
				}
				foreach ( $posted as $name => $post) {
					if(!is_array($post))
					{$email_body = str_replace("{".$name."}", $post, $email_body);}
					else
					{$email_body = str_replace("{".$name."}", implode(", ", $post), $email_body);}
				}
				
				/**
				 * Add IP address if required
				 */
				if ( $email_params->recordip == "1" ) {
					if ( $email_params->emailtype == "html" ) {
						$email_body .= "<br /><br />";
					}
					$email_body .= "Submitted by ".$_SERVER['REMOTE_ADDR'];
				}
				/**
				 * Wrap page code around the html message body
				 */
				if ( $email_params->emailtype == "html" ) {
					$email_body = "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">
									<html>
										<head>
											<title></title>
											<base href=\"JURI::base()/\" />
										</head>
										<body>$email_body</body>
									</html>";
				}
				$fromname = (trim($email->fromname)) ? trim($email->fromname) : JRequest::getVar( trim($email->dfromname), NULL, 'post', 'string' );
				$from = (trim($email->fromemail)) ? trim($email->fromemail) : JRequest::getVar( trim($email->dfromemail), NULL, 'post', 'string' );
				$subject = (trim($email->subject)) ? trim($email->subject) : JRequest::getVar( trim($email->dsubject), NULL, 'post', 'string' );
				// Recepients
				$recipients = array();
				if(trim($email->to)){
					$recipients = explode(",", trim($email->to));
				}
				if(trim($email->dto)){
					$dynamic_recipients = explode(",", trim($email->dto));
					foreach($dynamic_recipients as $dynamic_recipient){
						$recipients[] = JRequest::getVar( trim($dynamic_recipient), NULL, 'post', 'string' );
					}
				}
				// CCs
				$ccemails = array();
				if(trim($email->cc)){
					$ccemails = explode(",", trim($email->cc));
				}
				if(trim($email->dcc)){
					$dynamic_ccemails = explode(",", trim($email->dcc));
					foreach($dynamic_ccemails as $dynamic_ccemail){
						$ccemails[] = JRequest::getVar( trim($dynamic_ccemail), NULL, 'post', 'string' );
					}
				}
				// BCCs
				$bccemails = array();
				if(trim($email->bcc)){
					$bccemails = explode(",", trim($email->bcc));
				}
				if(trim($email->dbcc)){
					$dynamic_bccemails = explode(",", trim($email->dbcc));
					foreach($dynamic_bccemails as $dynamic_bccemail){
						$bccemails[] = JRequest::getVar( trim($dynamic_bccemail), NULL, 'post', 'string' );
					}
				}
				// Replies		
				$replyto_email = NULL;
				$replyto_name = NULL;
				
				$mode = ($email_params->emailtype == 'html') ? true : false;
				
				if(!$mode){
					$email_body = JFilterInput::clean($email_body, 'STRING');
				}
				
				/**
				 * Send the email(s)
				 */
				$email_sent = JUtility::sendMail($from, $fromname, $recipients, $subject, $email_body, $mode, $ccemails, $bccemails, $attachments, $replyto_email, $replyto_name );
				if ( $debug ) {
					if ($email_sent)$mainframe->enqueueMessage('An email has been SENT successfully from ('.$fromname.')'.$from.' to '.implode(',', $recipients));
					if (!$email_sent)$mainframe->enqueueMessage('An email has failed to be sent from ('.$fromname.')'.$from.' to '.implode(',', $recipients));
				}
				// :: HACK :: insert debug
				if ( $debug ) {
					echo "<h4>E-mail message</h4>
					<div style='border:1px solid black; padding:6px;margin:6px;'>
					<p>From: $fromname [$from]<br />
					To:  ".implode($recipients,', ')."<br />
					Subject: $subject</p>
					$email_body<br /></div>";
				}
			}
		}		  
	}
	    
	
	
	if ( !$error_found ) {
	/*************** check to see if order was specified, if not then use the default old one ************************/
		if((!$paramsvalues->plugins_order)&&(!$paramsvalues->onsubmitcode_order)&&(!$paramsvalues->autogenerated_order)){
			$paramsvalues->autogenerated_order=3;
			$paramsvalues->onsubmitcode_order=2;
			$paramsvalues->plugins_order=1;
		}
	
		for($ixx = 1 ; $ixx <= 3; $ixx++){
			if($paramsvalues->plugins_order == $ixx){
				$ava_plugins = explode(",",$paramsvalues->plugins);
				$ava_plugins_order = explode(",",$paramsvalues->mplugins_order);
				//$ava_plugins_array = array();
				array_multisort($ava_plugins_order, $ava_plugins);
				foreach($ava_plugins as $ava_plugin){
					$query     = "SELECT * FROM #__chrono_contact_plugins WHERE form_id='".$rows[0]->id."' AND event='ONSUBMIT' AND name='".$ava_plugin."'";
					$database->setQuery( $query );
					$plugins = $database->loadObjectList();
					if(count($plugins)){
						require_once(JPATH_SITE."/components/com_chronocontact/plugins/".$ava_plugin.".php");
						${$ava_plugin} = new $ava_plugin();
						$registry2 = new JRegistry();
						$registry2->loadINI( $plugins[0]->params );
						$params = $registry2->toObject( );
						if($params->onsubmit != 'before_email'){
							${$ava_plugin}->onsubmit( 'com_chronocontact', $params , $plugins[0] );
						}
					}
				}
			}
			/**
			 * Run the On-submit 'post e-mail' code if there is any
			 */
			if($paramsvalues->onsubmitcode_order == $ixx){
				if ( !empty($rows[0]->onsubmitcode) ) {
					ob_start();
					eval( "?>".$rows[0]->onsubmitcode );
					$onsubmitcode = ob_get_clean();
					foreach ( $posted as $name => $post) {
						$onsubmitcode = str_replace("{".$name."}", $post, $onsubmitcode);
					}
					echo $onsubmitcode;
				}
			}
	
			/**
			 * Run the SQL query if there is one
			 */
			if($paramsvalues->savedataorder == 'after_email'){
				if($paramsvalues->autogenerated_order == $ixx){
					if ( !empty($rows[0]->autogenerated) ) {
						eval( "?>".$rows[0]->autogenerated );
					}
				}
			}
		}
		if ( $debug ) {
			$mainframe->enqueueMessage('Debug End');
		}
		/**
		 * Redirect the page if requested
		 */
		if ( !$debug ) {
			if ( !empty($rows[0]->redirecturl) ) {
				$mainframe->redirect($rows[0]->redirecturl);
			}
		}
	}
}
/**
 * Handle uploaded files
 *
 * @param unknown_type $uploadedfile
 * @param string $filename
 * @param string $limits
 * @param string $directory
 * @return unknown
 */
function handle_uploaded_files($uploadedfile, $filename, $limits = TRUE, $directory = FALSE)
{
    $uploaded_files = "";
    $upload_path = JPATH_SITE.'/components/com_chronocontact/upload/';
    if ( is_file($uploadedfile) ) {
        $targetfile = $upload_path.$filename;
        while ( file_exists($targetfile) ) {
            $targetfile = $upload_path.rand(1,1000).'_'.$filename;
        }
        move_uploaded_file($uploadedfile, $targetfile);
        $uploaded_files = $targetfile;
    }
    return $uploaded_files;
}

/**
 * Display JavaScript alert box as error message
 *
 * @param string $message
 */
function showErrorMessage($message) {
    echo "<script> alert('$message'); </script>\n";
}
?>