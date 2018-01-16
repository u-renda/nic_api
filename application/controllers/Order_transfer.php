<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Order_transfer extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('order_transfer_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_order = filter($this->post('id_order'));
		$total = filter(trim($this->post('total')));
		$status = filter(trim($this->post('status')));
		
		$data = array();
		if ($id_order == FALSE)
		{
			$data['id_order'] = 'required';
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
		
		if (in_array($status, $this->config->item('default_order_transfer_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['id_order'] = $id_order;
			$param['total'] = $total;
			$param['status'] = $status;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->the_model->create($param);
			
			if ($query != 0 || $query != '')
			{
				$data['create'] = 'success';
				$data['id_order_transfer'] = $query;
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
	
	function info_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_order_transfer = filter($this->get('id_order_transfer'));
		$id_order = filter($this->get('id_order'));
		
		$data = array();
		if ($id_order_transfer == FALSE && $id_order == FALSE)
		{
			$data['id_order_transfer'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_order_transfer != '')
			{
				$param['id_order_transfer'] = $id_order_transfer;
			}
			else
			{
				$param['id_order'] = $id_order;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_order_transfer' => $row->id_order_transfer,
					'total' => intval($row->total),
					'date' => $row->date,
					'photo' => $row->photo,
					'account_name' => $row->account_name,
					'other_information' => $row->other_information,
					'resi' => $row->resi,
					'status' => intval($row->status),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'order' => array(
						'id_order' => $row->id_order,
						'id_member' => $row->id_member,
						'name' => $row->name
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
		
		if (in_array($order, $this->config->item('default_order_transfer_order')) && ($order == TRUE))
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
		
		if (in_array($status, $this->config->item('default_order_transfer_status')) && ($status == TRUE))
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
					'id_order_transfer' => $row->id_order_transfer,
					'id_order' => $row->id_order,
					'total' => intval($row->total),
					'date' => $row->date,
					'photo' => $row->photo,
					'account_name' => $row->account_name,
					'other_information' => $row->other_information,
					'resi' => $row->resi,
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
		
		$id_order_transfer = filter($this->post('id_order_transfer'));
		$id_order = filter($this->post('id_order'));
		$total = filter(trim($this->post('total')));
		$date = filter(trim($this->post('date')));
		$photo = filter(trim(strtolower($this->post('photo'))));
		$account_name = filter(trim(strtolower($this->post('account_name'))));
		$other_information = filter(trim(strtolower($this->post('other_information'))));
		$status = filter(trim($this->post('status')));
		$resi = filter(trim($this->post('resi')));
		
		$data = array();
		if ($id_order_transfer == FALSE)
		{
			$data['id_order_transfer'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_order_transfer_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_order_transfer' => $id_order_transfer));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_order == TRUE)
				{
					$param['id_order'] = $id_order;
				}
				
				if ($total == TRUE)
				{
					$param['total'] = $total;
				}
				
				if ($date == TRUE)
				{
					$param['date'] = $date;
				}
				
				if ($photo == TRUE)
				{
					$param['photo'] = $photo;
				}
				
				if ($account_name == TRUE)
				{
					$param['account_name'] = $account_name;
				}
				
				if ($other_information == TRUE)
				{
					$param['other_information'] = $other_information;
				}
				
				if ($resi == TRUE)
				{
					$param['resi'] = $resi;
				}
				
				if ($status == TRUE)
				{
					if ($status == 2)
					{
						$query2 = $this->member_model->info(array('id_member' => $query->row()->id_member));
						
						// send email
						$content = array();
						$content['member_name'] = ucwords($query2->row()->name);
						$content['email'] = $query2->row()->email;
						
						$send_email = email_order_transfer_confirmation($content);
						
						if ($send_email)
						{
							$data['send_email'] = 'success';
						}
						else
						{
							$data['send_email'] = 'failed';
						}
					}
					
					$param['status'] = $status;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_order_transfer, $param);
					
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
				$data['id_order_transfer'] = 'not found';
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
