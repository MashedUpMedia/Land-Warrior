<?php
/**
@version 1.0: mod_s5_weather
Author: Shape 5 - Professional Template Community
Free extension available for download at www.shape5.com
*/


// no direct access
defined('_JEXEC') or die('Restricted access');
global $mosConfig_offset, $mosConfig_live_site, $mainframe;
$text  = $params->get( 'textif');
$zipcode  = $params->get( 'zipcode');
$s5_tempscale  = $params->get( 's5_tempscale');


$LiveSite = JURI::base();
?>
	
<?php
$br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser.
if(ereg("msie 6", $br)) {
$iss_ie6 = "yes";
} 
else {
$iss_ie6 = "no";
}
?>

<?php if ($s5_tempscale == "f") { }?>
		

<script type="text/javascript">//<![CDATA[
    document.write('<link href="<?php echo $LiveSite?>/modules/mod_s5_weather/s5_weather/style.css" rel="stylesheet" type="text/css" media="screen" />');
//]]></script>	

<?php
	
	error_reporting(0); 
	
	include('weather.class.php');

	if ($s5_tempscale == "f") {
	
	$ret = weather($zipcode,  ($_GET['u'] == 'f'));
	}

	if ($s5_tempscale == "c") {
	
	$ret = weather($zipcode,  ($_GET['u'] = 'c'));
	}
		
	echo "<strong>".$ret[0]['location']."</strong><br/><br/>";
	foreach($ret as $day) {
		
		if ($iss_ie6 == "yes") {
		echo "<div><div class=\"s5weather_middle\"><div class=\"s5weather_tl\"></div><div class=\"s5weather_tr\"></div></div><div style=\"clear:both;margin-top:-6px;\"></div>";
		} else {
		echo "<div><div class=\"s5weather_middle\"><div class=\"s5weather_tl\"></div><div class=\"s5weather_tr\"></div></div><div style=\"clear:both;\"></div>";		
		}		
		echo "<div class=\"s5weather_brep\"><div class=\"s5weather_rl\"><div class=\"s5weather_rr\"><div class=\"s5weather_padding\"><div style=\"float:left;width:30px;line-height:32px;\">".$day['when'].'</div><div style="float:left;width:48px;"><img src="'. $LiveSite. '/modules/mod_s5_weather/s5_weather/'.$day['image'].'" alt=""/></div><div style="float:left;font-size:11px;">'.$day['text'].'<br/>';
		if(isset($day['temp'])) {
			echo $day['temp'].', Windchill: '.$day['windchill'].'<br/>';
			echo 'Wind: '.$day['wind'].'<br/>';
			echo 'Humidity: '.$day['humidity'].'<br/>';
			echo 'Visibility: '.$day['visibility'].'<br/>';
			echo 'pressure: '.$day['pressure'].'<br/>';
			echo 'Sunrise: '.$day['sunrise'].'<br/>';
			echo 'Sunset: '.$day['sunset'];
		}
		else echo 'Hi: '.$day['high'].', Low: '.$day['low'];
		echo "</div><div style=\"clear: both;\"></div></div></div></div></div>";
		echo "</div><div style=\"clear:both;\"><div class=\"s5weather_bmiddle\"><div class=\"s5weather_bl\"></div><div class=\"s5weather_br\"></div></div></div>";
	}
?>


