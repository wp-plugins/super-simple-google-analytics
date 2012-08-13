<?php
/*
Plugin Name: Super Simple Google Analytics
Plugin URI: http://bitacre.com/plugins/super-simple-google-analytics
Description: Bare bones option for people looking to simply insert the basic Google Analytics tracking code into the head section of every page without fuss.
Version: 1.6
Author: bitacre
Author URI: http://bitacre.com
License: GPLv3
	Copyright 2012 Shinra Web Holdings (http://shinraholdings.com)
*/

// FUNCTIONS

function super_simple_google_analytics_get_defaults() { 
	$defaults = array( 
		'account' => '', 
		'profile' => '', 
		'insert_code' => 0,
		'location' => 'head',
		'track_admin' => 0,
		'track_adsense' => 0
	);
	return $defaults;
}

function super_simple_google_analytics_set_plugin_meta( $links, $file ) { 
/*	short desc: define additional plugin meta links (appearing under plugin on Plugins page)
	parameters:
		$links = (array) passed from wp
		$file = (array) passed from wp*/
	$plugin = plugin_basename( __FILE__ ); // '/nofollow/nofollow.php' by default
    if ( $file == $plugin ) { // if called for THIS plugin then:
		$newlinks = array( '<a href="options-general.php?page=super-simple-google-analytics">' . __( 'Settings' ) . '</a>'	); // array of links to add
		return array_merge( $links, $newlinks ); // merge new links into existing $links
	}
return $links; // return the $links (merged or otherwise)
}

function super_simple_google_analytics_options_init() { 
// short desc: add plugin's options to white list
	register_setting( 'super_simple_google_analytics_options_options', 'super_simple_google_analytics_item', 'super_simple_google_analytics_options_validate' );
}

function super_simple_google_analytics_options_add_page() { 
// add link to plugin's settings page under 'settings' on the admin menu 
	add_options_page( __( 'Super Simple Google Analytics Settings' ), __( 'Google Analytics'), 'manage_options', 'super-simple-google-analytics', 'super_simple_google_analytics_options_do_page');
}

function super_simple_google_analytics_options_validate( $input ) { 
/* 	short desc: sanitize and validate input. accepts an array, returns a sanitized array.
	parameters: $input = (array) option input to validate
	return: (array) sanitized option input */

	// sanatize inputs:
	$input['insert_code'] = ( $input['insert_code'] ? 1 : 0 ); 	// (checkbox) if TRUE then 1, else NULL
	$input['track_admin'] = ( $input['track_admin'] ? 1 : 0 ); 	// (checkbox) if TRUE then 1, else NULL
	$input['track_adsense'] = ( $input['track_adsense'] ? 1 : 0 ); 	// (checkbox) if TRUE then 1, else NULL
	$input['account'] =  wp_filter_nohtml_kses( $input['account'] ); // (textbox) safe text, no html
	$input['profile'] =  wp_filter_nohtml_kses( $input['profile'] ); // (textbox) safe text, no html
	$input['location'] = ( $input['location'] == 'head' ? 'head' : 'body' ); // (radio) either head or body
	return $input;
}

