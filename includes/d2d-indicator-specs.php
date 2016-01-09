<?php

 /* @updated 21-Dec-2015
 *    Added field review_tab to identify which tab will display the indicator 
 *    Added field chart_id to identify which chart will display the indicator 
 */
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( !class_exists( 'D2D_data_specs' ) ) {
	class D2D_data_specs{
		
		public $data_specs = array(

		// Header set
			array( "name" => "team_code",
				"specs" => array(
					"indicator_class" => "team",
					"full_label" => "Team code",
					"short_label" => "team_code",
					"menu_class" => "top",
					"db_type" => "CHAR(20)",
					"value" => "Enter code",
					"css_style" => NULL,
					"indicator_group" => "header",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "year_code",
				"specs" => array(
					"indicator_class" => "selector",
					"full_label" => "Iteration",
					"short_label" => "year_code",
					"menu_class" => "top",
					"db_type" => "CHAR(20)",
					"value" => array( 
						"D2D 1.0",
						"D2D 2.0",
						"D2D 3.0"
						 ),
					"options" => array( 
						"D2D 1.0",
						"D2D 2.0",
						"D2D 3.0"
						 ),
					"css_style" => NULL,
					"indicator_group" => "header",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "save_status",
				"specs" => array(
					"indicator_class" => "team",
					"full_label" => "Save status",
					"short_label" => "save_status",
					"menu_class" => "top",
					"db_type" => "CHAR(20)",
					"value" => "saved",
					"css_style" => NULL,
					"indicator_group" => "header",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "save_date",
				"specs" => array(
					"indicator_class" => "team",
					"full_label" => "Save status",
					"short_label" => "save_date",
					"menu_class" => "top",
					"db_type" => "CHAR(20)",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "header",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "email_rcpt",
				"specs" => array(
					"indicator_class" => "team",
					"full_label" => "Email receipt",
					"short_label" => "email_rcpt",
					"menu_class" => "top",
					"db_type" => "CHAR(20)",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "header",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),

		//  Profile set
			array( "name" => "setting",
				"specs" => array(
					"indicator_class" => "selector",
					"full_label" => "Setting",
					"short_label" => "setting",
					"menu_class" => "top",
					"db_type" => "CHAR(20)",
					"value" => array( "Urban", "Rural"),
					"options" => array( "Urban", "Rural"),
					"css_style" => NULL,
					"indicator_group" => "profile",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "teaching",
				"specs" => array(
					"indicator_class" => "selector",
					"full_label" => "Teaching status",
					"short_label" => "teaching",
					"menu_class" => "top",
					"db_type" => "CHAR(20)",
					"value" => array( 
						"Academic", 
						"Teaching", 
						"Non-teaching"),
					"options" => array( 
						"Academic", 
						"Teaching", 
						"Non-teaching"),
					"css_style" => NULL,
					"indicator_group" => "profile",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "emr",
				"specs" => array(
					"indicator_class" => "selector",
					"full_label" => "Access to hospital discharge data",
					"short_label" => "hosp_emr",
					"menu_class" => "top",
					"db_type" => "CHAR(20)",
					"value" => array( 
						"N/A",
						"HRM", 
						"POI",
						"TDIS",
						"SPIRE",
						"None",
						"Unkn."),
					"options" => array( 
						"N_A",
						"HRM", 
						"POI",
						"TDIS",
						"SPIRE",
						"None",
						"Unkn."),
					"css_style" => NULL,
					"indicator_group" => "profile",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "quality_agree",
				"specs" => array(
					"indicator_class" => "selector",
					"full_label" => "Agree to quality calc",
					"short_label" => "quality_agree",
					"menu_class" => NULL,
					"value" => array( 
						"No",
						"Yes"),
					"options" => array( 
						"No",
						"Yes"),
					"db_type" => "CHAR(8)",
					"css_style" => NULL,
					"indicator_group" => "qual_agree"
					),
				),
			array( "name" => "share_agree",
				"specs" => array(
					"indicator_class" => "selector",
					"full_label" => "Agree to share data",
					"short_label" => "share_agree",
					"menu_class" => NULL,
					"value" => array( 
						"No",
						"Yes"),
					"options" => array( 
						"No",
						"Yes"),
					"db_type" => "CHAR(8)",
					"css_style" => NULL,
					"indicator_group" => "share_agree"
					),
				),
			array( "name" => "confirm_review",
				"specs" => array(
					"indicator_class" => "selector",
					"full_label" => "Confirm team reviewed",
					"short_label" => "confirm_review",
					"menu_class" => NULL,
					"value" => array( 
						"No",
						"Yes"),
					"options" => array( 
						"No",
						"Yes"),
					"db_type" => "CHAR(8)",
					"css_style" => NULL,
					"indicator_group" => "confirm_review"
					),
				),

		// Cost D2D indicator set
			array( "name" => "cost",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Total Cost",
					"short_label" => "cost",
					"menu_class" => "top",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "data",
					"review_tab" => "cost_inds",
					"chart_id" => "cost_drill"
					),
				),
			array( "name" => "cost_adj",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Adjusted Total Cost",
					"short_label" => "cost_adj",
					"menu_class" => "top",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "data",
					"review_tab" => "cost_inds",
					"chart_id" => "cost_inds"
					)
				),
			array( "name" => "cost_prim",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Cost - Primary Care",
					"short_label" => "cost_prim",
					"menu_class" => "top",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "data",
					"review_tab" => "cost_inds",
					"chart_id" => "cost_drill"
					)
				),
			array( "name" => "cost_serv",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Cost - Services",
					"short_label" => "cost_serv",
					"menu_class" => "top",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "data",
					"review_tab" => "cost_inds",
					"chart_id" => "cost_drill"
					)
				),
			array( "name" => "cost_settings",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Cost - Settings",
					"short_label" => "cost_settings",
					"menu_class" => "top",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "data",
					"review_tab" => "cost_inds",
					"chart_id" => "cost_drill"
					)
				),
			array( "name" => "cost_inst",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Cost - Institutions",
					"short_label" => "cost_inst",
					"menu_class" => "top",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "data",
					"review_tab" => "cost_inds",
					"chart_id" => "cost_drill"
					)
				),
			
// Added 2015-11-25 on M.Krahn instructions

			array( "name" => "cap_phys_appin",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Physician Capacity - office appointments",
					"short_label" => "cap_phys_appin",
					"menu_class" => "qual",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate"
					)
				),
			array( "name" => "cap_phys_other",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Physician Capacity - other activities",
					"short_label" => "cap_phys_other",
					"menu_class" => "qual",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate"
					)
				),
			array( "name" => "cap_phys_special",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Physician Capacity - specialized services",
					"short_label" => "cap_phys_special",
					"menu_class" => "qual",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate"
					)
				),
			array( "name" => "num_phys_est",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Physicians represented in estimate (as % total on team)",
					"short_label" => "num_phys_est",
					"menu_class" => "qual",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate"
					)
				),
			array( "name" => "cap_ihp_appin",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "IHP Capacity - office appointments",
					"short_label" => "cap_ihp_appin",
					"menu_class" => "qual",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate"
					)
				),
 			array( "name" => "cap_ihp_other",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "IHP Capacity - other activities",
					"short_label" => "cap_ihp_other",
					"menu_class" => "qual",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate"
					)
				),
			array( "name" => "cap_ihp_special",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "IHP Capacity - specialized services",
					"short_label" => "cap_ihp_special",
					"menu_class" => "qual",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate"
					)
				),
			array( "name" => "num_ihp_est",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "IHPs represented in estimate (as % total on team)",
					"short_label" => "num_ihp_est",
					"menu_class" => "qual",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate"
					)
				),

			// 
			array( "name" => "pts_served",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Patients served",
					"short_label" => "pts_served",
					"menu_class" => "top",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "data"
					)
				),
			array( "name" => "pts_rostered",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Patients rostered",
					"short_label" => "pts_rostered",
					"menu_class" => "top",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "data"
					)
				),
			array( "name" => "sami_score",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "SAMI score",
					"short_label" => "sami_score",
					"menu_class" => "top",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "data",
					"hyperlink" => "http://www.afhto.ca/members-only/sami-score/ "
					)
				),

		//  Core d2d set
		//   - patient_centered
			array( "name" => "involved",
				"specs" => array(
					"indicator_class" => "hex",
					"menu_class" => "pat_exp",
					"full_label" => "Patients involved in decisions",
					"short_label" => "involved",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"review_tab" => "core_d2d_inds",
					"chart_id" => "pat_centered",
					"chart_label" => "Patient Involved",
					"hyperlink" => "http://www.afhto.ca/members-only/patient-experience-involved/"
					)
				),
			array( "name" => "courtesy",
				"specs" => array(
					"indicator_class" => "hex",
					"menu_class" => "pat_exp",
					"full_label" => "Courtesy of office staff",
					"short_label" => "courtesy",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"review_tab" => "core_d2d_inds",
					"chart_id" => "pat_centered",
					"chart_label" => "Courtesy of staff",
					"hyperlink" => "http://www.afhto.ca/members-only/pt-satisfaction-with-office-staff/"
					)
				),
			array( "name" => "reas_wait",
				"specs" => array(
					"indicator_class" => "hex",
					"menu_class" => "pat_exp",
					"full_label" => "Reasonable wait for appt.",
					"short_label" => "reas_wait",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"review_tab" => "core_d2d_inds",
					"chart_id" => "access",
					"chart_label" => "Reasonable wait time",
					"hyperlink" => "http://www.afhto.ca/members-only/reasonable-wait-for-appointment/"
					)
				),
		//   - effectiveness
			array( "name" => "colorectal_ca_ices",
				"specs" => array(
					"indicator_class" => "hex",
					"menu_class" => "ICES",
					"full_label" => "Colorectal Cancer screening",
					"short_label" => "colorectal_ca_ices",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"review_tab" => "core_d2d_inds",
					"chart_id" => "effectiveness",
					"chart_label" => "Colorectal ca screening",
					"hyperlink" => "http://www.afhto.ca/members-only/colorectal-cancer-screening/ "
					),
				),
			array( "name" => "cervical_ca_ices",
				"specs" => array(
					"indicator_class" => "hex",
					"menu_class" => "ICES",
					"full_label" => "Cervical Cancer screening",
					"short_label" => "cervical_ca_ices",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"review_tab" => "core_d2d_inds",
					"chart_id" => "effectiveness",
					"chart_label" => "Cervical ca screening",
					"hyperlink" => "http://www.afhto.ca/members-only/cervical-cancer-screening/"
					),
				),
			array( "name" => "child_imm",
				"specs" => array(
					"indicator_class" => "hex",
					"menu_class" => "emr",
					"full_label" => "Childhood immunizations - all",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"short_label" => "child_imm",
					"indicator_group" => "rate",
					"review_tab" => "core_d2d_inds",
					"chart_id" => "effectiveness",
					"chart_label" => "Child Immunization - all",
					"hyperlink" => "http://www.afhto.ca/members-only/childhood-immunization/ "
					// "iteration_limit" => -3
					),
				),

		//   - access
			array( "name" => "next_day",
				"specs" => array(
					"indicator_class" => "hex",
					"menu_class" => "pat_exp",
					"full_label" => "Same/next day appointment",
					"short_label" => "next_day",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"review_tab" => "core_d2d_inds",
					"chart_id" => "access",
					"chart_label" => "Same/next day appt",
					"hyperlink" => "http://www.afhto.ca/members-only/samenext-day-appointments/"
					),
				),
			array( "name" => "primary_prov",
				"specs" => array(
					"indicator_class" => "hex",
					"menu_class" => "ICES",
					"full_label" => "Regular primary care provider - individual",
					"short_label" => "primary_prov",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"review_tab" => "core_d2d_inds",
					"chart_id" => "access",
					"chart_label" => "Reg provider - ind",
					"hyperlink" => "http://www.afhto.ca/members-only/regular-primary-care-provider/"
					),
				),
			array( "name" => "primary_prov_team",
				"specs" => array(
					"indicator_class" => "hex",
					"menu_class" => "ICES",
					"full_label" => "Regular primary care provider - team",
					"short_label" => "primary_prov_team",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"review_tab" => "core_d2d_inds",
					"chart_id" => "access",
					"chart_label" => "Reg provider - team",
					"hyperlink" => "http://www.afhto.ca/members-only/regular-primary-care-provider/"
					),
				),
			
		//   - integration
			array( "name" => "readmission",
				"specs" => array(
					"indicator_class" => "hex",
					"menu_class" => "ICES",
					"full_label" => "Readmissions to hospital",
					"short_label" => "readmission",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"review_tab" => "core_d2d_inds",
					"chart_id" => "integration",
					"chart_label" => "Readmission to hospital",
					"hyperlink" => "http://www.afhto.ca/members-only/readmissions-to-hospital/"
					),
				),


			array( "name" => "diabetes_core",
				"specs" => array(
					"indicator_class" => "simple",
					"menu_class" => "diab",
					"full_label" => "Diabetes care",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"short_label" => "diabetes_core",
					"indicator_group" => "rate",
					"review_tab" => "core_d2d_inds",
					"chart_id" => "effectiveness",
					"chart_label" => "Diabetes care",
					"iteration_limit" => 3,
					"hyperlink" => "http://www.afhto.ca/members-only/collaborative-patient-care/health-promotion-cdpm/diabetes-care/"
					),
				),
			
				
		// Expanded quality indicator set
			array( "name" => "personal_probs",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Personal problems related to health condition",
					"short_label" => "personal_probs",
					"menu_class" => "pat_exp",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => "quality",
					"drill_down" => NULL
					),
				),
			array( "name" => "ask_questions",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Opportunity to ask questions",
					"short_label" => "ask_questions",
					"menu_class" => "pat_exp",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "enough_time",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Spend enough time",
					"short_label" => "enough_time",
					"menu_class" => "pat_exp",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "dr_find_out",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Find out your concerns",
					"short_label" => "dr_find_out",
					"menu_class" => "pat_exp",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "say_important",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Say what was important",
					"short_label" => "say_important",
					"menu_class" => "pat_exp",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "dr_take_seriously",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Take your concerns seriously",
					"short_label" => "dr_take_seriously",
					"menu_class" => "pat_exp",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "dr_feelings",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Concerned about your feelings",
					"short_label" => "dr_feelings",
					"menu_class" => "pat_exp",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "acsh",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Hospitalizations for ambulatory care sensitive conditions",
					"menu_class" => "ICES",
					"short_label" => "acsh",
					"menu_class" => "ICES",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "ed_visits",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Emergency department visits",
					"short_label" => "ed_visits",
					"menu_class" => "ICES",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "mammograms",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Breast cancer screening",
					"short_label" => "mammograms",
					"menu_class" => "ICES",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "diabetes_w_codes",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Diabetic management assessment - Billing code K030",
					"short_label" => "diabetes_w_codes",
					"menu_class" => "ICES",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "hga1c",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Diabetic blood sugar management",
					"short_label" => "hga1c",
					"menu_class" => "emr",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "coumadin_inr",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Coumadin management",
					"short_label" => "coumadin_inr",
					"menu_class" => "emr",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "hypertension",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Hypertension screening",
					"short_label" => "hypertension",
					"menu_class" => "emr",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "diabetes",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Diabetes screening",
					"short_label" => "diabetes",
					"menu_class" => "emr",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "diab_LDL",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Diabetic cholesterol management",
					"short_label" => "diab_LDL",
					"menu_class" => "emr",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "hyp_reg",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Hypertension registry",
					"short_label" => "hyp_reg",
					"menu_class" => "emr_reg",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "stroke_reg",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Stroke registry",
					"short_label" => "stroke_reg",
					"menu_class" => "emr_reg",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "chf_reg",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Congestive heart failure registry",
					"short_label" => "chf_reg",
					"menu_class" => "emr_reg",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "depress_reg",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Depression registry",
					"short_label" => "depress_reg",
					"menu_class" => "emr_reg",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "athro_reg",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Arteriosclerotic heart disease registry",
					"short_label" => "athro_reg",
					"menu_class" => "emr_reg",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "bipolar_reg",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Bipolar affect disease registry",
					"short_label" => "bipolar_reg",
					"menu_class" => "emr_reg",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "schiz_reg",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Schizophrenia registry",
					"short_label" => "schiz_reg",
					"menu_class" => "emr_reg",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "asthma_reg",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Asthma registry",
					"short_label" => "asthma_reg",
					"menu_class" => "emr_reg",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "copd_reg",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "COPD registry",
					"short_label" => "copd_reg",
					"menu_class" => "emr_reg",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "epilepsy_reg",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Epilepsy registry",
					"short_label" => "epilepsy_reg",
					"menu_class" => "emr_reg",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "hypothyroid_reg",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Hypothyroidism registry",
					"short_label" => "hypothyroid_reg",
					"menu_class" => "emr_reg",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "diabetes_reg",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Diabetes registry",
					"short_label" => "diabetes_reg",
					"menu_class" => "emr_reg",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "dx_reconciliation",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Reconciliation of diagnoses",
					"short_label" => "dx_reconciliation",
					"menu_class" => "emr",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "med_reconciliation",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Medication reconciliation",
					"short_label" => "med_reconciliation",
					"menu_class" => "emr",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "flu_imm",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Influenza immunization",
					"short_label" => "flu_imm",
					"menu_class" => "emr",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "smoking",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Smoking status",
					"short_label" => "smoking",
					"menu_class" => "emr",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "ed_elsewhere",
				"specs" => array(
					"indicator_class" => 'hex',
					"full_label" => "Emergency department visits for conditions best managed elsewhere",
					"short_label" => "ed_elsewhere",
					"menu_class" => "hbp",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "quality",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "X7_day_text",
				"specs" => array(
					"indicator_class" => 'text',
					"full_label" => "Description of teams approach to monitoring 7 day follow-up",
					"short_label" => "X7_day_text",
					"menu_class" => NULL,
					"db_type" => "TEXT",
					"text" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"parent_id" => NULL,
					"drill_down" => NULL
					),
				),
			array( "name" => "emr_q_cervical",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "EMR/SAR Compare - Cervical ca",
					"short_label" => "emr_q_cervical",
					"menu_class" => "data_qual",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"chart_id" => "emr_data_quality",
					'hyperlink' => 'http://www.afhto.ca/members-only/emr-data-quality'
					),
				),
			array( "name" => "emr_q_colorectal",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "EMR/SAR Compare - Colorectal ca ",
					"short_label" => "emr_q_colorectal",
					"menu_class" => "data_qual",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"chart_id" => "emr_data_quality",
					),
				),
			array( "name" => "emr_q_smoking",
				"specs" => array(
					"indicator_class" => "simple",
					"full_label" => "Smoking Status Complete",
					"short_label" => "emr_q_smoking",
					"menu_class" => "data_qual",
					"db_type" => "DOUBLE",
					"value" => NULL,
					"css_style" => NULL,
					"indicator_group" => "rate",
					"chart_id" => "emr_data_quality",
					),
				),
