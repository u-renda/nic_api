<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Order extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('order_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_cart_total = filter($this->post('id_cart_total'));
		$id_member = filter($this->post('id_member'));
		$name = filter(trim($this->post('name')));
		$phone = filter(trim($this->post('phone')));
		$email = filter(trim($this->post('email')));
		$status = filter(trim($this->post('status')));
		
		$data = array();
		if ($id_cart_total == FALSE)
		{
			$data['id_cart_total'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($name == FALSE)
		{
			$data['name'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($phone == FALSE)
		{
			$data['phone'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($email == FALSE)
		{
			$data['email'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($status == FALSE)
		{
			$data['status'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_order_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['name'] = $name;
			$param['image'] = $image;
			$param['price_public'] = intval($price_public);
			$param['price_member'] = intval($price_member);
			$param['description'] = $description;
			$param['quantity'] = intval($quantity);
			$param['status'] = intval($status);
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->the_model->create($param);
			
			if ($query > 0)
			{
				// insert image ke product image
				$param2 = array();
				$param2['id_product'] = $query;
				$param2['image'] = $image;
				$param2['status'] = 1;
				$param2['created_date'] = date('Y-m-d H:i:s');
				$param2['updated_date'] = date('Y-m-d H:i:s');
				$query2 = $this->product_image_model->create($param2);
				
				if ($query2 > 0)
				{
					$data['create'] = 'success';
					$validation = 'ok';
					$code = 200;
				}
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
	
	function info_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_order = filter($this->get('id_order'));
		
		$data = array();
		if ($id_order == FALSE)
		{
			$data['id_order'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_order != '')
			{
				$param['id_order'] = $id_order;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_order' => $row->id_order,
					'name' => $row->name,
					'phone' => $row->phone,
					'email' => $row->email,
					'status' => intval($row->status),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'cart_total' => array(
						'id_cart_total' => $row->id_cart_total,
						'unique_code' => $row->unique_code,
						'total' => intval($row->total)
					),
					'member' => array(
						'id_member' => $row->id_member,
						'name' => $row->member_name
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_order'] = 'Not Found';
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
		
		if (in_array($order, $this->config->item('default_order_order')) && ($order == TRUE))
		{
			$order = $order;
		}
		else
		{
			$order = 'name';
		}
		
		if (in_array($sort, $this->config->item('default_sort')) && ($sort == TRUE))
		{
			$sort = $sort;
		}
		else
		{
			$sort = 'asc';
		}
		
		if (in_array($status, $this->config->item('default_order_status')) && ($status == TRUE))
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
					'id_order' => $row->id_order,
					'id_cart_total' => $row->id_cart_total,
					'id_member' => $row->id_member,
					'name' => $row->name,
					'phone' => $row->phone,
					'email' => $row->email,
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
}
