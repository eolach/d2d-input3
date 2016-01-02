<?php
/**
 *
 *
 * @author Neil
 * @version 0.5
 * @created 17-Apr-2015 8:54:10 PM
 * @updated 15-May-2015
 */
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

// require_once D2D_INPUT_ABSPATH . 'includes/d2d-form.php';

if ( !class_exists( 'D2D_review' ) ) {
	class D2D_review {

		public $chartList = array();
		public $values = "Null";

		public function __construct() {
			add_shortcode( 'review data', array( $this, 'data_shortcode' ) );
			add_shortcode( 'get review data', array( $this, 'test_data_shortcode' ) );
		}

		public function process_get_data(){
			if ( class_exists( 'D2D_fetch_data' ) ) {
				$D2D_fetch_data = new D2D_fetch_data();
			}

			$D2D_fetch_data  -> test_wp_get_data();		
		}

		/**
		 * Lays out the tabbed portion of the review page
		 * Within the major tabs, calls the chart shortcodes that display the charts within the relevant 
		 * divs of the HTML pages. The upper portion is handles by main_page.php.
		 * The tab funtionality is managed within the javascript file in js/d2d_js.js
		 * The table option within the core d2d tab is managed by a separate function below
		 * @return none.
		 */
		public function process_functions(){
			include( D2D_REVIEW_ABSPATH . 'includes/main_page.php');
				
			echo '<div class="tab-content">';
				
			//  Layout the composite indicators
				echo '<div id="tab1" class="tab active">';
					echo '<center><b>Roll-up indicators</b></center>';
					// Select drill down or roll up
					
					// Select data set for quality
					echo '<div id="drill_qual">';
						echo '<center><a id="drill-roll-qual" href="#level1q" style="width:48%; float: left">
							Click to drill down - Quality</a></center>';
					echo '</div>';

					echo '<div id="drill_cost">';
						echo '<center><a id="drill-roll-cost" href="#level1c" style="width:48%">
							Click to drill down - Cost</a></center>';
					echo '</div>';
					
					echo '<div id="select_d2d">';
						echo '<select name="sel_data">';
							echo '<option value="d2d_only" float: right>Teams with core data only</option>';
							echo '<option value="extended" selected="checked"; >Teams with core and expanded data</option>';
						echo '</select>';
					echo '</div>';
					
					// Lay out the rollup charts
					echo '<div id="level1q" class="tab active">';
						echo amcharts_shortcode(
							array(
								'id' => 'quality_rollup'
							)
						);
					echo '</div>';

					echo '<div id="level2q" class="tab">';
						echo amcharts_shortcode(
							array(
								'id' => 'quality_drill'
							)
						);
					echo '</div>';

					// Lay out the drill down charts
					echo '<div id="level1c" class="tab active">';
						echo amcharts_shortcode(
							array(
								'id' => 'cost_rollup'
							)
						);
					echo '</div>';

					echo '<div id="level2c" class="tab">';
						echo amcharts_shortcode(
							array(
								'id' => 'cost_drill'
							)
						);
					echo '</div>';

					echo '<div id="message">';
						echo '<center><h2>Capacity indicator</h2></center>
							<p>To achieve AFHTO’s vision  that all Ontarians have timely 
							access to high-quality, comprehensive, inter-professional 
							primary care, we need to know how much capacity we have to 
							provide that care.  Discussions are underway to refine 
							the definition of a measure and develop processes to access 
							that data for subsequent iterations of D2D.';
					echo '</div>';

				echo '</div>';

				//  Lay out the performance indicators
				echo '<div id="tab2" class="tab">';
					echo '<div id="tab2_title"><center><b>Core D2D indicators X.0</b></center></div>';
					//  Select
					
					// Select teams or trend
					echo '<div id="tables">';
						echo'<center><a id="table_view" href="#teams">Click for table view</a></center>';
					echo '</div>';
					echo '<div id="trends">';
						echo'<center><a id="teams_trend" href="#teams">Click to view my team\'s data over iterations</a></center>';
					echo '</div>';

					// echo '<div>';
					// 	echo '<select name="rost_all">';
					// 		echo '<option value="rostered" selected="checked">Rostered patients</option>';
					// 		echo '<option value="total">All patients</option>';
					// 	echo '</select>';
					// echo '</div>';
					
					// Lay out the teams
					echo '<div id="teams" class="tab active">';
						// echo '<div>';
							echo amcharts_shortcode(
								array(
									'id' => 'patient_centered'
								)
							);
							echo amcharts_shortcode(
								array(
									'id' => 'effectiveness'
								)
							);
						// echo '</div>';

						// echo '<div>';
							echo amcharts_shortcode(
								array(
									'id' => 'access'
								)
							);
							echo amcharts_shortcode(
								array(
									'id' => 'integration'
								)
							);
						// echo '</div>';
					echo '</div>';


					// Lay out the trends
					echo '<div id="trend" class="tab">';
						// echo '<div id="top_charts">';
							echo amcharts_shortcode(
								array(
									'id' => 'pat_cent_trend'
								)
							);
							echo  amcharts_shortcode(
								array(
									'id' => 'effect_trend'
								)
							);
						// echo '</div>';

						// echo '<div id="bottom_charts">';
							echo amcharts_shortcode(
								array(
									'id' => 'access_trend'
								)
							);
							echo amcharts_shortcode(
								array(
									'id' => 'integration_trend'
								)
							);
						// echo '</div>';
					echo '</div>';

					// Lay out the trends
					echo '<div id="table" class="tab">';

					echo '<table id="review_table">';
					echo '<thead><tr><th >PCPMF Domain</th>
					<th>Core D2D indicators</th>
					<th>Team</th>
					<th>Peer</th>
					<th>Peer N</th>
					<th>Peer SAMI</th>
					<th>D2D</th>
					<th>D2D N</th>
					<th>D2D SAMI</th>
					<th>D2D range</td></th></thead>';

					echo '<tbody><tr>';

					echo '</tr></tbody>';

					echo '</table>';


					echo '</div>';


				echo '</div>' ;
			echo '</div>';
		echo '</div>';
		}

		

			// foreach ( $indicator_labels as $ind){
			// 	array_push($inds, $ind['indicator']);
			// 	array_push($links, $ind['hyperlink']);
			// 	$val_array = array();
			// 	// Distinguihing between hex and simple indicators
			// 	if ( $type == 'hex' ){
			// 		$val_array[] = number_format( $this -> d2d_values[$ind ['short_label']]['team'][$this -> rost_all], 1)  ;
			// 		$val_array[] = number_format( $this -> d2d_values[$ind ['short_label']]['peers'][$this -> rost_all], 1 ) ;
			// 		$val_array[] = number_format( $this -> d2d_values[$ind ['short_label']]['total'][$this -> rost_all], 1 ) ;



		/**
		 * Build the table using the values calculated in the d2d_fetch_data object in d2d-get-data.php
		 * @return array(array) Array of indicator values for each of the fixed indicators in the table.
		 */
		// private function build_table(){
		// 	// $values = $D2D_fetch_data -> d2d_values;
		// 	echo 'Printing values';
		// 	echo ($this -> values);
		// 	$t_view = array();
		// 	$t_row = array(
		// 		'PCPMF'   => 'Effectiveness',
		// 		'CoreD2D' => 'Cervical Ca screening',
		// 		'Team'    => 70,
		// 		'Peer'    => 75,
		// 		'Peer_N'  => NULL,
		// 		'Peer_SAMI' => NULL,
		// 		'D2D'     => 80,
		// 		'D2D_N'   => NULL,
		// 		'D2D_SAMI'    => NULL,
		// 		'D2D_range'    => 75
		// 		);
		// 	array_push($t_view, $t_row);
		// 	return $t_view;
		// }

		public function build_chart_list(){

			$this -> chartList = array(
				'qual_cost' => array(
					'chart1' => 'quality_rollup',
					'chart2' => 'cost_rollup',
					'chart3' => 'quality_drilldown',
					'chart4' => 'cost_drill_down'
					),
				'performance' => array(
					'chart1' => 'patient_centered',
					'chart2' => 'effectiveness',
					'chart3' => 'access',
					'chart4' => 'integration'
					)
				);
		}


		// Create the shortcode for this plugin
		public function data_shortcode() {
			ob_start();
			$this -> process_functions();
			return ob_get_clean();
		}
		public function test_data_shortcode() {
			ob_start();
			$this -> process_get_data();
			return ob_get_clean();
		}
	}
}

if ( class_exists( 'D2D_review' ) ) {
	$D2D_review = new D2D_review();
}