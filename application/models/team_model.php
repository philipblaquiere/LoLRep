<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Team_model extends MY_Model {
  /**
  *Table columns:
  *esportid int
  *name varchar 32
  *abbrv varchar 8
  *type varchar 16
  *description varchar 128
  *imageurl varchar 128
  */

  protected $table = 'teams';
  protected $pkey = 'teamid';

  public function __construct() {
    parent::__construct();
    $this->db1 = $this->load->database('default', TRUE);
  }

  public function create_team($team)
  {
    $uniqueid = $this->generate_unique_key();
    $team['team_name'] = $this->make_mysql_friendly($team['team_name']);
    $sql = "INSERT INTO player_teams (teamid, playerid)
            VALUES ('". $uniqueid ."' , '" . $team['playerid'] . "')";
    $this->db1->query($sql);
    $sql = "INSERT INTO teams (teamid, team_name, esportid, captainid) 
          VALUES ('". $uniqueid ."','". $team['team_name'] ."', '". $team['esportid'] ."', '". $team['captainid'] ."')";
    $this->db1->query($sql);
    return;
  }

  public function get_teams($teamids)
  {
    $this->db1->trans_start();

    $teams = array();
    foreach ($teamids as $teamid)
    {
      //Get Team information
      $sql = "SELECT  t.team_name as team_name,
                      t.created as created,
                      t.captainid as captainid,
                      t.teamid as teamid
              FROM teams t 
              WHERE t.teamid = '$teamid'
              LIMIT 1";
      $result = $this->db1->query($sql);
      $team = $result->row_array();

      //Get players on the team
      $sql = "SELECT  p.player_name as player_name,
                      p.playerid as playerid,
                      p.joined as joined
              FROM players p 
              INNER JOIN player_teams pt ON pt.playerid = p.playerid
              WHERE pt.teamid = '$teamid'";
      $result = $this->db1->query($sql);
      $players = $result->result_array();
      foreach ($players as $player)
      {
        $team['players'][$player['playerid']] = $player;
      }

      //Get the League
      $sql = "SELECT  l.league_name as league_name,
                      l.leagueid as leagueid,
                      s.seasonid,
                      s.season_status,
                      s.start_date,
                      s.end_date
              FROM leagues l, seasons s, season_teams st, league_teams lt, season_leagues sl
              WHERE lt.teamid = '$teamid'
                AND lt.leagueid = l.leagueid
                AND lt.leagueid = sl.leagueid
                AND sl.seasonid = s.seasonid
                AND s.seasonid = st.seasonid
                AND st.teamid = '$teamid'
                AND lt.status = 'active'";

      $result = $this->db1->query($sql);
      
      $results = $result->result_array();
      $leagues = array();
      if(!empty($results))
      {
        foreach ($results as $result)
        {
          if($result['season_status'] == 'active')
          {
            $leagues['current_league'] = $result['leagueid'];
            $leagues['current_season'] = $result['seasonid'];
          }
          if(!array_key_exists($result['leagueid'], $leagues))
          {
            $league['leagueid'] = $result['leagueid'];
            $league['league_name'] = $result['league_name'];
            $league['seasons'] = array();
            $leagues[$result['leagueid']] = $league;
          }
          if(array_key_exists('seasonid', $result))
          {
            $leagues[$result['leagueid']]['seasons'][$result['seasonid']]['seasonid'] = $result['seasonid'];
            $leagues[$result['leagueid']]['seasons'][$result['seasonid']]['start_date'] = $result['start_date'];
            $leagues[$result['leagueid']]['seasons'][$result['seasonid']]['end_date'] = $result['end_date'];
            $leagues[$result['leagueid']]['seasons'][$result['seasonid']]['season_status'] = $result['season_status'];
          }
        }
        $team['leagues'] = $leagues;
      }
      $teams[$teamid] = $team;
    }
    $this->db1->trans_complete();
    return $teams;
  }

  public function get_teamname_by_teamid($teamid) {
    $sql = "SELECT team_name FROM teams WHERE teamid = '$teamid' LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_team_by_name($name,$esportid) {
    $sql = "SELECT * FROM teams WHERE team_name = '$name' AND esportid = '$esportid' LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_all_teams_by_captainid($id) {
    $sql = "SELECT * FROM teams WHERE captainid = '$id'";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_team_by_captainid($uid, $esportid) {
    $sql = "SELECT * FROM teams WHERE captainid = '$uid' AND esportid = '$esportid' LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

  public function get_team_id_by_summonername($summonername) {
    $sql = "SELECT t.teamid as teamid FROM teams t
        INNER JOIN teams_lol l ON t.teamid = l.teamid 
        INNER JOIN summoners s ON s.summonerid = l.summonerid WHERE  s.SummonerName = '$summonername' LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }

   public function get_teams_by_playerid($id, $esportid) 
   {
    //Returns a list of the players's team's names, team id's, and their creation date.
    $sql = "SELECT  t.teamid,
                    t.team_name,
                    t.created,
                    t.captainid
            FROM teams t
            INNER JOIN player_teams pt 
              ON t.teamid = pt.teamid
            WHERE pt.playerid = '$id' 
              AND t.esportid = '$esportid'";
    $result = $this->db1->query($sql);
    return $result->result_array();
  }

  public function get_teams_byleagueid($leagueid, $esportid)
  {
    $sql = "SELECT * FROM leagues l 
              INNER JOIN league_teams lt ON l.leagueid = lt.leagueid 
              INNER JOIN teams t ON t.teamid = lt.teamid 
              WHERE l.leagueid = '$leagueid' 
                AND t.esportid = '$esportid' 
                AND lt.status = 'active' 
                AND l.private = '0'";
      $result = $this->db1->query($sql);
      $results = $result->result_array();
      $league = array();
      $teams = array();
      foreach ($results as $result) {
        if(array_key_exists('teams', $teams)) {
          //League array already created, add team
          $league['teams'][$result['teamid']] = array();
          $league['teams'][$result['teamid']]['teamid'] = $result['teamid'];
          $league['teams'][$result['teamid']]['joined'] = $result['joined'];
          $league['teams'][$result['teamid']]['team_name'] = $result['team_name'];
          $league['teams'][$result['teamid']]['captainid'] = $result['captainid'];
        }
        else {
          //Not in league array, create new league
          $league['league_name'] = $result['league_name'];
          $league['leagueid'] = $result['leagueid'];
          $league['teams'][$result['teamid']] = array();
          $league['teams'][$result['teamid']]['teamid'] = $result['teamid'];
          $league['teams'][$result['teamid']]['joined'] = $result['joined'];
          $league['teams'][$result['teamid']]['team_name'] = $result['team_name'];
          $league['teams'][$result['teamid']]['captainid'] = $result['captainid'];
        }
      }
      return $league;
  }
}

