<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('UTC');

class User extends CI_Controller {

	function __construct() {
	    parent::__construct();
	    $this->load->model('user_model', '', TRUE);
	}

	// Unused index action
	public function index()
	{
	}

	// Login
	public function login()
	{
		// Validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_login_validation');
        
		// Fail
        if ($this->form_validation->run() == FALSE) {
            redirect('', 'refresh');
		// Success
        } else {
            redirect('', 'refresh');
        }
	}

	// Validate Login Callback
	public function login_validation($password)
	{
		// Get other parameters
        $username = $this->input->post('username');
		// Compare to database
        $result = $this->user_model->login($username, $password);        
        // Fail
		if (! $result) {
            $this->form_validation->set_message('check_database', 'Invalid username or password');
            return false;
		// Success
		} else {
			// Login
            $sess_array = array();
            foreach ($result as $row) {
                $sess_array = array(
                    'id' => $row['id'],
                    'username' => $row['username']
                );
                $this->session->set_userdata('logged_in', $sess_array);
            }
            return TRUE;
        }
	}

	// Register
	public function register()
	{
		// Validation
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_register_validation|min_length[6]|matches[confirm]');
        $this->form_validation->set_rules('confirm', 'Confirm', 'trim|required|xss_clean');
        // Fail
        if ($this->form_validation->run() == FALSE) {
            redirect('', 'refresh');
        // Success
        } else {
            redirect('', 'refresh');
        }
	}

	// Validate Register Callback
    public function register_validation($password) {
        $email = "placeholder@gmail.com";
        $username = $this->input->post('username');
        $password = md5($password);
		// Email Validation
        $this->load->helper('email');
        if (!valid_email($email)) {
            $this->form_validation->set_message('insert_database', 'This is not a working email address');
            return false;
        } else {
			// Attempt new user register
            $facebook_id = 0;
            $result = $this->user_model->register($username, $password, $email, $facebook_id);
			// Fail
            if (! $result) {
                $this->form_validation->set_message('insert_database', 'Username already exists');
                return false;
			// Success
            } else {
				// Login
                $sess_array = array();
                $sess_array = array(
                    'id' => $result,
                    'username' => $username
                );
                $this->session->set_userdata('logged_in', $sess_array);
                return TRUE;
            }
        }
    }

	// Logout
    public function logout() {
        $this->session->unset_userdata('logged_in');
        redirect('', 'refresh');
    }

    // Page Not Found
	public function page_not_found()
	{
		$this->load->view('page_not_found');
	}
}