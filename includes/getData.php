<?php

$colors = array(
	'this_team' => '#8AA1B0',
	'peer_group' => '#FBB75E',
	'total' => '1E4B7C');
// Build the array

$dataArray = array(
	array(
		'indicator' => 'This team',
		'Score' => 3.3,
		'color' => $colors['this_team']
	),
	array(
		'indicator' => 'Peer average',
		'Score' => 1.0,
		'color' => $colors['peer_group']
	),
	array(
		'indicator' => 'Total average',
		'Score' => 7.2,
		'color' => $colors['total']
	)	
);

$jsonString = json_encode($dataArray);

echo $jsonString;