// This is a template for a new indicator.
// For test pusposes, the name is set to "email_rcpt", since this is an unused indicator
// in the "indicator" table in the database. 
// In adding a new indicator, a new vaiaable should be added to the "indicator" table in the database.
			// array( "name" => "email_rcpt",
			// 	"specs" => array(
			// 		"indicator_class" => "simple",
			// 		"full_label" => "Testing new indicator",
			// 		"short_label" => "email_rcpt",
			// 		"menu_class" => NULL,
			// 		"db_type" => "DOUBLE",
			// 		"value" => NULL,
			// 		"css_style" => NULL,
			// 		"indicator_group" => "rate",
			// 		"chart_id" => "access",
			// 		"chart_label" => "Test indicator",
			// 		),
			// 	),
 );

			function __construct(){

			}

		public function check_for_table(){
			global $wpdb;
			$sql = 'SELECT 1 FROM d2d_indicators';
			$val = $wpdb -> query($sql);
			return $val; 
		}

		/**
		 * Creates a datbase table that will hold a list of indicators
		 * @return [type] [description]
		 */
		public function create_db_table(){
			global $wpdb;
			$sql = "SHOW TABLES LIKE 'd2d_indicators'";
			$val = $wpdb -> query($sql);
			if ($val == NULL ){
				$sql = "CREATE TABLE `d2d_indicators` (
  					`ind_name` CHAR(20) NOT NULL,
  					`short_label` CHAR(20) NOT NULL,
  					`full_label` CHAR(80) NULL,
  					`db_type` CHAR(80) NULL,
  					`value` CHAR(80) NULL,
					'text' TEXT NULL,
  					`options` CHAR(80) NULL,
   					`css_style` CHAR(80) NULL,
   					`indicator_class` CHAR(10) NOT NULL,
  					`menu_class` CHAR(10) NOT NULL,
 					`indicator_group` CHAR(20) NOT NULL,
  					`parent_id` CHAR(20) NULL,
  					`drill_down` CHAR(8) NULL,
  					`date_update` CHAR(20) NULL,
  					`email_address` CHAR(40) NULL,
  					PRIMARY KEY (`ind_name`));";
				$wpdb -> query($sql);
				$this -> fill_db_table();
				echo 'Created table';
			} else {
				echo 'Did not create table';
			}

		}

		/**
		 * Fills the database table of indicators with the data describing the inindicators
		 * in this file
		 * @return [type] [description]
		 */
		public function fill_db_table(){
			$these_specs = $this -> get_specs();
			global $wpdb;
			// Clear the table
			$wpdb -> query('TRUNCATE d2d_indicators');
			// Iterate through all the specs here 
			// and build an array for each
			// insert the array into the database
			foreach( $these_specs as $ind ){
				$db_array_data = array();
				$db_array_fmt = array();

				$db_array_data['ind_name'] = $ind['name'];
				array_push ( $db_array_fmt,  '%s');
				$spec_array = $ind['specs'];
				// // $keys = array_keys($spec_array);
				$keys = array_keys($spec_array);
				foreach($keys as $k){
					if ( is_array( $spec_array[$k] ) ) {
						$implode_string = implode(';', $spec_array[$k]);
						$db_array_data[$k] = 'is_option;' . $implode_string;
						array_push ( $db_array_fmt,  '%s');
					} else {
						$db_array_data[$k] = $spec_array[$k];
						array_push ( $db_array_fmt,  '%s');
					}
				}
				$wpdb -> insert('d2d_indicators', $db_array_data, $db_array_fmt);
			}
		}

		/**
		 * Retrieves the fields of indicators
		 * @return [type] [description]
		 */
		public function get_db_table(){
			global $wpdb;
				$table_results = $wpdb -> get_results('SELECT * FROM d2d_indicators', ARRAY_A);
			return $table_results;
		}

		public function create_indicators_table(){
			$these_specs = $this -> get_specs();
			global $wpdb;
			$sql = "CREATE TABLE indicators (`id` INT NOT NULL AUTO_INCREMENT,";
			$comma = " ";
			foreach ($these_specs as $spec){
				if ( $spec['specs']['indicator_class'] == 'hex'){
					$this_type = 'CHAR(80)';
				} else {
					$this_type = $spec['specs']['db_type'];
				}
				$sql .= $comma  . $spec['specs']['short_label'] . " " . $this_type . " NULL ";
				$comma = ", ";
			}
			$sql .= ", PRIMARY KEY (`id`));";
			$wpdb ->query($sql);


		}


		/**
		 * Method to provide the complete array of specifications
		 * @return array array of arrays, each one a specification of an indicator
		 */
		public function get_specs(){
			return $this -> data_specs;
		}

		/**
		 * Method to create indicators
		 * @return array of D2D_indicator Objects
		 */
		public function make_indicators(){
			$indicator_array = array();
			
			foreach($this -> data_specs as $spec){
			// echo 'Adding indicator ' . $params['name']. '<p></p>';
				switch ( $spec['specs']['indicator_class'] ) {
					case 'simple':
					$indicator_array[$spec['name']] = new D2D_simple_indicator( $spec['specs'] );
					break;
					case 'team':
					$indicator_array[$spec['name']] = new D2D_team_indicator( $spec['specs'] );
					break;
					case 'triple':
					$indicator_array[$spec['name']] = new D2D_rate_indicator( $spec['specs'] );
					break;
					case 'hex':
					$indicator_array[$spec['name']] = new D2D_rostered_rate_indicator( $spec['specs'] );
					break;
					case 'selector':
					$indicator_array[$spec['name']] = new D2D_select_indicator( $spec['specs'] );
					break;
					case 'text':
					$indicator_array[$spec['name']] = new D2D_text_indicator( $spec['specs'] );
					break;
				}
			}

			return $indicator_array;
		}

		public function get_current_period(){
			$num_periods = count( $this -> data_specs[ 1 ]['specs']['value'] );
			$latest_period = $this -> data_specs[ 1 ]['specs']['value'][$num_periods - 1] ;
			return $latest_period;
		}

		/**
		 * Extracts the indicators that are grouped under the review tabs
		 * @param  [string] $tab_name [neame of the tab group as used here]
		 * @return [array]           [short labels of the relevant indicators]
		 */
		public function make_tab_group($tab_name){
			
			$tab_array = array();

			foreach($this -> data_specs as $spec){
				if ($spec['specs']['review_tab'] == $tab_name ) {
					$temp = $spec['specs']['short_label'];
					array_push($tab_array, $temp);
				}
			}
			return $tab_array;
		}

		// *
		//  * Extracts the indicators that will be displayed in the named chart.
		//  * @param  [string] $chart_name [name of the chart]
		//  * @return [array]             [array of the short label and display label]
		 
		public function make_chart($chart_name, $iteration){
			$chart_array = array();
			foreach($this -> data_specs as $spec){
				$iter_lim = $spec['specs']['iteration_limit'];
				if ( ( $iter_lim == null) or 
					( ( $iter_lim < 0 ) and ( ($iter_lim + $iteration )  <   0 ) ) or
					( ( $iter_lim > 0 ) and ( ($iteration - $iter_lim )  >=  0 ) ) ) 
					{
					if ($spec['specs']['chart_id'] == $chart_name ) {
						$temp = array(
							"indicator" => $spec['specs']['chart_label'],
							"short_label" => $spec['specs']['short_label'],
							"hyperlink" => $spec['specs']['hyperlink']
							);
						array_push($chart_array, $temp);
					}
				}
			}
			return $chart_array;
		}
	}
}

if ( class_exists( 'D2D_data_specs' ) ) {
	$d2d_data_specs = new D2D_data_specs();

}

