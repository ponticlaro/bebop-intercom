<?php

namespace Ponticlaro\Bebop\Intercom;

use Ponticlaro\Bebop\ScriptsLoader\Js;

class WordPressPlugin {

    /**
     * This class current and only instance
     *
     * @var object Ponticlaro\Bebop\Intercom\WordPressPlugin
     */
    protected static $instance;

    /**
     * Boots plugin
     *
     * @param  string $caller_file Absolute path a file inside the root directory of the plugin
     * @return object              Ponticlaro\Bebop\Intercom\WordPressPlugin instance
     */
    public static function boot($caller_file)
    {
        if (is_null(static::$instance))
            static::$instance = new static($caller_file);

        return static::$instance;
    }

    /**
     * Returns the single instance of the plugin class
     *
     * @return object Ponticlaro\WordPress\CloudMedia\Plugin
     */
    public static function getInstance($caller_file = null)
    {
        if (is_null(static::$instance))
            static::boot($caller_file);

        return static::$instance;
    }

    /**
     * Instantiates CloudMedia WordPress plugin
     */
    protected function __construct($caller_file)
    {
        if (!is_readable($caller_file))
            throw new \Exception('$caller_file needs to be readable. Could not read '. $caller_file .'"');

        // Instantiate configuration
        $config = Config::getInstance();

        // Store base URL and path
        $config->set('plugin_base_url', plugin_dir_url($caller_file));
        $config->set('plugin_base_path', plugin_dir_path($caller_file));

        // Register activation/deactivation/uninstallation hook actions
        register_activation_hook(__FILE__, [__CLASS__, 'onActivation']);
        register_uninstall_hook(__FILE__, [__CLASS__, 'onUninstallation']);

        // Register 
        add_action('init', [$this, 'onInit']);
    }

    /**
     * Runs whenever the plugin is activated
     *
     * @return void
     */
    public static function onActivation()
    {
        static::getInstance();

        // Store plugin configuration version if not present
        if (!get_option(Config::CONFIG_VERSION_OPTION_KEY))
            add_option(Config::CONFIG_VERSION_OPTION_KEY, Config::CONFIG_VERSION);
    }

    /**
     * Runs whenever the plugin is uninstalled
     *
     * @return void
     */
    public static function onUninstallation()
    {
        delete_option(Config::CONFIG_VERSION_OPTION_KEY);
        delete_option(Config::CONFIG_OPTION_KEY);
    }

    /**
     * Register stuff on the init hook
     * 
     * @return void
     */
    public function onInit()
    {
        $config = Config::getInstance();

        if ($config->get('app_id') && is_user_logged_in()) {

            // Get logged in user data
            global $current_user;
            get_currentuserinfo();

            $script_id          = 'bebop-intercom';
            $script_name        = $config->get('dev_env_enabled') ? 'bebop-intercom.js' : 'bebop-intercom.min.js';
            $script_path        = $config->get('plugin_base_url') .'assets/js/'. $script_name;
            $localization_name  = 'intercomSettings';
            $localization_value = [
                'app_id'     => $config->get('app_id'),
                'created_at' => date('U')
            ];

            // Add user WordPress data, if enabled
            if (!$config->get('dont_send_user_data')) {
                $localization_value['user_id'] = $_SERVER['HTTP_HOST'] .'-'. $current_user->ID;
                $localization_value['email']   = $current_user ? $current_user->user_email : null;
                $localization_value['name']    = $current_user ? $current_user->display_name : null;
            }

            // Register, localize and enqueue plugin script on both front and back-end
            $js = JS::getInstance();
            $js->getHook('front')
               ->register($script_id, $script_path)
               ->localize($script_id, $localization_name, $localization_value)
               ->enqueue($script_id);

            $js->getHook('back')
               ->register($script_id, $script_path)
               ->localize($script_id, $localization_name, $localization_value)
               ->enqueue($script_id);
        }
    }
}