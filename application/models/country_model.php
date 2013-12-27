<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Country_Model {
  /**
   * countryid int
   * country varchar
   */

  public function __construct() {
  }

  public function get_supported_countries()
  {
  	$sql = "SELECT c.cid as cid, c.name as name FROM countries c INNER JOIN locationsupport l WHERE c.cid = l.countryid ";
  }
  public function get_supported_provincestates($countryid)
  {
  	$sql = "SELECT s.name as name, s.provincestateid as provincestateid FROM state s INNER JOIN locationsupport l ON s.provincestateid = l.provincestateid WHERE l.countryid = '$countryid'";
  }
  public function get_supported_region($provincestateid)
  {
  	$sql = "SELECT r.name as name, r.regionid as regionid FROM region r INNER JOIN locationsupport l ON r.regionid = l.regionid WHERE l.provincestateid = '$provincestateid'";
  }
}
