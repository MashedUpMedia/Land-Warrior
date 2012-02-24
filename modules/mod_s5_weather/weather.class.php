<?php
/* Author: http://www.bluepalmtrees.com/?id=4 */
	

function weather($zip, $metric=false) {
	$file = "weather.{$zip}.xml";
	if(!file_exists($file) || filemtime($file) < time() - 3600 || filesize($file) == 0) {
		$data = @file_get_contents("http://xml.weather.yahoo.com/forecastrss?p={$zip}&u=f");
		if($data != '') @file_put_contents($file, $data);
		else return false;
	}
	else $data = @file_get_contents($file);
	$ret = array();

	if($metric) $units = array('C', 'km', 'mb', 'kph');
	else $units = array('F', 'mi', 'in', 'mph');

	$pos = strpos($data, 'yweather:location');
	$attr = explode('"', substr($data, $pos, strpos($data, '/>', $pos)-$pos));
	$ret[0]['location'] = $attr[1].', '.$attr[3].', '.$attr[5];
	$ret[0]['when'] = 'Now';

	$pos = strpos($data, 'yweather:condition');
	$attr = explode('"', substr($data, $pos, strpos($data, '/>', $pos)-$pos));
	if($metric) $attr[5] = number_format(($attr[5] - 32) / 1.8, 0);
	$ret[0]['text'] = $attr[1];
	$ret[0]['temp'] = $attr[5].'&deg;'.$units[0];
	$ret[0]['image'] = $attr[3];

	$dir = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N');
	$pos = strpos($data, 'yweather:wind');
	$attr = explode('"', substr($data, $pos, strpos($data, '/>', $pos)-$pos));
	if($metric) {
		$attr[1] = number_format(($attr[1] - 32) / 1.8, 0);
		$attr[5] = number_format($attr[5] * 1.609344, 0);
	}
	$ret[0]['windchill'] = $attr[1].'&deg;'.$units[0];
	$ret[0]['wind'] = $attr[5].' '.$units[3].' '.$dir[round($attr[3]/45)];

	$dir = array('steady', 'rising', 'falling');
	$pos = strpos($data, 'yweather:atmosphere');
	$attr = explode('"', substr($data, $pos, strpos($data, '/>', $pos)-$pos));
	if($metric) {
		$attr[3] = number_format($attr[3] * 0.01609344, 0);
		$attr[5] = number_format($attr[5] * 33.8637526, 0);
	}
	else $attr[3] = number_format($attr[3] / 100, 0);
	$ret[0]['humidity'] = $attr[1].'%';
	$ret[0]['visibility'] = $attr[3].' '.$units[1];
	$ret[0]['pressure'] = $attr[5].' '.$units[2].' '.$dir[$attr[7]];

	$pos = strpos($data, 'yweather:astronomy');
	$attr = explode('"', substr($data, $pos, strpos($data, '/>', $pos)-$pos));
	$ret[0]['sunrise'] = $attr[1];
	$ret[0]['sunset'] = $attr[3];

	for($pos = 0; ($pos = strpos($data, 'yweather:forecast', $pos)) !== false; $pos+=2) {
		$attr = explode('"', substr($data, $pos, strpos($data, '/>', $pos)-$pos));
		if($metric) {
			$attr[5] = number_format(($attr[5] - 32) / 1.8, 0);
			$attr[7] = number_format(($attr[7] - 32) / 1.8, 0);
		}
		$day = array();
		$day['when'] = $attr[1];
		$day['low'] = $attr[5].'&deg;'.$units[0];
		$day['high'] = $attr[7].'&deg;'.$units[0];
		$day['text'] = $attr[9];
		$day['image'] = $attr[11];
		$ret[] = $day;
	}

	foreach($ret as $key=>$info) {
		$code = $info['image'];
		if($code < 0 || $code > 47) $code = 3200;
		elseif(in_array($code, array(37, 44, 47))) {
			$t = date('G');
			$code .= ($t <= 5 || $t >= 20 ? 'n' : 'd');
		}
		$ret[$key]['image'] = $code.'.png';
	}

	return $ret;
}
?>