function super_simple_google_analytics_options_do_page() { 
// short desc: draw the html/css for the settings page
	
	?>
	<div class="wrap">
    <div class="icon32" id="icon-options-general"><br /></div>
		<h2><?php _e( 'Super Simple Google Analytics Settings' ); ?></h2>
		<form name="form1" id="form1" method="post" action="options.php">
			<?php settings_fields( 'super_simple_google_analytics_options_options' ); // nonce settings page ?>
			<?php $options = get_option( 'super_simple_google_analytics_item', super_simple_google_analytics_get_defaults() ); // populate $options array from database ?>
			
			<!-- Description -->
			<p style="font-size:0.95em"><?php 
				_e( sprintf( 'You may post a comment on this plugin\'s %1$shomepage%2$s if you have any questions, bug reports, or feature suggestions.', '<a href="http://bitacre.com/plugins/super-simple-google-analytics" rel="help">', '</a>' ) ); ?></p>
			
			<table class="form-table">

            	 <!-- <?php _e( 'Insert Tracking Code (checkbox)' ); ?> -->
			<?php $insert_code_style = ( !$options['insert_code'] ? 'style="color:#F00;" ' : '' ); ?>
				<tr valign="top"><th scope="row"><label <?php echo $insert_code_style; ?>for="super_simple_google_analytics_item[insert_code]"><?php _e( 'Insert Tracking Code?' ); ?></label></th>
					<td><input name="super_simple_google_analytics_item[insert_code]" type="checkbox" value="1" <?php checked( $options['insert_code'], 1 ); ?>/></td>
                </tr>
				 
				 <!-- <?php _e( 'UA-numbers (text boxes)' ); ?> -->
				<tr valign="top"><th scope="row"><label for="super_simple_google_analytics_item[account]"><?php _e( 'Google Analytics Numbers' ); ?>: </label></th>
					<td>
                    	UA-<input type="text" name="super_simple_google_analytics_item[account]" value="<?php echo $options['account']; ?>" style="width:90px;" maxlength="8" />
	                    &ndash;<input type="text" name="super_simple_google_analytics_item[profile]" value="<?php echo $options['profile']; ?>" style="width:30px;" maxlength="3" />
					</td>
				</tr>
				
                <!-- Head/Body insert (radio buttons) -->
                <tr valign="top"><th scope="row" valign="middle"><label for="super_simple_google_analytics_item[location]"><?php _e( 'Insert Location' ); ?>:</label></th>
					<td>
						<input name="super_simple_google_analytics_item[location]" type="radio" value="head" <?php checked( $options['location'], 'head', TRUE ); ?> />
						<?php _e( sprintf( 'before %1$shead%2$s tag', '&lt;/', '&gt;' ) ); ?><br />
						<input name="super_simple_google_analytics_item[location]" type="radio" value="body" <?php checked( $options['location'], 'body', TRUE ); ?> />
						<?php _e( sprintf( 'before %1$sbody%2$s tag', '&lt;/', '&gt;' ) ); ?>
                    </td>
                </tr>

				<!-- Track Administrator Views (checkbox) -->
				<tr valign="top"><th scope="row"><label for="super_simple_google_analytics_item[track_admin]"><?php _e( 'Track Administrator Hits?' ); ?></label></th>
					<td><input name="super_simple_google_analytics_item[track_admin]" type="checkbox" value="1" <?php checked( $options['track_admin'], 1 ); ?>/></td>
                </tr>
				
				<!-- Track Integrated Adsense (checkbox) -->
				<tr valign="top"><th scope="row"><label for="super_simple_google_analytics_item[track_adsense]"><?php _e( 'Track Integrated Adsense?' ); ?></label></th>
					<td><input name="super_simple_google_analytics_item[track_adsense]" type="checkbox" value="1" <?php checked( $options['track_adsense'], 1 ); ?>/></td>
                </tr>
				
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
			</p>
		</form>
	</div>
    
	<?php
}

function super_simple_google_analytics_print_code() { 
// 	short desc: Google Analytics html tracking code to be inserted in header/footer
	$plugin_url = 'http://bitacre.com/plugins/super-simple-google-analytics';
	$options = get_option( 'super_simple_google_analytics_item', super_simple_google_analytics_get_defaults() ); // thanks again to Tacit Slagger for catching this one!

// code removed for admin
$admin = '<!-- 
Plugin: Super Simple Google Analytics 
Plugin URL: ' . $plugin_url . '

You\'ve chosen to prevent the tracking code from being inserted on 
pages viewed by logged-in administrators. 

You can re-enable the insertion of the tracking code on all pages
for all users by going to Settings > Google Analytics on the Dashboard.
-->
';

// Simple tracker code as of 29-march-2012 (thank you Tacit Slager for finding the 1.4 bug!)
$code = '<!--
Plugin: Super Simple Google Analytics 
Google Analytics Tracking Code.
Plugin URL: ' . $plugin_url . '
-->

' . ( $options['track_adsense'] ? '

<script type="text/javascript">
	window.google_analytics_uacct = "UA-' . $options['account'] . '-' . $options['profile'] . '";
</script>

' : '' ) . '<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push([\'_setAccount\', \'UA-' . $options['account'] . '-' . $options['profile'] . '\']);
  _gaq.push([\'_trackPageview\']);

  (function() {
    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
'; 

// code removed for all pages
$disabled = '<!-- 
Plugin: Super Simple Google Analytics 
Plugin URL: ' . $plugin_url . '

You\'ve chosen to prevent the tracking code from being inserted on 
any page. 

You can enable the insertion of the tracking code by going to 
Settings > Google Analytics on the Dashboard.
-->
';

	if( !$options['insert_code'] ) echo $disabled; 
	elseif( current_user_can( 'manage_options' ) && !$options['track_admin'] ) echo $admin;
	else echo $code;
	return; 

}

// HOOKS AND FILTERS
add_filter( 'plugin_row_meta', 'super_simple_google_analytics_set_plugin_meta', 10, 2 ); // add plugin page meta links
add_action( 'admin_init', 'super_simple_google_analytics_options_init' ); // whitelist options page
add_action( 'admin_menu', 'super_simple_google_analytics_options_add_page' ); // add link to plugin's settings page in 'settings' menu on admin menu initilization

// insert tracking code on page head initilization 
$options = get_option( 'super_simple_google_analytics_item', super_simple_google_analytics_get_defaults() );
if( $options['location'] == 'head' ) 
	add_action( 'wp_head', 'super_simple_google_analytics_print_code', 99999 ); 
elseif( $options['location'] == 'body' )
	add_action( 'wp_footer', 'super_simple_google_analytics_print_code', 99999 ); 
?>