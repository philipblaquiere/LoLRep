<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class League_model extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
	}

    public function get_league_byid($leagueid) {
      $sql = "SELECT * FROM leagues
          WHERE leagueid = '$leagueid'
          LIMIT 1";
      $result = $this->db1->query($sql);
      return $result->row_array();
    }
  	public function get_league_by_name($leaguename) {
      $leaguename = $this->make_mysql_friendly($leaguename);
	  	$sql = "SELECT * FROM leagues
	  			WHERE league_name = '$leaguename'
	  			LIMIT 1";
	  	$result = $this->db1->query($sql);
		  return $result->row_array();
  	}

  	public function create_league($league, $season) {
      $season_uniqueid = $this->generate_unique_key();
  		$league_uniqueid = $this->generate_unique_key();
      $league['name'] = $this->make_mysql_friendly($league['name']);

      $this->db1->trans_start();
      
      $sql = "INSERT INTO seasons (seasonid, owner_userid, season_duration, season_esportid)
          VALUES ('" . $season_uniqueid . "', '" . $season['userid'] . "', '" . $season['season_duration'] . "', '" . $season['season_esportid'] . "')";
      $this->db1->query($sql);

      $sql = "INSERT INTO season_leagues (seasonid, leagueid)
              VALUES ('" . $season_uniqueid . "', '" . $league_uniqueid . "')";
      $this->db1->query($sql);

      $sql = "INSERT INTO leagues(leagueid, league_name, esportid, league_type, max_teams,invite, private)
          VALUES ('" . $league_uniqueid . "', '" . $league['name'] . "', '" . $league['esportid'] . "', '" . $league['typeid'] . "', '" . $league['max_teams'] . "', '" . $league['invite'] . "', '" . $league['privateleague'] . "')";
      $this->db1->query($sql);

      $sql = "INSERT INTO league_meta(leagueid, first_matches, seasonid)
          VALUES";
      foreach ($league['league_meta'] as $league_meta) {
        $sql .= "('" . $league_uniqueid . "', '" . $league_meta . "', '" . $season_uniqueid . "'),";
      }
      $sql = substr($sql, 0, -1);
      $this->db1->query($sql);
      $sql = "INSERT INTO league_owners(leagueid, userid, seasonid, esportid)
              VALUES ('" . $league_uniqueid . "', '" . $season['userid'] . "', '" . $season_uniqueid . "', '" . $league['esportid'] . "')";
      
      $this->db1->query($sql);
  		$this->db1->trans_complete();
      return true;
  	}

    public function get_league_byesport($esportid) {
      $sql = "SELECT * FROM leagues
              WHERE esportid='$esportid'";
      $result = $this->db1->query($sql);
      return $result->result_array();
    }

    public function get_all_leagues() {
      $sql = "SELECT * FROM leagues";
      $result = $this->db1->query($sql);
      return $result->result_array();
    }

    public function get_leagues($esportid, $private = 0)
    {
      $sql = "SELECT  l.*, 
                      s.start_date, 
                      s.end_date, 
                      s.season_duration, 
                      s.season_status,
                      s.seasonid,
                      lm.first_matches
              FROM leagues AS l, seasons AS s, league_types AS tp, season_leagues AS sl 
              INNER JOIN league_meta AS lm
              WHERE l.private = '$private'
                AND l.esportid = '$esportid'
                AND s.seasonid = sl.seasonid
                AND l.league_type = tp.league_type_id
                AND sl.leagueid = l.leagueid
                AND lm.leagueid = l.leagueid";
              
      $result = $this->db1->query($sql);
      $results = $result->result_array();
      $leagues = array();
      
      foreach ($results as $result)
      {
        if(array_key_exists($result['leagueid'], $leagues))
        {
          //League array already created, add first game time only.
          array_push($leagues[$result['leagueid']]['seasons'][$result['seasonid']]['first_matches'], $this->get_local_datetime($result['first_matches']));
        }
        else
        {
          //Not in league array, create new league
          $leagues[$result['leagueid']] = array();
          $leagues[$result['leagueid']]['seasons'] = array();
          $leagues[$result['leagueid']]['leagueid'] = $result['leagueid'];
          $leagues[$result['leagueid']]['league_name'] = $result['league_name'];
          $leagues[$result['leagueid']]['league_type'] = $result['league_type'];
          $leagues[$result['leagueid']]['max_teams'] = $result['max_teams'];
          $leagues[$result['leagueid']]['invite'] = $result['invite'];

          //populate season
          $season = array();
          $season['start_date'] = $result['start_date'];
          $season['end_date'] = $result['end_date'];
          $season['season_duration'] = $result['season_duration'];
          $season['season_status'] = $result['season_status'];
          $season['seasonid'] = $result['seasonid'];
          $season['first_matches'] = array();
          array_push($season['first_matches'], $this->get_local_datetime($result['first_matches']));

          $leagues[$result['leagueid']]['seasons'][$season['seasonid']] = $season;
          
        }
      }
      return $leagues;
    }

    /*
    *Returns all league and their active teams
    */
    public function get_league_teams($esportid, $leagueids, $result_num = 25, $page_num = 1, $order_by_recent = TRUE)
    {
      $this->db1->trans_start();
      $sql = "SELECT l.leagueid AS leagueid, l.league_name AS league_name 
            FROM leagues l
            WHERE l.esportid = '$esportid'
              AND l.private = '0' 
              AND l.leagueid IN ('" . implode("','", $leagueids) . "')";
      $result = $this->db1->query($sql);
      $leagues = $result->result_array();

      $sql = "SELECT t.teamid AS teamid, t.team_name AS team_name, lt.leagueid AS leagueid
              FROM teams t
              INNER JOIN league_teams lt 
                ON t.teamid = lt.teamid
              WHERE lt.leagueid IN ('" . implode("','", $leagueids) . "')";
      
      $result = $this->db1->query($sql);
      $teams = $result->result_array();
      $this->db1->trans_complete();

      
      $result = $this->db1->query($sql);
      $results = $result->result_array();
      $active_leagues = array();

      $league_teams = array();
      foreach ($leagues as $league)
      {
        $league['teams'] = array(); 
        $league_teams[$league['leagueid']] = $league;    
      }
      foreach ($teams as $team)
      {
        array_push($league_teams[$team['leagueid']]['teams'], $team); 
      }
      return $league_teams;
    }

    public function get_league_details($leagueid)
    {
      $sql =  "SELECT l.*, sl.*, s.*, lt.*, e.*, lm.first_matches FROM leagues AS l
              INNER JOIN season_leagues AS sl ON sl.leagueid = l.leagueid
              INNER JOIN seasons AS s ON s.seasonid = sl.seasonid
              INNER JOIN league_types AS lt ON l.league_type = lt.league_type_id
              INNER JOIN esports AS e ON l.esportid = e.esportid
              INNER JOIN league_meta AS lm ON lm.leagueid = l.leagueid
              WHERE l.leagueid = '$leagueid'";
      $results = $this->db1->query($sql);
      $results = $results->result_array();
      $league = $results[0];
      $league['first_matches'] = array();
      foreach ($results as $result) {
        array_push($league['first_matches'], $result['first_matches']);
      }
      return $league;
    }

    public function get_league_types()
    {
  		$sql = "SELECT * FROM league_types";
	  	$result = $this->db1->query($sql);
		  return $result->result_array();
  	}

    public function get_league_owner($uid, $seasonid)
    {
      $sql = "SELECT * FROM league_owners
              WHERE seasonid = '$seasonid' AND UserId = '$uid'";
      $result = $this->db1->query($sql);
      return $result->row_array();      
    }

    public function get_leagues_by_uid($uid, $esportid)
    {
      $sql = "SELECT  l.league_name as league_name
                      l.leagueid as leagueid 
              FROM leagues l
          INNER JOIN user_players up ON up.userid = '$uid'
          INNER JOIN players p ON p.playerid = up.playerid
          INNER JOIN player_teams pt ON pt.playerid = p.playerid
          INNER JOIN teams t ON t.teamid = pt.teamid
          INNER JOIN league_teams lt ON lt.teamid = t.teamid
          WHERE l.leagueid = lt.leagueid and l.league_status != 'inactive'";
      $result = $this->db1->query($sql);
      return $result->result_array();
    }

    public function get_active_league_first_matches($leagueid)
    {
      $sql = "SELECT lm.first_matches as first_matches FROM league_meta lm
              INNER JOIN leagues l ON l.leagueid = lm.leagueid
              WHERE lm.leagueid = '$leagueid' AND l.status != 'inactive'";
      $result = $this->db1->query($sql);
      $result = $result->result_array(); 
      $first_matches = array();
      foreach ($result as $match)
      {
        array_push($first_matches, $match['first_matches']);
      } 
      return $first_matches;    
    }

    public function join_league($leagueid, $teamid)
    {
      $sql = "INSERT INTO league_teams(leagueid, teamid)
              VALUES ('" . $leagueid . "', '" . $teamid . "')";
      $this->db1->query($sql);
      return;
    }

    public function leave_league($teamid, $leagueid)
    {
      $sql = "UPDATE league_teams
              SET status='leave'
              WHERE teamid = '$teamid' AND leagueid = '$leagueid' AND status = 'active'";
      $this->db1->query($sql);
      return;
    }
}