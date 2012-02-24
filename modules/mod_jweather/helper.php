<?php
/**
 * @version		$Id:helper.php 14 2008-02-06 09:35:30Z p0l0 $
 * @package		Joomla jWeather
 * @author		Marco Neumann
 * @copyright	Copyright (c) 2008, Marco Neumann
 * @license		BSD
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 	- Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 *
 * 	- Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * 	- Neither the name of the jWeather nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

/*
 * This module is based on the code of the Wordpress plugin GetWeather:
 *
 * Plugin Name: GetWeather
 * Plugin URI: http://dev.wp-plugins.org/wiki/GetWeather
 * Author: Jeff Minard
 * Author URI: http://thecodepro.com/
 * Version: 1.2.1
 * Description: Shows current weather conditions by querying the weather.com website.
 * Date: 22 January 2005
 */
class modJWeatherHelper
{
	/**
	 * Loads default values for global vars
	 *
	 */
	function defines()
	{
		/**
		 * Measurement System
		 *
		 * s = US Standard
		 * m = Metric
		 *
		 * @var String
		 */
		$this->_measurementSystem = 'm';

		/**
		 * Iconset path
		 *
		 * @var String
		 */
		$this->_iconDirURL =  'icons';

		/**
		 * Number of forecast days
		 *
		 * @var Int
		 */
		$this->_numDays = 3;

		/**
		 * Show day names
		 *
		 * @var Int/String
		 */
		$this->_dayNames = 0;

		/**
		 * Temperature separator
		 *
		 * @var String
		 */
		$this->_tempSeparator = '';

		/**
		 * Available showTypes
		 *
		 * @var array
		 */
		$this->_avlShowTypes = array('desc', 'icon', 'temp', 'forecast', 'curtime', 'sunrise', 'sunset', 'sunrise-sunset',
								   'vis', 'wind', 'hum', 'dew', 'high', 'low', 'high-low'
							 );

		/**
		 * Use cache (NOT IMPLEMENTED YET!)
		 *
		 * @var Boolean
		 */
		$this->_useCache = false;

		/**
		 * Holds the data to be shown
		 *
		 * @var array
		 */
		$this->_showType = array();
	}

	/**
	 * Initialize global vars
	 *
	 * @param JParameter $params
	 */
	function initialize(&$params)
	{
		modJWeatherHelper::defines();

		$this->_measurementSystem = ($params->get('measurement_system'))?$params->get('measurement_system'):$this->_measurementSystem;
		/**
		 * TODO - Fix for getting correctly if path is relative or absolute
		 */
		$this->_iconDirURL = ($params->get('icondir') && $params->get('icondir') != $this->_iconDirURL)?$params->get('icondir'):JURI::base() . 'modules/mod_jweather/tmpl/'.$this->_iconDirURL;
		$this->_numDays = ($params->get('numdays'))?$params->get('numdays'):$this->_numDays;
		$this->_dayNames = ($params->get('days'))?$params->get('days'):$this->_dayNames;
		$this->_tempSeparator = $params->get('tempseparator');

		foreach ($this->_avlShowTypes as $curShowType)
		{
			if ($params->get($curShowType) == 1)
			{
				array_push($this->_showType, $curShowType);
			}
		}
	}

