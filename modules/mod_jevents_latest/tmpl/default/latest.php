<?php
/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the module  frontend
 *
 * @static
 */
class DefaultModLatestView
{
	var $_modid = null;
	var $modparams				= null;

	// Note that we encapsulate all this in a class to create
	// an isolated name space from everythng else (I hope).

	var $aid				= null;
	var $lang				= null;
	var $catid				= null;
	var $inccss				= null;

	var $maxEvents			= null;
	var $dispMode			= null;
	var $rangeDays			= null;
	var $norepeat			= null;
	var $displayLinks		= null;
	var $displayYear		= null;
	var $disableDateStyle	= null;
	var $disableTitleStyle	= null;
	var $linkCloaking		= null;
	var $customFormatStr	= null;
	var $_defaultfFormatStr12= '${eventDate}[!a: - ${endDate(%I:%M%p)}]<br />${title}';
	var $_defaultfFormatStr24= '${eventDate}[!a: - ${endDate(%H:%M)}]<br />${title}';
	var $defaultfFormatStr	= null;
	var $linkToCal			= null;	// 0=no, 1=top, 2=bottom
	var $sortReverse		= null;

	var $displayRSS			= null;
	var $rsslink 			= null;

	var $com_starday		= null;
	var $com_calUseStdTime	= null;


	var $datamodel 			= null;
	var $catout			= null;


	function DefaultModLatestView($params, $modid){

		$this->_modid = $modid;
		$this->modparams	= & $params;

		global  $mainframe;

		$jevents_config		= & JEVConfig::getInstance();

		$this->datamodel	=& new JEventsDataModel();
		// find appropriate Itemid and setup catids for datamodel
		$this->myItemid = $this->datamodel->setupModuleCatids($this->modparams);
		$this->catout	= $this->datamodel->getCatidsOutLink(true);

		$user =& JFactory::getUser();
		$this->aid     = $user->aid;
		// Can't use getCfg since this cannot be changed by Joomfish etc.
		$tmplang		=& JFactory::getLanguage();
		$this->langtag	= $tmplang->getTag();

		// get params exclusive to module
		$this->inccss	= $params->get('modlatest_inccss', 0);

		// get params exclusive to component
		$this->com_starday			= intval($jevents_config->get('com_starday',0));
		$this->com_calUseStdTime	= intval($jevents_config->get('com_calUseStdTime',1));
		if ($this->com_calUseStdTime) {
			$this->defaultfFormatStr = $this->_defaultfFormatStr12;
		} else {
			$this->defaultfFormatStr = $this->_defaultfFormatStr24;
		}

		// get params depending on switch
		if (intval($params->get('modlatest_useLocalParam',  0)) == 1) {
			$myparam = &$params;
		} else {
			$myparam = &$jevents_config;
		}
		$this->maxEvents			= intval($myparam->get('modlatest_MaxEvents', 15));
		$this->dispMode				= intval($myparam->get('modlatest_Mode',   0));
		$this->rangeDays			= intval($myparam->get('modlatest_Days', 30));
		$this->norepeat				= intval($myparam->get('modlatest_NoRepeat',   0));
		$this->displayLinks			= intval($myparam->get('modlatest_DispLinks', 1));
		$this->displayYear			= intval($myparam->get('modlatest_DispYear',  0));
		$this->disableDateStyle		= intval($myparam->get('modlatest_DisDateStyle',  0));
		$this->disableTitleStyle	= intval($myparam->get('modlatest_DisTitleStyle', 0));
		$this->linkCloaking			= intval($myparam->get('modlatest_LinkCloaking', 0));
		$this->linkToCal			= intval($myparam->get('modlatest_LinkToCal', 0));
		$this->customFormatStr		= $myparam->get('modlatest_CustFmtStr', '');
		$this->displayRSS			= intval($myparam->get('modlatest_RSS', 0));
		$this->sortReverse			= intval($myparam->get('modlatest_SortReverse', 0));

		if($this->dispMode > 4) $this->dispMode = 0;

		// $maxEvents hardcoded to 105 for now to avoid bad mistakes in params
		if($this->maxEvents > 150) $this->maxEvents = 150;

		if ($this->displayRSS){
			if ($modid>0){
				// do not use JRoute since this creates .rss link which normal sef can't deal with
				$this->rsslink=  JURI::root().'index.php?option='.JEV_COM_COMPONENT.'&task=modlatest.rss&format=feed&type=rss&modid='.$modid;
			}
			else {
				$this->displayRSS=false;
			}

		}
	}

