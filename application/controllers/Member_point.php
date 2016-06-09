<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Member_point extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('member_point_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_events = $this->post('id_events');
		$id_member = $this->post('id_member');
		$status = trim(intval($this->post('status')));
		
		$data = array();
		if ($id_events == FALSE)
		{
			$data['id_events'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($id_member == FALSE)
		{
			$data['id_member'] = 'required';
			$validation = 'error';
			$code = 400;
		}

		if ($validation == 'ok')
		{
			$param = array();
			$param['id_events'] = $id_events;
			$param['id_member'] = $id_member;
			$param['status'] = $status;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->member_point_model->create($param);
			
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
		
        $id_member_point = $this->post('id_member_point');
        
		$data = array();
        if ($id_member_poin == FALSE)
		{
			$data['id_member_point'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->member_point_model->info(array('id_member_point' => $id_member_point));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->member_point_model->delete($id_member_point);
				
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
				$data['id_member_point'] = 'not found';
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
		
		$id_member_point = $this->get('id_member_point');
		$id_events = $this->get('id_events');
		$id_member = $this->get('id_member');
		
		$data = array();
		if ($id_member_point == FALSE && $id_events == FALSE && $id_member == FALSE)
		{
			$data['id_member_point'] = 'required (id_events, id_member)';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_member_point)
			{
				$param['id_member_point'] = $id_member_point;
			}
			elseif ($id_events)
			{
				$param['id_events'] = $id_events;
			}
			else
			{
				$param['id_member'] = $id_member;
			}
			
			$query = $this->member_point_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_member_point' => $row->id_member_point,
					'status' => $row->status,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'member' => array(
						'id_member' => $row->id_member,
						'name' => $row->name,
						'email' => $row->email,
						'username' => $query->row()->username,
						'idcard_type' => $query->row()->idcard_type,
						'idcard_number' => $query->row()->idcard_number,
						'idcard_photo' => $query->row()->idcard_photo,
						'idcard_address' => $query->row()->idcard_address,
						'shipment_address' => $query->row()->shipment_address,
						'postal_code' => $query->row()->postal_code,
						'gender' => $query->row()->gender,
						'phone_number' => $query->row()->phone_number,
						'birth_place' => $query->row()->birth_place,
						'birth_date' => $query->row()->birth_date,
						'marital_status' => $query->row()->marital_status,
						'occupation' => $query->row()->occupation,
						'religion' => $query->row()->religion,
						'shirt_size' => $query->row()->shirt_size,
						'photo' => $query->row()->photo,
						'status' => $query->row()->status,
						'member_number' => $query->row()->member_number,
						'member_card' => $query->row()->member_card,
						'approved_date' => $query->row()->approved_date,
						'created_date' => $query->row()->member_created_date,
						'updated_date' => $query->row()->member_updated_date,
					),
					'events' => array(
						'id_events' => $query->row()->id_events,
						'title' => $query->row()->title,
						'date' => $query->row()->date,
						'created_date' => $query->row()->events_created_date,
						'updated_date' => $query->row()->events_updated_date
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_member_poin'] = 'not found (id_events, id_member)';
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
		$id_events = $this->get('id_events');
		$id_member = $this->get('id_member');
		$default_order = array("id_events", "id_member", "created_date");
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
			$order = 'created_date';
		}
		
		if (in_array($sort, $default_sort) && ($sort == TRUE))
		{
			$sort = $sort;
		}
		else
		{
			$sort = 'desc';
		}
		
		$param = array();
		$param2 = array();
		if ($id_events == TRUE)
		{
			$param['id_events'] = $id_events;
			$param2['id_events'] = $id_events;
		}
		if ($id_member == TRUE)
		{
			$param['id_member'] = $id_member;
			$param2['id_member'] = $id_member;
		}
		
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;
		
		$query = $this->member_poin_model->lists($param);
		$total = $this->member_poin_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_member_poin' => $row->id_member_poin,
					'id_events' => $row->id_events,
					'id_member' => $row->id_member,
					'poin' => $row->poin,
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
		
		$id_member_poin = $this->post('id_member_poin');
		$poin = trim(intval($this->post('poin')));
		$default_poin = array("0", "1");
		
		$data = array();
		if ($id_member_poin == FALSE)
		{
			$data['id_member_poin'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->member_poin_model->info(array('id_member_poin' => $id_member_poin));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($poin == TRUE)
				{
					$param['poin'] = $poin;
				}
		
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->member_poin_model->update($id_member_poin, $param);
					
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
				$data['id_member_poin'] = 'not found';
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
