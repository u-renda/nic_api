<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Demographic extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('member_model', 'the_model');
    }
	
	function gender_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		$data = array();
		
		if ($validation == 'ok')
		{
			$laki = 0;
			$perempuan = 0;
			
			$param = array();
			$param['status'] = 4;
			$query = $this->the_model->demographic($param);
			
			foreach ($query->result() as $row)
			{
				if ($row->gender == 0)
				{
					$laki = $laki + 1;
				}
				else
				{
					$perempuan = $perempuan + 1;
				}
				
				$data = array(
					'laki-laki' => $laki,
					'perempuan' => $perempuan
				);
			}
		}
		
		$rv = array();
		$rv['message'] = 'ok';
		$rv['code'] = 200;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $rv['code']);
	}
	
	function age_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		$data = array();
		
		if ($validation == 'ok')
		{
			$more50 = 0;
			$more30 = 0;
			$more19 = 0;
			$under20 = 0;
				
			$param = array();
			$param['status'] = 4;
			$query = $this->the_model->demographic($param);
			
			foreach ($query->result() as $row)
			{
				$datediff = date_diff(date_create($row->birth_date), new DateTime("now"))->y;
				
				if ($datediff > 50)
				{
					$more50 = $more50 + 1;
				}
				elseif ($datediff > 30)
				{
					$more30 = $more30 + 1;
				}
				elseif ($datediff > 19)
				{
					$more19 = $more19 + 1;
				}
				else
				{
					$under20 = $under20 + 1;
				}
				
				$data = array(
					'lebih dari 51' => $more50,
					'31 - 50' => $more30,
					'20 - 30' => $more19,
					'dibawah 20' => $under20
				);
			}
		}
		
		$rv = array();
		$rv['message'] = 'ok';
		$rv['code'] = 200;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $rv['code']);
	}
}
