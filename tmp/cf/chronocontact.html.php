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

/* ensuring that this file is called up from another file */
defined('_JEXEC') or die('Restricted access');
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();

class HTML_ChronoContact {
// Procedure for building the table
 function showform( $rows , $imver, $posted ) {
 	global $mainframe;
	$database =& JFactory::getDBO();
	$registry = new JRegistry();
	$registry->loadINI( $rows[0]->paramsall );
	$paramsvalues = $registry->toObject( );
 if((!empty($rows[0]->name))&&($rows[0]->published)){
		?>
		<?php if ($paramsvalues->LoadFiles == 'Yes'){ ?>	
		<?php JHTML::_('behavior.mootools'); ?>
		<!--[if gte IE 6]><link href="<?php echo JURI::Base().'components/com_chronocontact/css/'; ?>style1-ie6.css" rel="stylesheet" type="text/css" /><![endif]-->
		<!--[if gte IE 7]><link href="<?php echo JURI::Base().'components/com_chronocontact/css/'; ?>style1-ie7.css" rel="stylesheet" type="text/css" /><![endif]-->
		<!--[if !IE]> <--><link href="<?php echo JURI::Base().'components/com_chronocontact/css/'; ?>style1.css" rel="stylesheet" type="text/css" /><!--> <![endif]-->
		<link href="<?php echo JURI::Base().'components/com_chronocontact/css/'; ?>calendar.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo JURI::Base().'components/com_chronocontact/css/'; ?>tooltip.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="<?php echo JURI::Base().'components/com_chronocontact/js/'; ?>calendar.js"></script>
		<script src="<?php echo JURI::Base().'components/com_chronocontact/js/'; ?>mooValidation.js" type="text/javascript"></script>
		<script type="text/javascript">
			Tips.implement({
			initialize: function(elements, lasthope,options){
					this.setOptions(options);
					this.lasthope = lasthope;
					this.toolTip = new Element('div', {
						'class': 'cf_'+this.options.className + '-tip',
						'id': this.options.className + '-tip-' + this.options.elementid,
						'styles': {
							'position': 'absolute',
							'top': '0',
							'left': '0',
							'visibility': 'hidden'
						}
					}).inject(document.body);
					this.wrapper = new Element('div').inject(this.toolTip);
					$$(elements).each(this.build, this);
					if (this.options.initialize) this.options.initialize.call(this);
				},
			
				build: function(el){
					el.$tmp.myTitle = (el.href && el.getTag() == 'a') ? el.href.replace('http://', '') : (el.rel || false);
					if (el.title){
						var dual = el.title.split('::');
						if (dual.length > 1){
							el.$tmp.myTitle = dual[0].trim();
							el.$tmp.myText = dual[1].trim();
						} else {
							el.$tmp.myText = el.title;
						}
						el.removeAttribute('title');
					} else {
						var dual = this.lasthope.split('::');
						if (dual.length > 1){
							el.$tmp.myTitle = dual[0].trim();
							el.$tmp.myText = dual[1].trim();
						} else {
							el.$tmp.myText = el.title;
						}
					}
					if (el.$tmp.myTitle && el.$tmp.myTitle.length > this.options.maxTitleChars) el.$tmp.myTitle = el.$tmp.myTitle.substr(0, this.options.maxTitleChars - 1) + "&hellip;";
					el.addEvent('mouseenter', function(event){
						this.start(el);
						if (!this.options.fixed) this.locate(event);
						else this.position(el);
					}.bind(this));
					if (!this.options.fixed) el.addEvent('mousemove', this.locate.bindWithEvent(this));
					var end = this.end.bind(this);
					el.addEvent('mouseleave', end);
					el.addEvent('trash', end);
				},
				start: function(el){
					this.wrapper.empty();
					if (el.$tmp.myTitle){
						this.title = new Element('span').inject(new Element('div', {'class': 'cf_'+this.options.className + '-title'}).inject(this.wrapper)).setHTML(el.$tmp.myTitle);
					}
					if (el.$tmp.myText){
						this.text = new Element('span').inject(new Element('div', {'class': 'cf_'+this.options.className + '-text'}).inject(this.wrapper)).setHTML(el.$tmp.myText);
					}
					$clear(this.timer);
					this.timer = this.show.delay(this.options.showDelay, this);
				}
			});
			window.addEvent('domready', function() {
				$ES('.tooltipimg').each(function(ed){
					var Tips2 = new Tips(ed, $E('div.tooltipdiv', ed.getParent().getParent()).getText(), {elementid:ed.getParent().getParent().getFirst().getNext().getProperty('id')+'_s'});
				});
				if($chk($E('input[type=file]', $('<?php echo "ChronoContact_".$rows[0]->name; ?>')))){
					$('<?php echo "ChronoContact_".$rows[0]->name; ?>').setProperty('enctype', 'multipart/form-data');
				}
			});
		</script>
		<?php } ?>		
		<?php if (($posted)&&($paramsvalues->captcha_dataload)){ ?>			
			<script type="text/javascript">			
			Element.extend({
				getInputByName : function(nome) {
					el = this.getFormElements().filterByAttribute('name','=',nome)
					return (el)?(el.length == 1)?el[0]:el:false;
				},
				setValue: function(value,append){ 
					if(value) { 
						value = value.toString(); 
						value = value.replace(/%25/g,"%"); 
						value = value.replace(/%26/g,"&"); 
						value = value.replace(/%2b/g,"+"); 
					} 
					switch(this.getTag()){ 
						case 'select': case 'select-one': 
							//this.value = value; 
							if ($type(value.split(","))=='array') value.split(",").each(function(v,i){value.split(",")[i]=v.toString()});
							sel = function(option) {
								if (($type(value.split(","))=='array'&&value.split(",").contains(option.value))||(option.value==value))option.selected = true
								else option.selected = false;
							}
							$each(this.options,sel);
							break; 
						case 'hidden': case 'text': case 'textarea': case 'input': 
							if(['checkbox', 'radio'].test(this.type)) { 	 
								//alert(value.split(",").contains(this.value)); alert ($type(value.split(","))); alert(this.name);
								//if(['1', 'checked', 'on', 'true', 'yes'].test(value)) this.checked = true; else this.checked = false; 
								//this.checked=((this.value==value)||(this.value == ','+value+',')||(this.value == '['+value+',')||(this.value == ','+value+']')||(this.value == '['+value+']'));
								this.checked=(($type(value.split(","))=='array')?value.split(",").contains(this.value):(this.value==value));
							} else {
								if(append) this.value += value; else this.value = value; 
							} 
							break; 
						case 'img': 
							this.src = value; 
							break; 
						//default: 
							//value=value.replace(//gi,"“"); value=value.replace(//gi,"”"); 
							//if(append) {this.innerHTML += value;} else {this.innerHTML = value;} 
							//if(append && this.scrollHeight) this.scrollTop = this.scrollHeight; 
							//break; 
					} 
					return this; 
				}
			});
			window.addEvent('domready', function() {
				<?php $post = JRequest::get( 'post' , JREQUEST_ALLOWRAW ); ?>
				<?php foreach($post as $data => $value){ ?>
				<?php if(is_array($value)){$value = "".implode(",", $value).""; $data = $data."[]";} ?>
					$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName('<?php echo $data; ?>').setValue(<?php echo preg_replace('/[\n\r]+/', '\n', "'".$value."'"); ?>, '');
				<?php } ?>			
			});
			</script>
			
		<?php } ?>
			<?php
				if( trim($paramsvalues->validate) == 'Yes'){
			?>
				<script type="text/javascript">
					Element.extend({
						getInputByName1 : function(nome) {
							el = this.getFormElements().filterByAttribute('name','=',nome)
							return (el)?(el.length)?el[0]:el:false;
						}
					});
					window.addEvent('domready', function() {
						<?php if(str_replace(" ","",$paramsvalues->val_required)){ ?>
						('<?php echo str_replace(" ","",$paramsvalues->val_required); ?>').split(',').each(function(field){
							$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName1(field).addClass('required');
						});
						<?php } ?>
						<?php if(str_replace(" ","",$paramsvalues->val_validate_number)){ ?>
						('<?php echo str_replace(" ","",$paramsvalues->val_validate_number); ?>').split(',').each(function(field){
							$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName1(field).addClass('validate-number');
						});
						<?php } ?>
						<?php if(str_replace(" ","",$paramsvalues->val_validate_digits)){ ?>
						('<?php echo str_replace(" ","",$paramsvalues->val_validate_digits); ?>').split(',').each(function(field){
							$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName1(field).addClass('validate-digits');
						});
						<?php } ?>
						<?php if(str_replace(" ","",$paramsvalues->val_validate_alpha)){ ?>
						('<?php echo str_replace(" ","",$paramsvalues->val_validate_alpha); ?>').split(',').each(function(field){
							$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName1(field).addClass('validate-alpha');
						});
						<?php } ?>
						<?php if(str_replace(" ","",$paramsvalues->val_validate_alphanum)){ ?>
						('<?php echo str_replace(" ","",$paramsvalues->val_validate_alphanum); ?>').split(',').each(function(field){
							$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName1(field).addClass('validate-alphanum');
						});
						<?php } ?>
						<?php if(str_replace(" ","",$paramsvalues->val_validate_date)){ ?>
						('<?php echo str_replace(" ","",$paramsvalues->val_validate_date); ?>').split(',').each(function(field){
							$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName1(field).addClass('validate-date');
						});
						<?php } ?>
						<?php if(str_replace(" ","",$paramsvalues->val_validate_email)){ ?>
						('<?php echo str_replace(" ","",$paramsvalues->val_validate_email); ?>').split(',').each(function(field){
							$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName1(field).addClass('validate-email');
						});
						<?php } ?>
						<?php if(str_replace(" ","",$paramsvalues->val_validate_url)){ ?>
						('<?php echo str_replace(" ","",$paramsvalues->val_validate_url); ?>').split(',').each(function(field){
							$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName1(field).addClass('validate-url');
						});
						<?php } ?>
						<?php if(str_replace(" ","",$paramsvalues->val_validate_date_au)){ ?>
						('<?php echo str_replace(" ","",$paramsvalues->val_validate_date_au); ?>').split(',').each(function(field){
							$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName1(field).addClass('validate-date-au');
						});
						<?php } ?>
						<?php if(str_replace(" ","",$paramsvalues->val_validate_currency_dollar)){ ?>
						('<?php echo str_replace(" ","",$paramsvalues->val_validate_currency_dollar); ?>').split(',').each(function(field){
							$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName1(field).addClass('validate-currency-dollar');
						});
						<?php } ?>
						<?php if(str_replace(" ","",$paramsvalues->val_validate_selection)){ ?>
						('<?php echo str_replace(" ","",$paramsvalues->val_validate_selection); ?>').split(',').each(function(field){
							$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName1(field).addClass('validate-selection');
						});
						<?php } ?>
						<?php if(str_replace(" ","",$paramsvalues->val_validate_one_required)){ ?>
						('<?php echo str_replace(" ","",$paramsvalues->val_validate_one_required); ?>').split(',').each(function(field){
							$('<?php echo "ChronoContact_".$rows[0]->name; ?>').getInputByName1(field).addClass('validate-one-required');
						});
						<?php } ?>
					});
				</script>
			<?php	
				}
			?>
		
		<?php if( trim($paramsvalues->validate) == 'Yes'){ ?>
			<?php if( trim($paramsvalues->validatetype) == 'prototype'){ ?>
				<script src="<?php echo JURI::Base().'components/com_chronocontact/js/'; ?>prototype.js" type="text/javascript"></script>
				<script src="<?php echo JURI::Base().'components/com_chronocontact/js/'; ?>effects.js" type="text/javascript"></script>
				<script src="<?php echo JURI::Base().'components/com_chronocontact/js/'; ?>validation.js" type="text/javascript"></script>
			<?php } ?>
			<?php if( (trim($paramsvalues->validatetype) == 'mootools')&&($paramsvalues->LoadFiles == 'No')){ ?>
				<?php JHTML::_('behavior.mootools'); ?>
				<script src="<?php echo JURI::Base().'components/com_chronocontact/js/'; ?>mooValidation.js" type="text/javascript"></script>
			<?php } ?>
		<?php } ?>
		<?php if(!empty($rows[0]->scriptcode)){ 
		echo "<script type='text/javascript'>\n";
        echo "//<![CDATA[\n";
		eval("?>".$rows[0]->scriptcode);
		echo "//]]>\n";
        echo "</script>\n";				
		}		
		?>
		<?php if(!empty($rows[0]->submiturl)){ 
		$actionurl = $rows[0]->submiturl;			
		} else {
		$actionurl = JURI::Base().'index.php?option=com_chronocontact&amp;task=send&amp;chronoformname='.$rows[0]->name;
		}		
		?>
		<?php
			$session =& JFactory::getSession();
		?>
		<?php if($session->get('chrono_verification_msg', '', md5('chrono'))){ ?>
		<style type="text/css">
		span.cf_alert {
			background:#FFD5D5 url(<?php echo JURI::Base().'components/com_chronocontact/css/'; ?>images/alert.png) no-repeat scroll 10px 50%;
			border:1px solid #FFACAD;
			color:#CF3738;
			display:block;
			margin:15px 0pt;
			padding:8px 10px 8px 36px;
		}
		</style>		
			<span class="cf_alert"><?php echo $ver_error_message = $session->get('chrono_verification_msg', 'default', md5('chrono')); ?></span>
		<?php } ?>
<form name="<?php echo "ChronoContact_".$rows[0]->name; ?>" id="<?php echo "ChronoContact_".$rows[0]->name; ?>" method="<?php echo $paramsvalues->formmethod; ?>" action="<?php echo $actionurl; ?>" <?php echo $rows[0]->attformtag; ?>>
		
				<?php 
					if( trim($paramsvalues->enmambots) == 'Yes'){
						global $mainframe;
						$params        =& $mainframe->getParams('com_content');
						$dispatcher       =& JDispatcher::getInstance();
						$type = 'content';
						JPluginHelper::importPlugin($type);
						//JPluginHelper::importPlugin($group, null, false);
						$rowmam->text = $rows[0]->html;
						$results_mambots = $mainframe->triggerEvent( 'onPrepareContent', array (&$rowmam, & $params, 0 ));
						$rows[0]->html = $rowmam->text;
					}
					$html_string = $rows[0]->html;
					/******************** ONLOAD plugins **********************/
					$ava_plugins = explode(",",$paramsvalues->plugins);
					foreach($ava_plugins as $ava_plugin){
						$query     = "SELECT * FROM #__chrono_contact_plugins WHERE form_id='".$rows[0]->id."' AND event='ONLOAD' AND name='".$ava_plugin."'";
						$database->setQuery( $query );
						$plugins = $database->loadObjectList();
						if(count($plugins)){
							require_once(JPATH_SITE."/components/com_chronocontact/plugins/".$ava_plugin.".php");
							${$ava_plugin} = new $ava_plugin();
							$registry2 = new JRegistry();
							$registry2->loadINI( $plugins[0]->params );
							$params = $registry2->toObject( );
							$html_string = ${$ava_plugin}->onload( 'com_chronocontact', $params, $html_string );
						}
					}					
					/**********************************************************/	
					$rows[0]->html = $html_string;
					$rows[0]->html = str_replace('{imageverification}',$imver,$rows[0]->html);
					eval( "?>".$rows[0]->html );
				?>
		<?php echo JHTML::_( 'form.token' ); ?>		
</form>
		<?php if( (trim($paramsvalues->validate) == 'Yes')||($paramsvalues->LoadFiles == 'Yes')){ ?>
			<script type="text/javascript">
				function formCallback(result, form) {
					window.status = "valiation callback for form '" + form.id + "': result = " + result;
				}
				var valid = new Validation('<?php echo "ChronoContact_".$rows[0]->name; ?>', {immediate : true, useTitles : true, onFormValidate : formCallback});
			</script>
		<?php } ?>
<!-- You are not allowed to remove or edit the following 3 lines anyway if you didnt buy a license --> 
<div class="chronoform">
<a href="http://www.chronoengine.com">Joomla Professional Work</a>
</div>
<!-- You are not allowed to remove or edit the above 3 lines anyway if you didnt buy a license -->
		<?php
		} else {
		echo "There is no form with this name or may be the form is unpublished, Please check the form and the url and the form management";
		}
	}
}
?>
