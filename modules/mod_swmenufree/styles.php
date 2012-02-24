<?php
/**
* swmenufree v4.5
* http://swonline.biz
* Copyright 2006 Sean White
**/

defined( '_JEXEC' ) or die( 'Restricted access' );

function gosuMenuStyle($swmenufree){
	global $mainframe;
	$absolute_path=JPATH_ROOT;
  $live_site = $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
  if(substr($live_site,(strlen($live_site)-1),1)=="/"){$live_site=substr($live_site,0,(strlen($live_site)-1));}
	//$str="<style type=\"text/css\">\n";
	//$str.="<!--\n";
	$str=".ddmx{\n";
	$str.="border:".$swmenufree['main_border']." !important ; \n";
	$str.="}\n";
	
	$str.=".ddmx a.item1,\n";
	$str.=".ddmx a.item1:hover,\n";
	$str.=".ddmx a.item1-active,\n";
	$str.=".ddmx a.item1-active:hover {\n";
	$str.=" padding: ".$swmenufree['main_padding']." !important ; \n";
	$str.=" top: ".$swmenufree['main_top']."px !important ; \n";
	$str.=" left: ".$swmenufree['main_left']."px; \n";
	$str.=" font-size: ".$swmenufree['main_font_size']."px !important ; \n";
	$str.=" font-family: ".$swmenufree['font_family']." !important ; \n";
	$str.=" text-align: ".$swmenufree['main_align']." !important ; \n";
	$str.=" font-weight: ".$swmenufree['font_weight']." !important ; \n";
	$str.=$swmenufree['main_font_color']?" color: ".$swmenufree['main_font_color']." !important ; \n":"";
	$str.=" text-decoration: none !important ; \n";
	$str.=" display: block; \n";
	$str.=" white-space: nowrap; \n";
	$str.=" position: relative; \n";
	$str.=is_file($absolute_path."/".$swmenufree['main_back_image']) ? " background-image: URL(\"".$live_site."/".$swmenufree['main_back_image']."\") ;\n":"background-image:none;";
	//$str.=$swmenufree['main_back']?" background-color: ".$swmenufree['main_back']." !important ; \n":"";
	if ($swmenufree['main_width']!=0){$str.= " width:".$swmenufree['main_width']."px; \n";}
	if ($swmenufree['main_height']!=0){$str.= " height:".$swmenufree['main_height']."px; \n";}
	$str.="}\n";

	$str.=".ddmx td.item11 {\n";
	$str.=$swmenufree['main_back']?" background-color: ".$swmenufree['main_back']." !important ; \n":"";
	$str.=" padding:0 !important ; \n";
	$str.=" border-top: ".$swmenufree['main_border_over']." !important ; \n";
	$str.=" border-left: ". $swmenufree['main_border_over']." !important ; \n";
	if($swmenufree['orientation']=="vertical"){
		$str.= " border-right: ".$swmenufree['main_border_over']." !important ; \n";
		$str.= " border-bottom: 0 !important ; \n";
	}else{
		$str.= " border-bottom: ".$swmenufree['main_border_over'].";\n" ;
		$str.= " border-right: 0 !important ; \n";
	}
	$str.=" white-space: nowrap !important ; \n";
	if ($swmenufree['main_width']!=0){$str.= " width:".$swmenufree['main_width']."px; \n";}
	if ($swmenufree['main_height']!=0){$str.= " height:".$swmenufree['main_height']."px; \n";}
	$str.="}\n";

	$str.= ".ddmx td.item11-last {\n";
	$str.=$swmenufree['main_back']?" background-color: ".$swmenufree['main_back']." !important ; \n":"";
	$str.= " padding:0 !important ; \n";
	$str.= " border: ". $swmenufree['main_border_over']." !important ; \n";
	$str.= " white-space: nowrap; \n";
	if ($swmenufree['main_width']!=0){$str.= " width:".$swmenufree['main_width']."px; \n";}
	if ($swmenufree['main_height']!=0){$str.= " height:".$swmenufree['main_height']."px; \n";}
	$str.="}\n";

	$str.= ".ddmx td.item11-acton {\n";
	$str.= " padding:0 !important ; \n";
	$str.= " border-top: ".$swmenufree['main_border_over']." !important ; \n";
	$str.= " border-left: ".$swmenufree['main_border_over']." !important ; \n";
	$str.= " white-space: nowrap; \n";
	if($swmenufree['orientation']=="vertical"){
		$str.= " border-right: ".$swmenufree['main_border_over']." !important ; \n";
	}else{
		$str.= " border-bottom: ".$swmenufree['main_border_over'].";\n" ;
	}
	$str.="}\n";

	$str.= ".ddmx td.item11-acton-last {\n";
	$str.= " border: ".$swmenufree['main_border_over']." !important ; \n";
	$str.="}\n";

	$str.= ".ddmx .item11-acton-last a.item1,\n";
	$str.= ".ddmx .item11-acton a.item1,\n";
	$str.= ".ddmx .item11-acton-last a:hover,\n";
	$str.= ".ddmx .item11-acton a:hover,\n";
	$str.= ".ddmx .item11 a:hover,\n";
	$str.= ".ddmx .item11-last a:hover,\n";
	$str.= ".ddmx a.item1-active,\n";
	$str.= ".ddmx a.item1-active:hover {\n";
	$str.= is_file($absolute_path."/".$swmenufree['main_back_image_over']) ? "background-image: URL(\"".$live_site."/".$swmenufree['main_back_image_over']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['main_font_color_over']?" color: ".$swmenufree['main_font_color_over']." !important ; \n":"";
	$str.=$swmenufree['main_over']?" background-color: ".$swmenufree['main_over']." !important ; \n":"";
	$str.="}\n";

	$str.= ".ddmx a.item2,\n";
	$str.= ".ddmx a.item2:hover,\n";
	$str.= ".ddmx a.item2-active,\n";
	$str.= ".ddmx a.item2-active:hover {\n";
	$str.= " padding: ".$swmenufree['sub_padding']." !important ; \n";
	$str.= " font-size: ".$swmenufree['sub_font_size']."px !important ; \n";
	$str.= " font-family: ".$swmenufree['sub_font_family']." !important ; \n";
	$str.= " text-align: ".$swmenufree['sub_align']." !important ; \n";
	$str.= " font-weight: ".$swmenufree['font_weight_over']." !important ; \n";
	$str.= " text-decoration: none !important ; \n";
	$str.= " display: block; \n";
	$str.= " white-space: nowrap; \n";
	$str.= " position: relative; \n";
	$str.= " z-index:500; \n";
	if ($swmenufree['sub_width']!=0){$str.= " width:".$swmenufree['sub_width']."px; \n";}
	if ($swmenufree['sub_height']!=0){$str.= " height:".$swmenufree['sub_height']."px; \n";}
	$str.= " opacity:".($swmenufree['specialA']/100)."; \n";
	$str.="}\n";

	$str.= ".ddmx a.item2 {\n";
	$str.= is_file($absolute_path."/".$swmenufree['sub_back_image']) ? " background-image: URL(\"".$live_site."/".$swmenufree['sub_back_image']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['sub_back']?" background-color: ".$swmenufree['sub_back']." !important ; \n":"";
	$str.=$swmenufree['sub_font_color']?" color: ".$swmenufree['sub_font_color']." !important ; \n":"";
	$str.= " border-top: ".$swmenufree['sub_border_over']." !important ; \n";
	$str.= " border-left: ".$swmenufree['sub_border_over']." !important ; \n";
	$str.= " border-right: ".$swmenufree['sub_border_over']." !important ; \n";
	$str.="}\n";

	$str.= ".ddmx a.item2-last {\n";
	$str.= is_file($absolute_path."/".$swmenufree['sub_back_image']) ? " background-image: URL(\"".$live_site."/".$swmenufree['sub_back_image']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['sub_back']?" background-color: ".$swmenufree['sub_back']." !important ; \n":"";
	$str.=$swmenufree['sub_font_color']?" color: ".$swmenufree['sub_font_color']." !important ; \n":"";
	$str.= " border-bottom: ".$swmenufree['sub_border_over']." !important ; \n";
	$str.= " z-index:500; \n";
	$str.="}\n";

	$str.= ".ddmx a.item2:hover,\n";
	$str.= ".ddmx a.item2-active,\n";
	$str.= ".ddmx a.item2-active:hover {\n";
	$str.= is_file($absolute_path."/".$swmenufree['sub_back_image_over']) ? " background-image: URL(\"".$live_site."/".$swmenufree['sub_back_image_over']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['sub_over']?" background-color: ".$swmenufree['sub_over']." !important ; \n":"";
	$str.=$swmenufree['sub_font_color_over']?" color: ".$swmenufree['sub_font_color_over']." !important ; \n":"";
	$str.= " border-top: ".$swmenufree['sub_border_over']." !important ; \n";
	$str.= " border-left: ".$swmenufree['sub_border_over']." !important ; \n";
	$str.= " border-right: ".$swmenufree['sub_border_over']." !important ; \n";
	$str.= "}\n";

	$str.= ".ddmx .section {\n";
	$str.= " border: ".$swmenufree['sub_border']." !important ; \n";
	$str.= " position: absolute; \n";
	$str.= " visibility: hidden; \n";
	$str.= " display: block; \n";
	$str.= " z-index: -1; \n";
	$str.="}\n";

	$str.= ".ddmxframe {\n";
	$str.= " border: ".$swmenufree['sub_border']." !important ; \n";
	$str.="}\n";

	$str.= "* html .ddmx td { position: relative; } /* ie 5.0 fix */\n";
	//$str.="-->\n";
	//$str.="</style>\n";
	
	
	return $str;
}



