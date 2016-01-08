
		<!-- first row -->
	<div class="no_chevron col-md-8 col-sm-12 col-xs-12">
		<input type="text" 
		class="new_code" name="team_code" value="" size="11" align="right">&nbsp;Type team code and press <b>Enter</b></div>
	<!-- <hr style="height: 0px; width: 99%; margin:0 auto;line-height:2px;background-color: #848484; border:0 none;"/> -->
		<!-- Left hand box -->
	<div class="no_chevron col-md-4 col-sm-12 col-xs-12">
		Iteration:	<select name ="year_code">
			<option value="D2D 1.0">D2D 1.0</option>
			<option value="D2D 2.0">D2D 2.0</option>
			<option value="D2D 3.0" selected="checked">D2D 3.0</option>
			</select>
	</div>
	<div class="no_chevron col-md-6 col-sm-3 col-xs-3">
	<div >Setting:
		<select name="review_setting">
			<option value="none" selected="checked">--</option>
			<option value="Urban">Urban</option>
			<option value="Rural">Rural</option>
		</select>	
	</div>
	<div >Teaching:
		<select name="review_teaching">
			<option value="none" selected="checked">--</option>
			<option value="Academic" >Academic</option>
			<option value="Teaching">Teaching</option>
			<option value="Non-teaching">Non-tchg</option>
		</select>
	</div>
	<div >Access to hosp. discharge data:</li>
		<select name="review_hosp_emr">
			<option value="none" selected="checked">--</option>
			<option value="No" >No</option>
			<option value="Yes">Yes</option>
		</select>	
	</div>
	<div>Rostered patients:</li>
		<select name="review_patients">
					<option value="none" >--</option>
					<option value="lt_10k" >Fewer than 10,000</option>
					<option value="10k_30k">10,000 to 30,000</option>
					<option value="gt_30k">More than 30,000</option>
				</select>
	</div>
</div>
	<div class="d2d_review_table col-md-6 col-sm-4 col-xs-4" ><!--style="width:40%; float:left"> -->
		<table ><!--style="width:250px" border="0"-->
		<tr>
			<td></td>
			<td>Team</td>
			<td>Peer</td>
			<td>D2D</td>
		</tr>
		  <tr>
		    <td><a href="http://www.afhto.ca/members-only/sami-score/ ">SAMI</td>
		    <td id="team_sami_score">--</td>
		    <td id="peer_sami_score">--</td>
		    <td id="total_sami_score">--</td>
		  </tr>
		  <tr>
		    <td><a  href = 'http://www.afhto.ca/members-only/emr-data-quality '>Data Quality</td>
		    <td id="team_qual_score">--</td> 
		    <td id="peer_qual_score">--</td> 
		    <td id="total_qual_score">--</td> 
		</table>	
	</div>

	<hr style="height: 2px; width: 99%; margin:0 auto;line-height:2px;background-color: #848484; border:0 none;"/>
	<div class="tabs col-md-9 col-sm-11 col-xs-12" style="background-color: ffffff">
    <p></p>
    <div class="tab-links" style="display:inline"> 
        <li class="active"><a class="d2d" href="#tab1">Roll-up</a></li>
        <li class="inactive"><a class="d2d" href="#tab2">Core D2D</a></li>
<!--     </ul>
    <ul class="tab-extra">
-->	    <li class="d2d-extra"><a class="d2d-ext" target="_blank" href="http://www.afhto.ca/members-only/d2d-exploratory-indicator">Time spent</a></li>
        <li class="d2d-extra"><a class="d2d-ext" target="_blank" href="http://www.afhto.ca/members-only/comparative-data-for-d2d/">Comparative data</a></li>
    </div>


