<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Message_test{

  private $CI;

  const MATCH_CACHE_LIMIT = 100000; //Maximum number of cached matches.
  const MATCH_LIFECYCLE_DURATION = 129600; // number of seconds a match is allowed to stay in Redis without being accessed
  const SLEEP_AMOUNT = 1; //number of seconds thread will sleep in order to allow other thread to update matches.
  const SLEEP_THRESHOLD = 4; //longest wait time before timeout
  const LAST_ACCESS_KEY = "last_access";
  const DIRTY_BIT = "dirty";
  const ACCESS_COUNT = "access_count";
  const MATCHID = "matchid";
  const GAMEID = "gameId";
  const MATCHIDS_KEY = "matchids";
  const MATCH_DETAILS = "match_details";
  const IS_MATCH_COMPLETE = "complete";


  public function __construct()
  {
    $this->CI =& get_instance();
    $this->CI->load->library('redis');
  }


  public function send_message_thread($thread, $msgbody){
    $this->CI->redis->publish($thread, $msgbody);
  }


  public function join_thread($thread){
    $this->CI->redis->subscribe($thread);
  }







}
