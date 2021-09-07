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
};

// Checks whether a lambda function results in a constant value after being applied to itself over and over.
$n_to_infinity = function( $input, callable $func, int $num_iterations, int $depth = 1 ): bool {
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
			$i++;
			return $iteration( $f, $result );
		} else {
			return NULL;
		}
	};
	if( $iteration( $func, $input ) ) {
		return TRUE;
	} else {
		return FALSE;
	}
};

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
};

// Test1
$array = [ 8, 203, [ 31, 2, [ 17, 23 ], 58, 2, 12 ], 4, [ 12, 1, 0, 0, 172 ], [ 1, [ 3, 17, 2 ], [ 36, 28, 583, 2 ], 1, 90 ] ];
var_dump( $array_check( 0, $array ) ); // Returns TRUE
var_dump( $array_check( 112, $array ) ); // Returns FALSE
var_dump( $array_check( 'Subject', $array ) ); // Returns FALSE
var_dump( $array_check( 583, $array ) ); // Returns TRUE

echo PHP_EOL;

// Test2
$func = function( $input ) {
	return round( $input/2, 0 );
};
var_dump( $n_to_infinity( 10, $func, 10 ) ); // Returns TRUE;
var_dump( $n_to_infinity( 10, $func, 1 ) ); // Returns FALSE
var_dump( $n_to_infinity( 10, $func, 4 ) ); // Returns FALSE
var_dump( $n_to_infinity( 10, $func, 5 ) ); // Returns TRUE (Five is the number of times required to get the same result twice for depth of 1)
var_dump( $n_to_infinity( 10, $func, 5, 2 ) ); // Returns FALSE
var_dump( $n_to_infinity( 10, $func, 6, 2 ) ); // Returns TRUE (When depth is 2, another iteration is needed to get same result)

echo PHP_EOL;

// Test3
var_dump( $func_iterate( 10, $func, 2, 1 ) ); // Returns 3.0, the result of $func after two iterations
var_dump( $func_iterate( 10, $func, 1e15, 1 ) ); // Returns 1.0 without running through 1e15
