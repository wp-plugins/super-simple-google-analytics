<?php
/*
Plugin Name: Super Simple Google Analytics
Plugin URI: http://wikiduh.com/plugins/super-simple-google-analytics
Description: Bare bones option for people looking to simply insert the basic Google Analytics tracking code into the head section of every page without fuss.
Version: 1.3
Author: bitacre
Author URI: http://wikiduh.com
License: GPLv2 
	Copyright 2011 bitacre (plugins@wikiduh.com)
*/

/* FUNCTIONS */
function set_defaults($option_array_name = 'ssga_item') { // insert default values for first initialization (99% of users won't need to change this)
	if(!get_option($option_array_name)) { // if option doesn't exist yet:
		$defaultvars = array( // set defaults in array
			'sometext1' => '', // first part of tracking code
			'sometext2' => '', // second part of tracking code
			'insertcode' => 0, // insert code (0=hide, 1=show)
		);
		return add_option($option_array_name, $defaultvars, '', 'no'); // then make it with default values
	}
}

function set_plugin_meta($links, $file) { // define additional plugin meta links
	$plugin = plugin_basename(__FILE__); // '/super-simple-google-analytics/SuperSimpleGoogleAnalytics.php' by default
    if ($file == $plugin) { // if called for THIS plugin then:
		$newlinks=array('<a href="options-general.php?page=super-simple-google-analytics">Settings</a>'); // array of links to add
		return array_merge( $links, $newlinks ); // merge new links into existing $links
	}
return $links; // return the $links (merged or otherwise)
}

function ssga_options_init() { // add plugin's options to white list
	register_setting( 'ssga_options_options', 'ssga_item', 'ssga_options_validate' );
}

function ssga_options_add_page() { // add link to plugin's settings page under 'settings' on the admin menu 
	add_options_page('Super Simple Google Analytics Settings', 'Google Analytics', 'manage_options', 'super-simple-google-analytics', 'ssga_options_do_page');
}

function ssga_options_validate($input) { // sanitize and validate input. accepts an array, returns a sanitized array.
// sanatize inputs:
	$input['insertcode'] = ( $input['insertcode'] == 1 ? 1 : 0 ); 	// (checkbox) if 1 then 1, else 0
	$input['sometext1'] =  wp_filter_nohtml_kses($input['sometext1']); // (textbox) safe text, no html
	$input['sometext2'] =  wp_filter_nohtml_kses($input['sometext2']); // (textbox) safe text, no html
	return $input;
}

function ssga_options_do_page() { // draw the settings page itself
	?>
	<div class="wrap">
    <div class="icon32" id="icon-options-general"><br /></div>
		<h2>Super Simple Google Analytics Settings</h2>
		<form method="post" action="options.php">
			<?php settings_fields('ssga_options_options'); // nonce settings page ?>
			<?php $options = get_option('ssga_item'); // populate $options array from database ?>
			<table class="form-table">
				<tr valign="top"><th scope="row">Insert Tracking Code?</th>
					<td><input name="ssga_item[insertcode]" type="checkbox" value="1" <?php checked('1', $options['insertcode']); ?> /></td>
                </tr>
				<tr valign="top"><th scope="row">Google Analytics Numbers:</th>
					<td>
                    	UA-<input type="text" name="ssga_item[sometext1]" value="<?php echo $options['sometext1']; ?>" style="width:90px;" maxlength="8" />
	                    -<input type="text" name="ssga_item[sometext2]" value="<?php echo $options['sometext2']; ?>" style="width:30px;" maxlength="3" />
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
}

function wp_print_GAcode() { // code to run on wp_head
	$options = get_option('ssga_item');
	if($options['insertcode']) {
		$gacode =  "<!-- Google Analytics tracking code. Plugin URL: http://wikiduh.com/plugins/super-simple-google-analytics -->
<script type='text/javascript'>
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-" . $options['sometext1'] . "-" . $options['sometext2'] . "']);
_gaq.push(['_trackPageview']);

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
"; // Simple tracker code as of 15-dec-2011
	echo $gacode;}
	else echo "<!-- Super Simple Google Analytics is currently disabled. Plugin URL: http://wikiduh.com/plugins/super-simple-google-analytics -->
	";
} 

/* HOOKS AND FILTERS */
add_filter( 'plugin_row_meta', 'set_plugin_meta', 10, 2 ); // add meta links to plugin's section on 'plugins' page (10=priority, 2=num of args)
add_action('admin_init', 'ssga_options_init' ); // add plugin's options to white list on admin initialization
add_action('admin_init', 'set_defaults' ); // add plugin's options to white list on admin initialization
add_action('admin_menu', 'ssga_options_add_page'); // add link to plugin's settings page in 'settings' menu on admin menu initilization
add_action('wp_head', 'wp_print_GAcode'); // insert tracking code on page head initilization 

?>