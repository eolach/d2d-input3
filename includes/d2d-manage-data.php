<?php
//  Implements RetrieveData class to retrieve data from indicators table
//  decode for real data (as "total" or "rostered"
//  display in table form and save as csv files as necessary.
//  The class is accessible from the "Fetch Data" class,
//  as a provider of data and as a receiver of data to be displayed and saved.)
//  
/**
 *
 *
 * @author Neil
 * @version 0.5
 * @created 11-January-2016
 */
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( !class_exists( 'D2D_manage_data' ) ) {
	class D2D_manage_data {


		public $team_results;
		public $weights;
		public $data_vars = array(); // Indicators from which quality scores are calculater
		public $data_table = array();
		public $quality_keys = array();
		public $quality_inds = array(); //Six plus one indicators of quality
		public $quality_table = array();

		public function __construct() {
			add_shortcode( 'manage data', array( $this, 'data_shortcode' ) );
		}

		public function build_tables(){
			$this -> retrieve_data();
			$this -> prepare_data_table_header();
			$this -> build_data_table();
			$this -> impute_data();
			$this -> prepare_quality_table_header();
			$this -> build_quality_table();
		}

		public function manage_data(){
			$this -> build_tables();

			$this -> display_data_table();
			
			$this -> display_quality_table();
			$this -> exportQualityToCSV();
		}

		/**
		 * Retrieve individual team data from the indicators database table
		 * Retrieve the active table of weights and levels
		 * This provides the list of indicators used in the composite indicators
		 * and the composite indicators calculated.
		 * @return [array] All locked team results as an array and weights as array.
		 */
		public function retrieve_data(){

			$sql_team = 'SELECT * FROM indicators WHERE save_status = "locked" ';
			$sql_weights = 'SELECT * FROM d2d_weights3_0';

			global $wpdb;
			$this -> team_results = $wpdb -> get_results( $sql_team, ARRAY_A );
			$this -> weights = $wpdb -> get_results( $sql_weights, ARRAY_A );
		}

// Data table

		public function prepare_data_table_header(){
			// Retrieve compositie variables as first column of weights array
			// $this -> data_vars[] = 'team_code';
			$this -> data_vars[] = 'team_year';
			// $this -> data_vars[] = 'team_year';
			foreach($this -> weights as $row){
				array_push($this -> data_vars, $row['short_label']);
			}
		}
			
		public function build_data_table(){
			//  Iterate through the team results
			//  For each one, iterate through the data vars and place them in the data table
			foreach ($this -> team_results as $team){
				$team_row = array();
				foreach( $this -> data_vars as $var){
					if ( $var != 'team_year'){
						if ( is_null($team[$var])){
							$team_row[$var] = null;
						} else {
							$numeric_var = $this -> d2d_decode($team[$var]);
							$team_row[$var] = $numeric_var['total'];
						}
					}
					$team_row['team_year'] = $team['team_code'] . '_' .  str_replace('D2D ', '', $team['year_code']);
				}
				$this -> data_table[$team_row['team_year']] = $team_row;
			}


		}

		public function impute_data(){
			$data_table_keys = array_keys($this -> data_table);
			// print_r($data_table_keys);
			foreach($this -> data_vars as $var){
					if ( $var != 'team_year'){
					$valid_var_table = array();
					foreach( $data_table_keys as $key){
						$this_val = $this -> data_table[$key][$var];
						if(!($this_val == '')){
							$valid_var_table[] = $this_val;
						} else {
							 $this -> data_table[$key][$var]  = "n/a" ;
						}
					}
					foreach( $data_table_keys as $key){
						$this_val = $this -> data_table[$key][$var];
						if( ($this_val == 'n/a')  or
							($this_val == 'missing') ) {
								$imputed_key  = array_rand ( $valid_var_table);
								$this -> data_table[$key][$var] = $valid_var_table[$imputed_key];						;
						}
					}
				}
			}
		}

		public function display_data_table(){
			echo '<table>';
			foreach($this -> data_vars  as $ind){
				echo '<th>' . $ind .'</th>';
			};

			foreach($this -> data_table as $row){
				echo '<tr>';
				foreach($this -> data_vars as $var){
					echo '<td>' . $row[$var] . '</td>';
				}
				echo '</tr>';
			}
			echo '</table>';
		}
		
// Quality table
		public function prepare_quality_table_header(){
			// Retrievequality variables as a subset of the columns of the weights
			$q_keys = array_keys($this -> weights[0]);
			$offset = 4;
			for ($i = 0; $i < 7; $i++){
				$this -> quality_keys[$i] = $q_keys[$i + $offset];
			}

		}

		public function build_quality_table(){
			$team_codes = array_keys($this -> data_table);
			foreach ($team_codes as $code){
				// echo ' Type is ' . gettype($code) . '<br>';
				// print_r($code); 
				$team = $this -> data_table[$code];
				$team_row = array();
				$starfield_array = $this -> calculate_starfield ( $this -> quality_keys, $team);
				foreach( $this -> quality_keys as $var){
					$team_row[$var] = $starfield_array[$var];
				}
				$this -> quality_table[$code] = $team_row;
				// print_r($team_row);
			}
		}

		public function display_quality_table(){
			echo '<table>';
			echo '<th>team_iteration</th>';
			foreach($this -> quality_keys  as $ind){
				echo '<th>' . $ind .'</th>';
			};

			$team_codes = array_keys($this -> quality_table);
			foreach($team_codes as $code){
				$team = $this -> quality_table[$code];
				echo '<tr>';
				echo '<td>' . $code . '</td>';
				foreach($this -> quality_keys  as $ind){
					echo '<td>' . $this -> quality_table[$code][$ind] . '</td>';
				}
				echo '</tr>';
			}
			echo '</table>';
		}

		public function calculate_starfield( $quality_labels,  $team_data ){
			$starfield_array = array();
			$weights = $this -> weights;
			foreach ( $quality_labels as $qual_name){
				$composite_number = 0;
				foreach ($weights as $w_row ){
					$data_name = $w_row['short_label'];
					$temp_value0 = $team_data[$data_name];
					$data_value = $this -> d2d_apply_threshold( $temp_value0, $w_row['lower'], $w_row['upper'] );
					
					$temp_number = $data_value * $w_row[$qual_name];
					$composite_number += $temp_number;
				}
				$starfield_array[$qual_name] = number_format( $composite_number / count($weights) ,2 );
			}
			return $starfield_array;
		}

		public function d2d_apply_threshold( $val, $low, $high ) {
			if ( $val < $low ) {
				$new_val = 0;
			}elseif ( $val > $high ) {
				$new_val = 100;
			} else {
				$new_val = 100 * ( ( ( $high - $low ) > 0 ) ? ( $val - $low )/( $high - $low ) : 1 );
			}

			return $new_val;
		}


		/**
		 * Decodes the packed hex data in the database
		 *
		 * @param string  $hex_code [six number separated by underscore]
		 * @return array  of values for rostered and total patients
		 * Note that the order of values in the pack is total first
		 * and then rostered.
		 * */
		private function d2d_decode( $hex_code ) {
			$stats_set = explode( '_', $hex_code );
			$stats = array(
				'rostered' => $stats_set[5],
				'total'    => $stats_set[2]
			);
			return $stats;
		}
		// Create the shortcode for this plugin

		public function data_shortcode() {
			ob_start();
			$this -> manage_data();
			return ob_get_clean();
		}

		public function get_quality_indicator($indicator,  $iteration){
			$record_id = $indicator . '_' .  str_replace('D2D ', '', $iteration);
			return $this -> quality_table[$record_id];
		}

		// private function exportQualityToCSV(){
		// 	// output headers so that the file is downloaded rather than displayed
		//      // header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		//      // header( 'Content-Description: File Transfer' );
		//      // header( 'Content-type: application/csv' );
		//      // header( 'Content-Disposition: attachment; filename="MyDataFile.csv"' );
		//      // header( 'Expires: 0' );
		//      // header( 'Pragma: public' );
		// 	ob_start();
		// 	     // Set header row values
		//     $csv_fields=array();
		//     $csv_fields[] = 'Team';
		//     $csv_fields[] = 'Iteration';
		//     $csv_fields[] = 'Overall';
		//     $csv_fields[] = 'Access';
		//     $csv_fields[] = 'Sensitivity';
		//     $csv_fields[] = 'Trust';
		//     $csv_fields[] = 'Knowledge';
		//     $csv_fields[] = 'Commitment';
		//     $csv_fields[] = 'Collaboration';
		// 	// create a file pointer connected to the output stream
		// 	$output = @fopen('wp-content/uploads/export.csv', 'w') or show_error("Can't open php://output");
		// 	echo $output;
		// 	// The results for every team is already available
		// 	// in $this -> total_results
		// 	echo '<table>';
		// 	for ($i = 0; $i < count($csv_fields); $i++){
		// 		echo '<th>' . $csv_fields[$i] .'</th>';
		// 	};
		// 	$qual_inds_array = array();

		// 	echo '</table>';




		// 	// output the column headings
		// 	fputcsv($output, $csv_fields);

		// 	fputcsv($output, array("Text1", "Text2") ); 
		// 	fclose($output);
		// 	$csvStr = ob_get_contents(); // + "csv";
		// 	ob_end_clean();

		// 	echo $csvStr;
		// } 


	}
}




if ( class_exists( 'D2D_manage_data' ) ) {
	$D2D_manage_data = new D2D_manage_data();
}