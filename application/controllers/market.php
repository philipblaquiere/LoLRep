<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Market extends MY_Controller
{
	/**
	 * Constructor: initialize required libraries.
	 */
    private $roles;

	public function __construct()
    {
        parent::__construct();
        $this->load->model('system_message_model');
        $this->load->model('team_model');
        $this->load->model('player_model');
        $this->load->model('market_model');
        $this->load->library('form_validation');
        if(!isset($this->roles))
        {
            $this->roles = $this->player_model->get_roles($this->get_esportid());
        }
    }

    public function index()
    {
        $this->require_login();
        $this->require_registered();

        $player = $this->get_player();
        $player['playerid'];
        $this->view_wrapper('market', array(), false);

    }

    public function join()
    {
        $data['roles'] = $this->roles;

        foreach ($this->roles as $roleid => $role_name)
        {
            $first_role = $role_name;
            continue;
        }
        $this->form_validation->set_rules($first_role, 'First Name', 'callback_selected');

        if($this->form_validation->run() == FALSE)
        {
            $this->view_wrapper('join_market', $data, false);
        }
        else
        {
            $roles_selected = array();
            if(isset($_POST['allrolescheckbox']))
            {
                foreach ($this->roles as $roleid => $role)
                {
                    array_push($roles_selected, $roleid);
                }
            }
            else
            {
                foreach ($this->roles as $roleid => $role_name)
                {
                    if(isset($_POST[$role_name]))
                    {
                        array_push($roles_selected, $roleid);
                    }
                }
            }
            $this->system_message_model->set_message('You have been added to the market', MESSAGE_SUCCESS);

            //$this->market_model->sign_up($playerid,$esportid);
            //$this->add_roles($roles);
            redirect('market');
        }
    }

    function selected()
    {
        $selected_roles = 0;
        foreach ($this->roles as $roleid => $role_name)
        {
            if (isset($_POST['role'.$role_name]))
            {
                $selected_roles.=1;
            }
        }
        if($selected_roles == 0 && !isset($_POST['allrolescheckbox']))
        {
            $this->form_validation->set_message('selected', 'You must select at least one role.');
            return FALSE;
        }
        else
        {
          return TRUE;
        }
    }
}
