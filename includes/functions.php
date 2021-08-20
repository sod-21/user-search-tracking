<?php

function sod_time_ago( $time_ago , $type = '') {
	$cur_time     = time();
	$time_elapsed = $cur_time - $time_ago;
	$seconds      = $time_elapsed;
	$minutes      = round( $time_elapsed / 60 );
	$hours        = round( $time_elapsed / 3600 );
	$days         = round( $time_elapsed / 86400 );
	$weeks        = round( $time_elapsed / 604800 );
	$months       = round( $time_elapsed / 2600640 );
	$years        = round( $time_elapsed / 31207680 );
 
	$output = '';

	if ($type == 'day') {
		if (30 > $days) {
			$output .= $days . __( ' days ago', 'sod_track' );
		}else if ( 12 >= $months ) {
			if ( 1 == $months ) {
				$output .= __( 'a month ago', 'sod_track' );
			} else {

				$output .= $months . __( ' months ago', 'sod_track' );
			}
		} else if ( $years >= 1) {
			if ( 1 == $years ) {
				$output .= __( 'a year ago', 'sod_track' );
			} else {

				$output .= $years . __( ' years ago', 'sod_track' );
			}
		}
		return $output;
	}
	
	if ( 60 >= $seconds ) {
		$output .= $seconds . __( ' seconds ago', 'sod_track' );
	} elseif ( 60 >= $minutes ) {
		if ( 1 === $minutes ) {
			$output .= __( 'one minute ago', 'sod_track' );
		} else {
			$output .= $minutes . __( ' minutes ago', 'sod_track' );
		}
	} elseif ( 24 >= $hours ) {
		if ( 1 === $hours ) {
			$output .= __( 'an hour ago', 'sod_track' );
		} else {
			$output .= $hours . __( ' hours ago', 'sod_track' );
		}
	} elseif ( 7 >= $days ) {
		if ( 1 === $days ) {
			$output .= __( 'yesterday', 'sod_track' );
		} else {
			$output .= $days . __( ' days ago', 'sod_track' );
		}
	} elseif ( 4.3 >= $weeks ) {
		if ( 1 === $weeks ) {
			$output .= __( 'a week ago', 'sod_track' );
		} else {
			$output .= $weeks . __( ' weeks ago', 'sod_track' );
		}
	} elseif ( 12 >= $months ) {
		if ( 1 === $months ) {
			$output .= __( 'a month ago', 'sod_track' );
		} else {
			$output .= $months . __( ' months ago', 'sod_track' );
		}
	} else {
		if ( 1 === $years ) {
			$output .= __( 'one year ago', 'sod_track' );
		} else {
			$output .= $years . __( ' years ago', 'sod_track' );
		}
	}

	return $output;
}

?>