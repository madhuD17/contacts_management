<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
	public function index()
	{
		$data['count_total'] = $this->common_model->count_all('contacts');
		$this->load->view('dashboard/index', $data);
	}
}
