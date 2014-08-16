<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron_match extends MY_Controller 
{

    const SERVER_PATH_TO_PHP = "C:\wamp\bin\php";
    const ABS_PATH_TO_CONTROLLER = "www\LoLRep\application\controllers\index.php";

	/**
	 * Constructor: initialize required libraries.
	 */
	public function __construct()
    {
        parent::__construct();
        $this->load->model('match_model');
        $this->load->model('statistics_model');
        $this->load->library('match_cache');
        // this controller can only be called from the command line
        /*if (!$this->input->is_cli_request()) 
        {
            show_error('Direct access is not allowed');
        }*/
    }

    public function foo($bar = 'bar')
    {
        echo "foo = $bar";
    }

    private function _format_request($controller, $function)
    {
        return self::SERVER_PATH_TO_PHP . " " . self::ABS_PATH_TO_CONTROLLER . " " . $controller . " " . $function;
    }

    /*
    |   Removes all match depricated or unused 
    |   instances from the Match cache, which are 
    |   also non-dirty;
    */
    public function clean_match_chache()
    {
        $this->match_cache->clean();
    }

    /*
    |   Writes all dirty matches to db
    |   sets matches to "non-dirty"
    */
    public function commit_match_cache()
    {
        $dirty_matches = $this->match_cache->get_dirty_matches();
        $this->match_model->update_matches($dirty_matches);
        $this->statistics_model->add_match_stats($dirty_matches, $this->get_esportid());
        //Update dirty bit to FALSE
        $dirty_matchids = array();
        foreach ($dirty_matches as $matchid => $match) 
        {
            array_push($dirty_matchids, $matchid);
        }
        $this->match_cache->mark_dirty($dirty_matchids, '0');

    }


    /*
    |   Gets all scheduled matches which
    |   could be finished and updates them to
    |   the cache 
    */
    public function update_match_cache($esportid)
    {
        $matchids = $this->match_model->get_scheduled_matchids(time(),$esportid);
        //$this->match_cache->
    }
}