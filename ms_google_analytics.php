<?php
/*
Plugin Name: Google Analytics
Plugin URI: http://michaelseiler.net
Description: Adds a Google analytics tracking code to the head of your theme, by hooking to wp_head.
Author: Michael Seiler
Version: 1.0
Original: Rachel McCollin, http://rachelmccollin.com, via https://premium.wpmudev.org/blog/create-google-analytics-plugin
 */

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );

function add_action_links ( $links ) {
 $mylinks = array(
 '<a href="' . admin_url( 'options-general.php?page=ms_google_analytics/ms_google_analytics.php' ) . '">Settings</a>',
 );
return array_merge( $links, $mylinks );
}

function ms_google_analytics() { 
$msga_account = get_option('MSGA_ACCOUNT');
?>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		ga('create','<?php echo $msga_account;?>','auto');
		ga('send', 'pageview');
		
		</script>
<?php }
add_action( 'wp_head', 'ms_google_analytics', 10 );

# CHECK TO SEE IF WE ARE GETTING AN UPDATE FROM OUR ADMIN SETTINGS PAGE
if( ($_POST) && ($_POST['updateMSGA'] == "YES") )
{
	# UPDATE WP_OPTIONS TABLE
	update_option('MSGA_ACCOUNT',$_POST['msga_account']);
		
	# NOW CALL THE ADMIN SETTINGS MENU AGAIN
	add_action('admin_menu', 'msga_admin_menu');
}
else
{
	# CALL THE ADMIN SETTINGS MENU
	add_action('admin_menu', 'msga_admin_menu');
}

function msga_admin_menu() {
	add_options_page("Google Analytics Settings","Google Analytics Settings","activate_plugins",__FILE__,"msga_options_page");
}

function msga_options_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	else
	{
		$msga_account = get_option('MSGA_ACCOUNT');
?>
<div class="wrap">
<h2><?php _e('Settings for Google Analytics','msga');?></h2>
<?php if($_POST['updateMSGA'] == "YES") { ?>
<div id='setting-error-settings_updated' class='updated settings-error'><p><strong><?php echo _e('Tracking ID Saved','msga');?></strong></p></div>
<?php } ?>
<h3>Google Analytics Plugin</h3>
<p>Enter the Tracking ID from Google below (UA-XXXXXXXX-XX):</p>
<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="updateMSGA" value="YES">
<table border="0" width="50%" cellpadding="2" cellspacing="0">
    <tr>
        <td><input type="text" name="msga_account" id="msga_account" size="30" value="<?php echo $msga_account;?>"></td>
    </tr>
	<tr>
        <td><input type="submit" name="Submit" value="<?php _e('Save Tracking ID','msga');?>"></td>
    </tr>
</table>
</form>
</div>
<?php
	}
}
