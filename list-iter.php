<?php

$list1 = array( 'Tom', 'Jane', 'Mary', 'Farouk', 'Elizabeth', 'Pat' );
$list2 = array( 'Worker', 'Manager', 'Contractor', 'Executive' );
$list3 = array( 'Experienced', 'Skilled', 'Novice' );
$list4 = array( 'Boston', 'Chicago', 'Los Angeles', 'San Francisco', 'Seattle', 'Atlanta', 'Austin', 'Houston' );

$loc = array(
	'Illinois' => [ 'Chicago' ],
	'California' => [ 'Los Angeles', 'San Francisco' ],
	'Georgia' => [ 'Atlanta' ],
	'Washington' => [ 'Seattle' ],
	'Texas' => [ 'Austin', 'Houston' ]
);

$eigenKey = array(
	[ 0, 1, 1, 3 ],
	[ 1, 2, 1, 4 ],
	[ 2, 0, 2, 6 ],
	[ 2, 0, 0, 2 ],
	[ 3, 1, 1, 0 ],
	[ 4, 2, 1, 5 ],
	[ 5, 0, 1, 4 ],
	[ 5, 0, 2, 4 ]
);

$output = '';
foreach( $eigenKey as $list ) {
	$output .= "{$list1[$list[0]]} is a&?{$list3[$list[2]]} {$list2[$list[1]]} from {$list4[$list[3]]}";
	foreach( $loc as $state => $cities ) {
		if( in_array( $list4[$list[3]], $cities ) ) {
			$output .= ", $state";
		}
	}
	$output .= '.' . PHP_EOL;
}

$output = preg_replace( [ '/(&\?)([^aeio])/i', '/(&\?)([aeio])/i' ], [ ' $2', 'n $2' ], $output );

echo $output;

exit;
