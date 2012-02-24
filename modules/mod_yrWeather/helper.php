<?php
/**
* @version	$Id: mod_yrWeather.php 11.01.2009 1200 1
* @package	Joomla 1.5
* @copyright Copyright (C) 2008 Bjorn Nornes. All rights reserved.
* @license	GNU/GPL,
* Parse and display yr.no  weather data
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modyrWeatherHelper
{
	public function convertEncodingUTF($yrraw)
	{
		$conv=str_replace("Ã¦", "æ", $yrraw);
		$conv=str_replace("Ã¸", "ø", $conv);
		$conv=str_replace("Ã¥", "å", $conv);
		$conv=str_replace("Ã†", "Æ", $conv);
		$conv=str_replace("Ã˜", "Ø", $conv);
		$conv=str_replace("Ã…", "Å", $conv);
		return $conv;
	}
		
	public function convertEncodingEntities($yrraw)
	{
		$conv=str_replace("æ", "&aelig;", $yrraw);
		$conv=str_replace("ø", "&oslash;", $conv);
		$conv=str_replace("å", "&aring;", $conv);
		$conv=str_replace("Æ", "&AElig;", $conv);
		$conv=str_replace("Ø", "&Oslash;", $conv);
		$conv=str_replace("Å", "&Aring;", $conv);
		return $conv;
	}
	
	function loadXMLData(&$params)
	{			

		// check if cache directory exists and is writeable
		$cacheDir =  JPATH_BASE.DS.'cache';	
		if ( !is_writable( $cacheDir ) ) 
		{	
			$cache_exists = false;
			//send error
			
		}else{
			$cache_exists = true;
		}		
		
			//check if URL is entered as parameter for module
			//send error if not.			
			//$test1 = str_replace("%2F", "/", urlencode("www.vackertvader.se/sverige/örebro-län/laxå/tivedstorp/"));
			//$test1 = str_replace("%3A", ":", $test1);
			//print $test1;
			//$url_Weather=$test1.'/forecast.xml';
			
			
			
			//$url_Weather = $params->get('url_Weather', NULL );		
			$url_Weather = str_replace("%2F", "/", urlencode($params->get('url_Weather', NULL )));
			$url_Weather = str_replace("%3A", ":", $url_Weather);
			
			$url_Weather.='/varsel.xml';
									
			$timeout=10;
						
			$xml_url=modyrWeatherHelper::convertEncodingUTF($url_Weather);		
				
			
			//$ctx = stream_context_create(array( 'http' => array('timeout' => $timeout)));

			$lokal_xml_url = $cacheDir.'/curl.temp.xml';
			$data='';
						
			$ch = curl_init($xml_url);
			
			// Open local cache copy for writing
			$fp = fopen($lokal_xml_url, "w");
			
			// Load from yr.no to local copy
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '');
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_exec($ch);
			curl_close($ch);
			
			// Close local copy
			fclose($fp);
			try {	
				// Open cache file and read
				$data=simplexml_load_file($lokal_xml_url);    

				//Delete temp file			
				unlink($lokal_xml_url);
			}
			catch (Exception $e) {						
						
			}
			return $data;		

	}	

	//build array of weather forcasts
	function buildArray($data){
		$rowCount = 0;
		foreach ($data->forecast->tabular->time as $item) 
		{
			$listArray[$rowCount] = array(fromTime=>$item[from], toTime=>$item[to], period=>$item[period], symbolNumber=>$item->symbol[number], symbolName=>$item->symbol[name], precipitationValue=>$item->precipitation[value], windDirectionDeg=>$item->windDirection[deg], windDirectionCode=>$item->windDirection[code], windDirectionName=>$item->windDirection[name], windSpeedMps=>$item->windSpeed[mps], windSpeedName=>$item->windSpeed[name], temperatureUnit=>$item->temperature[unit], temperatureValue=>$item->temperature[value], pressureUnit=>$item->pressure[unit], pressureValue=>$item->pressure[value]);
			$rowCount += 1;
		}		
		
		return $listArray;
	}
	
	//build array of text weather forcasts
	function buildtextArray($data){
		$rowCount = 0;
			foreach ($data->forecast->text->location->time as $item) 
			{
				$textArray[$rowCount] = array(fromTime=>$item[from], toTime=>$item[to], title=>$item->title, body=>$item->body);
				$rowCount += 1;
			}	
			return $textArray;
	
	}	
		
	//build array of meta data/geo data etc....
	function buildMetaArray($data){
		$rowCount = 0;
		foreach ($data->location as $item) 
		{
			if($rowCount == 0)
			{
				$textArray[$rowCount] = array(name=>$item->name, type=>$item->type, country=>$item->country, timezoneId=>$item->timezone[id], timezoneUTC=>$item->timezone[utcoffsetMinutes], locationAltitude=>$item->location[altitude], locationLatitude=>$item->location[latitude], locationLongitude=>$item->location[longitude], locationGeobase=>$item->location[geobase], locationGeobaseId=>$item->location[geobaseid], lastUpdate=>$data->meta->lastupdate , nextUpdate=>$data->meta->nextupdate);
			}
			$rowCount += 1;
		}		
		
		return $textArray;
	}
	
	//build array of source links.....
	function buildLinksArray($data){
		$rowCount = 0;
		foreach ($data->links->link as $item) 
		{
			$textArray[$rowCount] = array(id=>$item[id], url=>$item[url]);
			$rowCount += 1;
		}				
		return $textArray;
	}	
	
	//calculate "feels like" temperatur.....
	function chillfactor($temperatur, $windspeed){
		// temperaturen celcius, 2 meters above ground.
		// windspeed km/hour, 10 meters above ground
		//convert from m/s to km/h
		$windspeed = $windspeed * 3.6;

		$avkjoling=((13.12 + 0.6215*$temperatur) + (0.3965 * $temperatur * pow($windspeed,0.16)) - (11.37 * pow($windspeed,0.16)));
		$avkjoling=round($avkjoling);

		return $avkjoling;
		
		//$vind_feltTemp1 = 11.37 * pow($windspeed, 0.16);
		//$vind_feltTemp1 = $vind_feltTemp1 + 0.3965 * $temperatur * pow($windspeed, 0.16);
		//$vind_feltTemp2 = 13.12 + (0.6215 * $temperatur);				  		  
		//$feltTemp = round($vind_feltTemp2 - $vind_feltTemp1);
		
		//return $feltTemp;
	}	
	
	//get windarrow images.....
	function getWindarrow($windDirection, $windSpeed){
		
		//formula borrowed from com_jyr
		$vinddir=round(($windDirection+7.5)/15)%24;
		$vindspeed=sprintf('%04d',round($windSpeed/2.5)*25);	
		
		$windArrow = 'vindpil.'.$vindspeed.'.'.sprintf('%03d',$vinddir*15).'.png';
		
		return $windArrow;
	}		
	
	function contains($str, $content, $ignorecase=true){
		if ($ignorecase){
			$str = strtolower($str);
			$content = strtolower($content);
		}  
	return strpos($content,$str) ? true : false;
}	
	
	function getFeed(&$params){
		//global $mainframe;
		$xml_Weather = array(); //init feed array

		// check if cache directory exists and is writeable
		$cacheDir =  JPATH_BASE.DS.'cache';	
		if ( !is_writable( $cacheDir ) ) {	
			$xml_Weather['error'][] = 'Cache folder is unwriteable. Solution: chmod 777 '.$cacheDir;
			$cache_exists = false;
		}else{
			$cache_exists = true;
		}
		//get local module parameters from xml file module config settings
		$cache_Weather		= $params->get( 'rsscache', 0 );		
		$url_Weather 		= $params->get( 'url_Weather', NULL );
		$items_Weather 		= $params->get( 'items_Weather', 5 );
		$link_target		= $params->get( 'link_target', 1 );
		$yr_imgpath			='http://fil.nrk.no/yr/grafikk/sym/b38/';
		$yr_datadir			='cache';
		$yr_maxage			=0;
		$try_curl			= true;
		$timeout			= 10;
		
		if(!$url_Weather){
			$xml_Weather['error'][] = 'Invalid feed url. Please enter a valid url in the module settings.';
			return $xml_Weather; //halt if no valid feed url supplied
		}
		
		switch($link_target){ //open links in current or new window
			case 1:
				$link_target='_blank';
				break;
			case 0:
				$link_target='_self';
				break;
			default:
				$link_target='_blank';
				break;
		}
		$xml_Weather['target'] = $link_target;				
		$weather_data = simplexml_load_file($url_Weather) or die("feed not loading");

		
		//return the feed data structure for the template	
		return $weather_data;
	}
	
	function moonphase($year, $month, $day)
	{
			/*
			modified from http://www.voidware.com/moon_phase.htm
			*/
			$c = $e = $jd = $b = 0;
			if ($month < 3)
			{
				$year--;
				$month += 12;
			}
			++$month;
			$c = 365.25 * $year;
			$e = 30.6 * $month;
			$jd = $c + $e + $day - 694039.09;	//jd is total days elapsed
			$jd /= 29.5305882;					//divide by the moon cycle
			$b = (int) $jd;						//int(jd) -> b, take integer part of jd
			$jd -= $b;							//subtract integer part to leave fractional part of original jd
			$b = round($jd * 8);				//scale fraction from 0-8 and round
			if ($b >= 8 )
			{
				$b = 0;//0 and 8 are the same so turn 8 into 0
			}
			switch ($b)
			{
				case 0:
					return 0; //'New Moon';
					break;
				case 1:
					return 1; //'Waxing Crescent Moon';
					break;
				case 2:
					return 2; //'Quarter Moon';
					break;
				case 3:
					return 3; //'Waxing Gibbous Moon';
					break;
				case 4:
					return 4; //'Full Moon';
					break;
				case 5:
					return 5; //'Waning Gibbous Moon';
					break;
				case 6:
					return 6; //'Last Quarter Moon';
					break;
				case 7:
					return 7; //'Waning Crescent Moon';
					break;
				default:
					return 'Error';
			}
		}		
}