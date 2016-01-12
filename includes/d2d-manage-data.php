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
		public $composite_vars = array(); // Indicators from which quality scores are calculater
		public $quality_keys = array();
		public $quality_inds = array(); //Six plus one indicators of quality
		public $data_table = array();
		public $quality_table = array();

		public function __construct() {
			add_shortcode( 'manage data', array( $this, 'data_shortcode' ) );
		}

		public function manage_data(){
			$this -> retrieve_data();
			$this -> prepare_composite_table_headers();
			$this -> prepare_quality_table();
			$this -> display_composite_table();
			$this -> display_quality_table();
			$this -> add_row_to_quality_table(22);
			print_r($this -> quality_table);
			$this -> add_row_to_quality_table(33);
			print_r($this -> quality_table);
		}

		/**
		 * Retrieve individual team data from the indicators database table
		 * Retrieve the active table of weights and levels
		 * This provides the list of indicators used in the composite indicators
		 * and the composite indicators calculated.
		 * @return [array] All locked team results as an array and weights as array.
		 */
		public function retrieve_data(){

			$sql_team = 'SELECT * FROM indicators WHERE save_status = "locked"';
			$sql_weights = 'SELECT * FROM d2d_weights3_0';

			global $wpdb;
			$this -> team_results = $wpdb -> get_results( $sql_team, ARRAY_A );
			$this -> weights = $wpdb -> get_results( $sql_weights, ARRAY_A );
		}

		public function prepare_composite_table_headers(){
			// Retrieve compositie variables as first column of weights array
			foreach($this -> weights as $row){
				array_push($this -> composite_vars, $row['short_label']);
			}
			// print_r($this -> composite_vars);
		}
			
		public function prepare_quality_table(){
			// Retrievequality variables as a subset of the columns of the weights
			$q_keys = array_keys($this -> weights[0]);
			$offset = 4;
			for ($i = 0; $i < 6; $i++){
				$this -> quality_keys[$i] = $q_keys[$i + $offset];
			}

		}
		
		public function add_row_to_quality_table($val){
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
		public function display_composite_table(){
			echo '<table>';
			foreach($this -> composite_vars  as $ind){
				echo '<th>' . $ind .'</th>';
			};
			echo '</table>';
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