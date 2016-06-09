<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Provinsi extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->helper('fungsi');
		$this->load->model('provinsi_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$provinsi = trim(strtolower($this->post('provinsi')));
		
		$data = array();
		if ($provinsi == FALSE)
		{
			$data['provinsi'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_provinsi_name($provinsi) == FALSE && $provinsi == TRUE)
		{
			$data['provinsi'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['provinsi'] = $provinsi;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->provinsi_model->create($param);
			
			if ($query > 0)
			{
				$data['create'] = 'success';
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['create'] = 'failed';
				$validation = 'error';
				$code = 400;
			}
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
	
	function delete_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
        $id_provinsi = trim($this->post('id_provinsi'));
        
		$data = array();
        if ($id_provinsi == FALSE)
		{
			$data['id_provinsi'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->provinsi_model->info(array('id_provinsi' => $id_provinsi));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->provinsi_model->delete($id_provinsi);
				
				if ($delete)
				{
					$data['delete'] = 'success';
					$validation = "ok";
					$code = 200;
				}
				else
				{
					$data['delete'] = 'failed';
					$validation = "error";
					$code = 400;
				}
			}
			else
			{
				$data['id_provinsi'] = 'not found';
				$validation = "error";
				$code = 400;
			}
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
	
	function info_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_provinsi = trim($this->get('id_provinsi'));
		$provinsi = trim(strtolower($this->get('provinsi')));
		
		$data = array();
		if ($id_provinsi == FALSE && $provinsi == FALSE)
		{
			$data['id_provinsi'] = 'required (provinsi)';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_provinsi)
			{
				$param['id_provinsi'] = $id_provinsi;
			}
			else
			{
				$param['provinsi'] = $provinsi;
			}
			
			$query = $this->provinsi_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$data = $query->row();
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_provinsi'] = 'not found (provinsi)';
				$validation = 'error';
				$code = 400;
			}
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
	
	function lists_get()
	{
		$this->benchmark->mark('code_start');
		
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		$order = trim($this->get('order'));
		$sort = trim($this->get('sort'));
		$q = trim($this->get('q'));
		$default_order = array("provinsi", "created_date");
		$default_sort = array("asc", "desc");
		
		if ($limit == TRUE && $limit < 20)
		{
			$limit = $limit;
		}
		elseif ($limit == TRUE && in_array($this->rest->key, $this->config->item('allow_api_key')))
		{
			$limit = $limit;
		}
		else
		{
			$limit = 20;
		}
		
		if ($offset == TRUE)
		{
			$offset = $offset;
		}
		else
		{
			$offset = 0;
		}
		
		if (in_array($order, $default_order) && ($order == TRUE))
		{
			$order = $order;
		}
		else
		{
			$order = 'provinsi';
		}
		
		if (in_array($sort, $default_sort) && ($sort == TRUE))
		{
			$sort = $sort;
		}
		else
		{
			$sort = 'asc';
		}
		
		$param = array();
		$param2 = array();
		if ($q == TRUE)
		{
			$param['q'] = $q;
			$param2['q'] = $q;
		}
		
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;
		
		$query = $this->provinsi_model->lists($param);
		$total = $this->provinsi_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_provinsi' => $row->id_provinsi,
					'provinsi' => $row->provinsi,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date
				);
			}
		}

		$rv = array();
		$rv['message'] = 'ok';
		$rv['code'] = 200;
		$rv['limit'] = intval($limit);
		$rv['offset'] = intval($offset);
		$rv['total'] = intval($total);
		$rv['count'] = count($data);
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $rv['code']);
	}
	
	function update_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_provinsi = trim(intval($this->post('id_provinsi')));
		$provinsi = trim(strtolower($this->post('provinsi')));
		
		$data = array();
		if ($id_provinsi == FALSE)
		{
			$data['id_provinsi'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->provinsi_model->info(array('id_provinsi' => $id_provinsi));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($provinsi == TRUE)
				{
					$param['provinsi'] = $provinsi;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->provinsi_model->update($id_provinsi, $param);
					
					if ($update)
					{
						$data['update'] = 'success';
						$validation = 'ok';
						$code = 200;
					}
				}
				else
				{
					$data['update'] = 'failed';
					$validation = 'error';
					$code = 400;
				}
			}
			else
			{
				$data['id_provinsi'] = 'not found';
				$validation = 'error';
				$code = 400;
			}
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
}
