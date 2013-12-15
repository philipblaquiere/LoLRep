<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file
 * Provides session-based semi-persistent messages that are cleared only upon
 * display to the user. Messages are rendered on any view.
 *
 * This model is not responsible for loading the PHP session, it is designed for
 * use with controllers like MY_SessionController which load the PHP session
 * upon construction.
 */

define('MESSAGE_NOTICE', 'notice');
define('MESSAGE_WARNING', 'warning');
define('MESSAGE_ERROR', 'error');

class System_Message_Model extends CI_Model {
  public function __construct() {
    parent::__construct();
    if (!isset($_SESSION['system_messages'])) {
      $_SESSION['system_messages'] = array();
    }
  }

  /**
   * Stores a new message for display to the user upon the next page load.
   *
   * @param $message
   *   The message to be displayed to the user.
   * @param $level
   *   The severity level of the message, possible values are MESSAGE_NOTICE,
   *   MESSAGE_WARNING or MESSAGE_ERROR.
   */
  public function set_message($message, $level = MESSAGE_NOTICE) {
    $_SESSION['system_messages'][$level][] = $message;
  }

  /**
   * Retrieves all messages cached for display.
   * @param $clear
   *   If TRUE, the stored messages will be cleared from the user's session.
   */
  public function get_messages($clear = TRUE) {
    $messages = $_SESSION['system_messages'];
    if ($clear) {
      $_SESSION['system_messages'] = array();
    }
    return $messages;
  }
}
