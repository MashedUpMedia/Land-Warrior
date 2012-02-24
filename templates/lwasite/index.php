<?php // no direct access defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php echo '<?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >


<head>     
	<jdoc:include type="head" />
	<link rel="shortcut icon" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/favicon.ico" />	
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/menu.css" type="text/css" />
        <link rel="shortcut icon" href="http://www.airsoftedinburgh.co.uk/favicon.ico" />
    <!--[if lte IE 6]>
		<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ieonly.css" rel="stylesheet" type="text/css" />
	<![endif]-->
	<!--[if lte IE 7]>
		<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ie7only.css" rel="stylesheet" type="text/css" />
	<![endif]-->
	<!--[if lte IE 8]>
		<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ie8only.css" rel="stylesheet" type="text/css" />
	<![endif]-->

<script type="text/javascript">
 
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-19275926-4']);
  _gaq.push(['_setDomainName', 'none']);
  _gaq.push(['_setAllowLinker', true]);
  _gaq.push(['_trackPageview']);
 
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
 
</script>


</head>

<body>
    <div id="website_wrap">

	    <div id="header">
			<div id="header_left">
			</div>
			<div id="header_right">
				<div id="banner">
					<jdoc:include type="modules" name="banner" />
				</div>	
			</div>	
		</div>
	    
	    <div id="top">
        	<div id="top_menu">	   
                <jdoc:include type="modules" name="top" /> 
            </div>
			<div id="login_menu">
				<jdoc:include type="modules" name="top_login" />
			</div>
        </div>
    

        <div id="main">
		    
		    <div id="left">
		        <div id="menu_header">
		            <div class="left_module_header">
		                <h6>> Explore Data</h6>
		            </div>    
		        </div>
		        <div id="menu_background">
					<div id="grid">
						<div id="menu_main">
							<jdoc:include type="modules" name="left" style="xhtml" />     
						</div>    
					</div>
		        </div>
		        <div id="menu_bot"></div>
		    </div>
		    
		    <div id="content"> 	
			        <div id="content_header">
			            <div class="content_module_header">
		                    <h6>> Intel</h6>
		                </div>  
			        </div>
			        <div id="content_background">
			            <div id="content_content">
			                <jdoc:include type="component" style="xhtml" /> 
			            </div>    
			        </div>
			        <div id="content_bot"></div>
		    </div> 	
        
            <div id="right">
		        
                <div class="right_header">
                    <div class="right_module_header">
		                    <h6>> Dropzone Status</h6>
		            </div>  
                </div>
		        <div class="right_background">
		            <div id="weather">
		                <jdoc:include type="modules" name="user1" style="xhtml" />
		            </div>    
		        </div>
		        <div class="right_bot"></div>
		    	
		    
		        <div class="right_module">
		            <div class="right_header">
		                <div class="right_module_header">
		                    <h6>> Lock 'n' Load</h6>
		                </div>  
		            </div>
		            <div class="right_background_fixed">
		                <div id="lwashop">
		                    <a href="http://www.landwarriorairsoft.com"><img src="../images/lwa_plug.png" alt="Airsoft Weapons from Land Warrior Airsoft" /></a>
		                </div>    
		            </div>
		            <div class="right_bot_black"></div>
		       </div>
		    </div>
		  
		  <div id="bot_lm">  
		    <div id="bot_lm_header">
		         <div class="left_module_header">
		                <h6>> Video Feed</h6>
		         </div>
		    </div>     
		    <div id="bot_lm_bg">
		        <div id="lws_video">
                           <jdoc:include type="modules" name="user2" style="xhtml" />
		        </div>
		    </div>
		    <div id="bot_lm_bot"></div>    
		 </div>
		 
		 <div id="bot_rm">  
		    <div id="bot_rm_header">
		         <div class="bot_right_module_header">
		                <h6>> Mission Status Report</h6>
		         </div>
		    </div>     
		    <div id="bot_rm_bg">
		        <div id="lws_news">
		            <jdoc:include type="modules" name="user3" style="xhtml" />
		        </div>
		    </div>
		    <div id="bot_rm_bot"></div>    
		 </div>
		    

		  </div> 
	    
    
	    <div id="footer">
	        <div id="footer_menu">
				<p class="footer">Website and Content(&copy;) 2009 Land Warrior Airsoft Ltd registered in Scotland</p>
				<p class="footer">Phone: 0131 654 2452</p> 
				<jdoc:include type="modules" name="footer_menu" />
	            
	        </div>
		</div>	    
    </div>
</body>
</html>