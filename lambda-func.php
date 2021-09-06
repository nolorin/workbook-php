<?php
// Integer multiplication function that uses lambda function arguments. Note: in PHP lambda functions are type cast to "callable" and are called "closures".
function multiply( $base, int $multiplicant, callable $iterant = NULL, callable $negative = NULL ) {
	if( !is_numeric( $base ) ) {
		if( empty( $iterant ) ) {
			return FALSE;
		}
		if( is_string( $base ) ) {
			$output = '';
		} else if( is_array( $base ) ) {
			$output = $base;
		} else if( is_object( $base ) ) {
			$output = new stdClass();
			$output->value = 'shell';
		} else {
			$output = NULL;
		}
		for( $i=0; $i<abs( $multiplicant ); $i++ ) {
			$output = $iterant( $base, $output );
		}
		if( !empty( $negative ) && $multiplicant < 0 ) {
			$output = $negative( $output );
		}
		return $output;
	} else {
		return $base*$multiplicant;
	}
}

// Multiplication lambdas per iteration
$multiplyString = function( string $base, string $previous ) {
	return $previous . $base;
};
$multiplyArray = function( array $base, array $previous ) {
	return array_merge( $previous, $base );
};
$multiplyObject = function( object $base, object $previous ) {
	$i = count( get_object_vars( $previous ) );
	$previous->{'child_'.($i)} = $base;
	return $previous;
};
// Negative operand lambdas for whole function
$negativeString = function( string $output ) {
	return strrev( $output );
};
$negativeArray = function( array $output ) use( $negativeArray ) {
	for( $i=0; $i<count( $output ); $i++ ) {
		if( is_string( $output[$i] ) ) {
			$output[$i] = strrev( $output[$i] );
		} else if( is_numeric( $output[$i] ) ) {
			$output[$i] *= -1;
		} else if( is_array( $output[$i] ) ) {
			$output[$i] = $negativeArray( $output[$i] );
		}
		// No effect for object items
	}
	return $output;
};
$negativeObject = function( object $output ) {
	// No effect for object
	return $output;
};

// Test
var_dump( multiply( 5, 37 ) );
var_dump( multiply( 'Harp', 7, $multiplyString, $negativeString ) );
var_dump( multiply( [ 'Here', 'There', 'Everywhere' ], -3, $multiplyArray, $negativeArray ) );
$obj = new stdClass();
$obj->value = 'item';
var_dump( multiply( $obj, 4, $multiplyObject, $negativeObject ) );
