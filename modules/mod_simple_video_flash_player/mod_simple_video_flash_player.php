<?php
/**
* Joomla
* Author : www.time2online.de
* Email: info@time2online.de
* Module: Simple Video Flash Player Module
* Version: 1.6
* 
* JW Player
* Author : Jeroen Wijering
* ULR: http://www.longtailvideo.com/players/jw-flv-player/
* Version: 4.4
*
* # Simple Video Flash Player Module #
* For download and demo and documentation use http://www.time2online.de.
*
* # JW Player License #
* Below you see a simple embedded example of the JW Player!
* Licensing: The player is licensed under a http://creativecommons.org/licenses/by-nc-sa/2.0/ Creative Commons License. It allows you to use, modify and redistribute the script, but only for noncommercial purposes.
*	
* For corporate use, http://www.longtailvideo.com/players/order Order commercial licenses please apply for a commercial license.
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** Embed parameters **/
// source
$player_variant = $params->get('player_variant', '');

if ($params->get('player_variant')){
    $player_variant = $params->get('player_variant', '');
    $player = $player_variant.'.swf';
}

// height 
$height =$params->get('height', '');

// width
$width = $params->get('width', '');

// count
$count = $params->get( 'count', '');

// version
$version = $params->get( 'version', '');


/** File properties **/
// file	
$video_url = $params->get('video_url', '');

// image
if ($params->get('preview_url')){
    $preview_url = $params->get('preview_url', '');
    $preview = '&image='.$preview_url;
}

// fallback image
if ($params->get('fallbackimage_url')){
    $fallbackimage_url = $params->get('fallbackimage_url', '');
    $fallback = '<img src="'.$fallbackimage_url.'" alt="" />';
	}
    else {
	$fallback = '<p><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</p>';
}

// link
if ($params->get('link_url')){
    $link_url = $params->get('link_url', '');
    $link = '&link='.$link_url.'&displayclick=link';
}


/** Layout **/
// controlbar
if ($params->get('controlbar')){
   $controlbar = "&controlbar=over";
}

// logo
if ($params->get('logo_url')){
    $logo_url = $params->get('logo_url', '');
    $logo = '&logo='.$logo_url;
}			
			
// playlist
$playlist_url = $params->get('playlist_url', '');

if ($params->get('playlist_url')){
	
	$playlist_position = $params->get('playlist_position', '');
	
		// playlistsize
		if ($params->get('playlist_size')){
			$playlist_size = $params->get('playlist_size', '');
			$playlist_size = '&playlistsize='.$playlist_size;
		}
		
	$video = $playlist_url.'&playlist='.$playlist_position.$playlist_size;
    } 
    else {
	$video = $video_url;
}
	
// skin
if ($params->get('skin_url')){
	$skin_url = $params->get('skin_url', '');
	$skin = '&skin='.$skin_url;
}

			
/** Behaviour  **/
// autostart
if ($params->get('autostart')){
   $autostart = "&autostart=true";
}

// repeat
if ($params->get('playlist_repeat')){
	$playlist_repeat = $params->get('playlist_repeat', '');
	$repeat = '&'.$playlist_repeat;
}

// shuffel
if ($params->get('playlist_shuffle')){
	$playlist_shuffle = $params->get('playlist_shuffle', '');
	$shuffle = '&'.$playlist_shuffle;
}


/** External Communication  **/
// plugins
if ($params->get('plugin_id')){
	$plugin_id = $params->get('plugin_id', '');
	$plugins = '&plugins='.$plugin_id;
}	
	
?>

<script type="text/javascript" src="<?php echo JURI::base();;?>modules/mod_simple_video_flash_player/swfobject.js"></script>
<div id="videoplayer<?php echo $count ?>"><?php echo $fallback ?></div>
<script type="text/javascript">
	window.addEvent('domready', function(){  
		var s<?php echo $count ?> = new SWFObject("<?php echo JURI::base();;?>modules/mod_simple_video_flash_player/<?php echo $player ?>","ply<?php echo $count ?>","<?php echo $width ?>","<?php echo $height ?>","<?php echo $version ?>","#FFFFFF");
		s<?php echo $count ?>.addParam("allowfullscreen","true");
		s<?php echo $count ?>.addParam("allowscriptaccess","always");
		s<?php echo $count ?>.addParam("wmode","opaque");
		s<?php echo $count ?>.addVariable("width");  
		s<?php echo $count ?>.addVariable("height");  
		s<?php echo $count ?>.addParam("flashvars","file=<?php echo $video ?><?php echo $preview ?><?php echo $logo ?><?php echo $link ?><?php echo $repeat ?><?php echo $shuffle ?><?php echo $autostart ?><?php echo $controlbar ?><?php echo $skin ?><?php echo $plugins ?>");
		s<?php echo $count ?>.write("videoplayer<?php echo $count ?>");
	});  
</script>
<div style="display:none;">Support: <a href="http://www.time2online.de">Simple Video Flash Player Module</a></div>