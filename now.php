<?php

$json = file_get_contents('http://www.ticketfly.com/api/events/upcoming.json?orgId=1075&maxResults=200&pageNum=0'); 
$data = json_decode($json);
// var_dump($data->events[1]);
		date_default_timezone_set('America/Denver');

if(!function_exists('get_my_date')) {
	function get_my_date($date_in) {
		date_default_timezone_set('America/Denver');
		$date_out = date_create_from_format('Y-m-d H:i:s',$date_in);
		$flag = 'now';
		$date_out = date_format($date_out,'g:ia n/d');
		$return = array($date_out,$flag);
		return $return;
	}
}
$show_list = array();
$i=0;

foreach ($data->events as $event) {
	echo "\n";
	echo $event->venue->name;
	echo "\n";
	echo $event->headlinersName;
	echo "\n";$event->venue->name;
	$starttime = $event->startDate;
	$eventtime = get_my_date($starttime);
	echo $eventtime[0];
	echo "\n\n";
	$show_list[$event->venue->name][$i]['name'] = $event->headlinersName;
	$show_list[$event->venue->name][$i]['time'] = $eventtime[0];
	$show_list[$event->venue->name][$i]['flag'] = $eventtime[1];
	$i++;
	var_dump($show_list);
	die;
}

?>