	/**
	 * This function loads weather data for a specified cityCode
	 *
	 * @param string $cityCode
	 * @return array
	 */
	function _loadWeatherData($cityCode)
	{
		$wURL = "http://xoap.weather.com/weather/local/".$cityCode."?cc=*&dayf=".$this->_numDays."&unit=".$this->_measurementSystem;
		$xml_parser = xml_parser_create();

		/**
		 * Initialize data
		 */
		$data = '';

		/**
		 * Check if we use can use cURL
		 */
		if (function_exists('curl_init'))
		{
			$fp = curl_init();
			$curlTimeout = 15; //TODO - Put this as config parameter
			curl_setopt ($fp, CURLOPT_URL, $wURL);
			curl_setopt ($fp, CURLOPT_CONNECTTIMEOUT, $curlTimeout);

			/**
			 * Get Data using cURL
			 */
			ob_start();
			curl_exec($fp);
			$data = ob_get_contents();
			ob_end_clean();

			/**
			 * Check if cURL retrieved data correctly
			 */
			if (curl_errno($fp) != 0)
			{
				JError::raiseWarning( 0, 'Weather Not Available ('.curl_error($fp).')' );
				return false;
			}

			/**
			 * Close cURL Handler because we dont need it anymore
			 */
			curl_close($fp);
		}
		else
		{
			/**
			 * Use fopen
			 */
			if (!($fp = fopen($wURL, "r"))) {
				JError::raiseWarning( 0, 'Weather Not Available (Read Error)' );
				return false;
			}

			while (!feof($fp)) {
				$data .= fread($fp, 8192);
			}
			fclose($fp);
		}

		/**
		 * Initialize arrays
		 */
		$vals = array();
		$index = array();

		xml_parse_into_struct($xml_parser, $data, $vals, $index);
		xml_parser_free($xml_parser);

		$params = array();
		$level = array();
		foreach ($vals as $xml_elem) {
			if ($xml_elem['type'] == 'open') {
				if (array_key_exists('attributes',$xml_elem)) {
					$level[$xml_elem['level']] = array_shift($xml_elem['attributes']);
				} else {
					$level[$xml_elem['level']] = $xml_elem['tag'];
				}
			}
			if ($xml_elem['type'] == 'complete') {
				$start_level = 1;
				$php_stmt = '$params';
				while($start_level < $xml_elem['level']) {
					$php_stmt .= '[$level['.$start_level.']]';
					$start_level++;
				}
				$php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
				eval($php_stmt);
			}
		}

		$this->_weatherData = $params;

		return true;
	}

