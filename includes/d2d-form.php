<?php
/**
 *
 *
 * @author Neil
 * @version 0.8
 * @created 17-Apr-2015 8:52:14 PM
 * @updated 23-Apr-2015
 */
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );


class D2D_form {

	/**
	 * @param 	$indicators  points to the set of indicators passed to the class
	 * @param   $form_indicators  is a list of the short_labels that belong to a given form
	 * @param   $owner points to the main class that owns the form
	 */
	private $platform; // This is the main class for the platform
	public $old_year_code;
	public $old_team_code;
	public $indicators; // Pointer to the basic set of indicators
	public $owner;
	public $form_indicators = array();

	function __construct( $set, $p_owner, $group = NULL) {
		$this -> indicators = $set;
		$this -> owner = $p_owner;
		$this -> add_indicators($group);
		// echo 'RateQ button ' . $parent -> rates_quality_flag . '<p></p>';
	}

	public function __destruct() {
	}

	public function render_form() {

	}

	/**
	 * Create and add indicators for a given group
	 *
	 * @param string  $group a type of form designated in the "indicator_group"
	 * of an indicator specification
	 */
	protected function add_indicators( $group ) {
		// echo 'Adding indicator for ' . $group . '<p></p>';set_value
		foreach ( $this -> indicators as $ind ) {
			// Find the indicators of this group type
			if ( $ind -> indicator_group == $group ) {
				// Add the name of the indicator to the list for this form
				$this -> form_indicators[$ind -> short_label] = $ind;

			}
		}
	}

	public function changeYear() {
	}

	public function switchToQualitySet() {
	}

	public function switchToRateSet() {
	}
}

// Control forms
class D2D_team_form extends D2D_form {

	public $year_list = array();
	public $load_button_label = "Load data";

	public function __construct( $set, $p_owner, $group ) {
		parent::__construct( $set, $p_owner, $group );
		// parent::add_indicators( 'header' );
		$this -> year_list = $p_owner -> indicator_set['year_code'];
 	}

	public function render_form( $isNew ) {

		// if ( !$this -> owner ->  short_form ){
			$echo_out2 = '&nbsp&nbsp&nbsp&nbsp<input type="submit" name="form_submitted" value="Submit final data">';
		// }
		
		// $load_button_label = ( ($p_owner -> new_record) or ( !$this -> valid_team and !$this -> first_time ) ) ? 
		// "Load data" : "Update data";

		echo '<form style="background-color: #eeede6" action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
		echo '<input type="hidden" name="first_time" value="' . $this -> first_time . '">';
		echo '<input type="hidden" name="short_form" value="' . $p_owner -> short_form . '">';
		echo '<input type="hidden" name="old_team_code" value="' .  $this -> old_team_code . '">';
		echo '<input type="hidden" name="old_year_code" value="' .  $this -> old_year_code . '">';
		echo '<input type="hidden" name="save_status" value="' .  $this -> owner -> save_status . '"><p>';
		
		if ( $isNew ) {
			
			echo 'Enter <em>Team Code</em> and select an <em>Iteration</em>';		
			echo '<p>Team code: <input type="text" name="team_code" value="' . $this -> owner -> team_code  . '" size="10" />
			&nbsp;&nbsp;&nbsp;Iteration: <select name ="year_code">';
			$this -> year_list -> display_in_form();
			echo '</select>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="update" value="' . $this -> load_button_label . '">';
		} else {
			
				echo '<input type="hidden" name="id" value="' .  $this -> owner -> id . '"><p>';
				echo '<input type="hidden" name="team_code" value="' .  $this -> owner -> team_code . '">';
				echo '<input type="hidden" name="year_code" value="' .  $this -> owner -> my_year . '"><p>';
				echo '<p><p>Team code: <b>' . $this -> owner -> team_code  . '</b>&nbsp;&nbsp;&nbsp;Iteration:<b>&nbsp;&nbsp;&nbsp;' . $this -> owner -> my_year  . '</b>';
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="update" value="Save this data">' . $echo_out2;
				echo '<input type="submit" name="new_record" value="Load new record">';
		}
		// echo '</form>';
		
	}
}

