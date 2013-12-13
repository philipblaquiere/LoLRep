<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Custom database layer
 *
 * This class interacts with the database and avoids use of the existing SQL
 * abstraction layer in CodeIgniter per the TA's request.
 */
class Database_layer {
  private $connection = NULL;
  private $preset = NULL;

  public function __construct($params = array(DEVELOPMENT_ENVIRONMENT)) {
    list($preset) = $params;
    if ($this->preset != $preset) {
      $this->connect_preset($preset);
    }
  }

  // --------------------------------------------------------------------

  function connect_preset($name = 'default') {
    $CI =& get_instance();
    $CI->config->load('database');
    $dbconf = $CI->config->item('database');
    if (!isset($dbconf[$name])) {
      show_error('The specified database preset \'' . $name . '\' does not exist.');
    }
    $this->connect($dbconf[$name]['hostname'], $dbconf[$name]['username'], $dbconf[$name]['password'], $dbconf[$name]['database']);
    $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  /**
   * Initiates a new database connection, discarding the previous one.
   */
  function connect($host=null, $username=null, $password=null, $database=null, $port=null, $socket=null) {
    // Disconnect if we are connected already.
    if ($this->connection) {
      $this->disconnect();
    }
    try {
      $this->connection = new PDO("mysql:host=$host;dbname=$database", $username, $password);
      $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }
    catch (PDOException $e) {
      show_error('Could not connect to the database: ' . $e->getMessage());
    }
  }

  /**
   * Close the database connection.
   */
  function disconnect()
  {
    $this->connection = NULL;
  }

  /**
   * Runs a query with binded parameters.
   */
  function query($sql, $bind = array()) {
    $query = $this->connection->prepare($sql);
    $query->execute($bind);
    return $query;
  }

  /**
   * Returns the ID of the last inserted row in the database.
   */
  function lastInsertId() {
    return $this->connection->lastInsertId();
  }

  // --------------------------------------------------------------------

}
// END Database_layer CLASS

/* End of file DatabaseLayer.php */
/* Location: ./application/libraries/DatabaseLayer.php */
