<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Debugging library
 *
 * A collection of handy calls for debugging.
 */
class Debugging {

  function pnd($item)
  {
    if (is_bool($item)) {
      die('<pre>' . print_r($item ? 'TRUE' : 'FALSE', TRUE) . '</pre>');
    }
    else {
      die('<pre>' . print_r($item, TRUE) . '</pre>');
    }
  }

  /**
   * Initiates a new database connection, discarding the previous one.
   */
  function connect($host=null, $username=null, $password=null, $database=null, $port=null, $socket=null) {
    // Disconnect if we are connected already.
    if ($this->connection) {
      $this->disconnect();
    }

    $connection = mysqli_connect($host, $username, $password, $database, $port, $socket);
    // Was there an error connecting?
    if ( mysqli_connect_errno() ) {
      die("Could not connect to ${host}: [" . mysqli_connect_errno() . "] " . mysqli_connect_error());
    }
    $this->connection = &$connection;
  }

  // --------------------------------------------------------------------

  /**
   * Close the database connection.
   */
  function disconnect() {
    $this->connection->close();
    $this->connection = NULL;
  }

  // --------------------------------------------------------------------

  /**
   * Executes SQL queries securely, avoiding SQL injections by parametrizing
   * all queries.
   * @param $query
   *   The SQL query string, with ? as a placeholder for text replacement
   * @param $params
   *   An array containing a number of child arrays equal to the number of
   *   parameters to parametrize in the $query. It will be parsed, split up and
   *   passed to mysqli_stmt::bind_param(). Child arrays contain two values, the
   *   type specification ("s", "i", "d" or "b") and replacement value.
   * @returns
   *
   * Example useage:
   *   $queryString = "SELECT * FROM `testing` WHERE `id`=? OR `id`=?"
   *   $result = $db->query($queryString, array( array('i', 1), array('i', 2) ));
   * $result[0]["field1"] would return the value for "field1" of the first row.
   * $result[1]["field2"] would return the value for "field2" of the second row.
   *
   * Side not - we will want to keep our eyes on Interpolique:
   * http://recursion.com/interpolique_sql.html
   */
  function query($query, $params=array()) {
    if ( $this->connection === NULL ) {
      return false;
    }

    $stmt = $this->connection->prepare($query);

    if ( !empty($params) ) {
      $parsedParams = array();
      // the master type specification string
      $parsedParams[] = "";

      $valid_types = array('s', 'i', 'd', 'b');
      // For each of the parameters...
      foreach ( $params as &$paramPair ) {
        if (!is_array($paramPair)) {
          // User probably has not passed an array of arrays
          trigger_error("Query parameters must be an array of arrays", E_USER_WARNING);
          break;
        }
        // Ensure the type string is valid
        if ( !in_array($paramPair[0], $valid_types) ) {
          return false;
        }
        // Append the type to the master typestring
        $parsedParams[0] .= $paramPair[0];
        // Append the value to the array
        $parsedParams[] = &$paramPair[1];
      }

      // $parsedParams is an array containing the list parameters we would
      // normally pass to $stmt->bind_param(), so use call_user_func_array() to
      // pass the elements of the array to the function in order.
      call_user_func_array(array(&$stmt, 'bind_param'), $parsedParams);
    }

    $stmt->execute();

    // Get the result metadata in order to find field names
    $md = $stmt->result_metadata();

    // If doing an INSERT or similar operation, we may not have any results.
    if ($md == false) {
      return array(array());
    }

    // This is what we bind the variables to and eventually return.
    $resultset = array();
    // This is the array containing the list of variables that will be passed
    // pass to bind_result. It will contain various indices of $resultset.
    $bindto = array();

    while ( $field = $md->fetch_field() ) {
      // Bind the value in <column> to $resultset[<column>]
      $bindto[] = &$resultset[$field->name];
    }
    call_user_func_array(array(&$stmt, 'bind_result'), $bindto);

    // For each row in the resultset

    $rows = array();
    while ( $stmt->fetch() ) {
      // This will NOT work:
      // $rows[] = $resultset;
      // $resultset is full of references so all you get is data from the last
      // row. Instead, we'll have to copy each part of the array bit by bit.
      $thisrow = array();
      foreach($resultset as $key => $value) {
        $thisrow[$key] = $value;
      }
      $rows[] = $thisrow;
    }

    $stmt->close();
    return $rows;
  }

}
// END Database_layer CLASS

/* End of file DatabaseLayer.php */
/* Location: ./application/libraries/DatabaseLayer.php */