class D2D_profile_form extends D2D_form {

	public function render_form() {

// echo "Rendering profile form</br>";

		echo '<p>';
		foreach ( $this-> form_indicators as $ind ) {
// var_dump($ind -> options);			
			echo  $ind -> full_label . ': ';
			echo '<select name = "' . $ind -> short_label . '">';
				 $ind -> display_in_form();
			echo '</select>&nbsp;&nbsp;&nbsp;&nbsp';
		}
		echo '</p>';
	}
}

class D2D_new_team_form extends D2D_form {

	public function render_form( $post_code ) {

		echo '<form  action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
		echo '<div class="d2dcontentHeader"><h1>' . $this -> text_message . '</h1></div>';
		echo '<p>&nbsp<input type="submit" name="register" value="Register">';
		echo '&nbsp<input type="submit" name="cancel_submission" value="Cancel">';
		echo '<input type="hidden" name="team_code" value="' .  $this -> owner -> team_code . '">';
		echo '<input type="hidden" name="year_code" value="' .  $this -> owner -> my_year . '">';
		echo '</form>';
	}
}

class D2D_quality_summary_form extends D2D_form {

	public $quality_value;


	public function set_quality_value( $q ){
		$quality_value = $q ;
	}

	public function render_form( $qr ) {
		$this -> set_quality_value(0.7);
		// $this -> owner -> rates_quality_flag = 'quality';
		// The flag $qr corresponds to the values of the rates_quality_flag
		// i.e. false brings up the rates page, and true brings up the quality page.
		// The difference here is the use of the return button.
		if ( $qr == 'quality' ) {
			$echo_out = 'name="rate" value="Enter Core D2D Indicators"';
		} else {
			$echo_out = 'name = "quality" value="Enter Expanded Data Submission"';
		}

		// The following code was to have been moved to the bottom of the core data and the expanded data forms 
		// but the structure of the forms did not allow it.
		$ind = $this -> form_indicators['quality_agree'];
		echo '<select name = "' . $ind -> short_label . '">';
			  $ind -> display_in_form();
		echo '</select>';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;Please indicate if your data may be included in the quality roll-up indicator';


		echo '<div id = "team_table">';
		echo '<table style="font-size: 14px">';
		// echo '<tr><th>Team Indicator</th><th class="d2dinputcoldata">Value</th><th></th><th></th><th></th></tr>';
		echo '<tr><td></td><td >' . $this -> quality_value  . '</td>
			<td colspan="2"><input class="d2dinputcolwords" type="submit" ' . $echo_out .'></td><td></td></tr>
            </table>';
        // }
	}
}

class D2D_data_form extends D2D_form {

	public function render_form() {
		echo '<h5>Teams participating in D2D can choose to submit for one or more indicators, 
		based on your team’s readiness to contribute data. 	The list and sources of data are outlined in the 
		<a href="http://www.afhto.ca/members-only/d2d-data-dictionary/" target="_blank">data dictionary</a>. 
		In the chart below, the rate will be automatically calculated from the numerator and denominator. 
		If you do not have the numerator and denominator, input the rate directly into the rate field.</h5>';
		echo '<table style="font-size: 14px"class="d2dinputtable"><tr><th>Indicator</th>
		<th>Value</th><th></th><th></th><th></th><th></th></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ($ind -> menu_class == 'top'){
				$ind -> display_in_form();
			}
		}
		echo '</table>';
		// echo '<p>Teams participating in D2D can choose to submit for one or more indicators, based on your team’s readiness to contribute data. <br />
		// The list and sources of data are outlined in the data dictionary. <br />
		// In the chart below, the rate will be automatically calculated from the numerator and denominator. 
		// If you do not have the numerator and denominator, input the rate directly into the rate field.</p>';

	}
}

