<?php
// Recursively iterating lambda functions

// Checks if variable is contained within array, including any arrays with scopes within that array's scope (i.e., sub-arrays, sub-sub-arrays, sub-sub-sub-arrays, etc.).
$array_check = function( $needle, array $haystack ) use( &$array_check ): bool {
	foreach( $haystack as $hay ) {
		if( $hay == $needle ) {
			return TRUE;
		} else if( is_array( $hay ) ) {
			if( $array_check( $needle, $hay ) ) {
				return TRUE;
			}
		}
	}
	return FALSE;
}
// Checks whether a lambda function results in a constant value after being applied to itself over and over.
$n_to_infinity = function( callable $func, int $num_iterations, int $depth = 1 ): bool {
	$i = 0;
	$d = 0;
	$iteration = function( callable $f, $prev ) use( &$iteration, &$i, &$d, $num_iterations, $depth ) {
		if( $i<$num_iterations ) {
			$result = $f( $prev );
			if( $result == $prev ) {
				$d++;
				if( $d >= $depth ) {
					return TRUE;
				}
			}
			return $iteration( $f, $result );
		} else {
			return NULL;
		}
	}
	if( $iteration( $func, NULL ) ) {
		return TRUE;
	} else {
		return FALSE;
	}
}
// Apply a lambda function over and over to a variable for a set number of times and until a specific result is achieved, whichever comes first
$func_iterate = function( $var, callable $func, int $num_iterations, $goal = NULL ) {
	if( $var != $goal ) {
		$i = 0;
		$result = $var;
		do {
			$result = $func( $result );
			$i++;
		} while( $i<$num_iterations && $result != $goal );
		return $result;
	} else {
		return NULL;
	}
}