	/**
	 * Returns retrieved weather data
	 *
	 * @param string $cityCode
	 * @return array
	 */
	function getWeatherData($cityCode)
	{
		modJWeatherHelper::_loadWeatherData($cityCode);

		$output = array();
		/**
		 * Save number of days to be shown
		 */
		$output['numDays'] = $this->_numDays;

		/**
		 * Need we save the day names?
		 */
		switch($this->_dayNames)
		{
			/**
			 * We use JText to be sure the dayname is translated
			 *
			 * TODO - Fix strftime to show correctly in localized language
			 */
			case 'short':
				for ($i=0; $i<$this->_numDays; $i++)
				{
					//$output['days'][$i] = ucfirst(JText::_(strftime("%a", strtotime("+".$i." days"))));
					$output['days'][$i] = ucfirst(JText::_(date("D", strtotime("+".$i." days"))));
				}
				break;
			case 'long':
				for ($i=0; $i<$this->_numDays; $i++)
				{
					//$output['days'][$i] = ucfirst(JText::_(strftime("%A", strtotime("+".$i." days"))));
					$output['days'][$i] = ucfirst(JText::_(date("l", strtotime("+".$i." days"))));
				}
				break;
		}

		foreach ($this->_showType as $showType)
		{
			switch ($showType)
			{
				case 'desc':
					$output['desc'] = $this->_weatherData["2.0"][$cityCode]['DNAM'];
					break;
				case 'icon':
					for ($i=0; $i<$this->_numDays; $i++)
					{
						if ($i == 0)
						{
							/**
							 * Update icon numeric code
							 */
							if (is_numeric($this->_weatherData["2.0"]['CC']['ICON']) && $this->_weatherData["2.0"]['CC']['ICON'] < 10)
							{
								$this->_weatherData["2.0"]['CC']['ICON'] = '0'.$this->_weatherData["2.0"]['CC']['ICON'];
							}
							$output['icon'][$i]['image'] = $this->_iconDirURL.'/'.$this->_weatherData["2.0"]['CC']['ICON'].'.png';
							$output['icon'][$i]['alt'] = $this->_weatherData["2.0"]['CC']['T'];
						}
						else
						{
						/**
							 * Update icon numeric code
							 */
							if (is_numeric($this->_weatherData["2.0"]['DAYF'][$i]['d']['ICON']) && $this->_weatherData["2.0"]['DAYF'][$i]['d']['ICON'] < 10)
							{
								$this->_weatherData["2.0"]['DAYF'][$i]['d']['ICON'] = '0'.$this->_weatherData["2.0"]['DAYF'][$i]['d']['ICON'];
							}
							$output['icon'][$i]['image'] = $this->_iconDirURL.'/'.$this->_weatherData["2.0"]['DAYF'][$i]['d']['ICON'].'.png';
							$output['icon'][$i]['alt'] = $this->_weatherData["2.0"]['DAYF'][$i]['d']['T'];
						}
					}
					break;
				case 'temp':
					for ($i=0; $i<$this->_numDays; $i++)
					{
						if ($i == 0)
						{
							$output['temp'][$i] = $this->_weatherData["2.0"]['CC']['TMP'] . $this->_tempSeparator . $this->_weatherData["2.0"]['HEAD']['UT'];
						}
						else
						{
							$output['temp'][$i] = $this->_weatherData["2.0"]['DAYF'][$i]['HI'] . $this->_tempSeparator . $this->_weatherData["2.0"]['HEAD']['UT'];
						}
					}
					break;
				case 'forecast':
					for ($i=0; $i<$this->_numDays; $i++)
					{
						if ($i == 0)
						{
							$output['forecast'][$i] = $this->_weatherData["2.0"]['CC']['T'];
						}
						else
						{
							$output['forecast'][$i] = $this->_weatherData["2.0"]['DAYF'][$i]['d']['T'];
						}
					}
					break;
				case 'sunrise':
					for ($i=0; $i<$this->_numDays; $i++)
					{
						if ($i == 0)
						{
							$output['sunrise'][$i] = $this->_weatherData["2.0"]["$cityCode"]['SUNR'];
						}
						else
						{
							$output['sunrise'][$i] = $this->_weatherData["2.0"]['DAYF'][$i]['SUNR'];
						}
					}
					break;
				case 'sunset':
					for ($i=0; $i<$this->_numDays; $i++)
					{
						if ($i == 0)
						{
							$output['sunset'][$i] = $this->_weatherData["2.0"]["$cityCode"]['SUNS'];
						}
						else
						{
							$output['sunset'][$i] = $this->_weatherData["2.0"]['DAYF'][$i]['SUNS'];
						}
					}
					break;
				case 'sunrise-sunset':
					for ($i=0; $i<$this->_numDays; $i++)
					{
						if ($i == 0)
						{
							$output['sunrise-sunset'][$i] = $this->_weatherData["2.0"]["$cityCode"]['SUNR'].'/'.$this->_weatherData["2.0"]["$cityCode"]['SUNS'];
						}
						else
						{
							$output['sunrise-sunset'][$i] = $this->_weatherData["2.0"]['DAYF'][$i]['SUNR'].'/'.$this->_weatherData["2.0"]['DAYF'][$i]['SUNS'];
						}
					}
					break;

				case 'vis':
					$output['vis'] = $this->_weatherData["2.0"]['CC']['VIS'] . $this->_weatherData["2.0"]['HEAD']['UD'];
					break;
				case 'wind':
					$output['wind'] = $this->_weatherData["2.0"]['CC']['WIND']['S'] . $this->_weatherData["2.0"]['HEAD']['US'];
					break;
				case 'hum':
					$output['hum'] = $this->_weatherData["2.0"]['CC']['HMID'];
					break;
				case 'dew':
					$output['dew'] = $this->_weatherData["2.0"]['CC']['DEWP'];
					break;
				case 'high':
					$output['high'] = $this->_weatherData["2.0"]['DAYF'][0]['HI'];
					break;
				case 'low':
					$output['low'] =  $this->_weatherData["2.0"]['DAYF'][0]['LOW'];
					break;
				case 'high-low':
					$output['high-low'] = $this->_weatherData["2.0"]['DAYF'][0]['HI'].'/'.$this->_weatherData["2.0"]['DAYF'][0]['LOW'];
					break;
			}
		}

		return $output;
	}
}
?>