	function getTheme(){
		$theme = JEV_CommonFunctions::getJEventsViewName();
		return $theme;

	}


	/**
		 * Cloaks html link whith javascript
		 *
		 * @param string The cloaking URL
		 * @param string The link text
		 * @return string HTML
		 */
	function _htmlLinkCloaking($url='', $text='') {

		//$link = JRoute::_($url);
		// sef already should be already called below
		$link = $url;

		if ($this->linkCloaking) {
			//return mosHTML::Link("", $text, array('onclick'=>'"window.location.href=\''.josURL($url).'\';return false;"'));
			return '<a href="#" onclick="window.location.href=\'' . $link . '\'; return false;">' . $text . '</a>';
		} else {
			//return mosHTML::Link(josURL($url), "$text");
			return '<a href="' . $link .'">' . $text . '</a>';
		}
	}

	// this could go to a data model class
	// for the time being put it here so the different views can inherit from this 'base' class
	function getLatestEventsData($limit=""){

		// RSS situation overrides maxecents
		$limit = intval($limit);
		if ($limit>0){
			$this->maxEvents = $limit;
		}

		global $mainframe;
		$db	=& JFactory::getDBO();

		$t_datenow = JEVHelper::getNow();
		$this->now = $t_datenow->toUnix(true);
		$this->now_Y_m_d	= date('Y-m-d', $this->now);
		$this->now_d		= date('d', $this->now);
		$this->now_m		= date('m', $this->now);
		$this->now_Y		= date('Y', $this->now);
		$this->now_w		= date('w', $this->now);

		// derive the event date range we want based on current date and
		// form the db query.

		$todayBegin = $this->now_Y_m_d." 00:00:00";
		$yesterdayEnd = date('Y-m-d', mktime(0,0,0,$this->now_m,$this->now_d - 1, $this->now_Y))." 23:59:59";

		switch ($this->dispMode){
			case 0:
			case 1:

				// week start (ie. Sun or Mon) is according to what has been selected in the events
				// component configuration thru the events admin interface.

				$numDay=($this->now_w - $this->com_starday + 7)%7;
				// begin of this week
				$beginDate = date('Y-m-d', mktime(0,0,0,$this->now_m,$this->now_d - $numDay, $this->now_Y))." 00:00:00";
				//$thisWeekEnd = date('Y-m-d', mktime(0,0,0,$this->now_m,$this->now_d - $this->now_w+6, $this->now_Y)." 23:59:59";
				// end of next week
				$endDate = date('Y-m-d', mktime(0,0,0,$this->now_m,$this->now_d - $numDay + 13, $this->now_Y))." 23:59:59";
				break;

			case 2:
			case 3:
				// begin of today - $days
				$beginDate = date('Y-m-d', mktime(0,0,0,$this->now_m,$this->now_d - $this->rangeDays, $this->now_Y))." 00:00:00";
				// end of today + $days
				$endDate = date('Y-m-d', mktime(0,0,0,$this->now_m,$this->now_d + $this->rangeDays, $this->now_Y))." 23:59:59";
				break;

			case 4:
			default:
				// beginning of this month
				$beginDate = date('Y-m-d', mktime(0,0,0,$this->now_m,1, $this->now_Y))." 00:00:00";
				// end of this month
				$endDate = date('Y-m-d', mktime(0,0,0,$this->now_m+1,0, $this->now_Y))." 23:59:59";
				break;
		}

		$periodStart=substr($beginDate,0,10);
		$periodEnd=substr($endDate,0,10);
	//	$rows = $this->datamodel->queryModel->listEvents($periodStart, $periodEnd,"");
		$rows = array();
		$icalrows = $this->datamodel->queryModel->listIcalEvents( $periodStart, $periodEnd);
		$rows = array_merge($rows,$icalrows);

		// sort combined array by date
		usort($rows,array(get_class($this), "_sortEventsByDate"));

		// determine the events that occur each day within our range

		$events = 0;
		// I need the date not the time of day !!
		//$date = $this->now;
		$date = mktime(0,0,0,$this->now_m,$this->now_d, $this->now_Y);
		$lastDate = mktime(0,0,0,intval(substr($endDate,5,2)),intval(substr($endDate,8,2)),intval(substr($endDate,0,4)));
		$i=0;

		$seenThisEvent = array();
		$this->eventsByRelDay = array();

		if(count($rows)){

			while($date <= $lastDate){
				// get the events for this $date
				$eventsThisDay = array();
				foreach ($rows as $row) {
					if ($row->checkRepeatDay($date))  {
						if ($this->norepeat){
							// make sure this event has not already been used!
							$eventAlreadyAdded = false;
							foreach ($this->eventsByRelDay as $ebrd){
								foreach ($ebrd as $evt) {
									// could test on devent detail but would need another config option
									if ($row->ev_id() == $evt->ev_id()){
										$eventAlreadyAdded = true;
										break;
									}
								}
								if ($eventAlreadyAdded) break;
							}
							if (!$eventAlreadyAdded) {
								$eventsThisDay[]=$row;
							}
						}
						else {
							$eventsThisDay[]=$row;
						}
					}
				}
				if(count($eventsThisDay)) {
					// dmcd May 7/04  bug fix to not exceed maxEvents
					$eventsToAdd = min($this->maxEvents-$events, count($eventsThisDay));
					$eventsThisDay = array_slice($eventsThisDay, 0, $eventsToAdd);
					//sort by time on this day
					usort($eventsThisDay,array(get_class($this), "_sortEventsByTime"));

					$this->eventsByRelDay[$i] = $eventsThisDay;
					$events += count($this->eventsByRelDay[$i]);
				}
				if($events >= $this->maxEvents) break;
				$date = strtotime("+1 day",$date);
				$i++;
			}
		}
		if($events < $this->maxEvents && ($this->dispMode==1 || $this->dispMode==3)){

			if(count($rows)){

				// start from yesterday
				// I need the date not the time of day !!
				$date = mktime(0,0,0,$this->now_m,$this->now_d-1,$this->now_Y);
				$lastDate = mktime(0,0,0,intval(substr($beginDate,5,2)),intval(substr($beginDate,8,2)),intval(substr($beginDate,0,4)));
				$i=-1;

				while($date >= $lastDate){
					// get the events for this $date
					$eventsThisDay = array();
					foreach ($rows as $row) {
						if ($row->checkRepeatDay($date))  {
							if ($this->norepeat){
								// make sure this event has not already been used!
								$eventAlreadyAdded = false;
								foreach ($this->eventsByRelDay as $ebrd){
									foreach ($ebrd as $evt) {
										// could test on devent detail but would need another config option
										if ($row->ev_id() == $evt->ev_id()){
											$eventAlreadyAdded = true;
											break;
										}
									}
									if ($eventAlreadyAdded) break;
								}
								if (!$eventAlreadyAdded) {
									$eventsThisDay[]=$row;
								}
							}
							else {
								$eventsThisDay[]=$row;
							}
						}
					}
					if(count($eventsThisDay)) {
						//sort by time on this day
						usort($eventsThisDay,array(get_class($this), "_sortEventsByTime"));
						$this->eventsByRelDay[$i] = $eventsThisDay;
						$events += count($this->eventsByRelDay[$i]);
					}
					if($events >= $this->maxEvents) break;
					$date = strtotime("-1 day",$date);
					$i--;
				}
			}
		}
		if(isset($this->eventsByRelDay) && count($this->eventsByRelDay)){

			// When we display these events, we just start at the smallest index of the $this->eventsByRelDay array
			// and work our way up so sort the data first

			ksort($this->eventsByRelDay, SORT_NUMERIC);
			reset($this->eventsByRelDay);
		}
		if ($this->sortReverse) {
			$this->eventsByRelDay = array_reverse($this->eventsByRelDay, true);

			foreach($this->eventsByRelDay as $relDay => $daysEvents) {
				$this->eventsByRelDay[$relDay] = array_reverse($daysEvents, true);
			}
		}

	}

