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

		public function manage_data(){
			$this -> retrieve_data();
			$this -> prepare_data_table_header();
			$this -> build_data_table();
			$this -> impute_data();
			$this -> display_data_table();
			
			$this -> prepare_quality_table_header();
			// $this -> build_quality_table();
			$this -> display_quality_table();
		}

		/**
		 * Retrieve individual team data from the indicators database table
		 * Retrieve the active table of weights and levels
		 * This provides the list of indicators used in the composite indicators
		 * and the composite indicators calculated.
		 * @return [array] All locked team results as an array and weights as array.
		 */
		public function retrieve_data(){

			$sql_team = 'SELECT * FROM indicators WHERE save_status = "locked" AND year_code = "D2D 2.0"';
			$sql_weights = 'SELECT * FROM d2d_weights3_0';

			global $wpdb;
			$this -> team_results = $wpdb -> get_results( $sql_team, ARRAY_A );
			$this -> weights = $wpdb -> get_results( $sql_weights, ARRAY_A );
		}

// Data table

		public function prepare_data_table_header(){
			// Retrieve compositie variables as first column of weights array
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
					if (is_null($team[$var]) ){
						$team_row[$var] = null;
					} else {
						$numeric_var = $this -> d2d_decode($team[$var]);
						$team_row[$var] = $numeric_var['total'];
					}
				}
				$this -> data_table[$team['team_code']] = $team_row;
			}


		}

		public function impute_data(){
			$data_table_keys = array_keys($this -> data_table);
			// print_r($data_table_keys);
			foreach($this -> data_vars as $var){
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
							$this -> data_table[$key][$var] = 
							'*' . $valid_var_table[$imputed_key];						;
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
			for ($i = 0; $i < 6; $i++){
				$this -> quality_keys[$i] = $q_keys[$i + $offset];
			}

		}

		public function build_quality_table($val){
			$new_row = array();
			foreach ($this -> quality_keys as $key){
				$new_row[$key] = $val;
			}
			$this -> quality_table[] = $new_row[$key];
		}

		public function display_quality_table(){
			echo '<table>';
			foreach($this -> quality_keys  as $ind){
				echo '<th>' . $ind .'</th>';
			};
			foreach($this-> quality_table as $qual_row){
				echo '<tr>';
				foreach($this -> quality_keys  as $ind){
					echo '<td>' . count($this-> quality_table). '</td>';
				}
				echo '</tr>';
			}
			echo '</table>';
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
	}

}




if ( class_exists( 'D2D_manage_data' ) ) {
	$D2D_manage_data = new D2D_manage_data();
}