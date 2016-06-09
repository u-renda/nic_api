<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Preferences extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->helper('fungsi');
		$this->load->model('preferences_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$key = trim(strtolower($this->post('key')));
		$value = trim($this->post('value'));
		$description = trim(strtolower($this->post('description')));

		$data = array();
		if ($key == FALSE)
		{
			$data['key'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		if ($value == FALSE)
		{
			$data['value'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_preferences_key($key) == FALSE && $key == TRUE)
		{
			$data['key'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['key'] = $key;
			$param['value'] = $value;
			$param['description'] = $description;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->preferences_model->create($param);
			
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
		
        $id_preferences = trim($this->post('id_preferences'));
        
		$data = array();
        if ($id_preferences == FALSE)
		{
			$data['id_preferences'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->preferences_model->info(array('id_preferences' => $id_preferences));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->preferences_model->delete($id_preferences);
				
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
				$data['id_preferences'] = 'not found';
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
		
		$id_preferences = trim($this->get('id_preferences'));
		$key = trim(strtolower($this->get('key')));
		
		$data = array();
		if ($id_preferences == FALSE && $key == FALSE)
		{
			$data['id_preferences'] = 'required (key)';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_preferences)
			{
				$param['id_preferences'] = $id_preferences;
			}
			else
			{
				$param['key'] = $key;
			}
			
			$query = $this->preferences_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_preferences' => $row->id_preferences,
					'key' => $row->key,
					'value' => $row->value,
					'description' => $row->description,
					'type' => intval($row->type),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
				);
				
                $validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_preferences'] = 'not found (key)';
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
		$type = trim($this->get('type'));
		$default_order = array("key", "created_date");
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
			$order = 'key';
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
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;

        if ($type == TRUE)
        {
            $param['type'] = $type;
            $param2['type'] = $type;
        }
		
		$query = $this->preferences_model->lists($param);
		$total = $this->preferences_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_preferences' => $row->id_preferences,
					'key' => $row->key,
					'value' => $row->value,
					'description' => $row->description,
					'type' => intval($row->type),
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
		
		$id_preferences = filter($this->post('id_preferences'));
		$key = filter(trim(strtolower($this->post('key'))));
		$value = filter(trim($this->post('value')));
		$description = filter(trim(strtolower($this->post('description'))));

		$data = array();
		if ($id_preferences == FALSE)
		{
			$data['id_preferences'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->preferences_model->info(array('id_preferences' => $id_preferences));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($key == TRUE)
				{
					$param['key'] = $key;
				}
				if ($value == TRUE)
				{
					$param['value'] = $value;
				}
				if ($description == TRUE)
				{
					$param['description'] = $description;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->preferences_model->update($id_preferences, $param);
					
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
				$data['id_preferences'] = 'not found';
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
