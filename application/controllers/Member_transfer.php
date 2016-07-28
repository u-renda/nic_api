<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Member_transfer extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('member_transfer_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_member = filter($this->post('id_member'));
		$total = filter(trim($this->post('total')));
		$type = filter(trim($this->post('type')));
		$status = filter(trim($this->post('status')));
		
		$data = array();
		if ($id_member == FALSE)
		{
			$data['id_member'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($total == FALSE)
		{
			$data['total'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($type == FALSE)
		{
			$data['type'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($status == FALSE)
		{
			$data['status'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($type, $this->config->item('default_member_transfer_type')) == FALSE && $type == TRUE)
		{
			$data['type'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_member_transfer_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['id_member'] = $id_member;
			$param['total'] = $total;
			$param['type'] = $type;
			$param['status'] = $status;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->member_transfer_model->create($param);
			
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
		
        $id_member_transfer = filter($this->post('id_member_transfer'));
        
		$data = array();
        if ($id_member_transfer == FALSE)
		{
			$data['id_member_transfer'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->member_transfer_model->info(array('id_member_transfer' => $id_member_transfer));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->member_transfer_model->delete($id_member_transfer);
				
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
				$data['id_member_transfer'] = 'not found';
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
		
		$id_member_transfer = filter($this->get('id_member_transfer'));
		
		$data = array();
		if ($id_member_transfer == FALSE)
		{
			$data['id_member_transfer'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_member_transfer)
			{
				$param['id_member_transfer'] = $id_member_transfer;
			}
			
			$query = $this->member_transfer_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_member_transfer' => $row->id_member_transfer,
					'id_member' => $row->id_member,
					'total' => intval($row->total),
					'date' => $row->date,
					'photo' => $row->photo,
					'account_name' => $row->account_name,
					'other_information' => $row->other_information,
					'type' => intval($row->type),
					'status' => intval($row->status),
					'created_date' => $query->row()->created_date,
					'updated_date' => $query->row()->updated_date,
					'member' => array(
						'name' => $row->name,
						'email' => $row->email,
						'username' => $row->username,
						'idcard_type' => intval($row->idcard_type),
						'idcard_number' => $row->idcard_number,
						'idcard_photo' => $row->idcard_photo,
						'idcard_address' => $row->idcard_address,
						'shipment_address' => $row->shipment_address,
						'postal_code' => $row->postal_code,
						'gender' => intval($row->gender),
						'phone_number' => $row->phone_number,
						'birth_place' => $row->birth_place,
						'birth_date' => $row->birth_date,
						'marital_status' => intval($row->marital_status),
						'occupation' => $row->occupation,
						'religion' => intval($row->religion),
						'shirt_size' => intval($row->shirt_size),
						'photo' => $row->member_photo,
						'status' => intval($row->member_status),
						'member_number' => $row->member_number,
						'member_card' => $row->member_card,
						'approved_date' => $row->approved_date,
						'created_date' => $row->member_created_date,
						'updated_date' => $row->member_updated_date,
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_member_transfer'] = 'not found';
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
		
		$offset = filter(intval(trim($this->get('offset'))));
		$limit = filter(intval(trim($this->get('limit'))));
		$order = filter(trim($this->get('order')));
		$sort = filter(trim($this->get('sort')));
		$id_member = filter($this->get('id_member'));
		$type = filter(intval(trim($this->get('type'))));
		$q = filter(trim($this->get('q')));
		
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
		
		if (in_array($order, $this->config->item('default_member_transfer_order')) && ($order == TRUE))
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
		
		if (in_array($type, $this->config->item('default_member_transfer_type')) && ($type == TRUE))
		{
			$type = $type;
		}
		
		$param = array();
		$param2 = array();
		if ($q == TRUE)
		{
			$param['q'] = $q;
			$param2['q'] = $q;
		}
		if ($id_member == TRUE)
		{
			$param['id_member'] = $id_member;
			$param2['id_member'] = $id_member;
		}
		if ($type == TRUE)
		{
			$param['type'] = $type;
			$param2['type'] = $type;
		}
		
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;
		
		$query = $this->member_transfer_model->lists($param);
		$total = $this->member_transfer_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_member_transfer' => $row->id_member_transfer,
					'total' => intval($row->total),
					'date' => $row->date,
					'photo' => $row->photo,
					'account_name' => $row->account_name,
					'other_information' => $row->other_information,
					'type' => intval($row->type),
					'resi' => $row->resi,
					'status' => intval($row->status),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'member' => array(
						'id_member' => $row->id_member,
						'name' => $row->name
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
		
		$id_member_transfer = filter($this->post('id_member_transfer'));
		$id_member = filter($this->post('id_member'));
		$total = filter(trim($this->post('total')));
		$date = filter(trim($this->post('date')));
		$photo = filter(trim(strtolower($this->post('photo'))));
		$account_name = filter(trim(strtolower($this->post('account_name'))));
		$other_information = filter(trim(strtolower($this->post('other_information'))));
		$type = filter(trim($this->post('type')));
		$status = filter(trim($this->post('status')));
		
		$data = array();
		if ($id_member_transfer == FALSE)
		{
			$data['id_member_transfer'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($type, $this->config->item('default_member_transfer_type')) == FALSE && $type == TRUE)
		{
			$data['type'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->member_transfer_model->info(array('id_member_transfer' => $id_member_transfer));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_member == TRUE)
				{
					$param['id_member'] = $id_member;
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
				
				if ($type == TRUE)
				{
					$param['type'] = $type;
				}
				
				if ($status == TRUE)
				{
					$param['status'] = $status;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->member_transfer_model->update($id_member_transfer, $param);
					
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
				$data['id_member_transfer'] = 'not found';
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
