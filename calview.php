<?php
	$day_names = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
	$first_of_month = array();
	$n = 5;
	$year = $_GET['year'];
	$month = $_GET['month'];
	echo generate_calendar($year, $month, 16,15,NULL,0,15, $first_of_month, $day_names, $day_names[$n]);
	echo '<input type="hidden" id="hdnYear" value="'.$year.'"/>';
	echo '<input type="hidden" id="hdnMonth" value="'.$month.'"/>';
	//echo generate_calendar($year, $month, 16, 3, NULL, 0, 15, $first_of_month, 5, $day_names);
	#   echo generate_calendar($year, $month, $days, $day_name_length, $month_href, $first_day, $pn);
	function generate_calendar($year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array())
	{
		$first_of_month = gmmktime(0, 0, 0, $month, 1, $year);
		$prevMonth = gmstrftime('%B', gmmktime(0, 0, 0, $month-1, 1, $year));
		$nextMonth = gmstrftime('%B', gmmktime(0, 0, 0, $month+1, 1, $year));

		#remember that mktime will automatically correct if invalid dates are entered
		# for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
		# this provides a built in "rounding" feature to generate_calendar()
		$imgfolder = 'assets/images/';
		$day_names = array(); #generate all the day names according to the current locale
		for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
			$day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name
	
		list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
		$weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
		$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names
		
		$yearTitle = $year;
		if($month===12){
			$yearTitle = $year++;
		}

		if($month===1){
			$nextMonth = $year--;
		}

		$prevTitle = $prevMonth.'&nbsp;'.$yearTitle;
		$nextTitle = $nextMonth.'&nbsp;'.$yearTitle;
		#Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
		@list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
		if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
		if($n) $n = '&nbsp;<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
		$calendar = '<table class="calendar">'."\n".
						'<thead>'.
							'<tr class="calendar-month">'.
								'<th class="tc month" colspan="7">'.
									'<a href="javascript:;" onclick="calprev()" title="Go to '.$prevTitle.'">'.
										'<img src='.$imgfolder.'cal-left.png id="calpre" alt="Go to '.$prevTitle.'" />'.
									'</a>'
									.$p.('<span>'.$title.'</span>').$n.
									'<a href="javascript:;" onclick="calnext(0,0)" id="calnext" title="Go to '.$nextTitle.'">
										<img src='.$imgfolder.'cal-right.png alt="Go to '.$nextTitle.'" />
									</a>'.
								"</th>
							</tr>
						\n<tr>";
	
		if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
			#if day_name_length is >3, the full name of the day will be printed
			foreach($day_names as $d)
				$calendar .= '<th class="tc" abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>';
			$calendar .= "</tr>\n<tr></thead>";
		}
	
		if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; #initial 'empty' days
		for($day=1,$days_in_month=gmdate('t', $first_of_month); $day<=$days_in_month; $day++,$weekday++){
			if($weekday == 7){
				$weekday   = 0; #start a new week
				$calendar .= "</tr>\n<tr>";
			}
			if(isset($days[$day]) and is_array($days[$day])){
				@list($link, $classes, $content) = $days[$day];
				if(is_null($content))  $content  = $day;
				$calendar .= '<td class="inactive"'.($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
					($link ? '<a href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).'</td>';
			}
			else 
			{
				$filterDay = ''; if(strlen($day)==1) $filterDay =  '0'.$day; else $filterDay = $day;
				$filterMonth = ''; if(strlen($month)==1) $filterMonth =  '0'.$month; else $filterMonth = $month;
				$filterYear = $year;
				
				$media = $day.'-'.$month.'-'.$year;
				$calfilter = $filterYear.'-'.$filterMonth.'-'.$filterDay;
				
				$calItemUrl = 'calendar/items?showfrom='.$calfilter.'&day='.$media;
				/*ClearBox modal*/
				$calendar .= "<td><strong>$day</strong>".
									'<div class="items">
										<a href="javascript:ShowCalItem('.$filterYear.','.$filterMonth.','.$filterDay.',1)" title="Pending tasks on ['.$media.']" target="_blank">
											<img src='.$imgfolder.'cal-pending.png alt="Pending tasks" />
										</a>
										<a href="javascript:ShowCalItem('.$filterYear.','.$filterMonth.','.$filterDay.',2)" title="Open opportinuties on ['.$media.']" target="_blank"><img src='.$imgfolder.'cal-progress.png alt="Open opportinuties" />
										</a>
										<a href="javascript:ShowCalItem('.$filterYear.','.$filterMonth.','.$filterDay.',3)" title="Won opportinuties on ['.$media.']" target="_blank">
										<img src='.$imgfolder.'cal-over.png alt="Won opportinuties" />
										</a>
										<a href="javascript:ShowCalItem('.$filterYear.','.$filterMonth.','.$filterDay.',4)" title="Pending opportinuties on ['.$media.']" target="_blank">
											<img src='.$imgfolder.'cal-nil.png alt="Pending opportinuties" />
										</a>
									</div>'
								."</td>";
			}
		}
		if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days
	
		return $calendar."</tr>\n</table>\n";
	}
	
	function daysLeftForBirthday($devabirthdate) 
	{ 
		/* input birthday date format -> Y-m-d */ 
		list($y, $m, $d) = explode('-',$devabirthdate); 
		$nowdate = mktime(0, 0, 0, date("m"), date("d"), date("Y")); 
		$nextbirthday = mktime(0,0,0,$m, $d, date("Y")); 
	
		if ($nextbirthday<$nowdate) 
			$nextbirthday=$nextbirthday+(60*60*24*365); 
	
		$daycount=intval(($nextbirthday-$nowdate)/(60*60*24)); 
	
		return $daycount; 
	}
?>