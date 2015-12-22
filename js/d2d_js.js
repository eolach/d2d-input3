jQuery(document).ready( function($) {
	
    // 
    // // Handler for startup - loads default settings of the form
//
    myHandler2();


    function myHandler1( event ){
        // event.preventDefault();


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
            if (typeof response['fail_code'] !== 'undefined' ){
            // alert("Team code " + response["team_code"] + ' not found');
            $('#d2d_instruct').html('Team code <em>"' + response["team_code"] + '"</em> not found. Try again.');
             } else {      
                // alert("handler1" + response);
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
                 if ($("INPUT[name='team_code']").val() == ""){
                        $('#d2d_instruct').html('Type a team code and press Enter.');
                 } else {
                     $('#d2d_instruct').html('Reviewing <em>"' + $("INPUT[name='team_code']").val() + '"</em>.');
                 }
           }
            return false;   
        });
    }
    
    function myHandler2( event ){
        // event.preventDefault();


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
            if (typeof response['fail_code'] !== 'undefined' ){
                // alert("Team code " + response["team_code"] + ' not found');
            $('#d2d_instruct').html('Team code <em>"' + response["team_code"] + '"</em> not found. Try again.');
             } else {      
                // alert("handler2" + response);
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
                // $("[name='review_patients']").val( response[14]['num_pts']);
                    // $('#team_code_val').html( response[13]['team']);
                    // $('#year_code_val').html( response[13]['year_code']);
                    // $('#rost_all_val').html( response[13]['rost_all']);
                 if ($("INPUT[name='team_code']").val() == ""){
                        $('#d2d_instruct').html('Type a team code and press Enter.');
                 } else {
                     $('#d2d_instruct').html('Reviewing <em>"' + $("INPUT[name='team_code']").val() + '"</em>.');
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
            $(this).text("Click to view trends")
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



