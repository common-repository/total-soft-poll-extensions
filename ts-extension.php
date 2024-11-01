<?php
/*
		Plugin name: Total Soft Poll Extensions
		Plugin URI: https://makwebdesign.com
		Description: Extension plugin for the popular Responsive Poll Plugin by Total-Soft.
		Version: 1.4.0
		Author: Kaleb Morgan
		License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
	*/



//hooks

//action to initiate the function to register the shortcodes
add_action( 'init', 'tse_register_shortcodes' );


//Action: register plugin options
add_action('admin_init','tse_register_options');

//Action: register custom menus
add_action('admin_menu','tse_admin_menus');

//Warning Message for having main Poll Plugin installed
add_action('admin_notices','tse_poll_plugin_warning');

//Warning Message for having an un-tested version of WordPress installed.
add_action('admin_notices','tse_check_wp_version');





//shortcodes

//Register the shortcodes
function tse_register_shortcodes(){
	add_shortcode('TS_Poll_All', 'Total_SofPoll_Short_ALL');
	add_shortcode('TSE_Multi', 'Total_SoftPoll_Short_added');
	
}

//Shortcode for displaying all polls. Either newest->oldest or oldest->newest
function Total_SofPoll_Short_ALL($atts, $content=null){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
 

 	$plugin = 'TS-Poll/index.php';
 	$plugin2 = 'poll-wp/index.php';
	// check for plugin using plugin name
	if ( is_plugin_active( $plugin ) ) {
    //plugin is activated


		$id = 1;
		$answer = tse_get_poll_count();
		$answer = count($answer);
		$return = '';

		// while(ifPollExists($id)!==9719){
		// 	echo(Total_Soft_Draw_Poll($id));
		// 	$id+=1;
		// }

		$checked = tse_get_option('tse_poll_order');
		if(!$checked){
			for($i=1;$i<=$answer;$i++){
				$return.=Total_Soft_Draw_Poll($i);
			}
		}else{
			for($i=$answer;$i>0;$i--){
				$return.=Total_Soft_Draw_Poll($i);
			}
		}
		return $return;
	}

		elseif(is_plugin_active($plugin2)){
		$id = 1;
		$answer = tse_get_poll_count();
		$answer = count($answer);
		$return = '';

		// while(ifPollExists($id)!==9719){
		// 	echo(Total_Soft_Draw_Poll($id));
		// 	$id+=1;
		// }

		$checked = tse_get_option('tse_poll_order');
		if(!$checked){
			for($i=1;$i<=$answer;$i++){
				$return.=T_S_Draw_P($i);
			}
		}else{
			for($i=$answer;$i>0;$i--){
				$return.=T_S_Draw_P($i);
			}
		}
		return $return;




		}





	}


//Shortcode to show a mix of polls passed through the shortcode(ids)
function Total_SoftPoll_Short_added($args, $content=null){
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
 

 	$plugin = 'TS-Poll/index.php';
 	$plugin2 = 'poll-wp/index.php';
	// check for plugin using plugin name
	if ( is_plugin_active( $plugin ) ) {



		$list_ids = '';
		$return = '';
		$id = 0;
		if(isset($args['id']))$list_ids = $args['id'];

		$print = explode(',',$list_ids);
		$answer = count($print);

		for($i=0;$i<=$answer-1;$i++){
			$id = $print[$i];
					$return.=Total_Soft_Draw_Poll($id);
				}

				return $return;

	}elseif(is_plugin_active($plugin2)){

		$list_ids = '';
		$return = '';
		$id = 0;
		if(isset($args['id']))$list_ids = $args['id'];

		$print = explode(',',$list_ids);
		$answer = count($print);

		for($i=0;$i<=$answer-1;$i++){
			$id = $print[$i];
					$return.=T_S_Draw_P($id);
				}

				return $return;


	}



}





//Admin Pages

// Registers all custom plugin options
function tse_register_options() {
	// plugin options
	register_setting('tse_plugin_options', 'tse_poll_order');

}


//Action: registers custom plugin admin menus
function tse_admin_menus(){
	//main menu
    $top_menu_item = 'tse_dashboard_admin_page';


    add_menu_page('','Poll Plugin Extensions','manage_options','tse_dashboard_admin_page','tse_dashboard_admin_page','dashicons-hammer');

}


