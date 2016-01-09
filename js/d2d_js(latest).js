
jQuery(document).ready( function($) {
	
    // 
    // // Handler for startup - loads default settings of the form
//
    myHandler2();

    function myHandler1( event ){
        // event.preventDefault();

        var table_data = 678;

        var selected_option = $(this).find( 'option:selected' ).text();
     
        var data = {
            action: 'get_new_data',
            team_code: $("INPUT[name='team_code']").val(),
            setting: $("[name='review_setting']").find( 'option:selected' ).val(),
            year_code: $("[name='year_code']").find( 'option:selected' ).val(),
            teaching: $("[name='review_teaching']").find( 'option:selected' ).val(),
            hosp_emr: $("[name='review_hosp_emr']").find( 'option:selected' ).val(),
            num_pts: $("[name='review_patients']").find( 'option:selected' ).val(), 
            core_only: $("[name='sel_data']").find( 'option:selected' ).val() 
            // rost_all: $("[name='rost_all']").find( 'option:selected' ).text()     
        };
        // the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
        $.post(ajax_object.ajaxurl, data, function(response) {
            
// alert("Handler 2 " + selected_option);

            if (response['fail_code'] == 'not_found' ){
                // $('#code_here').val('Team code <em>"' + response["team_code"] + '"</em> not found. Try again.');
                // $("INPUT[name='team_code']").blur();
                // $("INPUT[name='team_code']").val('');
                // $("INPUT[name='team_code']").focus();
                alert("Team code " + response["team_code"] + ' was not found');
             } else {      
                 // alert("table 1 " + response[15]);
               for (i = 0; i < 12 ; i++){
                var this_chart =  eval('amchart' + (i + 1));
                this_chart.dataProvider = response[i];
                this_chart.validateData();
                this_chart.animateAgain();
                this_chart.invalidateSize();
                }
                $('#team_qual_score').html( response[13]['team']);
                $('#peer_qual_score').html( response[13]['peers']);
                $('#total_qual_score').html( response[13]['total']);
                $('#team_sami_score').html( response[12]['team']);
                $('#peer_sami_score').html( response[12]['peers']);
                $('#total_sami_score').html( response[12]['total']);
                $("[name='review_setting']").val( response[14]['setting']);
                $("[name='review_hosp_emr']").val( response[14]['hosp_emr']);
                $("[name='review_teaching']").val( response[14]['teaching']);
                $("[name='review_patients']").val( response[14]['num_pts']);
                $("#tab2_title").html( response[16] );
                if ($("INPUT[name='team_code']").val() == ""){
                        $('#d2d_instruct').html('Type a team code and press Enter.');
                 } else {
                     $('#d2d_instruct').html('Reviewing <em>"' + $("INPUT[name='team_code']").val() + '"</em>.');
                 }
                $('#review_table > tbody').find("tr:gt(0)").remove();
                var trHTML;
                var kkey;
                var kval;
                for (res in response[15]){
                    trHTML = '<tr>';
                    jsonData = response[15][res];
                    keys = Object.keys(jsonData);
                    for (i = 0; i < 10; i++){
                        data = jsonData[keys[i]];
                        if (data == null) {
                            data = '';
                        }
                        if (data == null) {
                            data = '';
                        }
                        if (keys[i] == "Peer_SAMI"){
                            data = response[12]['peers'];
                        }
                        if (keys[i] == "D2D_SAMI"){
                            data = response[12]['total'];
                        }
                        trHTML += "<td>" + data + "</td>";
                    } 
                    $('#review_table > tbody').append(trHTML + '<tr>');
                }

           }

            return false;   
        });
    }
    
    function myHandler2( event ){
        event.preventDefault();


        var selected_option = $(this).find( 'option:selected' ).text();
     
        var data = {
            action: 'get_new_data',
            team_code: $("INPUT[name='team_code']").val(),
            setting: $("[name='review_setting']").find( 'option:selected' ).val(), 
            year_code: $("[name='year_code']").find( 'option:selected' ).val(), 
            teaching: $("[name='review_teaching']").find( 'option:selected' ).val(), 
            hosp_emr: $("[name='review_hosp_emr']").find( 'option:selected' ).val(), 
            num_pts: $("[name='review_patients']").find( 'option:selected' ).val(),  
            core_only: $("[name='sel_data']").find( 'option:selected' ).val() 
             // rost_all: $("[name='rost_all']").find( 'option:selected' ).text()     
        };
        // the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
        $.post(ajax_object.ajaxurl, data, function(response) {
  // alert("Handler 1 " + selected_option);
          if (response['fail_code'] == 'not_found' ){
                // $("INPUT[name='team_code']").blur();
                // $("INPUT[name='team_code']").val('');
                // $("INPUT[name='team_code']").focus();
                $('#new_code').val('Team code <em>"' + response["team_code"] + '"</em> not found. Try again.');
                 alert("This team code (" + response["team_code"] + ') was not found.');
            } else {      
         // alert("Updating " + response["team_code"]);
               
                // $("INPUT[name='team_code']").blur();
                // $("select").blur();

                // alert("table 2" + response[15]['Team']);
                for (i = 0; i < 12 ; i++){
                var this_chart =  eval('amchart' + (i + 1));
                this_chart.dataProvider = response[i];
                this_chart.invalidateSize();
                this_chart.validateData();
                this_chart.animateAgain();
                }
                $('#team_qual_score').html( response[13]['team']);
                $('#peer_qual_score').html( response[13]['peers']);
                $('#total_qual_score').html( response[13]['total']);
                $('#team_sami_score').html( response[12]['team']);
                $('#peer_sami_score').html( response[12]['peers']);
                $('#total_sami_score').html( response[12]['total']);
                $("[name='review_setting']").val( response[14]['setting']);
                $("[name='review_hosp_emr']").val( response[14]['hosp_emr']);
                $("[name='review_teaching']").val( response[14]['teaching']);
                $("[name='review_patients']").val( response[14]['num_pts']);
                $("#tab2_title").html( response[16] );
                // $("[name='review_patients']").val( response[14]['num_pts']);
                    // $('#team_code_val').html( response[13]['team']);
                    // $('#year_code_val').html( response[13]['year_code']);
                    // $('#rost_all_val').html( response[13]['rost_all']);
                if ($("INPUT[name='team_code']").val() == ""){
                        $('#d2d_instruct').html('Type a team code and press Enter.');
                } else {
                     $('#d2d_instruct').html('Reviewing <em>"' + $("INPUT[name='team_code']").val() + '"</em>.');
                }
                
                $('#review_table > tbody').find("tr:gt(0)").remove();
                var trHTML;
                var kkey;
                var kval;
                for (res in response[15]){
                    trHTML = '<tr>';
                    jsonData = response[15][res];
                    keys = Object.keys(jsonData);
                    for (i = 0; i < 10; i++){
                        data = jsonData[keys[i]];
                        if (data == null) {
                            data = '';
                        }
                        if (keys[i] == "Peer_SAMI"){
                            data = response[12]['peers'];
                        }
                        if (keys[i] == "D2D_SAMI"){
                            data = response[12]['total'];
                        }
                        trHTML += "<td>" + data + "</td>";
                    } 
                    $('#review_table > tbody').append(trHTML + '<tr>');
                }

           }
            return false;   
        });
    }
    

     $( "#d2d_review_form" ).on( "submit", function( event ) {
            event.preventDefault();
    });

    


    $('#team_code_input').on('click', myHandler2);
    
    $('.new_code').on('change', myHandler2 );



    $('select').change( myHandler1 ); 

    // Manage the drill-down, rollup of the composite indicators
    $('#drill-roll-qual').on('click', function(e) {
        e.preventDefault();
        // alert("Drill down quality");
        var currentState = $(this).attr('href');
        if( currentState == '#level1q'){
            $(this).text("Click to roll-up - quality");
            $(this).attr('href', '#level2q');
            $('#level2q').addClass('tab active');
            $('#level1q').removeClass('tab active').addClass('tab');
        } else {
            $(this).text("Click to drill down - quality")
            $(this).attr('href', '#level1q');
            $('#level1q').addClass('tab active');
            $('#level2q').removeClass('tab active').addClass('tab');
        }
            for (i = 0; i < 12 ; i++){
                var this_chart =  eval('amchart' + (i + 1));
                this_chart.invalidateSize();
                // this_chart.validateData();
                this_chart.animateAgain();
            }
   })

    $('#drill-roll-cost').on('click', function(e) {
        e.preventDefault();
        // alert("Drill down cost");

        var currentState = $(this).attr('href');
        if( currentState == '#level1c'){
            $(this).text("Click to roll-up - cost");
            $(this).attr('href', '#level2c');
            $('#level2c').addClass('tab active');
            $('#level1c').removeClass('tab active').addClass('tab');
        } else {
            $(this).text("Click to drill down - cost")
            $(this).attr('href', '#level1c');
            $('#level1c').addClass('tab active');
            $('#level2c').removeClass('tab active').addClass('tab');
        }
            for (i = 0; i < 12 ; i++){
                var this_chart =  eval('amchart' + (i + 1));
                // this_chart.validateData();
                this_chart.invalidateSize();
                this_chart.animateAgain();
            }
   })

    // Manage the trend viewing, rollup of the composite indicators
    $('#teams_trend').on('click', function(e) {
        e.preventDefault();

        var currentState = $(this).attr('href');
        if( currentState == '#teams'){
            $(this).text("Click to view this iteration's data");
            $(this).attr('href', '#trend');
            $('#trend').addClass('tab active');
            $('#teams').removeClass('tab active').addClass('tab');
        } else {
            $(this).text("Click to view my team\'s data over iterations")
            $(this).attr('href', '#teams');
            $('#teams').addClass('tab active');
            $('#trend').removeClass('tab active').addClass('tab');
        }
         
            for (i = 0; i < 12 ; i++){
                var this_chart =  eval('amchart' + (i + 1));
                this_chart.invalidateSize();
                // this_chart.validateData();
                this_chart.animateAgain();
           }
     })

    // Manage the table view, return to main view
    $('#table_view').on('click', function(e) {
        e.preventDefault();

        var currentState = $(this).attr('href');
        if( currentState == '#teams'){
            $(this).text("Click to return to chart view");
            $(this).attr('href', '#table');
            $('#table').addClass('tab active');
            $('#teams_trend').text("");
            $('#teams').removeClass('tab active').addClass('tab');
        } else {
            $(this).text("Click for table view")
            $(this).attr('href', '#teams');
            $('#teams').addClass('tab active');
            $('#teams_trend').text("Click to view my team\'s data over iterations");
            $('#table').removeClass('tab active').addClass('tab');
        }
        // var tab_data = $(this).html(table_data);
        // $("#review_table > tbody:last").append("<tr><td>" + tab_data + "</td><td>Value2 </td></tr>");

      })

    // Manage main tabs
    $('.tabs .tab-links .d2d').on('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');
             // alert("Switch core and extended");
           $('.tabs ' + currentAttrValue).addClass('tab active');
            $('.tabs ' + currentAttrValue).siblings().removeClass('tab active').addClass('tab');

            $(this).parent('li').removeClass('inactive').siblings().removeClass('active');
            $(this).parent('li').addClass('active').siblings().addClass('inactive');
       	
        // Switch the actual tabs

        // Do things with the charts
        // $("[id^='amchart']").each().invalidateSize();
            for (i = 0; i < 12 ; i++){
                var this_chart =  eval('amchart' + (i + 1));
                this_chart.invalidateSize();
                // this_chart.validateData();
                 this_chart.animateAgain();
            }
  
        
        e.preventDefault();
    });

    // // Manage hyperlinks in chart labels
    $('[id^="amchart"]').on('click', '.amcharts-category-axis .amcharts-axis-label', function() {
        var category = $(this).find('tspan').text();
        var thisChart = eval($(this).parents('[id^="amchart"]').attr('id'));
        var dataItem = thisChart.dataProvider[thisChart.getCategoryIndexByValue(category)];
        alert('clicked: ' + dataItem.url );
        if ( dataItem.url !== undefined){
            window.open(dataItem.url);
        }
    });



});




