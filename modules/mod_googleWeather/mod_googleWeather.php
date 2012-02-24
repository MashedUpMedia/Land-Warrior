<?php
/**
* googleWeather module
* Displays current weather status at a module position
* Author: computer-daten-netze:feenders - dirk hoeschen
* Copyright (C) by feenders.de - GNU GPL v2
* Website: http://www.feenders.de
* v1.2 August 2009
*/

defined( '_JEXEC' ) or die( 'Direct Access not allowed.' );

require_once (dirname(__FILE__).DS.'helper.php');
		

// detect laguage if demanded
if ($params->get('use_page_language','true')) {	
	require_once JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'language'.DS.'helper.php'; 
	$weather_language = JLanguageHelper::detectLanguage();
	$weather_language = substr($weather_language,0,2);	
} else {
	$weather_language = $params->get('weather_language','en');	
}
		
// build Google API url
$weather_url = 'http://www.google.co.uk/ig/api?weather='.urlencode(trim($params->get('weather_location','berlin'))).'&hl='.urlencode($weather_language);
// create output string
$output = "<div class='mod_weather".$params->get( 'moduleclass_sfx' )."'>\n";

// try to use alternate method if allow_url_fopen is disabled
if (ini_get('allow_url_fopen')) {
	$wxml = @file($weather_url);
	$wxml = $wxml[0];
} else if (function_exists('curl_exec')) { // try curl
	$wxml = modGoogleWeatherHelper::getXMLbyCurl($weather_url);
}

// parse XML - file
if ($wxml) {
	$wxml = utf8_encode($wxml);
	// use alternate method if simpleXML does not exists (PHP4.x)
	if (function_exists('simplexml_load_file')) {
 		$xml = new SimpleXMLElement($wxml); 
 		$weather = $xml->weather;
 	} else {
 		require_once JPATH_ROOT.DS.'modules'.DS.'mod_googleWeather'.DS.'simpleXML'.DS.'IsterXmlSimpleXMLImpl.php';
 		$sxml = new IsterXmlSimpleXMLImpl;
 		$xml = $sxml->load_string($wxml);
 		$weather = $xml->xml_api_reply->weather;
 	}
	$current = $weather->current_conditions;
 	if ($current) {			
			$output .= "<div style='display: block;overflow: hidden;' class='weather_current'>\n";
			//$output .="<b>>&nbsp;Weather Report</b><br /><br />";
			if ($params->get('show_icon')) {
				$icon = strrchr(modGoogleWeatherHelper::getData($current->icon),"/");
				$icon = ($icon!="") ? substr($icon,1,-3)."gif" : "unknown.gif";
				//$output .= "<img class='mod_weather' src='".JURI::Base(false)."modules/mod_googleWeather/icons/".$icon."' alt='".modGoogleWeatherHelper::getData($current->condition)."' align='".$params->get('img_align')."' hspace='10' />";
				$output .= "<img class='mod_weather' src='".JURI::Base(false)."modules/mod_googleWeather/icons/".$icon."' alt='".modGoogleWeatherHelper::getData($current->condition)."' align='right' hspace='10' padding-right='100px' /></div>\n";
			}
 			//$output .= "<b>>&nbsp;Current</b><br />";
			$output .= "<div style='display: block;overflow: hidden;' class='weather_current'>\n";
			$output .= "<b>>&nbsp;".((modGoogleWeatherHelper::getData($current->condition)!="") ? modGoogleWeatherHelper::getData($current->condition) : "Changeable" )."</b> ";
 			switch ($params->get('temp_unit')) {
 				case 'c':
					$output .= modGoogleWeatherHelper::getData($current->temp_c)."&nbsp;<sup>o</sup>C";
				break;
 				case 'f':  					
					$output .= modGoogleWeatherHelper::getData($current->temp_f)."&nbsp;<sup>o</sup>F";
 				break;	
 				default:
 					$output .= "(".modGoogleWeatherHelper::getData($current->temp_f)."&nbsp;<sup>o</sup>F&nbsp;&bull;&nbsp;";
					$output .= modGoogleWeatherHelper::getData($current->temp_c)."&nbsp;<sup>o</sup>C)";
 			   }	
 			$output .= "<br /><b>>&nbsp;".str_replace(":",":</b>",modGoogleWeatherHelper::getData($current->humidity))."<br />";
			$output .= "<b>>&nbsp;".str_replace(":",":</b>",modGoogleWeatherHelper::getData($current->wind_condition))."<br /><br />";
 			$output .= "</div>\n";					
			// output forecast
			if (($xtrap=="forecast") || ($params->get('show_forecast',1)==1)) {
				$unit = (modGoogleWeatherHelper::getData($weather->forecast_information->unit_system)=="US") ? "F" : "C";
				$flimit = 0;
				//$output .="<br /><b>>&nbsp; Forecast Conditions";
				foreach ($weather->forecast_conditions as $val => $current) {
					$output .= "<div class='weather_forecast'>"
							."<b>>&nbsp;".modGoogleWeatherHelper::getData($current->day_of_week)."</b>&nbsp;"
							//.modGoogleWeatherHelper::getData($current->low)."&nbsp;-&nbsp;"
							//.modGoogleWeatherHelper::getData($current->high)."&nbsp;<sup>o</sup>".$unit
							." &raquo;&nbsp;".modGoogleWeatherHelper::getData($current->condition)."&nbsp;&laquo;</div>\n";
					$flimit++;
					if ($flimit>=3) break; 
				}
			}
	} else {
		$output .= "<p>Could not get weather informations for ".$location."</p>\n";
	}
} else {
	if ($params->get('show_errors','true')) {	
 		$output .= "<p><i><b>Could not load data from google!</b><br/>" 
 		   		."In order to use google-weather, you must enable allow_url_fopen in php.ini."
 		   		."Remember: PHP must be able to read external XML-files!</i></p>\n";
	}	   		
}
$output .= "</div>\n";
// output weather informations
echo $output;			


?>
