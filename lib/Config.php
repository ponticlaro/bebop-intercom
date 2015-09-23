<?php

namespace Ponticlaro\Bebop\Intercom;

use Ponticlaro\Bebop\Common\Collection;

class Config {

  /**
   * Plugin configuration version
   */
  const CONFIG_VERSION = 'v1';

  /**
   * Key for wordpress option that contains the plugin configuration version
   */
  const CONFIG_VERSION_OPTION_KEY = 'po_bebop_intercom_version';

  /**
   * Key for wordpress option that contains the plugin configuration
   */
  const CONFIG_OPTION_KEY = 'po_bebop_intercom';

  /**
   * Key for wordpress option that contains the configuration for the Intercom Javascript library
   */
  const REMOTE_CONFIG_OPTION_KEY = 'po_bebop_intercom_remote_config';

  /**
   * Key for wordpress option that flags that it failed to retrieve configuration from remote config URL
   */
  const REMOTE_CONFIG_FAILED_TIMESTAMP_OPTION_KEY = 'po_bebop_intercom_remote_config_failed_at';

  /**
   * Time to wait before attempting again to get remote configuration
   */
  const REMOTE_CONFIG_ATTEMPT_INTERVAL = 30; // 10 minutes

  /**
   * Expiration time for the remote configuration
   */
  const REMOTE_CONFIG_OPTION_EXPIRATION = 21600; // 6 hours

  /**
   * This class instance
   *
   * @var Ponticlaro\Bebop\Intercom\Config
   */
  protected static $instance;

  /**
   * Map matching configuration keys with their environment variables
   *
   * @var array
   */
  protected $env_config_map = [
      'dev_env_enabled'     => 'PO_BEBOP_INTERCOM__DEV_ENV_ENABLED',
      'app_id'              => 'PO_BEBOP_INTERCOM__APP_ID',
      'remote_config_url'   => 'PO_BEBOP_INTERCOM__REMOTE_CONFIG_URL',
      'dont_send_user_data' => 'PO_BEBOP_INTERCOM__DONT_SEND_USER_DATA',
      'allow_visitors'      => 'PO_BEBOP_INTERCOM__ALLOW_VISITORS'
  ];

  /**
   * Configuration data
   *
   * @var object Ponticlaro\Bebop\Common\Collection
   */
  protected $data;

  /**
   * Instantiates class
   *
   */
  protected function __construct()
  {
    // Initialize configuration object
    $this->data = new Collection(get_option(static::CONFIG_OPTION_KEY) ?: []);
  }

  /**
   * Returns class instance
   *
   * @return Ponticlaro\Bebop\Intercom\Config
   */
  public static function getInstance()
  {
    if (is_null(static::$instance))
      static::$instance = new static;

    return static::$instance;
  }

  /**
   * Returns a single configuration value
   *
   * @param  string $key Configuration key
   * @return mixed       Configuration value, otherwise null
   */
  public function get($key)
  {
      return $this->hasEnv($key) ? $this->getEnv($key) : $this->data->get($key);
  }

  /**
   * Sets a single configuration value
   *
   * @param string $key   Configuration key
   * @param mixed  $value Configuration value
   */
  public function set($key, $value)
  {
      $this->data->set($key, $value);

      return $this;
  }

  /**
   * Returns configuration value from a constant
   *
   * @param  string $key Configuration key
   * @return mixe        The constant value, otherwise null
   */
  public function getEnv($key)
  {
      return isset($this->env_config_map[$key]) && getenv($this->env_config_map[$key]) ? getenv($this->env_config_map[$key]) : null;
  }

  /**
   * Checks if the target configuration constant was defined
   *
   * @param  string $key Configuration key
   * @return mixed       True if defined, otherwise false
   */
  public function hasEnv($key)
  {
      return isset($this->env_config_map[$key]) && getenv($this->env_config_map[$key]) ? true : false;
  }
}
