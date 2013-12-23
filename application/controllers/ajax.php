<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends MY_Controller {

	public function __construct(){
	    parent::__construct();
	    $this->load->model('user_model');
	    $this->load->model('lol_model');
	    $this->load->model('lolapi_model');
	    $this->load->model('riotapi_model');
	}

	public function authenticate_summoner($summonerinput) {

	    if(!$summonerinput) {
	      //user didn't enter anything, show eror message and reload.
	      $this->system_message_model->set_message('You must enter a summoner name to validate.', MESSAGE_ERROR);
	      redirect('user/register_LoL', 'location');
	    }
	    else {

			//check riot servers to see if summoner actually exists.
			$riotsummoner = $this->riotapi_model->getSummonerByName($summonerinput);
			/*if(!$riotsummoner) {
				$this->system_message_model->set_message('The specified summoner was not found in the specified region.', MESSAGE_ERROR);
	      		redirect('user/register_LoL', 'location');
			}*/
			//else {
				//print_r($riotsummoner);
				//summoner exists, check if summoner exists already in our db
				$summoner = $this->lol_model->registered_summoner($summonerinput);

				if(!$summoner) {
					//summoner doesn't exist in db yet. Generate a Rune Page Key
					$data['runepagekey'] = $this->user_model->generate_rune_page_key();
					$data['summonername'] = $summonerinput;
					$this->load->view('ajax/authenticate_summoner', $data);

				}
				else {
					//summoner already existing return error
					$this->system_message_model->set_message('This Summoner is already registered here', MESSAGE_ERROR);
					redirect('user/register_LoL', 'location');
				}
			//}
		}
  	}//end function

}
