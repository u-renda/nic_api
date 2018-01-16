<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Order extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('cart_model');
		$this->load->model('cart_shipment_model');
		$this->load->model('cart_total_model');
		$this->load->model('order_model', 'the_model');
		$this->load->model('product_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_cart_total = filter($this->post('id_cart_total'));
		$id_member = filter($this->post('id_member'));
		$name = filter(trim($this->post('name')));
		$phone = filter(trim($this->post('phone')));
		$email = filter(trim(strtolower($this->post('email'))));
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
		
		if ($id_member == FALSE)
		{
			$data['id_member'] = 'required';
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
		
		if (valid_email($email) == FALSE && $email == TRUE)
		{
			$data['email'] = 'wrong format';
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
			$param['id_cart_total'] = $id_cart_total;
			$param['id_member'] = $id_member;
			$param['name'] = $name;
			$param['phone'] = $phone;
			$param['email'] = $email;
			$param['status'] = intval($status);
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->the_model->create($param);
			
			if ($query != 0 || $query != '')
			{
				// update unique transfer ID
				get_update_unique_code();
				
				$query2 = $this->cart_total_model->info(array('id_cart_total' => $id_cart_total));
				
				if ($query2->num_rows() > 0)
				{
					$unique_code = $query2->row()->unique_code;
					$total_transfer = $query2->row()->total;
					$delivery_cost = 0;
					$total_product = 0;
					
					$param2 = array();
					$param2['unique_code'] = $unique_code;
					$param2['order'] = 'created_date';
					$param2['sort'] = 'asc';
					$param2['limit'] = 20;
					$param2['offset'] = 0;
					$query3 = $this->cart_model->lists($param2);
					
					if ($query3->num_rows() > 0)
					{
						$product = array();
						foreach ($query3->result() as $row)
						{
							$query5 = $this->product_model->info(array('id_product' => $row->id_product));
							
							if ($query5->num_rows() > 0)
							{
								$result = $query5->row();
						
								$temp = array();
								$temp['product_name'] = $result->name;
								$temp['product_quantity'] = $row->quantity;
								$temp['product_price'] = $result->price_member;
								$temp['product_total'] = $temp['product_quantity'] * $temp['product_price'];
								$product[] = $temp;
								$total_product += $temp['product_total'];
							}
						}
					}
					
					$query4 = $this->cart_shipment_model->info(array('unique_code' => $unique_code));
					
					if ($query4->num_rows() > 0)
					{
						$delivery_cost = $query4->row()->total;
					}

					// send email
					$content = array();
					$content['member_name'] = ucwords($name);
					$content['email'] = $email;
					$content['product'] = $product;
					$content['delivery_cost'] = $delivery_cost;
					$content['unique_code'] = $total_transfer - $delivery_cost - $total_product;
					$content['total_transfer'] = $total_transfer;
					$content['link_web_transfer'] = $this->config->item('link_web_transfer').'?o='.$query;
					
					//$send_email = email_order_create($content);
					//
					//if ($send_email)
					//{
					//	$data['send_email'] = 'success';
					//}
					//else
					//{
					//	$data['send_email'] = 'failed';
					//}
				}
				
				$data['create'] = 'success';
				$data['id_order'] = $query;
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
		
        $id = filter($this->post('id_order'));
        
		$data = array();
        if ($id == FALSE)
		{
			$data['id_order'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_order' => $id));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id);
				
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
				$data['id_order'] = 'not found';
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
