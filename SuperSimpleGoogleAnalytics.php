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

// default plugin options

function ssGA_default_options() { 
	$output = array(
		'account' => '',
		'profile' => '',
		'insert_code' => '',
		'location' => 'head',
		'track_admin' => 0,
		'adsense' => 0
	);
	
	return $output;
}

// add setting link to plugin dashboard
function ssGA_set_plugin_meta( $links, $file ) { 
	$plugin = plugin_basename( __FILE__ );
    if ( $file == $plugin ) { // if called for THIS plugin then:
		$newlink = sprintf( '<a href="options-general.php?page=%1$s">%2$s</a>', 
			'super-simple-google-analytics', __( 'Settings', 'SuperSimpleGoogleAnalytics' ) );
		return array_merge( $links, array( $newlink ) ); // merge new links into existing $links
	}
	return $links; // return the $links (merged or otherwise)
}

// register options
function ssGA_options_init() { 
	$options_group = 'super_simple_google_analytics_options_options';
	$options_name = 'super_simple_google_analytics_item';
	$validate_function = 'ssGA_options_validate';
	register_setting( $options_group, $options_name, $validate_function );
}

// add link to plugin's settings page under 'settings' on the admin menu 
function ssGA_options_add_page() { 
	$plugin_title = __( 'Super Simple Google Analytics Settings', 'SuperSimpleGoogleAnalytics' );
	$menu_text = __( 'Google Analytics', 'SuperSimpleGoogleAnalytics' );
	$has_cap = 'manage_options';
	$options_url ='super-simple-google-analytics';
	$draw_function = 'ssGA_options_do_page';
	add_options_page( $plugin_title, $menu_text, $has_cap, $options_url, $draw_function );
}

// for well formed input
function ssGA_options_validate( $input ) { 

// sanatize inputs:
	$input['insert_code'] = ( $input['insert_code'] || $input['insert_code'] == '1' ? 1 : '' ); 
	$input['track_admin'] = ( $input['track_admin'] || $input['track_admin'] == '1' ? 1 : '' ); 
	$input['adsense'] = ( $input['adsense'] || $input['adsense'] == '1' ? 1 : '' ); 
	$input['account'] =  wp_filter_nohtml_kses( $input['account'] );
	$input['profile'] =  wp_filter_nohtml_kses( $input['profile'] );
	$input['location'] = ( $input['location'] == 'body' ? 'body' : 'head' );
	return $input;
}

function ssGA_options_do_page() { 
// short desc: draw the html/css for the settings page
	
	?>
	<div class="wrap">
    <div class="icon32" id="icon-options-general"><br /></div>
		<h2><?php echo __( 'Super Simple Google Analytics Settings', 'SuperSimpleGoogleAnalytics' ); ?></h2>
		<form name="form1" id="form1" method="post" action="options.php">
			<?php settings_fields( 'super_simple_google_analytics_options_options' ); // nonce settings page ?>
			<?php $options = get_option( 'super_simple_google_analytics_item', ssGA_default_options() ); // populate $options array from database ?>
			
			<!-- Description -->
			<p style="font-size:0.95em"><?php 
				printf( __( 'You may post a comment on this plugin\'s %1$shomepage</a> if you have any questions, bug reports, or feature suggestions.', 'SuperSimpleGoogleAnalytics' ), '<a href="http://bitacre.com/plugins/super-simple-google-analytics" rel="help">' ); ?></p>
			
			<table class="form-table">

            	 <!-- <?php echo __( 'Insert Tracking Code (checkbox)', 'SuperSimpleGoogleAnalytics' ); ?> -->
<?php $insert_code_checked = ( $options['insert_code'] ? 'checked="checked" ' : '' );
$insert_code_style = ( !$options['insert_code'] ? 'style="color:#F00;" ' : '' ); ?>
				<tr valign="top"><th scope="row"><label <?php echo $insert_code_style; ?>for="super_simple_google_analytics_item[insert_code]"><?php echo __( 'Insert Tracking Code?', 'SuperSimpleGoogleAnalytics' ); ?></label></th>
					<td><input name="super_simple_google_analytics_item[insert_code]" type="checkbox" value="1" <?php echo $insert_code_checked; ?>/></td>
                </tr>
				 
				 <!-- <?php echo __( 'UA-numbers (text boxes)', 'SuperSimpleGoogleAnalytics' ); ?> -->
				<tr valign="top"><th scope="row"><label for="super_simple_google_analytics_item[account]"><?php echo __( 'Google Analytics Numbers', 'SuperSimpleGoogleAnalytics' ); ?>: </label></th>
					<td>
                    	UA-<input type="text" name="super_simple_google_analytics_item[account]" value="<?php echo $options['account']; ?>" style="width:90px;" maxlength="8" />
	                    &ndash;<input type="text" name="super_simple_google_analytics_item[profile]" value="<?php echo $options['profile']; ?>" style="width:30px;" maxlength="3" />
					</td>
				</tr>
				
                <!-- Head/Body insert (radio buttons) -->
                <tr valign="top"><th scope="row" valign="middle"><label for="super_simple_google_analytics_item[location]"><?php echo __( 'Insert Location', 'SuperSimpleGoogleAnalytics' ); ?>:</label></th>
					<td>
						<input name="super_simple_google_analytics_item[location]" type="radio" value="head" <?php checked( $options['location'], 'head', TRUE ); ?> /><?php echo __( 'before &lt;/head&gt;', 'SuperSimpleGoogleAnalytics' ); ?><br />
						<input name="super_simple_google_analytics_item[location]" type="radio" value="head" <?php checked( $options['location'], 'head', TRUE ); ?> /><?php echo __( 'before &lt;/body&gt;', 'SuperSimpleGoogleAnalytics' ); ?>
                    </td>
                </tr>

				<!-- Track Administrator Views (checkbox) -->
				<tr valign="top"><th scope="row"><label for="super_simple_google_analytics_item[track_admin]"><?php echo __( 'Track Administrator Hits?', 'SuperSimpleGoogleAnalytics' ); ?></label></th>
					<td><input name="super_simple_google_analytics_item[track_admin]" type="checkbox" value="1" <?php checked( $options['track_admin'], 1 ); ?> /></td>
                </tr>
				
				<!-- Integrate Adsense) -->
				<tr valign="top"><th scope="row"><label for="super_simple_google_analytics_item[adsense]"><?php echo __( 'Integrate linked Adsense account?', 'SuperSimpleGoogleAnalytics' ); ?></label></th>
					<td><input name="super_simple_google_analytics_item[adsense]" type="checkbox" value="1" <?php checked( $options['adsense'], 1 ); ?> /></td>
                </tr>
				
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php echo __( 'Save Changes', 'SuperSimpleGoogleAnalytics' ) ?>" />
			</p>
		</form>
	</div>
    
	<?php
}

