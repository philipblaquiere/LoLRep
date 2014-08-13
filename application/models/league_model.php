<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class League_model extends MY_Model 
{

  const ACTIVE_STATUS = 'active';

	public function __construct()
  {
		parent::__construct();
		$this->db1 = $this->load->database('default', TRUE);
    $this->load->library('league_cache');
	}

  public function get_leagues($leagueids)
  {
    $cached_leagues = $this->league_cache->get_leagues($leagueids);
    if(count($cached_leagues) == count($leagueids))
    {
      return $cached_leagues;
    }

    foreach ($cached_leagues as $cached_leagueid => $cached_league)
    {
      unset($leagueids[$cached_leagueid]);
    }

    $sql = "SELECT  l.*, 
                    s.start_date, 
                    s.end_date, 
                    s.season_duration, 
                    s.season_status,
                    s.seasonid,
                    lm.first_matches,
                    lo.userid,
                    tp.league_type,
                    tp.league_typeid
            FROM leagues AS l, seasons AS s, league_types AS tp, season_leagues AS sl, league_owners AS lo, league_meta AS lm
            WHERE s.seasonid = sl.seasonid
              AND l.league_typeid = tp.league_typeid
              AND sl.leagueid = l.leagueid
              AND lm.leagueid = l.leagueid
              AND lo.leagueid = l.leagueid
              AND (l.leagueid IN ('" . implode("','", $leagueids) . "'))";

    $sql_teams = "SELECT t.teamid, 
                    t.team_name, 
                    lt.leagueid, 
                    lt.joined, 
                    sl.seasonid
            FROM teams t, league_teams lt, season_leagues sl, season_teams st, leagues l
            WHERE   lt.leagueid = sl.leagueid
                AND lt.teamid = st.teamid
                AND lt.teamid = t.teamid
                AND l.leagueid = lt.leagueid
                AND (l.leagueid IN ('" . implode("','", $leagueids) . "'))";

    $this->db1->trans_start();
    
    $result = $this->db1->query($sql);
    $result_teams = $this->db1->query($sql_teams);

    $this->db1->trans_complete();

    $results = $result->result_array();
    $teams = $result_teams->result_array();
            
    $result = $this->db1->query($sql);
    $results = $result->result_array();
    $leagues = array();
    foreach ($results as $result)
    {
      if(!array_key_exists($result['leagueid'], $leagues))
      {
        //Not in league array, create new league
        $leagues[$result['leagueid']]['leagueid'] = $result['leagueid'];
        $leagues[$result['leagueid']]['league_name'] = $result['league_name'];
        $leagues[$result['leagueid']]['league_typeid'] = $result['league_typeid'];
        $leagues[$result['leagueid']]['league_type'] = $result['league_type'];
        $leagues[$result['leagueid']]['max_teams'] = $result['max_teams'];
        $leagues[$result['leagueid']]['private'] = $result['private'];
        $leagues[$result['leagueid']]['invite'] = $result['invite'];
        $leagues[$result['leagueid']]['ownerid'] = $result['userid'];
        $leagues[$result['leagueid']]['imageurl'] = $result['imageurl'];
        if($result['season_status'] == self::ACTIVE_STATUS)
        {
            $leagues[$result['leagueid']]['current_season'] = $result['seasonid'];
        }
        $leagues[$result['leagueid']]['seasons'] = array();
      }
      if(!array_key_exists($result['seasonid'], $leagues[$result['leagueid']]['seasons']))
      {
        //populate season
        $season = array();
        $season['start_date'] = $result['start_date'];
        $season['end_date'] = $result['end_date'];
        $season['season_duration'] = $result['season_duration'];
        $season['season_status'] = $result['season_status'];
        $season['seasonid'] = $result['seasonid'];
        $season['first_matches'] = array();
        $leagues[$result['leagueid']]['seasons'][$season['seasonid']] = $season;
      }
      array_push($leagues[$result['leagueid']]['seasons'][$result['seasonid']]['first_matches'],$result['first_matches']);
    }
    foreach ($teams as $team)
    {
      $team_info['teamid'] = $team['teamid'];
      $team_info['team_name'] = $team['team_name'];
      $team_info['joined'] = $team['joined'];
      $leagues[$team['leagueid']]['seasons'][$team['seasonid']]['teams'][$team['teamid']] = $team_info;
    }
    $this->league_cache->add_leagues($leagues);
    return $cached_leagues + $leagues;
  }

	public function create_league($league, $season)
  {
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

    $sql = "INSERT INTO leagues(leagueid, league_name, esportid, league_typeid, max_teams,invite, private, description)
        VALUES ('" . $league_uniqueid . "', '" . $league['name'] . "', '" . $league['esportid'] . "', '" . $league['typeid'] . "', '" . $league['max_teams'] . "', '" . $league['invite'] . "', '" . $league['private'] . "', '" . $league['description'] . "')";
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

  public function get_all_leagues($esportid, $private = 0)
  {
    $sql = "SELECT  l.*, 
                    s.start_date, 
                    s.end_date, 
                    s.season_duration, 
                    s.season_status,
                    s.seasonid,
                    lm.first_matches,
                    lo.userid,
                    tp.league_type,
                    tp.league_typeid
            FROM leagues AS l, seasons AS s, league_types AS tp, season_leagues AS sl, league_owners AS lo, league_meta AS lm
            WHERE l.private = '$private'
              AND l.esportid = '$esportid'
              AND s.seasonid = sl.seasonid
              AND l.league_typeid = tp.league_typeid
              AND sl.leagueid = l.leagueid
              AND lm.leagueid = l.leagueid
              AND lo.leagueid = l.leagueid";

    $sql_teams = "SELECT t.teamid, 
                    t.team_name, 
                    lt.leagueid, 
                    lt.joined, 
                    sl.seasonid
            FROM teams t, league_teams lt, season_leagues sl, season_teams st
            WHERE   lt.leagueid = sl.leagueid
                AND lt.teamid = st.teamid
                AND lt.teamid = t.teamid 
                AND (lt.status = 'new' || lt.status = 'active')";

    $this->db1->trans_start();
    
    $result = $this->db1->query($sql);
    $result_teams = $this->db1->query($sql_teams);

    $this->db1->trans_complete();

    $results = $result->result_array();
    $teams = $result_teams->result_array();

    $leagues = array();
    foreach ($results as $result)
    {
      if(!array_key_exists($result['leagueid'], $leagues))
      {
        //Not in league array, create new league
        $leagues[$result['leagueid']]['leagueid'] = $result['leagueid'];
        $leagues[$result['leagueid']]['league_name'] = $result['league_name'];
        $leagues[$result['leagueid']]['league_typeid'] = $result['league_typeid'];
        $leagues[$result['leagueid']]['league_type'] = $result['league_type'];
        $leagues[$result['leagueid']]['max_teams'] = $result['max_teams'];
        $leagues[$result['leagueid']]['invite'] = $result['invite'];
        $leagues[$result['leagueid']]['private'] = $result['private'];
        $leagues[$result['leagueid']]['ownerid'] = $result['userid'];
        $leagues[$result['leagueid']]['imageurl'] = $result['imageurl'];
        if($result['season_status'] == self::ACTIVE_STATUS)
        {
            $leagues[$result['leagueid']]['current_season'] = $result['seasonid'];
        }
        $leagues[$result['leagueid']]['seasons'] = array();
      }
      if(!array_key_exists($result['seasonid'], $leagues[$result['leagueid']]['seasons']))
      {
        //populate season
        $season = array();
        $season['start_date'] = $result['start_date'];
        $season['end_date'] = $result['end_date'];
        $season['season_duration'] = $result['season_duration'];
        $season['season_status'] = $result['season_status'];
        $season['seasonid'] = $result['seasonid'];
        $season['first_matches'] = array();
        $leagues[$result['leagueid']]['seasons'][$season['seasonid']] = $season;
      }
      array_push($leagues[$result['leagueid']]['seasons'][$result['seasonid']]['first_matches'],$result['first_matches']);
    }
    foreach ($teams as $team)
    {
      $team_info['teamid'] = $team['teamid'];
      $team_info['team_name'] = $team['team_name'];
      $team_info['joined'] = $team['joined'];
      $leagues[$team['leagueid']]['seasons'][$team['seasonid']]['teams'][$team['teamid']] = $team_info;
    }
    $this->league_cache->add_leagues($leagues);
    return $leagues;
  }

  public function join_league($teamid, $leagueid, $seasonid)
  {
    $sql = "INSERT INTO league_teams(leagueid, teamid)
            VALUES ('" . $leagueid . "', '" . $teamid . "')";
    $sql = "INSERT INTO season_teams(seasonid, teamid)
            VALUES ('" . $leagueid . "', '" . $teamid . "')";
    $this->db1->query($sql);
    return;
  }

  public function leave_league($teamid, $leagueid, $seasonid)
  {
    $sql = "UPDATE league_teams
            SET status ='leave',
                leave = now()
            WHERE teamid = '$teamid' AND leagueid = '$leagueid'";
    $this->db1->query($sql);
    $sql = "UPDATE season_teams
            SET status='leave'
            WHERE teamid = '$teamid' AND seasonid = '$seasonid'";
    $this->db1->query($sql);
    return;
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
  public function get_league_by_name($leaguename)
  {
    $leaguename = $this->make_mysql_friendly($leaguename);
    $sql = "SELECT * FROM leagues
      WHERE league_name = '$leaguename'
      LIMIT 1";
    $result = $this->db1->query($sql);
    return $result->row_array();
  }
}