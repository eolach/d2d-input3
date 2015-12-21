<?php


// $d2d_specs = new D2D_data_specs();
// Builds a databas table of indicator (types)
//$d2d_specs -> create_db_table();
// Fills the table with the latest indicators
//$d2d_specs -> fill_db_table();

// Creates a new table oh records for the latest set of indicators
// USE WITH CAUTION - THIS OVERWRIES THE DATA IN THE MAIN TABLE
// $d2d_specs -> create_indicators_table();
// $table_inds = $d2d_specs -> get_db_table();

// Build a sql wuery
function d2d_get_team_status( $team_code ) {
	$sql_get_team_status =  'SELECT save_status FROM indicators WHERE team_code =  "' . $team_code . '" AND year_code =  "D2D 2.0"';
	global $wpdb;
	$team_status = $wpdb -> get_row( $sql_get_team_status, ARRAY_N );
	return $team_status[0];
}

function d2d_set_team_status( $team_code ) {
	$sql_set_team_status =  'UPDATE indicators SET save_status = "saved" WHERE team_code =  "' . $team_code . '" AND year_code =  "D2D 2.0"';
	global $wpdb;
	$team_status = $wpdb -> query( $sql_set_team_status );
	return $team_status;
}

$response = 'Enter a team code';

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
	$this_code = $_POST['team_code'];
	if ( $this_code != ''){
		d2d_set_team_status( $this_code ) ;
		$temp = d2d_get_team_status( $this_code ) ;
		if ( $temp == 'saved'){
			$response = $this_code . ' is now unlocked.';
		} else {
			$response =  $this_code . ' is still locked. An error has occured';
		}
	} else {
		$response = 'No team code entered';
	}
}


?>

<div class="wrap">

	<h2>Unlock team code</h2>
	<h3>Enter team code and press "Unlock" to allow access to D2D 2.0 record for revision.</h3>
<?php 		echo '<form style="background-color: #eeede6" action="' . $_SERVER['REQUEST_URI'] . '" method="post">';?>
		
	<div class="d2d_upper_box" style="width:75px"><input type="text" class="new_code" name="team_code" value="" size="11" align="right"></div>
	<?php submit_button('Unlock') ?>
	<p><?php echo $response; ?></p>
</form>

</div>