function transMenuStyle($swmenufree,$show_shadow){
	global $mainframe;
	$absolute_path=JPATH_ROOT;
  $live_site = $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
  if(substr($live_site,(strlen($live_site)-1),1)=="/"){$live_site=substr($live_site,0,(strlen($live_site)-1));}
	//<style type="text/css">
	//<!--
	$str= ".transMenu {\n";
	$str.= " position:absolute ; \n";
	$str.= " overflow:hidden; \n";
	$str.= " left:-1000px; \n";
	$str.= " top:-1000px; \n";
	$str.= "}\n";

	$str.= ".transMenu .content {\n";
	$str.= " position:absolute  ; \n";
	$str.= "}\n";

	$str.= ".transMenu .items {\n";
	$str.= $swmenufree['sub_width']?" width: ". $swmenufree['sub_width']."px;":"";
	$str.= " border: ". $swmenufree['sub_border']." ; \n";
	$str.= " position:relative ; \n";
	$str.= " left:0px; top:0px; \n";
	$str.= " z-index:2; \n";
	$str.= "}\n";

	//$str.= ".transMenu.top .items {\n";
	//$str.= "}\n";

	$str.= ".transMenu  td\n";
	$str.= "{\n";
	$str.= " padding: ". $swmenufree['sub_padding']." !important;  \n";
	$str.= " font-size: ". $swmenufree['sub_font_size']."px !important ; \n";
	$str.= " font-family: ". $swmenufree['sub_font_family']." !important ; \n";
	$str.= " text-align: ". $swmenufree['sub_align']." !important ; \n";
	$str.= " font-weight: ". $swmenufree['font_weight_over']." !important ; \n";
	$str.=$swmenufree['sub_font_color']?" color: ".$swmenufree['sub_font_color']." !important ; \n":"";
	$str.= "} \n";
	
	$str.= "#subwrap \n";
	$str.= "{ \n";
	$str.= " text-align: left ; \n";
	$str.= "}\n";

	$str.= ".transMenu  .item.hover td\n";
	$str.= "{ \n";
	$str.=$swmenufree['sub_font_color_over']?" color: ".$swmenufree['sub_font_color_over']." !important ; \n":"";
	$str.= "}\n";

	$str.= ".transMenu .item { \n";
	$str.= $swmenufree['sub_height']?" height: ". $swmenufree['sub_height']."px;":"";
	$str.= " text-decoration: none ; \n";
	$str.= " cursor:pointer; \n";
	$str.= " cursor:hand; \n";
	$str.= "}\n";

	$str.= ".transMenu .background {\n";
	$str.= is_file($absolute_path."/".$swmenufree['sub_back_image']) ? " background-image: URL(\"".$live_site."/".$swmenufree['sub_back_image']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['sub_back']?" background-color: ".$swmenufree['sub_back']." !important ; \n":"";
	$str.= " position:absolute ; \n";
	$str.= " left:0px; top:0px; \n";
	$str.= " z-index:1; \n";
	$str.= " opacity:". ($swmenufree['specialA']/100)."; \n";
	$str.= " filter:alpha(opacity=". ($swmenufree['specialA']).") \n";
	$str.= "}\n";

	$str.= ".transMenu .shadowRight { \n";
	$str.= " position:absolute ; \n";
	$str.= " z-index:3; \n";
	if($show_shadow){
	$str.= " top:3px; width:2px; \n";
	}else{
		$str.= " top:-3000px; width:2px; \n";
	}
	$str.= " opacity:". ($swmenufree['specialA']/100)."; \n";
	$str.= " filter:alpha(opacity=". ($swmenufree['specialA']).")\n";
	$str.= "}\n";

	$str.= ".transMenu .shadowBottom { \n";
	$str.= " position:absolute ; \n";
	$str.= " z-index:1; \n";
	if($show_shadow){
	$str.= " left:3px; height:2px; \n";
	}else{
	$str.= " left:-3000px; height:2px; \n";	
	}
	$str.= " opacity:". ($swmenufree['specialA']/100)."; \n";
	$str.= " filter:alpha(opacity=". ($swmenufree['specialA']).")\n";
	$str.= "}\n";

	$str.= ".transMenu .item.hover {\n";
	$str.= is_file($absolute_path."/".$swmenufree['sub_back_image_over']) ? " background-image: URL(\"".$live_site."/".$swmenufree['sub_back_image_over']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['sub_over']?" background-color: ".$swmenufree['sub_over']." !important ; \n":"";
	$str.= "}\n";

	$str.= ".transMenu .item img { \n";
	$str.= " margin-left:10px !important ; \n";
	$str.= "}\n";

	$str.= "table.menu {\n";
	$str.= " top: ". $swmenufree['main_top']."px; \n";
	$str.= " left: ". $swmenufree['main_left']."px; \n";
	$str.= " position:relative ; \n";
	$str.= " margin:0px !important ; \n";
	$str.= " border: ". $swmenufree['main_border']." ; \n";
	$str.= " z-index: 1; \n";
	$str.= "}\n";

	$str.= "table.menu a{\n";
	$str.= " margin:0px !important ; \n";
	$str.= " padding: ". $swmenufree['main_padding']." !important ; \n";
	$str.= " display:block !important; \n";
	$str.= " position:relative !important ; \n";
	$str.= "}\n";

	$str.= "div.menu a,\n";
	$str.= "div.menu a:visited,\n";
	$str.= "div.menu a:link {\n";
	if ($swmenufree['main_width']!=0){$str.= " width:".$swmenufree['main_width']."px; \n";}
	if ($swmenufree['main_height']!=0){$str.= " height:".$swmenufree['main_height']."px; \n";}
	$str.= " font-size: ". $swmenufree['main_font_size']."px !important ; \n";
	$str.= " font-family: ". $swmenufree['font_family']." !important ; \n";
	$str.= " text-align: ". $swmenufree['main_align']." !important ; \n";
	$str.= " font-weight: ". $swmenufree['font_weight']." !important ; \n";
	$str.=$swmenufree['main_font_color']?" color: ".$swmenufree['main_font_color']." !important ; \n":"";
	$str.= " text-decoration: none !important ; \n";
	$str.= " margin-bottom:0px !important ; \n";
	$str.= " display:block !important; \n";
	$str.= " white-space:nowrap ; \n";
	$str.= "}\n";

	$str.= "div.menu td {\n";
	if (substr($swmenufree['orientation'],0,8)=="vertical"){
		$str.= " border-right: ".$swmenufree['main_border_over']." ; \n";
	}else{
		$str.= " border-bottom: ".$swmenufree['main_border_over']." ; \n";
	}
	$str.= " border-top: ". $swmenufree['main_border_over']." ; \n";
	$str.= " border-left: ". $swmenufree['main_border_over']." ; \n";
	$str.= is_file($absolute_path."/".$swmenufree['main_back_image']) ? " background-image: URL(\"".$live_site."/".$swmenufree['main_back_image']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['main_back']?" background-color: ".$swmenufree['main_back']." !important ; \n":"";
	$str.= "} \n";

	$str.= "div.menu td.last {\n";
	if (substr($swmenufree['orientation'],0,8)=="vertical"){
		$str.= " border-bottom: ".$swmenufree['main_border_over']." ; \n";
	}else{
		$str.= " border-right: ".$swmenufree['main_border_over']." ; \n";
	}
	$str.= "} \n";
	
	$str.= "#trans-active a{\n";
	$str.=$swmenufree['main_font_color_over']?" color: ".$swmenufree['main_font_color_over']." !important ; \n":"";
	$str.= is_file($absolute_path."/".$swmenufree['main_back_image_over']) ? " background-image: URL(\"".$live_site."/".$swmenufree['main_back_image_over']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['main_over']?" background-color: ".$swmenufree['main_over']." !important ; \n":"";
	$str.= "} \n";

	

	$str.= "#menu a.hover   { \n";
	$str.= is_file($absolute_path."/".$swmenufree['main_back_image_over']) ? " background-image: URL(\"".$live_site."/".$swmenufree['main_back_image_over']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['main_font_color_over']?" color: ".$swmenufree['main_font_color_over']." !important ; \n":"";
	$str.=$swmenufree['main_over']?" background-color: ".$swmenufree['main_over']." !important ; \n":"";
	$str.= "}\n";

	$str.= "#menu span {\n";
	$str.= " display:none; \n";
	$str.= "}\n";

	return $str;
}