class D2D_rostered_rate_form extends D2D_form {

	Private $group;

	public function __construct( $set, $p_owner, $group ) {
		parent::__construct( $set, $p_owner, $group );
		$this -> group = $group;
	}

	public function render_form() {
		switch ($this -> group){
			case 'rate':
				$this -> render_rate_header();
				$this -> render_rate_body();
				$this -> render_rate_footer();
				break;
			case 'quality':
				$this -> render_quality_header();
				$this -> render_quality_body();
				$this -> render_quality_footer();
				break;
		}
	}

	private function render_rate_header(){
		echo '<input type="hidden" name="rates_quality_flag" value="rate"><p>';
		echo '<table style="font-size: 14px"style="table-layout: fixed">';
		echo '<tr><th style="width: 120px"></th><th style="padding: 0px; text-align: center; 
		border-right: 2px solid #000" colspan = "3" ><centre>Rostered Patients</centre></th>';
		// <th style="padding: 0px; width:2px">  </th>
		echo '<th style="padding: 0px; text-align: center;" colspan = "3">Patients Served</th></tr>';

		echo '<tr><th style="padding: 0px;">Indicator</th>
		<th style="padding: 0px; width: 70px">Numerator</th>
		<th style="padding: 0px; width: 70px">Denominator</th>
		<th style="padding: 0px; width: 70px; border-right: 2px solid #000;">Rate (%)</th>';
		// <td style="padding: 0px; width:2px "></td>
		echo '<th style="padding: 0px; width: 70px">Numerator</th>
		<th style="padding: 0px; width: 70px">Denominator</th>
		<th style="padding: 0px; width: 70px">Rate (%)</th></tr>';
	}

