<?php
/*
Plugin Name: Super Simple Google Analytics
Plugin URI: http://wikiduh.com/plugins/super-simple-google-analytics
Description: Bare bones option for people looking to simply insert the basic Google Analytics tracking code into the head section of every page without fuss.
Version: 1.2
Author: bitacre
Author URI: http://wikiduh.com
License: GPLv2 
	Copyright 2011 bitacre (plugins@wikiduh.com)
*/

// PLUGIN SETUP
function set_plugin_meta($links, $file) {
	$plugin = plugin_basename(__FILE__);
    if ($file == $plugin) {
		return array_merge(
		$links,
		array( sprintf( '<a href="options-general.php?page=super-simple-google-analytics">Settings</a>', $plugin, __('Settings') ) )
		);
	}
return $links;
}
add_filter( 'plugin_row_meta', 'set_plugin_meta', 10, 2 );

// OPTIONS MENU
add_action('admin_init', 'ssga_options_init' );
add_action('admin_menu', 'ssga_options_add_page');

// Init plugin options to white list our options
function ssga_options_init(){
	register_setting( 'ssga_options_options', 'ssga_item', 'ssga_options_validate' );
}

// Add menu page
function ssga_options_add_page() {
	add_options_page('Super Simple Google Analytics Settings', 'Google Analytics', 'manage_options', 'super-simple-google-analytics', 'ssga_options_do_page');
}

// Draw the menu page itself
function ssga_options_do_page() {
	?>
	<div class="wrap">
		<h2 style="text-align:center;">Super Simple Google Analytics Settings</h2>
		<form method="post" action="options.php">
			<?php settings_fields('ssga_options_options'); ?>
			<?php $options = get_option('ssga_item'); ?>
			<table class="form-table">
				<tr valign="top"><th scope="row">Insert Tracking Code?</th>
					<td><input name="ssga_item[insertcode]" type="checkbox" value="1" <?php checked('1', $options['insertcode']); ?> /></td>
				</tr>
				<tr valign="top"><th scope="row">Google Analytics Numbers:</th>
					<td>UA-<input type="text" name="ssga_item[sometext1]" value="<?php echo $options['sometext1']; ?>" style="width:90px;" maxlength="8" />-<input type="text" name="ssga_item[sometext2]" value="<?php echo $options['sometext2']; ?>" style="width:30px;" maxlength="3" /></td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function ssga_options_validate($input) {
	// Our first value is either 0 or 1
	$input['insertcode'] = ( $input['insertcode'] == 1 ? 1 : 0 );
	// Say our second option must be safe text with no HTML tags
	$input['sometext1'] =  wp_filter_nohtml_kses($input['sometext1']);
	$input['sometext2'] =  wp_filter_nohtml_kses($input['sometext2']);
	return $input;
}

// PLUGIN MEAT
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
</script>";
	echo $gacode;}
	else echo "<!-- Super Simple Google Analytics is currently disabled. Plugin URL: http://wikiduh.com/plugins/super-simple-google-analytics -->";
}

add_action('wp_head', 'wp_print_GAcode'); ?>