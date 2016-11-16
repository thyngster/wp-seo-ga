<?php
/*
Plugin Name: WP SEO GA
Plugin URI: https://www.thyngster.com/tools/wp-seo-ga
Description: Wordpress plugin to track bot visits on Google Analytics
Version: 0.1
Author: David Vallejo
Author URI: https://www.thyngster.com
License: GPL2
*/
/*
Copyright 2016  David Vallejo  (email : thyngster@gmail)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once 'vendor/autoload.php';
use UAParser\Parser;


if(!class_exists('WP_Plugin_Seo_Ga'))
{
    $user_agent_parsed;
    class WP_Plugin_Seo_Ga
    {
        /**
         * Construct the plugin object
         */

        public function __construct($data)
        {
		$user_agent_parsed = $data;
		if($user_agent_parsed->device->family=="Spider" ||$_GET["dev"]==1){
	        	add_action( 'wp', 'track_search_bots' );
		}

		function theme_settings_page()
		{
		    ?>
			    <div class="wrap">
			    <h1>SEO Meets Google Analytics</h1>
	        <?php
		   $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_options';
	        ?>
		           <h2 class="nav-tab-wrapper">
			            <a class="nav-tab <?php echo $active_tab == 'general_options' ? 'nav-tab-active' : ''; ?>" href="?page=wp-seo-ga-panel&tab=general_options" class="nav-tab">General</a>
			            <a class="nav-tab <?php echo $active_tab == 'about' ? 'nav-tab-active' : ''; ?>" href="?page=wp-seo-ga-panel&tab=about" class="nav-tab">About</a>
		        </h2>
			    <form method="post" action="options.php">
			        <?php
				    if( $active_tab == 'general_options' ) {
				            settings_fields("section");
						
					    echo "<br /><br />This plugin takes care of detecting search bot visits, and register them on Google Analytics using the measurement protocol. Got to the <a href='/wp-admin/admin.php?page=wp-seo-ga-panel&tab=about'>About tab</a> to get more info.";
				            do_settings_sections("wp-seo-ga-options");
				            echo "* Please use a value with the following format:<br /> <strong>/(UA|YT|MO)-\d+-\d+/</strong>";
				            submit_button(); 
				    }else{
				            do_settings_sections("wp-seo-ga-about");
					?>
					<div id="post-body-content">
						WP SEO GA version 0.1
	
						<hr />
						<br />
						<br />This plugin uses <a href="https://github.com/ua-parser/uap-core">uap-core</a> to detect whenever a search bot visits your page, and then send the info about the bot and visited page to the configured Google Analytics Property ID.	
						<br />
						<br />Read more about the Measurement Protocol <a href="https://developers.google.com/analytics/devguides/collection/protocol/v1/?hl=en">here</a> .
						<br />
						<br />Take in mind this a Beta product and it has not been deeply tested. Try it first on a testing enviroment before installing it on a production site.
						<br />
						<br />You may find the sourcecode on the following github repo: <a href="https://github.com/thyngster/wp-seo-ga">https://github.com/thyngster/wp-seo-ga</a>
						<br />You may read some more info and leave a comment on the following blog post: <a href="https://www.thyngster.com/seo-meets-ga-tracking-search-engines-visits-within-measurement-protocol">https://www.thyngster.com/seo-meets-ga-tracking-search-engines-visits-within-measurement-protocol</a>
						<br />
						<br /><a href="https://www.twitter.com/thyng">@thyng</a>

&nbsp;
					</div>
 
				       <?php
					    echo "<p>This </p>";
				    }
			        ?>
			    </form>
			</div>
		<?php
		}

		function display_google_analytics_property_element()
		{
			?>
		    	<input type="text" name="google_analytics_property_id" id="google_analytics_property_id" value="<?php echo get_option('google_analytics_property_id'); ?>" />
		    <?php
		}


		function display_theme_panel_fields()
		{
			add_settings_section("section", "All Settings", null, "wp-seo-ga-options");
			add_settings_section("section", "About", null, "wp-seo-ga-about");
			add_settings_field("google_analytics_property_id", "Google Analytics Profile ID", "display_google_analytics_property_element", "wp-seo-ga-options", "section");
		        register_setting("section", "google_analytics_property_id");
		}

		add_action("admin_init", "display_theme_panel_fields");


		function add_theme_menu_item()
		{
			add_menu_page("WP SEO GA", "WP SEO GA", "manage_options", "wp-seo-ga-panel", "theme_settings_page", null, 99);
		}

		add_action("admin_menu", "add_theme_menu_item");

		function getUserIP() {
		    if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
		        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
		            $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
		            return trim($addr[0]);
		        } else {
		            return $_SERVER['HTTP_X_FORWARDED_FOR'];
		        }
		    }
		    else {
		        return $_SERVER['REMOTE_ADDR'];
		    }
		}

		function track_search_bots()
		{
          	     global $user_agent_parsed;
		     if(preg_match('/(UA|YT|MO)-\\d+-\\d+/', get_option("google_analytics_property_id"), $matches)==1){
		      $reverse = gethostbyaddr(getUserIP());
	       	      $payload_template = array (
		 	       'v' => '1',
			        '_v' => 'j44',
			        'a' => '333565997',
	        		't' => 'pageview',
			        '_s' => '1',
			        'dl' => '',
		        	'ul' => 'en',
			        'de' => 'UTF-8',
			        'dt' => '',
		        	'sd' => '24-bit',
			        'sr' => '1920x1080',
			        'vp' => '1771x429',
			        'je' => '0',
			        'fl' => '22.0 r0',
	        		'_u' => 'SDCCiEIrB~',
			        'jid' => '',
			        'cid' => '884755937.1468560970',
	        		'tid' => get_option("google_analytics_property_id"),
			        'z' => '509999257'
		      );
		      $cid = md5(ip2long(getUserIP()));
		      $cid = substr_replace($cid, "-", 8, 0);
		      $cid = substr_replace($cid, "-", 12, 0);
		      $cid = substr_replace($cid, "-", 16, 0);
		      $cid = substr_replace($cid, "-", 20, 0);

		      $payload_template["uid"] = ip2long(getUserIP());
		      $payload_template["cid"] = $cid;
		      $payload_template["z"] = rand(100000,9999999);
		      $payload_template["a"] = rand(100000,9999999);
		      $payload_template["ds"] = "wp-seo-ga";
		      //$payload_template["dh"] = $_SERVER["SERVER_NAME"];
		      $payload_template["dl"] = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		      $payload_template["cd1"] = $user_agent_parsed->ua->family;
		      if(!preg_match('/.*(googlebot|google)\.com$/', $reverse, $matches) && $user_agent_parsed->ua->family == "Googlebot"){
			      $payload_template["cd1"] = "Fake-".$user_agent_parsed->ua->family;
		      }
		      $payload_template["cd1"] = $user_agent_parsed->ua->family;
		      $payload_template["cd2"] = $user_agent_parsed->device->family;
		      $payload_template["cd3"] = $user_agent_parsed->device->model;
		      $payload_template["cd4"] = $_SERVER['HTTP_USER_AGENT'];
	 	      if(is_404()){
		              $payload_template["cd5"] = "404";
		      }else{
		              $payload_template["cd5"] = "200";
		      }

		      $payload_template["cd6"] = $cid;
		      $payload_template["cd7"] = ip2long(getUserIP());
	              $payload_template["cd8"] = round(memory_get_peak_usage() / 1024 /  1024, 0);
                      $payload_template["cd9"] = timer_stop();
                      $payload_template["cd10"] = get_num_queries();
       	              $payload_template["cd11"] = $user_agent_parsed->ua->family;
                      $payload_template["cd12"] = $user_agent_parsed->device->family;
       	              $payload_template["cd13"] = $user_agent_parsed->device->model;
		      $hitPayload = "https://www.google-analytics.com/collect?".http_build_query($payload_template, '', '&');
//		      $hitPayload = "https://www.thyngster.com/collect?".http_build_query($payload_template, '', '&');
		      // Create a stream
		      $opts = array(
		        'http'=>array(
       			  'method'=>"GET",
		          'header'=>"Accept-language: en\r\n" .
       			            "User-agent: SearchBot Tracking Plugin WP 1.0\r\n"
		        )
		      );
		      $context = stream_context_create($opts);
		      $file = file_get_contents($hitPayload, false, $context);	
		     }
		}
        } // END public function __construct
    


        /**
         * Activate the plugin
         */

        public static function activate()
        {
            // Do nothing
        } // END public static function activate
    
        /**
         * Deactivate the plugin
         */     
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate


    } // END class WP_Plugin_Template
} // END if(!class_exists('WP_Plugin_Template'))


if(class_exists('WP_Plugin_Seo_Ga'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('WP_Plugin_Seo_Ga', 'activate'));
	register_deactivation_hook(__FILE__, array('WP_Plugin_Seo_Ga', 'deactivate'));
	// instantiate the plugin class
	$ua = $_SERVER['HTTP_USER_AGENT'];
//	$ua = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
	$parser = Parser::create();
	$user_agent_parsed = $parser->parse($ua);
	$wp_plugin_seo_ga = new WP_Plugin_Seo_Ga($user_agent_parsed);
}
