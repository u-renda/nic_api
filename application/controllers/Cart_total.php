<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Cart_total extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('cart_total_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$unique_code = filter(trim($this->post('unique_code')));
		$total = filter(trim($this->post('total')));
		$status = filter(trim($this->post('status')));
		
		$data = array();
		if ($unique_code == FALSE)
		{
			$data['unique_code'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($total == FALSE)
		{
			$data['total'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($status == FALSE)
		{
			$data['status'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_cart_total_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['unique_code'] = $unique_code;
			$param['total'] = $total;
			$param['status'] = $status;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->the_model->create($param);
			
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
		
        $id_cart_total = filter($this->post('id_cart_total'));
        
		$data = array();
        if ($id_cart_total == FALSE)
		{
			$data['id_cart_total'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_cart_total' => $id_cart_total));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id_cart_total);
				
				if ($delete > 0)
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
				$data['id_cart_total'] = 'not found';
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
		
		$id_cart_total = filter($this->get('id_cart_total'));
		$unique_code = filter($this->get('unique_code'));
		$status = filter($this->get('status'));
		
		$data = array();
		if ($id_cart_total == FALSE && $unique_code == FALSE)
		{
			$data['id_cart_total'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_cart_total != '')
			{
				$param['id_cart_total'] = $id_cart_total;
			}
			else
			{
				$param['unique_code'] = $unique_code;
			}
			
			if ($status != '')
			{
				$param['status'] = $status;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_cart_total' => $row->id_cart_total,
					'unique_code' => $row->unique_code,
					'total' => intval($row->total),
					'status' => intval($row->status),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_cart_total'] = 'Not Found';
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
		
		$offset = filter(trim(intval($this->get('offset'))));
		$limit = filter(trim(intval($this->get('limit'))));
		$order = filter(trim(strtolower($this->get('order'))));
		$sort = filter(trim(strtolower($this->get('sort'))));
		$status = filter(trim($this->get('status')));
		
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
		
		if (in_array($order, $this->config->item('default_cart_total_order')) && ($order == TRUE))
		{
			$order = $order;
		}
		else
		{
			$order = 'created_date';
		}
		
		if (in_array($sort, $this->config->item('default_sort')) && ($sort == TRUE))
		{
			$sort = $sort;
		}
		else
		{
			$sort = 'desc';
		}
		
		if (in_array($status, $this->config->item('default_cart_total_status')) && ($status == TRUE))
		{
			$status = $status;
		}
		
		$param = array();
		$param2 = array();
		if ($status == TRUE)
		{
			$param['status'] = intval($status);
			$param2['status'] = intval($status);
		}
		
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;
		
		$query = $this->the_model->lists($param);
		$total = $this->the_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_cart_total' => $row->id_cart_total,
					'unique_code' => $row->unique_code,
					'total' => intval($row->total),
					'status' => intval($row->status),
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
		
		$id_cart_total = filter($this->post('id_cart_total'));
		$unique_code = filter(trim($this->post('unique_code')));
		$total = filter(trim($this->post('total')));
		$status = filter(trim($this->post('status')));
		
		$data = array();
		if ($id_cart_total == FALSE)
		{
			$data['id_cart_total'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_cart_total_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_cart_total' => $id_cart_total));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($unique_code == TRUE)
				{
					$param['unique_code'] = $unique_code;
				}
				
				if ($total == TRUE)
				{
					$param['total'] = $total;
				}
				
				if ($status == TRUE)
				{
					$param['status'] = $status;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_cart_total, $param);
					
					if ($update > 0)
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
				$data['id_cart_total'] = 'not found';
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
