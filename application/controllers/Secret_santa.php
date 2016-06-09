<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Secret_santa extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('secret_santa_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$name = trim(strtolower($this->post('name')));
		
		$data = array();
		if ($name == FALSE)
		{
			$data['name'] = 'required';
			$validation = 'error';
			$code = 400;
		}

		if ($validation == 'ok')
		{
			$param = array();
			$param['name'] = $name;
			$param['status'] = 0;
			$param['chosen_id'] = 0;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->secret_santa_model->create($param);
			
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
		
        $id_secret_santa = trim($this->post('id_secret_santa'));
        
		$data = array();
        if ($id_secret_santa == FALSE)
		{
			$data['id_secret_santa'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->secret_santa_model->info(array('id_secret_santa' => $id_secret_santa));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->secret_santa_model->delete($id_secret_santa);
				
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
				$data['id_secret_santa'] = 'not found';
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
		
		$id_secret_santa = intval($this->get('id_secret_santa'));
		$name = trim(strtolower($this->get('name')));
		$status = filter($this->get('status'));

		$data = array();
		if ($id_secret_santa == FALSE && $name == FALSE)
		{
			$data['id_secret_santa'] = 'required (name)';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_secret_santa != '')
			{
				$param['id_secret_santa'] = $id_secret_santa;
			}
			elseif ($name != '')
			{
				$param['name'] = $name;
			}
			
			if ($status != '')
			{
				$param['status'] = $status;
			}
			
			$query = $this->secret_santa_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$data = $query->row();
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_secret_santa'] = 'not found (name)';
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
		$order = trim(strtolower($this->get('order')));
		$sort = trim(strtolower($this->get('sort')));
		$not_id_secret_santa = trim($this->get('not_id_secret_santa'));
		$chosen = filter($this->get('chosen'));
		$default_order = array("name", "created_date");
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
			$order = 'name';
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
		
		if ($not_id_secret_santa != '')
		{
			$param['not_id_secret_santa'] = $not_id_secret_santa;
			$param2['not_id_secret_santa'] = $not_id_secret_santa;
		}
		if ($chosen != '')
		{
			$param['chosen'] = $chosen;
			$param2['chosen'] = $chosen;
		}
		
		$query = $this->secret_santa_model->lists($param);
		$total = $this->secret_santa_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_secret_santa' => $row->id_secret_santa,
					'name' => $row->name,
					'chosen_id' => $row->chosen_id,
					'status' => $row->status,
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
	
/*
	function random_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
        $id_secret_santa = trim($this->post('id_secret_santa'));
        
		$data = array();
        if ($id_secret_santa == FALSE)
		{
			$data['id_secret_santa'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->secret_santa_model->info(array('id_secret_santa' => $id_secret_santa, 'status' => 0));
			
			if ($query->num_rows() > 0)
			{
				// Get data other users
				$query2 = $this->secret_santa_model->lists(array('not_id_secret_santa' => $id_secret_santa, 'status' => 0, 'order' => 'name', 'sort' => 'asc', 'limit' => 20, 'offset' => 0));
				
				if ($query2->num_rows() > 0)
				{
					$temp = array();
					foreach ($query2->result() as $row)
					{
						$temp['id_secret_santa'][] = $row->id_secret_santa;
					}

					$random = array_rand($temp['id_secret_santa'], 2);
					
					$data['result'] = $random;
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
				$data['id_secret_santa'] = 'not found';
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
*/
	
	function update_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
        $id_secret_santa = trim($this->post('id_secret_santa'));
        $chosen_id = trim($this->post('chosen_id'));
        $status = filter($this->post('status'));
        $chosen = filter($this->post('chosen'));
        
		$data = array();
        if ($id_secret_santa == FALSE)
		{
			$data['id_secret_santa'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->secret_santa_model->info(array('id_secret_santa' => $id_secret_santa));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($chosen_id != '')
				{
					$param['chosen_id'] = $chosen_id;
				}
				if ($status != '')
				{
					$param['status'] = $status;
				}
				if ($chosen != '')
				{
					$param['chosen'] = $chosen;
				}
				
				if (count($param) > 0)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->secret_santa_model->update($id_secret_santa, $param);
					
					if ($update)
					{
						$data['update'] = 'success';
						$validation = "ok";
						$code = 200;
					}
					else
					{
						$data['update'] = 'failed';
						$validation = "error";
						$code = 400;
					}
				}
                else
                {
                    $data['update'] = 'Nothing to update';
                    $validation = 'ok';
                    $code = 200;
                }
			}
			else
			{
				$data['id_secret_santa'] = 'not found';
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
}
