<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Match_cache
{
	private $esportid;
	private $playerid;
    private $last_match_index;

	public function __construct($params)
    {
        $this->esportid = $params['esportid'];
        $this->playerid = $params['playerid'];
        $this->last_match_index = null;
    }

    public function get_next_matches($match_load_count = 10)
    {
        $return_matches = array();
        if($this->last_match_index == NULL)
        {
            $this->last_match_index = 0;
        }
        for ($i=$this->last_match_index; $i < $match_load_count; $i++)
        { 
            
        }
    }

    public function add_matches($new_matches)
    {
        $loaded_matches = $this->get_matches();

        foreach ($new_matches as $new_match)
        {
            array_push($loaded_matches, $new_match);
        }
    }