<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Cart extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('cart_model', 'the_model');
		$this->load->model('product_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_product = filter($this->post('id_product'));
		$id_member = filter($this->post('id_member'));
		$quantity = filter(trim($this->post('quantity')));
		$unique_code = filter(trim($this->post('unique_code')));
		$total = filter(trim($this->post('total')));
		$status = filter($this->post('status'));
		$size = filter(trim($this->post('size')));
		
		$data = array();
		if ($id_product == FALSE)
		{
			$data['id_product'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
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
		
		if (in_array($status, $this->config->item('default_cart_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['id_product'] = $id_product;
			$param['id_member'] = $id_member;
			$param['quantity'] = $quantity;
			$param['unique_code'] = $unique_code;
			$param['total'] = $total;
			$param['status'] = $status;
			$param['size'] = $size;
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
		
        $id_cart = filter($this->post('id_cart'));
        
		$data = array();
        if ($id_cart == FALSE)
		{
			$data['id_cart'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_cart' => $id_cart));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id_cart);
				
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
				$data['id_cart'] = 'not found';
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
		
		$id_cart = filter($this->get('id_cart'));
		$id_member = filter($this->get('id_member'));
		
		$data = array();
		if ($id_cart == FALSE && $id_member == FALSE)
		{
			$data['id_cart'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_cart != '')
			{
				$param['id_cart'] = $id_cart;
			}
			else
			{
				$param['id_member'] = $id_member;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_cart' => $row->id_cart,
					'id_member' => $row->id_member,
					'quantity' => intval($row->quantity),
					'size' => $row->size,
					'unique_code' => $row->unique_code,
					'total' => intval($row->total),
					'status' => intval($row->status),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'product' => array(
						'id_product' => $row->id_product,
						'image' => $row->image,
						'name' => $row->name,
						'slug' => $row->slug,
						'price_public' => intval($row->price_public),
						'price_member' => intval($row->price_member)
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_cart'] = 'Not Found';
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
		$id_member = filter($this->get('id_member'));
		$unique_code = filter($this->get('unique_code'));
		
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
		
		if (in_array($order, $this->config->item('default_cart_order')) && ($order == TRUE))
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
		
		if (in_array($status, $this->config->item('default_cart_status')) && ($status == TRUE))
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
		if ($id_member == TRUE)
		{
			$param['id_member'] = $id_member;
			$param2['id_member'] = $id_member;
		}
		if ($unique_code == TRUE)
		{
			$param['unique_code'] = $unique_code;
			$param2['unique_code'] = $unique_code;
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
				$query2 = $this->product_model->info(array('id_product' => $row->id_product));
				
				$data[] = array(
					'id_cart' => $row->id_cart,
					'id_member' => $row->id_member,
					'quantity' => intval($row->quantity),
					'size' => $row->size,
					'unique_code' => $row->unique_code,
					'total' => intval($row->total),
					'status' => intval($row->status),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'product' => array(
						'id_product' => $row->id_product,
						'image' => $query2->row()->image,
						'name' => $query2->row()->name,
						'slug' => $query2->row()->slug,
						'price_public' => intval($query2->row()->price_public),
						'price_member' => intval($query2->row()->price_member)
					)
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
		
		$id_cart = filter($this->post('id_cart'));
		$id_product = filter($this->post('id_product'));
		$id_member = filter($this->post('id_member'));
		$quantity = filter(trim($this->post('quantity')));
		$unique_code = filter(trim($this->post('unique_code')));
		$total = filter(trim($this->post('total')));
		$status = filter($this->post('status'));
		$size = filter(trim($this->post('size')));
		
		$data = array();
		if ($id_cart == FALSE)
		{
			$data['id_cart'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_cart_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_cart' => $id_cart));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_product == TRUE)
				{
					$param['id_product'] = $id_product;
				}
				
				if ($id_member == TRUE)
				{
					$param['id_member'] = $id_member;
				}
				
				if ($quantity == TRUE)
				{
					$param['quantity'] = $quantity;
				}
				
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
				
				if ($size == TRUE)
				{
					$param['size'] = $size;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_cart, $param);
					
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
				$data['id_cart'] = 'not found';
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
