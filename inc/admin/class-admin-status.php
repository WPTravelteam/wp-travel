<?php

class WT_Admin_status {

    private $_db;
    private static $_instance;
    public $yes_text;
    public $no_text;

    public function __construct() {
        global $wpdb;

        $this->_db = $wpdb;
        $this->yes_text = '<span class="no"> <span class="dashicons dashicons-yes"></span> Yes</span> ';
        $this->no_text = '<span class="no"> <span class="dashicons dashicons-no"></span> No </span>';
    }

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new WT_Admin_status();
        }

        return self::$_instance;
    }

    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        // $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    function memory_size_convert($size) {
        $l = substr($size, -1);
        $ret = substr($size, 0, -1);
        switch (strtoupper($l)) {
            case 'P':
                $ret *= 1024;
            case 'T':
                $ret *= 1024;
            case 'G':
                $ret *= 1024;
            case 'M':
                $ret *= 1024;
            case 'K':
                $ret *= 1024;
        }
        return $ret;
    }

    function check_memory() {
        $memory = $common->memory_size_convert(WP_MEMORY_LIMIT);
        if (function_exists('memory_get_usage')) {
            $system_memory = $common->memory_size_convert(@ini_get('memory_limit'));
            $memory = max($memory, $system_memory);
        }
        if ($memory < 67108864) {
            $memory_text = '<span class="warning"><span class="dashicons dashicons-warning"></span> ' . sprintf(__('%s - For better performance, we recommend setting memory to at least 64MB. See: %s', 'wp-travel' ), size_format($memory), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . __('Increasing memory allocated to PHP', 'wp-travel' ) . '</a>') . '</span>';
        } else {
            $memory_text = '<span class="ok">' . size_format($memory) . ' </span>';
        }

        return $memory_text;
    }

    function checkPHPVersion() {
        if (function_exists('phpversion')) {
            $php_version = phpversion();

            if (version_compare($php_version, '5.6', '<')) {
                $php_version_text = '<span class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf(__('%s - Recommend  PHP version of 5.6. See: %s', 'wp-travel' ), esc_html($php_version), '<a href="#" target="_blank">' . __('How to update your PHP version', 'wp-travel' ) . '</a>') . '</span>';
            } else {
                $php_version_text = '<span class="yes">' . esc_html($php_version) . '</span>';
            }
        } else {
            $php_version_text = __("Couldn't determine PHP version because phpversion() doesn't exist.", 'wp-travel' );
        }

        return $php_version_text;
    }

    function checkcURL() {
        if (function_exists('curl_version')) {
            $curl_version = curl_version();
            $curl_text = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
        } else {
            $curl_text = __('N/A', 'wp-travel' );
        }
    }

    function checkMySQL() {
        if ($this->_db->use_mysqli) {
            $ver = mysqli_get_server_info($this->_db->dbh);
        } else {
            $ver = mysql_get_server_info();
        }
        if (!empty($this->_db->is_mysql) && !stristr($ver, 'MariaDB')) {
            $mysql_text = $this->_db->db_version();
            if (version_compare($mysql_version, '5.6', '<')) {
                $mysql_text .= '<span class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf(__('%s - We recommend a minimum MySQL version of 5.6. See: %s', 'wp-travel' ), esc_html($mysql_version), '<a href="https://wordpress.org/about/requirements/" target="_blank">' . __('WordPress Requirements', 'wp-travel' ) . '</a>') . '</span>';
            } else {
                $mysql_text .= '<span class="yes">' . esc_html($mysql_version) . '</span>';
            }
        }
    }

    function checkRemoteStatus() {

        $response = wp_remote_get('https://www.paypal.com/cgi-bin/webscr', array(
                'timeout' => 60,
                'user-agent' => 'travel/' . 1.0,
                'httpversion' => '1.1',
                'body' => array(
                    'cmd' => '_notify-validate'
                )
            ));
            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code == 200) {

                $return  = true;
            } else {
                $$return = false;
            }

            return $return;

    }

    public function wpInfo() {

        $upload_dir = wp_upload_dir();

        $data = array(
            __('Home URL', 'wp-travel' ) => form_option('home'),
            __('Site URL', 'wp-travel' ) => form_option('siteurl'),
            __('WP Version', 'wp-travel' ) => bloginfo('version'),
            __('WP Multisite', 'wp-travel' ) => is_multisite() ? $this->yes_text : $this->no_text,
            __('WP Memory Limit', 'wp-travel' ) => $this->check_memory(),
            __('WP Debug Mode', 'wp-travel' ) => (defined('WP_DEBUG') && WP_DEBUG) ? $this->yes_text : $this->no_text,
            __('WP Cron', 'wp-travel' ) => (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) ? $this->yes_text : $this->no_text,
            __('Language', 'wp-travel' ) => get_locale(),
            __('Upload Directory  Location', 'wp-travel' ) => $upload_dir['baseurl'],
        );

        return $data;
    }

    function serverInfo() {

         $default_timezone = date_default_timezone_get();

        $data = array(
            __('Server Info', 'wp-travel' ) => esc_html($_SERVER['SERVER_SOFTWARE']),
            __('PHP Version', 'wp-travel' ) => $this->checkPHPVersion(),
        );

        if (function_exists('ini_get')) {

            $init_data = array(
                __('PHP Post Max Size', 'wp-travel' ) => size_format($common->memory_size_convert(ini_get('post_max_size'))),
                __('PHP Time Limit', 'wp-travel' ) => ini_get('max_execution_time'),
                __('PHP Max Input Vars', 'wp-travel' ) => ini_get('max_input_vars'),
                __('cURL Version', 'wp-travel' ) => $this->checkcURL(),
                __('SUHOSIN Installed', 'wp-travel' ) => extension_loaded('suhosin') ? $this->yes_text : $this->no_text,
                __('MySQL Version', 'wp-travel' ) => $this->checkMySQL(),
                __('Max Upload Size', 'wp-travel' ) => size_format(wp_max_upload_size()),
                __('Default Timezone is UTC', 'wp-travel' ) => ('UTC' !== $default_timezone) ? $this->no_text : $this->yes_text,
                __('PHP Error Log File Location', 'wp-travel' ) => ini_get('error_log'),

                __('fsockopen/cURL', 'wp-travel' ) => (function_exists('fsockopen') || function_exists('curl_init')) ? $this->yes_text : $this->no_text,
                __('SoapClient', 'wp-travel' ) => (class_exists('SoapClient'))? $this->yes_text : $this->no_text,
                __('DOMDocument', 'wp-travel' ) => (class_exists('DOMDocument')) ? $this->yes_text : $this->no_text,
                __('GZip', 'wp-travel' ) =>  (is_callable('gzopen')) ? $this->yes_text : $this->no_text ,
                __('Multibyte String', 'wp-travel' ) => (extension_loaded('mbstring')) ? $this->yes_text : $this->no_text,
                __('Remote Get Status', 'wp-travel' ) => $this->checkRemoteStatus() ? $this->yes_text : $this->no_text,
                __('', 'wp-travel' ) => '',
            );
        }
    }

    function themeInfo() {

    }

    function pluginInfo() {

    }

}