	private function render_rate_body(){
		echo '<tr><td colspan="7" style="background-color:#E0E0E0"><b><em>Patient experience surveys</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "pat_exp"){
				$ind -> display_in_form();
			}
		}
		echo '<tr><td colspan="7" style="background-color:#E0E0E0"><b><em>Data from ICES (access through HQO Primary Care Practice Report)</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "ICES"){
				$ind -> display_in_form();
			}
		}
		echo '<tr><td colspan="7" style="background-color:#E0E0E0"><b><em>Data from EMR</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "emr"){
				$ind -> display_in_form();
			}
		}
		echo '<tr><td colspan="7" style="background-color:#E0E0E0"><b>
		<em>Diabetes from EMR (expressed as a numeral, e.g. 0.75)</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "diab"){
				$ind -> display_in_form();
			}
		}
	}

	private function render_rate_footer(){

		// echo '</table>';
		// echo '<table style="font-size: 14px">';
		echo '<tr><td colspan="7" style="background-color:#E0E0E0"><b>
		<em>EMR Data Quality (expressed as a numeral, e.g. 0.75)</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ($ind -> menu_class == 'data_qual'){
				$ind -> display_in_form();
			}
		}
		echo '<tr><td colspan="7" style="background-color:#E0E0E0"><b>
		<em>Time spent delivering primary care (hours) - pre-cursor to capacity</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "qual"){
				$ind -> display_in_form();
			}
		}
		echo '</table>';
		echo "<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Description of team's approach to <em>Capacity Indicator</em></b>";
		echo '<p><textarea name="X7_day_text" rows="5" cols="80">' . $this -> form_indicators['X7_day_text'] -> text  . '</textarea>';

		echo '</form>';

		// This code was moved from the upper level of the form to the bottom 
		// $ind = $this -> form_indicators['quality_agree'];
		// echo '<select name = "' . $ind -> short_label . '">';
		// 	  $ind -> display_in_form();
		// echo '</select>';
		// echo '&nbsp;&nbsp;&nbsp;&nbsp;Please indicate if your data may be included in the quality roll-up indicator';



		$echo_out2 = '&nbsp&nbsp&nbsp&nbsp<input type="submit" name="form_submitted" value="Submit final data">';
		echo '&nbsp;&nbsp;&nbsp;<input type="submit" name="update" value="Save this data">' . $echo_out2;

	}

	private function render_quality_header(){
		echo '<input type="hidden" name="rates_quality_flag" value="quality"><p>';
		echo '<table style="font-size: 14px">';
		echo '<tr><th style="width: 120px"></th><th style="padding: 0px; text-align: center; 
		border-right: 2px solid #000" colspan = "3" ><centre>Rostered Patients</centre></th>';
		// <th style="padding: 0px; width:2px">  </th>
		echo '<th style="padding: 0px; text-align: center;" colspan = "3">Patients Served</th></tr>';
		echo '<tr><th style="padding: 0px;">Indicator</th>
		<th style="padding: 0px; width: 70px">Numerator</th>
		<th style="padding: 0px; width: 70px">Denominator</th>
		<th style="padding: 0px; width: 70px; border-right: 2px solid #000;">Rate (%)</th>';
		// <td style="padding: 0px; width:2px "></td>
		echo '<th style="padding: 0px; width: 70px">Numerator</th>
		<th style="padding: 0px; width: 70px">Denominator</th>
		<th style="padding: 0px; width: 70px">Rate (%)</th></tr>';

	}

	private function render_quality_body(){
		echo '<tr><td colspan="7" style="background-color:#E0E0E0"><b><em>Patient Experience Survey</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "pat_exp"){
				$ind -> display_in_form();
			}
		}
		echo '<tr><td colspan="7" style="background-color:#E0E0E0"><b><em>Data from ICES (access through HQO Primary Care Practice Report)</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "ICES"){
				$ind -> display_in_form();
			}
		}
		echo '<tr><td colspan="7" style="background-color:#E0E0E0"><b><em>Data from EMR</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "emr"){
				$ind -> display_in_form();
			}
		}
		echo '<tr><td colspan="7" style="background-color:#E0E0E0"><b><em>Data from EMR (Review of patients in registries)</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "emr_reg"){
				$ind -> display_in_form();
			}
		}
		echo '<tr><td colspan="7" style="background-color:#E0E0E0"><b><em>Heath data branch portal</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "hbp"){
				$ind -> display_in_form();
			}
		}
		// echo '<tr><td colspan="7" style="background-color:#E0E0E0"><b><em>Direct input from team</em></b></td></tr>';
		// foreach ( $this -> form_indicators as $ind ) {
			// if ( $ind -> menu_class == "di_team"){
				// $ind -> display_in_form();
			// }
		// }

	}

	private function render_quality_footer(){
			echo '</table>';
	}


}

class D2D_rate_form extends D2D_form {

	public function render_form() {
		echo 'These are interim indicators. All indicators are subject to change';
		echo '<table style="font-size: 14px">';
		echo '<tr><th>Quality Roll-up Indicator</th><th>Numerator</th>
		<th>Denominator</th><th>Rate (%)</th></tr>';
		echo '<tr><td colspan="4" style="background-color:#E0E0E0"><b><em>Patient Experience Survey</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "pat_exp"){
				$ind -> display_in_form();
			}
		}
		echo '<tr><td colspan="4" style="background-color:#E0E0E0"><b><em>Data from ICES (access through HQO Primary Care Practice Report)</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "ICES"){
				$ind -> display_in_form();
			}
		}
		echo '<tr><td colspan="4" style="background-color:#E0E0E0"><b><em>Data from EMR</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "emr"){
				$ind -> display_in_form();
			}
		}
		echo '<tr><td colspan="4" style="background-color:#E0E0E0"><b><em>Data from QIP</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "qip"){
				$ind -> display_in_form();
			}
		}
		echo '<tr><td colspan="4" style="background-color:#E0E0E0"><b><em>ED direct input</em></b></td></tr>';
		foreach ( $this -> form_indicators as $ind ) {
			if ( $ind -> menu_class == "ed"){
				$ind -> display_in_form();
			}
		}

		echo '</table>';
	}
}

