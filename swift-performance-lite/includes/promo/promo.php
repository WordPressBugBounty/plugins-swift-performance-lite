<?php

class Swift_Performance_BugMonitor {

      public function __construct(){
            $dismissed = get_transient('swiftperformance_bmpromo');
            if ($dismissed < time()){
                  add_action('admin_notices', array($this, 'admin_notice'));
            }

            add_action('admin_init', array($this, 'navigate'));
      }

      public function admin_notice() {
          ?>
          <div id="bmpromo-notice" class="notice notice-success is-dismissible" style="padding: 10px 10px 20px;">
              <p><strong>Activate free BugMonitor for WordPress!</strong></p>
              <p>During their operation, most WordPress sites experience errors that can prevent them from functioning properly: you might not be able to submit a form, the checkout might not work, a button might not be clickable, a map might not load, etc.</p>
              <p>Visitors usually don't report these issues, but they can cause significant damage: lost sales, unnecessarily spent advertising money, and poor user experience.</p>
              <p>BugMonitor can detect and report these errors instantly, so they can be fixed before they cause significant damage to your business.</p>
              <a href="<?php echo add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG, 'subpage' => 'bmpromo', 'nonce' => wp_create_nonce('bmpromo')), admin_url('tools.php'));?>" class="button-primary">Activate now</a>
              <a href="<?php echo add_query_arg(array('page' => SWIFT_PERFORMANCE_SLUG, 'subpage' => 'bmpromo-dismiss', 'nonce' => wp_create_nonce('bmpromo')), admin_url('tools.php'));?>" class="button-secondary">Remind me later</a>
              <span style="position:absolute;bottom:10px;right:10px;font-size:.85em;">BugMonitor by Swift Performance</span>
          </div>
          <?php
      }

      public function navigate(){
            if (isset($_GET['page']) && $_GET['page'] == SWIFT_PERFORMANCE_SLUG && !empty($_GET['subpage']) && wp_verify_nonce($_GET['nonce'], 'bmpromo')){
                  switch ($_GET['subpage']){
                        case 'bmpromo':
                              define('AUTOMATIC_TRANSLATOR_UPDATES', false);

                              require_once ABSPATH . 'wp-admin/includes/plugin.php';
                              require_once ABSPATH . 'wp-admin/includes/file.php';
                              require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

                              $installation = null;
                              if (!file_exists(WP_PLUGIN_DIR .'/bug-monitor/main.php')){
                                    require_once __DIR__ . '/class.updater-skin.php';
                                    $upgrader = new Plugin_Upgrader(new Swift_Performance_Upgrader_Skin());
                                    $installation = $upgrader->install(__DIR__ . '/bug-monitor.zip');
                              }

                              if (!is_wp_error($installation)){
                                    activate_plugin('bug-monitor/main.php');
                              }

                              if (class_exists('Bug_Monitor')){
                                    Bug_Monitor::update_option('promo', 1);
                              }

                              wp_redirect(add_query_arg('page','bug-monitor', admin_url('tools.php')));
                              die;

                              break;
                        case 'bmpromo-dismiss':
                              set_transient('swiftperformance_bmpromo', time() + (WEEK_IN_SECONDS * 3));
                              wp_redirect(!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url());die;
                              break;
                  }
            }
      }
}

if (is_admin() && !defined('DOING_AJAX') && !defined('BUG_MONITOR_PLUGIN_FILE')){
      new Swift_Performance_BugMonitor();
}
?>