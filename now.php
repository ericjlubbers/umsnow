<?php

$json = file_get_contents('http://www.ticketfly.com/api/events/upcoming.json?orgId=1075&maxResults=200&pageNum=0'); 
$data = json_decode($json);
// var_dump($data->events[1]);
$user_tz = 'America/Denver';

if(!function_exists('get_my_date')) {
	function get_my_date($date_in) {
		$date_out = date_create_from_format('Y-m-d H:i:s',$date_in);
		$date_out = date_format($date_out,'g:ia n/d');
		return $date_out;
	}
}



foreach ($data->events as $event) {
	echo "\n";
	echo $event->venue->name;
	echo "\n";
	echo $event->headlinersName;
	echo "\n";
	$starttime = $event->startDate;
	$eventtime = get_my_date($starttime);
	echo $eventtime;
	echo "\n\n";

}
?>