class D2D_submit_form extends D2D_form {
	public function render_form( $flag ) {

echo "Rendering submit form";

		echo '<form  action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
		echo '<input type="hidden" name="team_code" value="' . $this -> owner -> team_code . '">';
		echo '<input type="hidden" name="save_status" value="' . $this -> owner -> save_status . '">';
		echo '<input type="hidden" name="year_code" value="' . $this -> owner -> my_year . '">';

		if ( $flag ){
			$this -> text_message = "<p>You are about to submit your team's indicator data 
			for inclusion in the AFHTO D2D database." .	' Once submitted, the data can no longer be modified.
				<p>If you do not wish to submit, please "Cancel" now.
				<p>If you wish to continue, please review the data one last time, 
				and press "Confirm" to proceed with submission </p>';
			echo '<div><h4>' . $this -> text_message . '</h4></div>';
			echo '&nbsp<input type="submit" name="confirm_submit" value="Confirm">';
			echo '&nbsp<input type="submit" name="cancel_submission" value="Cancel"><p></p>';
		} else {
			$this -> text_message = "There are problems with the data that prevent you from 
			submitting your team's indicator data for inclusion in the AFHTO D2D database.<p>" .
			'Please review the data again, and press "Submit" to re-submit the data.<br />
			You may press "Cancel" to save the data for now and return to it later';
			echo '<div><h4>' . $this -> text_message . '</h4></div>';
			echo '&nbsp<input type="submit" name="form_submitted" value="Submit">';
			echo '&nbsp<input type="submit" name="cancel_submission" value="Cancel">';
		}
		echo '</form>';
	}
}

class D2D_confirm_form extends D2D_form {

	public function render_form(  ) {

echo "Rendering confirm form";

		echo '<form  action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
		echo '<input type="hidden" name="team_code" value="' . $this -> owner -> team_code . '">';
		echo '<input type="hidden" name="save_status" value="' . $this -> owner -> save_status . '">';
		echo '<input type="hidden" name="year_code" value="' . $this -> owner -> my_year . '">';
		
		$this -> text_message = '
		Has your team gone through the necessary team-based approval process to 
		submit data to D2D? <em>(Mandatory)</em>';
		echo '<div><h4>' . $this -> text_message . '&nbsp&nbsp&nbsp';
		$ind = $this -> indicators['confirm_review'];
		echo '<select name = "' . $ind -> short_label . '">';
			  $ind -> display_in_form();
		echo '</select>';
		echo '</h4></div>';
		


		$this -> text_message = '
		Would you be willing to share your team name with fellow teams if they are 
		interested in discussing particular indicators? Your team name would never 
		be shared with anyone without asking your permission first. <em>(Optional)</em>';
		echo '<div><h4>' . $this -> text_message . '&nbsp&nbsp&nbsp';
		$ind = $this -> indicators['share_agree'];
		echo '<select name = "' . $ind -> short_label . '">';
			  $ind -> display_in_form();
		echo '</select>';
		echo '</h4></div>';
		echo '&nbsp&nbsp<input type="submit" name="send_data" value="Send data">
		&nbsp&nbsp<input type="submit" name="cancel_submission" value="Cancel">';
		echo '<input type="hidden" name="message" value="Message sent">';
		echo '</form>';
	}
}

class D2D_control_form extends D2D_form {

	public function render_form() {

echo "Rendering control form";

		echo '<p><input type="submit" name="update" value="Update">';
		echo '&nbsp<input type="submit" name="form_submitted" value="Submit">';
		echo '&nbsp<input type="submit" name="rate" value="Back to rates">';
		echo '</form>';
	}
}

