<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * mod_mainmenu Helper class
 */
class modGoogleWeatherHelper
{
	// a bit complicate but if the simpleXML replacement is not 100% compatible
	function getData( $node ) {
		if (isset($node)) {
			$data = $node->attributes();
			return $data['data'];
		}	
	}
	
	function getXMLbyCurl($url) {
		$ch = curl_init();
		$header = array();
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: en-us,en;q=0.5";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 180);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$wxml = curl_exec($ch);
		curl_close($ch);
		return $wxml; 
	}

}