//Google Analytics html tracking code to be inserted in header/footer
function ssGA_print_code() { 
	$options = get_option( 'super_simple_google_analytics_item', ssGA_default_options() ); 
	$plugin_url = 'http://bitacre.com/plugins/super-simple-google-analytics';

// code removed for admin
$admin = sprintf( '%1$s<!--%1$sPlugin: Super Simple Google Analytics%1$sPlugin URL: %2$s%1$s%1$s You\'ve chosen to prevent the tracking code from being inserted on %1$spages viewed by logged-in administrators.%1$s%1$sYou can re-enable the insertion of the tracking code on all pages%1$sfor all users by going to Settings > Google Analytics on the Dashboard. %1$s-->%1$s%1$s', "\r\n", $plugin_url );

// Simple tracker code as of 29-march-2012 (thank you Tacit Slager for finding the 1.4 bug!)
$code = sprintf( '<!--%1$s Plugin: Super Simple Google Analytics%1$sPlugin URL: %2$s%1$sGoogle Analytics Tracking Code. -->%1$s%1$s', "\r\n", $plugin_url );

if( $options['adsense'] == 1 || $options['adsense'] == '1' ) 
	$code .= sprintf( '<script type="text/javascript">%1$swindow.google_analytics_uacct = "UA-%2$s-%3$s";%1$s</script>%1$s%1$s', "\r\n", $options['account'], $options['profile'] );

$code .= sprintf( '<script type="text/javascript">%1$s%1$s%2$svar _gaq = _gaq || [];%1$s%2$s_gaq.push([\'_setAccount\', \'UA-%3$s-%4$s\']);%1$s%2$s_gaq.push([\'_trackPageview\']);%1$s%1$s%2$s(function() {%1$s%2$s%2$svar ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;%1$s%2$s%2$sga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';%1$s%2$s%2$svar s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);%1$s%2$s})();%1$s%1$s</script>%1$s',
	"\r\n", '  ', $options['account'], $options['profile'] ); 

// code removed for all pages
$disabled = sprintf( '<!--%1$s Plugin: Super Simple Google Analytics%1$sPlugin URL: %2$s%1$sYou\'ve chosen to prevent the tracking code from being inserted on %1$sany page. %1$s%1$sYou can enable the insertion of the tracking code by going to %1$sSettings > Google Analytics on the Dashboard. -->', "\r\n", $plugin_url );

	if( !$options['insert_code'] ) { 
		echo $disabled; 
		return; 
	} elseif( current_user_can( 'manage_options' ) && !$options['track_admin'] ) { 
		echo $admin; 
		return; 
	} else { 
		echo $code;  
		return; 
	}
}

function ssGA_load_textdomain() {
	$lang_dir = trailingslashit( basename( dirname(__FILE__) ) ) . 'lang';
	load_plugin_textdomain( 'SuperSimpleGoogleAnalytics', false,  $lang_dir );
}

// HOOKS AND FILTERS
add_filter( 'plugin_row_meta', 'ssGA_set_plugin_meta', 10, 2 ); // add plugin page meta links
add_action( 'admin_init', 'ssGA_options_init' ); // whitelist options page
add_action( 'admin_menu', 'ssGA_options_add_page' ); // add link to plugin's settings page
add_action( 'plugins_loaded', 'ssGA_loadtextdomain' ); // load i18n

// insert tracking code on page head initilization 
$options = get_option( 'super_simple_google_analytics_item', ssGA_default_options() );
if( $options['location'] == 'head' ) 
	add_action('wp_head', 'ssGA_print_code', 99999 ); 
elseif( $options['location'] == 'body' ) 
	add_action('wp_footer', 'ssGA_print_code', 99999 ); 
?>