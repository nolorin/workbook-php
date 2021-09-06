<?php
// Closure that encapsulates other example enclosures.
$option = !empty( $argv[1] ) ? (string) $argv[1] : 'default';
$info = new stdClass();
$info->value = !empty( $argv[2] ) ? (bool) $argv[2] : FALSE;

echo ( function( $option ) use( &$info ) {
	// Input sanitizing on $info variable goes here
	foreach( get_object_vars( $info ) as $prop ) {
		if( !in_array( $prop, [ 'value' ] ) ) {
			unset( $info->$prop );
		}
	}
	$info->value = $info->value ? TRUE : FALSE;

	// Partially open closure
	if( $option == 'partial' ) {

		return ( function() use( &$info ) {
			$output = $info->value ? 'purple' : 'neon green';
			return $output;
		} )();

	// Restricted open closure
	} else if( $option == 'restricted' ) {

		return ( function( bool $value ) {
			$output = $value ? 'Sunday' : 'Wednesday';
			return $output;
		} )( $info->value );

	// Fully encapsulated and scope-isolated closure
	} else {

		return( function() {
			return TRUE;
		} )();

	}
} )( $option ) . PHP_EOL;