	function _sortEventsByDate(&$a, &$b)
	{
		$adate = $a->publish_up();
		$bdate = $b->publish_up();
		return strcmp( $adate, $bdate );
	}

	function _sortEventsByTime (&$a, &$b) {
		// this custom sort compare function compares the start times of events that are referenced by the a & b vars
		//if ($a->publish_up() == $b->publish_up()) return 0;

		list( $adate, $atime ) = split( ' ', $a->publish_up() );
		list( $bdate, $btime ) = split( ' ', $b->publish_up() );

		// if allday event, sort by title first on day
		if ($a->alldayevent()) $atime = '00:00'.$a->title();
		if ($b->alldayevent()) $btime = '00:00'.$b->title();
		return strcmp( $atime, $btime );

	}

	function processFormatString(){
		// see if $customFormatStr has been specified.  If not, set it to the default format
		// of date followed by event title.
		if($this->customFormatStr == NULL) $this->customFormatStr = $this->defaultfFormatStr;
		else {
			$this->customFormatStr = preg_replace('/^"(.*)"$/', "\$1", $this->customFormatStr);
			$this->customFormatStr = preg_replace("/^'(.*)'$/", "\$1", $this->customFormatStr);
			// escape all " within the string
			// $customFormatStr = preg_replace('/"/','\"', $customFormatStr);
		}

		// strip out event variables and run the string thru an html checker to make sure
		// it is legal html.  If not, we will not use the custom format and print an error
		// message in the module output.  This functionality is not here for now.

		// parse the event variables and reformat them into php syntax with special handling
		// for the startDate and endDate fields.
		//asdbg_break();

		// interpret linefeed as <br />
		$customFormat=str_replace("\n", "<br />", $this->customFormatStr);

		$keywords = array(
		'content',				'eventDetailLink',		'createdByAlias',	'color',
		'createdByUserName',	'createdByUserEmail',	'createdByUserEmailLink',
		'eventDate',			'endDate',				'startDate',		'title',	'category',
		'contact',				'addressInfo',			'location',			'extraInfo'
		);
		$keywords_or = implode('|', $keywords);
		$whsp		= '[\t ]*'; // white space
		$datefm		= '\([^\)]*\)'; // date formats
		//$modifiers	= '(?::[[:alnum:]]*)';

		$pattern		= '/(\$\{'.$whsp.'(?:'.$keywords_or.')(?:'.$datefm.')?'.$whsp.'\})/';	// keyword pattern
		$cond_pattern	= '/(\[!?[[:alnum:]]+:[^\]]*])/';	// conditional string pattern e.g. [!a: blabla ${endDate(%a)}]

		// tokenize conditional strings
		$splitTerm = preg_split($cond_pattern, $customFormat, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		$this->splitCustomFormat = array();
		foreach ( $splitTerm as $key => $value) {
			if (preg_match('/^\[(.*)\]$/', $value, $matches)) {
				// remove outer []
				$this->splitCustomFormat[$key]['data'] = $matches[1];
				// split condition
				preg_match('/^([^:]*):(.*)$/', $this->splitCustomFormat[$key]['data'], $matches);
				$this->splitCustomFormat[$key]['cond'] = $matches[1];
				$this->splitCustomFormat[$key]['data'] = $matches[2];
			} else {
				$this->splitCustomFormat[$key]['data'] = $value;
			}
			// tokenize into array
			$this->splitCustomFormat[$key]['data'] = preg_split($pattern, $this->splitCustomFormat[$key]['data'], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		}

		// cleanup, remove white spaces from key words, seperate date parm string and modifier into array;
		// e.g.  ${ keyword ( 'aaaa' ) } => array('keyword', 'aaa',)
		foreach($this->splitCustomFormat as $ix => $yy) {
			foreach($this->splitCustomFormat[$ix]['data'] as $keyToken => $customToken) {
				if (preg_match('/\$\{'.$whsp.'('.$keywords_or.')('.$datefm.')?'.$whsp.'}/', $customToken, $matches)) {
					$this->splitCustomFormat[$ix]['data'][$keyToken] = array();
					$this->splitCustomFormat[$ix]['data'][$keyToken]['keyword'] = stripslashes($matches[1]);
					if (isset($matches[2])) {
						// ('aaa') => aaa
						$this->splitCustomFormat[$ix]['data'][$keyToken]['dateParm'] = preg_replace('/^\(["\']?(.*)["\']?\)$/',"\$1", stripslashes($matches[2]));
					}
				} else {
					$this->splitCustomFormat[$ix]['data'][$keyToken] = stripslashes($customToken);
				}
			}
		}
	}

	function displayLatestEvents(){

		// this will get the viewname based on which classes have been implemented
		$viewname = $this->getTheme();

		$cfg = & JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;

		$viewpath = "components/".JEV_COM_COMPONENT."/views/".$viewname."/assets/css/";

		if ($this->inccss){
			JHTML::stylesheet( "modstyle.css", $viewpath);
		}

		global $mainframe;
		$dispatcher	=& JDispatcher::getInstance();
		$datenow	= JEVHelper::getNow();

		$this->getLatestEventsData();

		$content = "";
		$content .= '<table class="mod_events_latest_table" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';

		if(isset($this->eventsByRelDay) && count($this->eventsByRelDay)){

			// Now to display these events, we just start at the smallest index of the $this->eventsByRelDay array
			// and work our way up.

			$firstTime=true;

			// initialize name of com_jevents module and task defined to view
			// event detail.  Note that these could change in future com_event
			// component revisions!!  Note that the '$this->itemId' can be left out in
			// the link parameters for event details below since the event.php
			// component handler will fetch its own id from the db menu table
			// anyways as far as I understand it.

			$task_events = 'icalrepeat.detail';

			$this->processFormatString();

			foreach($this->eventsByRelDay as $relDay => $daysEvents){

				reset($daysEvents);

				// get all of the events for this day
				foreach($daysEvents as $dayEvent){
					// get the title and start time
					$startDate	= strtotime($dayEvent->publish_up());
					if ($relDay>0){
						$eventDate	= strtotime($datenow->toFormat('%Y-%m-%d ').strftime('%H:%M', $startDate)." +$relDay days");
					}
					else {
						$eventDate	= strtotime($datenow->toFormat('%Y-%m-%d ').strftime('%H:%M', $startDate)." $relDay days");
					}
					$endDate	= strtotime($dayEvent->publish_down());

					list($st_year, $st_month, $st_day) = explode('-', strftime('%Y-%m-%d', $startDate));
					list($ev_year, $ev_month, $ev_day) = explode('-', strftime('%Y-%m-%d', $startDate));

					if($firstTime) $content .= '<tr><td class="mod_events_latest_first">';
					else $content .= '<tr><td class="mod_events_latest">';

					// generate output according custom string
					foreach($this->splitCustomFormat as $condtoken) {

						if (isset($condtoken['cond'])) {
							if ( $condtoken['cond'] == 'a'  && !$dayEvent->alldayevent()) continue;
							if ( $condtoken['cond'] == '!a' &&  $dayEvent->alldayevent()) continue;
						}
						foreach($condtoken['data'] as $token) {
							unset($match);
							unset($dateParm);
							$match='';
							if (is_array($token)) {
								$match = $token['keyword'];
								$dateParm = isset($token['dateParm']) ? trim($token['dateParm']) : "";
							} else {
								$content .= $token;
								continue;
							}


							switch ($match){

								case 'endDate':
								case 'startDate':
								case 'eventDate':
									// Note we need to examine the date specifiers used to determine if language translation will be
									// necessary.  Do this later when script is debugged.

									if(!$this->disableDateStyle) $content .= '<span class="mod_events_latest_date">';

									if (!$dayEvent->alldayevent() && $match=="endDate" && ($dayEvent->noendtime() || $dayEvent->getUnixStartTime()==$dayEvent->getUnixEndTime())){
										$time_fmt = "";
									}
									else if (!isset($dateParm) || $dateParm == ''){
											if ($this->com_calUseStdTime) {
												$time_fmt = $dayEvent->alldayevent() ? '' : ' @%I:%M%p';
											} else {
												$time_fmt = $dayEvent->alldayevent() ? '' : ' @%H:%M';
											}
											$dateFormat = $this->displayYear ? '%a %b %d, %Y'.$time_fmt : '%a %b %d'.$time_fmt;
											$jmatch = new JDate($$match);
											$content .= $jmatch->toFormat($dateFormat);
											//$content .= JEV_CommonFunctions::jev_strftime($dateFormat, $$match);
									} else {
										// if a '%' sign detected in date format string, we assume strftime() is to be used,
										if(preg_match("/\%/", $dateParm)) {
											$jmatch = new JDate($$match);
											$content .= $jmatch->toFormat($dateParm);
										}
										// otherwise the date() function is assumed.
										else $content .= date($dateParm, $$match);
									}

									if(!$this->disableDateStyle) $content .= "</span>";
									break;

								case 'title':

									if (!$this->disableTitleStyle) $content .= '<span class="mod_events_latest_content">';
									if ($this->displayLinks) {

										$link = $dayEvent->viewDetailLink($ev_year,$ev_month,$ev_day,false,$this->myItemid);
										$link = JRoute::_($link.$this->datamodel->getCatidsOutLink());

										$content .= $this->_htmlLinkCloaking($link,JEventsHTML::special($dayEvent->title()));
										/*
										"index.php?option=".$compname
										. "&task="  . $task_events
										. "&agid="  . $dayEvent->id()
										. "&year="  . date("Y", $eventDate)
										. "&month=" . date("m", $eventDate)
										. "&day=" 	. date("d", $eventDate)
										. "&Itemid=". $this->myItemid . $this->catout, $dayEvent->title());
										*/
									} else {
										$content .= JEventsHTML::special($dayEvent->title());
									}
									if (!$this->disableTitleStyle) $content .= '</span>';
									break;

								case 'category':
									$catobj   = $dayEvent->getCategoryName();
									$content .= JEventsHTML::special($catobj);
									break;

								case 'contact':
									// Also want to cloak contact details so
									$this->modparams->set("image",1);
									$dayEvent->text = $dayEvent->contact_info();
									$dispatcher->trigger( 'onPrepareContent', array( &$dayEvent, &$this->modparams, 0 ), true );
									$dayEvent->contact_info($dayEvent->text);
									$content .= $dayEvent->contact_info();
									break;

								case 'content':  // Added by Kaz McCoy 1-10-2004
								$this->modparams->set("image",1);
								$dayEvent->data->text = $dayEvent->content();
								$results = $dispatcher->trigger( 'onPrepareContent', array( &$dayEvent->data, &$this->modparams, 0 ), true );
								$dayEvent->content($dayEvent->data->text);
								//$content .= substr($dayEvent->content, 0, 150);
								$content .= $dayEvent->content();
								break;

								case 'addressInfo':
								case 'location':
									$this->modparams->set("image",0);
									$dayEvent->data->text = $dayEvent->location();
									$results = $dispatcher->trigger( 'onPrepareContent', array( &$dayEvent->data, &$this->modparams, 0 ), true );
									$dayEvent->location($dayEvent->data->text);
									$content .= $dayEvent->location();
									break;

								case 'extraInfo':
									$this->modparams->set("image",0);
									$dayEvent->data->text = $dayEvent->extra_info();
									$results = $dispatcher->trigger( 'onPrepareContent', array( &$dayEvent->data, &$this->modparams, 0 ), true );
									$dayEvent->extra_info($dayEvent->data->text);
									$content .= $dayEvent->extra_info();
									break;

								case 'createdByAlias':
									$content .= $dayEvent->created_by_alias();
									break;

								case 'createdByUserName':
									$catobj   = JEVHelper::getUser($dayEvent->created_by());
									$content .= $catobj->username;
									break;

								case 'createdByUserEmail':
									// Note that users email address will NOT be available if they don't want to receive email
									$catobj   = JEVHelper::getUser($dayEvent->created_by());
									$content .= $catobj->sendEmail ? $catobj->email : '';
									break;

								case 'createdByUserEmailLink':
									// Note that users email address will NOT be available if they don't want to receive email
									$content .= JRoute::_("index.php?option="
									. $compname
									. "&task=".$task_events
									. "&agid=".$dayEvent->id()
									. "&year=".$st_year
									. "&month=".$st_month
									. "&day=".$st_day
									. "&Itemid=".$this->myItemid . $this->catout);
									break;

								case 'color':
									$content .= $dayEvent->bgcolor();
									break;

								case 'eventDetailLink':
									$link = $dayEvent->viewDetailLink($st_year,$st_month,$st_day,false,$this->myItemid);
									$link = JRoute::_($link.$this->datamodel->getCatidsOutLink());
									$content .= $link;

									/*
									$content .= JRoute::_("index.php?option="
									. $compname
									. "&task=".$task_events
									. "&agid=".$dayEvent->id()
									. "&year=".$st_year
									. "&month=".$st_month
									. "&day=".$st_day
									. "&Itemid=".$this->myItemid . $this->catout);
									*/
									break;

								default:
									if ($match) $content .= $match;
									break;
							} // end of switch
						} // end of foreach
					} // end of foreach
					$content .= "</td></tr>\n";
					$firstTime=false;
				} // end of foreach
			} // end of foreach

		} else {
			$content .= '<tr><td class="mod_events_latest_noevents">'. JText::_('JEV_NO_EVENTS') . '</td></tr>' . "\n";
		}
		$content .="</table>\n";

		$callink_HTML = '<div class="mod_events_latest_callink">'
		. $this->_htmlLinkCloaking(JRoute::_("index.php?option=$compname" .  "&Itemid=". $this->myItemid . $this->catout, true), JText::_('JEV_CLICK_TOCOMPONENT'))
		. '</div>';

		if ($this->linkToCal == 1) $content = $callink_HTML . $content;
		if ($this->linkToCal == 2) $content .= $callink_HTML;

		if ($this->displayRSS){
			$rssimg = JURI::root() . "images/M_images/livemarks.png";

			$callink_HTML = '<div class="mod_events_latest_rsslink">'
			.'<a href="'.$this->rsslink.'" title="'.JText::_("RSS Feed").'" target="_blank">'
			.'<img src="'.$rssimg.'" alt="'.JText::_("RSS Feed").'" />'
			.JText::_("Subscribe to RSS Feed")
			. '</a>'
			. '</div>';
			$content .= $callink_HTML;
		}
		return $content;
	} // end of function

} // end of class