function tigraMenuStyle($swmenufree){
	global $mainframe;
	$absolute_path=JPATH_ROOT;
  $live_site = $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
  if(substr($live_site,(strlen($live_site)-1),1)=="/"){$live_site=substr($live_site,0,(strlen($live_site)-1));}

	//<style type="text/css">
	//<!--
	$str="";
	$str.= ".m0l0iout { \n";
	$str.= " font-family: ".$swmenufree['font_family']." !important ; \n";
	$str.= " font-size: ".$swmenufree['main_font_size'].'px'." !important ; \n";
	$str.= " text-decoration: none !important ; \n";
	$str.= " padding: ".$swmenufree['main_padding']." !important ; \n";
	$str.=$swmenufree['main_font_color']?" color: ".$swmenufree['main_font_color']." !important ; \n":"";
	$str.= " font-weight: ".$swmenufree['font_weight']." !important ; \n";
	$str.= " text-align: ".$swmenufree['main_align']." !important ; \n";
	$str.= "}\n";

	$str.= ".m0l0iover {\n";
	$str.= " font-family: ".$swmenufree['font_family']." !important ; \n";
	$str.= " font-size: ".$swmenufree['main_font_size'].'px'." !important ; \n";
	$str.= " text-decoration: none !important ; \n";
	$str.= " padding: ".$swmenufree['main_padding']." !important ; \n";
	$str.=$swmenufree['main_font_color_over']?" color: ".$swmenufree['main_font_color_over']." !important ; \n":"";
	$str.= " font-weight: ".$swmenufree['font_weight_over']." !important ; \n";
	$str.= " text-align: ".$swmenufree['main_align']." !important ; \n";
	$str.= "}\n";

	$str.= ".m0l0oout { \n";
	$str.= " text-decoration : none !important ; \n";
	$str.= " border: ".$swmenufree['main_border']." !important ; \n";
	$str.=is_file($absolute_path."/".$swmenufree['main_back_image']) ? " background-image: URL(\"".$live_site."/".$swmenufree['main_back_image']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['main_back']?" background-color: ".$swmenufree['main_back']." !important ; \n":"";
	$str.= "}\n";

	$str.= ".m0l0oover { \n";
	$str.= " text-decoration : none !important ; \n";
	$str.= " border: ".$swmenufree['main_border_over']." !important ; \n";
	$str.=is_file($absolute_path."/".$swmenufree['main_back_image_over']) ? " background-image: URL(\"".$live_site."/".$swmenufree['main_back_image_over']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['main_over']?" background-color: ".$swmenufree['main_over']." !important ; \n":"";
	$str.= "}\n";
	

	$str.= ".m0l1iout {\n";
	$str.= " font-family: ".$swmenufree['sub_font_family']." !important ; \n";
	$str.= " font-size: ".$swmenufree['sub_font_size'].'px'." !important ; \n";
	$str.= " text-decoration: none !important ; \n";
	$str.= " padding: ".$swmenufree['sub_padding']." !important ; \n";
	$str.=$swmenufree['sub_font_color']?" color: ".$swmenufree['sub_font_color']." !important ; \n":"";
	$str.= " font-weight: ".$swmenufree['font_weight']." !important ; \n";
	$str.= " text-align: ".$swmenufree['sub_align']." !important ; \n";
	$str.= "}\n";

	$str.= ".m0l1iover { \n";
	$str.= " font-family: ".$swmenufree['sub_font_family']." !important ; \n";
	$str.= " font-size: ".$swmenufree['sub_font_size'].'px'." !important ; \n";
	$str.= " text-decoration: none !important ; \n";
	$str.= " padding: ".$swmenufree['sub_padding']." !important ; \n";
	$str.=$swmenufree['sub_font_color_over']?" color: ".$swmenufree['sub_font_color_over']." !important ; \n":"";
	$str.= " font-weight: ".$swmenufree['font_weight_over']." !important ; \n";
	$str.= " text-align: ".$swmenufree['sub_align']." !important ; \n";
	$str.= "}  \n";

	$str.= ".m0l1oout { \n";
	$str.= " text-decoration : none !important ; \n";
	$str.= " border: ".$swmenufree['sub_border']." !important ; \n";
	$str.=is_file($absolute_path."/".$swmenufree['sub_back_image']) ? " background-image: URL(\"".$live_site."/".$swmenufree['sub_back_image']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['sub_back']?" background-color: ".$swmenufree['sub_back']." !important ; \n":"";
	$str.= " opacity:". ($swmenufree['specialA']/100)."; \n";
	$str.= " filter:alpha(opacity=". ($swmenufree['specialA']).") \n";
	$str.= "} \n";

	$str.= ".m0l1oover {\n";
	$str.= " text-decoration : none !important ; \n";
	$str.= " border: ".$swmenufree['sub_border_over']." !important ; \n";
	$str.=is_file($absolute_path."/".$swmenufree['sub_back_image_over']) ? " background-image: URL(\"".$live_site."/".$swmenufree['sub_back_image_over']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['sub_over']?" background-color: ".$swmenufree['sub_over']." !important ; \n":"";
	$str.= "}\n";

	
	$str.= "#swactive_menu a,\n";
	$str.= "#swactive_menu div{ \n";
	$str.=$swmenufree['main_font_color_over']?" color: ".$swmenufree['main_font_color_over']." !important ; \n":"";
	$str.= " font-weight: ".$swmenufree['font_weight_over']." !important ; \n";
	$str.= " text-decoration : none !important ; \n";
	$str.=is_file($absolute_path."/".$swmenufree['main_back_image_over']) ? "background-image: URL(\"".$live_site."/".$swmenufree['main_back_image_over']."\") ;\n":"background-image:none;";
	$str.=$swmenufree['main_over']?" background-color: ".$swmenufree['main_over']." !important ; \n":"";
	$str.= "}\n";
	
	
	//-->
	//</style>
	return $str;
}



?>
