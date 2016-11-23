<?php
/*
Plugin Name: WP SEO GA
Plugin URI: https://www.thyngster.com/tools/wp-seo-ga
Description: Wordpress plugin to track bot visits on Google Analytics
Version: 0.1
Author: David Vallejo
Author URI: https://www.thyngster.com
License: MIT
*/
/*
Copyright 2016  David Vallejo  (email : thyngster@gmail)
*/

require_once 'vendor/autoload.php';
use UAParser\Parser;

if(!class_exists('WP_Plugin_Seo_Ga'))
{
    class WP_Plugin_Seo_Ga
    {
        /**
         * Construct the plugin object
         */
        public function __construct()
        {
          // Add Main action, Track when WP has been fully loaded, to know the 404 status.
          add_action( 'wp', array( $this, 'trackGoogleAnalytics' ) );
          // Admin panel Actions
          add_action("admin_menu", "add_theme_menu_item");
          add_action("admin_init", "display_theme_panel_fields");

          function add_theme_menu_item()
          {
              add_menu_page("WP SEO GA", "WP SEO GA", "manage_options", "wp-seo-ga-panel", "theme_settings_page", null, 99);
          }

          function display_theme_panel_fields()
          {
              add_settings_section("section", "All Settings", null, "wp-seo-ga-options");
              add_settings_section("section", "About", null, "wp-seo-ga-about");
              add_settings_field("google_analytics_property_id", "Google Analytics Profile ID", "display_google_analytics_property_element", "wp-seo-ga-options", "section");
              add_settings_field("wp_seo_ga_endpoint", "Endpoint URL", "display_wp_seo_ga_endpoint_element", "wp-seo-ga-options", "section");
              register_setting("section", "google_analytics_property_id");
              register_setting("section", "wp_seo_ga_endpoint");

          }


          function display_google_analytics_property_element()
          {
              ?>
                  <input type="text" name="google_analytics_property_id" id="google_analytics_property_id" value="<?php echo get_option('google_analytics_property_id'); ?>" />
              <?php
          }
          function display_wp_seo_ga_endpoint_element()
          {
              ?>
                  <input type="text" size="80" name="wp_seo_ga_endpoint" id="wp_seo_ga_endpoint" value="<?php echo get_option('wp_seo_ga_endpoint'); ?>" />
              <?php
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
                          <hr /><br />
                          <br />This plugin uses <a href="https://github.com/ua-parser/uap-core">uap-core</a> to detect whenever a search bot visits your page, and then send the info about the bot and visited page to the configured Google Analytics Property ID.
                          <br />
                          <br />Read more about the Measurement Protocol <a href="https://developers.google.com/analytics/devguides/collection/protocol/v1/?hl=en">here</a> .
                          <br />
                          <br />Take in mind this a Beta product and it has not been deeply tested. Try it first on a testing enviroment before installing it on a production site.
                          <br />
                          <br />You may find the sourcecode on the following github repo: <a href="https://github.com/thyngster/wp-seo-ga">https://github.com/thyngster/wp-seo-ga</a>
                          <br />You may read some more info and leave a comment on the following blog post: <a href="https://www.thyngster.com/seo-meets-ga-tracking-search-engines-visits-within-measurement-protocol">https://www.thyngster.com/seo-meets-ga-tracking-search-engines-visits-within-measurement-protocol</a>
                          <br />
                          <br />David Vallejo <a href="https://www.twitter.com/thyng">@thyng</a>
                          &nbsp;
                          </div>
                         <?php
                          }
                          ?>
                  </form>
              </div>
          <?php
          }

        } // END public function __construct

        public function trackGoogleAnalytics(){
            $bot_details = $this->get_bot_details();
            if($bot_details["model"]!="Spider"){
              return;
            }

            if(preg_match('/(UA|YT|MO)-\d+-\d{1,3}$/', get_option("google_analytics_property_id"))==false){
              return;
            }

            $core_payload_template = array (
              'v' => '1',
              't' => 'pageview',
              'dl' => ($_SERVER["HTTPS"] == "on" ? "https" : "http").'://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],
              'ul' => substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2),
              'de' => 'UTF-8',
              'dt' => wp_get_document_title(),
              'cid' => $bot_details["ga_client_id"],
              'uid' => $bot_details["ga_user_id"],
              'tid' => get_option("google_analytics_property_id"),
              'ds' => 'wp-seo-ga',
              'a' => rand(100000,9999999),
              'z' => rand(100000,9999999),
              'cd1' => $bot_details["name"],
              'cd2' => $bot_details["model"],
              'cd3' => $bot_details["type"],
              'cd4' => $bot_details["user_agent"],
              'cd5' => '200',
              'cd6' => $bot_details["ga_client_id"],
              'cd7' => ip2long($bot_details["ip_address"]),
              'cd8' => round(memory_get_peak_usage() / 1024 /  1024, 0),
              'cd9' => timer_stop(),
              'cd10' => get_num_queries(),
              'cd11' => $bot_details["name"],
              'cd12' => $bot_details["model"],
              'cd13' => $bot_details["type"],
              'cd14' => ($_SERVER["HTTPS"] == "on" ? "https" : "http"),
              'cd15' => $_SERVER["SERVER_PROTOCOL"],
              'cd16' => $bot_details["ip_reverse_domain"]
            );

            if(is_404()){
              $core_payload_template["cd5"]= '404';
            }
            $this->send_ga_hit($core_payload_template);
        }

        public function send_ga_hit($payload){
          if(!get_option('wp_seo_ga_endpoint'))
              $endpoint_url = 'https://www.google-analytics.com/collect';
          $hitPayload = $endpoint_url."?".http_build_query($payload, '', '&');

          if(ini_get("allow_url_fopen")==true){
            // Create a stream
            $opts = array(
              'http'=>array(
                   'method'=>"GET",
                'header'=>"Accept-language: en\r\n" .
                             "User-agent: SEO Meets Google Analytics Plugin WP 0.2\r\n"
              )
            );
            $context = stream_context_create($opts);
            $file = file_get_contents($hitPayload, false, $context);
          } else {
            $file = $this->file_get_contents_curl($hitPayload);
          }
        }

        public function file_get_contents_curl($url, $opts = [])
        {
          if(function_exists('curl_version')){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_USERAGENT, "SEO Meets Google Analytics Plugin WP 0.2");
            curl_setopt($ch, CURLOPT_URL, $url);
            if(is_array($opts) && $opts) {
              foreach($opts as $key => $val) {
                curl_setopt($ch, $key, $val);
              }
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if(FALSE === ($retval = curl_exec($ch))) {
              error_log(curl_error($ch));
            } else {
              return $retval;
            }
          }
        }

        public function get_bot_details(){
          $ua = $_SERVER['HTTP_USER_AGENT'];
          $parser = Parser::create();
          $user_agent_parsed = $parser->parse($ua);
          $bot_info = array(
            'name' => $user_agent_parsed->ua->family,
            'model' => $user_agent_parsed->device->family,
            'type' => $user_agent_parsed->device->model,
            'ip_address' => $this->get_ip_address(),
            'user_agent' => $ua
          );

          $bot_info["ip_reverse_domain"] = $this->getRootDomain(gethostbyaddr($bot_info["ip_address"]));
          // Generate UUIDv4 from Ip Address
          $cid = md5(ip2long($bot_info["ip_address"]));
          $cid = substr_replace($cid, "-", 8, 0);
          $cid = substr_replace($cid, "-", 12, 0);
          $cid = substr_replace($cid, "-", 16, 0);
          $cid = substr_replace($cid, "-", 20, 0);
          $bot_info["ga_client_id"] = $cid;

          // Set bot's User ID
          $bot_info["ga_user_id"] = ip2long($bot_info["ip_address"]);
          return $bot_info;
        }

        public function getRootDomain($domain){
          // Not the best way to do this, trying to avoid the need to deal with Top TLD lists
          if (preg_match("/\.[a-z]{2,3}\.[a-z]{2,3}$/i", $domain)) {
              return implode(".",array_slice(explode(".",$domain), -3, 3, true));
          }else{
              return implode(".",array_slice(explode(".",$domain), -2, 2, true));
          }
        }

        public function get_ip_address() {
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

        /**
         * Activate the plugin
         */

        public static function activate()
        {
          // Set the GA endpoint value to default value on activations
          update_option('wp_seo_ga_endpoint','https://www.google-analytics.com/collect');
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
    //register_deactivation_hook(__FILE__, array('WP_Plugin_Seo_Ga', 'deactivate'));

    $wp_plugin_seo_ga = new WP_Plugin_Seo_Ga();
}