//Action: Dashboard admin page
function tse_dashboard_admin_page(){


    

    $options = tse_get_current_options();
    $checked = '';

    if($options['tse_poll_order']){
    	$checked='checked = "checked"';
    }

	
	echo('<div class="wrap">
		
		<h2>Poll Plugin Extension Settings</h2>
		
		<form action="options.php" method="post">');
		
			// outputs a unique nounce for our plugin options
			settings_fields('tse_plugin_options');
			// generates a unique hidden field with our form handling url
            @do_settings_fields('tse_plugin_options','');
			
			echo('<table class="form-table">
			
				<tbody>
			
					<tr>
						<th scope="row"><label for="tse_poll_order">Newest Polls First?</label></th>
						<td>
							<input type="checkbox" name="tse_poll_order" value="checked" class="" '.$checked.'"/>
							<p class="description" id="tse_poll_order-description">Whether you want your polls to show newest first, or oldest first.</p>
						</td>
					</tr>
					
			
				</tbody>
				
			</table>');
		
			// outputs the WP submit button html
			@submit_button();
		
		
		echo('</form>
	
	</div>');

}







//Helper Functions


//Grabs the number of polls based on the database tables from the total soft plugin.
function tse_get_poll_count(){

		global $wpdb;

		$table_name1 = $wpdb->prefix . "totalsoft_poll_manager";


	// $table_name3 = $wpdb->prefix . "totalsoft_poll_id";

		$polls = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name1 WHERE id>%d order by id",0));

		// $TotalSoftPollShortID = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name3 WHERE id>%d order by id desc limit 1",0));
		// echo($TotalSoftPollShortID[0]->Poll_ID+1);

		return $polls;
	}


// hint: get's the current options and returns values in associative array
function tse_get_current_options() {
	
	// setup our return variable
	$current_options = array();
	
	try {
	
		// build our current options associative array
		$current_options = array(
			'tse_poll_order' => tse_get_option('tse_poll_order'),
		);
	
	} catch( Exception $e ) {
		
		// php error
	
	}
	
	// return current options
	return $current_options;
	
}


// hint: returns the requested page option value or it's default
function tse_get_option( $option_name ) {
	
	// setup return variable
	$option_value = '';	
	
	
	try {
		
		// get default option values
		$defaults = tse_get_default_options();
		
		// get the requested option
		switch( $option_name ) {
			
			case 'tse_poll_order':
				// subscription page id
				$option_value = (get_option('tse_poll_order')) ? get_option('tse_poll_order') : $defaults['tse_poll_order'];
				break;
			
		}
		
	} catch( Exception $e) {
		
		// php error
		
	}
	
	// return option value or it's default
	return $option_value;
	
}


//Sets the default options for the options menu out of the box
function tse_get_default_options(){
	$defaults = array();


	$defaults = array(
		'tse_poll_order' => '',
	);



	return $defaults;
}


//Displays the error message if the Total Soft Poll plugin is not activated or installed.
function tse_poll_plugin_warning($views){
	$plugin = 'TS-Poll/index.php';
	$plugin2 = 'poll-wp/index.php';

	if(is_plugin_active($plugin)||is_plugin_active($plugin2)){
		
	}else{
		echo '<div class="error notice"><p style = "color:red">
        <strong> You do not have a version of Total Soft\'s Responsive Poll Plugin installed/activated. To use Total Soft Poll Extension, you must have this plugin installed.</strong>
        </p></div>';

	}

}





// 5.17
// hint: checks the current version of wordpress and displays a message in the plugin page if the version is untested
function tse_check_wp_version() {
	
	global $pagenow;
	
	
	if ( $pagenow == 'plugins.php' && is_plugin_active('TS-Poll-Extension/ts-extension.php') ):
	
		// get the wp version
		$wp_version = get_bloginfo('version');
		
		// tested vesions
		// these are the versions we've tested our plugin in
		$tested_versions = array(
			'4.2.0',
			'5.4.2',
		);
		
		// IF the current wp version is not in our tested versions...
		if( !in_array( $wp_version, $tested_versions ) ):
			
			// get notice html
			$notice = tse_get_admin_notice('Snappy List Builder has not been tested in your version of WordPress. It still may work though...','error');
			
			// echo the notice html
			echo( $notice );
			
		endif;
	
	endif;
	
}



// 6.27
// hint: returns html formatted for WP admin notices
function tse_get_admin_notice( $message, $class ) {
	
	// setup our return variable
	$output = '';
	
	try {
		
		// create output html
		$output = '
		 <div class="'. $class .'">
		    <p>'. $message .'</p>
		</div>
		';
	    
	} catch( Exception $e ) {
		
		// php error
		
	}
	
	// return output
	return $output;
	
}









// function create_section_for_multi_select($value) { 
// 		create_opening_tag($value);
// 		echo '<ul class="mnt-checklist" id="'.$value['id'].'" >'."\n";
// 		foreach ($value['options'] as $option_value => $option_list) {
// 			$checked = " ";
// 			if (get_option($value['id']."_".$option_value)) {
// 				$checked = " checked='checked' ";
// 			}
// 			echo "<li>\n";
// 			echo '<input type="checkbox" name="'.$value['id']."_".$option_value.'" value="true" '.$checked.' class="depth-'.($option_list['depth']+1).'" />'.$option_list['title']."\n";
// 			echo "</li>\n";
// 		}
// 		echo "</ul>\n";
// 		create_closing_tag($value);





//Warning for the ACF Older Version



