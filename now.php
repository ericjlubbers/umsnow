<?php

echo 'version 1.1.3';

date_default_timezone_set('America/Denver');

if ($argv[1] != 'local') {
	if ( !class_exists('ftp') ) {
		require('/var/www/lib/class.ftp.php');
	}
	$ftp_error_display = TRUE;
	$ftp_directory_local = '/var/www/vhosts/denverpostplus.com/httpdocs/ums';
	$ftp_directory_remote = '';
	$ftp_file_format = '';
	$ftp_file_mode = FTP_ASCII;
}

$json = file_get_contents('http://www.ticketfly.com/api/events/upcoming.json?orgId=1075&maxResults=200&pageNum=0'); 
$data = json_decode($json);
$file_name = 'ums-upcoming.json';
$show_list = array();
$final_list = array();
$i=0;

if(!function_exists('get_my_date')) {
	function get_my_date($date_in) {
		$event_time = strtotime($date_in);
		$current_time = time();
		//$current_time = 1406334600;
		$time_diff = $event_time - $current_time;
		if ($time_diff < -3500) {
			$flag = 'past';
		} else if ($time_diff > -3500 && $time_diff < 0) {
			$flag = 'now';
		} else {
			$flag = 'next';
		}
		$date_out = date('g:ia n/d',$event_time);
		//echo $time_diff . "\n";
		//echo $date_out . "\n";
		//echo $flag . "\n";
		//echo "\n";
		$return = array($date_out,$flag);
		return $return;
	}
}

function get_new_keys($inputarray) {
	$newarray = array();
	$ii=0;
	foreach($inputarray as $inarray) {
		$newarray[$ii]['name'] = $inarray['name'];
		$newarray[$ii]['time'] = $inarray['time'];
		$newarray[$ii]['flag'] = $inarray['flag'];
		$ii++;
	}
	return $newarray;
}

foreach ($data->events as $event) {
	$starttime = $event->startDate;
	$eventtime = get_my_date($starttime);
	$show_list[$event->venue->name][$i]['name'] = $event->headlinersName;
	$show_list[$event->venue->name][$i]['time'] = $eventtime[0];
	$show_list[$event->venue->name][$i]['flag'] = $eventtime[1];
	$i++;
}

foreach($show_list as $venuekey => $venueitem) {
	if ($venuekey !== 'The UMS Box Office') {
		$final_list[$venuekey] = get_new_keys($venueitem);
	}
}

$filestring = json_encode($final_list);
file_put_contents($file_name, $filestring);

if ($argv[1] != 'local') {
	$ftp = new ftp();
	$ftp->connection_passive();
	$ftp->file_put($file_name, $ftp_directory_local, $ftp_file_format, $ftp_error_display, $ftp_file_mode, $ftp_directory_remote);
	$ftp->ftp_connection_close();
}
?>