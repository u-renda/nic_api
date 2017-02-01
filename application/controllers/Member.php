<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Member extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('kota_model');
		$this->load->model('member_model', 'the_model');
		$this->load->model('member_point_model');
		$this->load->model('member_transfer_model');
    }
	
	function chart_registered_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		$code = 200;
		
		$year = filter(trim($this->get('year')));
		
		if ($year == FALSE)
		{
			$year = date('Y');
		}
		
		$data = array();
		$query = $this->the_model->chart_registered(array('year' => $year, 'status' => 4));
		
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'month' => intval($row->month),
					'total' => intval($row->total)
				);
			}
		}
		else
		{
			$validation = 'error';
			$code = 400;
		}
		
		$rv = array();
		$rv['message'] = $validation;
		$rv['code'] = $code;
		$rv['result'] = $data;
		$this->benchmark->mark('code_end');
		$rv['load'] = $this->benchmark->elapsed_time('code_start', 'code_end') . ' seconds';
		$this->response($rv, $code);
	}
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_kota = filter($this->post('id_kota'));
		$id_admin = filter($this->post('id_admin'));
		$name = filter(trim(strtolower($this->post('name'))));
		$email = filter(trim(strtolower($this->post('email'))));
		$username = filter(trim(strtolower($this->post('username'))));
		$idcard_type = filter(trim(intval($this->post('idcard_type'))));
		$idcard_number = filter(trim($this->post('idcard_number')));
		$idcard_photo = filter(trim($this->post('idcard_photo')));
		$idcard_address = filter(trim($this->post('idcard_address')));
		$shipment_address = filter(trim($this->post('shipment_address')));
		$postal_code = filter(trim($this->post('postal_code')));
		$gender = filter(trim($this->post('gender')));
		$phone_number = filter(trim($this->post('phone_number')));
		$birth_place = filter(trim($this->post('birth_place')));
		$birth_date = filter(trim($this->post('birth_date')));
		$shirt_size = filter(trim($this->post('shirt_size')));
		$photo = filter(trim($this->post('photo')));
		$status = filter(trim(intval($this->post('status'))));
		$member_number = filter(trim($this->post('member_number')));
		$member_card = filter(trim($this->post('member_card')));
		$approved_date = filter(trim($this->post('approved_date')));
		$notes = filter(trim($this->post('notes')));
		
		$data = array();
		if ($id_kota == FALSE)
		{
			$data['id_kota'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($name == FALSE)
		{
			$data['name'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($email == FALSE)
		{
			$data['email'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($idcard_type == FALSE)
		{
			$data['idcard_type'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($idcard_number == FALSE)
		{
			$data['idcard_number'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($idcard_address == FALSE)
		{
			$data['idcard_address'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($shipment_address == FALSE)
		{
			$data['shipment_address'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($postal_code == FALSE)
		{
			$data['postal_code'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($gender == '')
		{
			$data['gender'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($phone_number == FALSE)
		{
			$data['phone_number'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($birth_place == FALSE)
		{
			$data['birth_place'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($birth_date == FALSE)
		{
			$data['birth_date'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($shirt_size == '')
		{
			$data['shirt_size'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($status == FALSE)
		{
			$data['status'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($idcard_photo == FALSE)
		{
			$data['idcard_photo'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($photo == FALSE)
		{
			$data['photo'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_member_name($name) == FALSE && $name == TRUE)
		{
			$data['name'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_member_email($email) == FALSE && $email == TRUE)
		{
			$data['email'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if (valid_email($email) == FALSE && $email == TRUE)
		{
			$data['email'] = 'wrong format';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_member_username($username) == FALSE && $username == TRUE)
		{
			$data['username'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($idcard_type, $this->config->item('default_member_idcard_type')) == FALSE && $idcard_type == TRUE)
		{
			$data['idcard_type'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_member_idcard_number($idcard_number) == FALSE && $idcard_number == TRUE)
		{
			$data['idcard_number'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($gender, $this->config->item('default_member_gender')) == FALSE && $gender == TRUE)
		{
			$data['gender'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_member_phone_number($phone_number) == FALSE && $phone_number == TRUE)
		{
			$data['phone_number'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($shirt_size, $this->config->item('default_member_shirt_size')) == FALSE && $shirt_size == TRUE)
		{
			$data['shirt_size'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_member_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['id_kota'] = $id_kota;
			$param['name'] = $name;
			$param['email'] = $email;
			$param['username'] = $username;
			$param['idcard_type'] = $idcard_type;
			$param['idcard_number'] = $idcard_number;
			$param['idcard_photo'] = $idcard_photo;
			$param['idcard_address'] = $idcard_address;
			$param['shipment_address'] = $shipment_address;
			$param['postal_code'] = $postal_code;
			$param['gender'] = $gender;
			$param['phone_number'] = str_replace(' ', '', $phone_number);
			$param['birth_place'] = $birth_place;
			$param['birth_date'] = $birth_date;
			$param['shirt_size'] = $shirt_size;
			$param['photo'] = $photo;
			$param['status'] = $status;
			$param['member_card'] = $member_card;
			$param['member_number'] = $member_number;
			$param['notes'] = $notes;
			$param['approved_date'] = $approved_date;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			
			if ($status == 4) // approved
			{
				// isi member number, member card
				$get_member_number = get_member_number();
				
				$param2 = array();
				$param2['birth_date'] = $birth_date;
				$param2['gender'] = $gender;
				$param2['get_member_number'] = $get_member_number;
				
				$get_member_card = get_member_card((object) $param2, $id_admin);
				
				if ($get_member_card != FALSE)
				{
					$param['member_number'] = $get_member_number;
					$param['member_card'] = $get_member_card;
					$param['short_code'] = md5($get_member_card.$email);
					$param['approved_date'] = date('Y-m-d H:i:s');
				}
			}
			
			$query = $this->the_model->create($param);
			
			if ($query != 0 || $query != '')
			{
				// send email
				$content = array();
				$content['member_name'] = ucwords($name);
				$content['email'] = $email;
				
				if ($status == 4)
				{
					$content['member_card'] = $param['member_card'];
					$content['short_code'] = $param['short_code'];
					$send_email = email_member_approved($content);
				}
				else
				{
					$send_email = email_member_create($content);
				}
				
				if ($send_email)
				{
					$data['send_email'] = 'success';
				}
				else
				{
					$data['send_email'] = 'failed';
				}
				
				$data['create'] = 'success';
				$data['id_member'] = $query;
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
		
        $id_member = filter($this->post('id_member'));
        
		$data = array();
        if ($id_member == FALSE)
		{
			$data['id_member'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_member' => $id_member));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id_member);
				
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
				$data['id_member'] = 'not found';
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
		
		$id_member = filter($this->get('id_member'));
		$name = filter(trim(strtolower($this->get('name'))));
		$email = filter(trim(strtolower($this->get('email'))));
		$username = filter(trim(strtolower($this->get('username'))));
		$idcard_number = filter(trim($this->get('idcard_number')));
		$phone_number = filter(trim($this->get('phone_number')));
		$member_number = filter(trim($this->get('member_number')));
		$member_card = filter(trim(strtoupper($this->get('member_card'))));
		$short_code = filter(trim($this->get('short_code')));
		
		$data = array();
		if ($id_member == FALSE && $name == FALSE && $email == FALSE && $username == FALSE
			&& $idcard_number == FALSE && $phone_number == FALSE && $member_number == FALSE
			&& $member_card == FALSE && $short_code == FALSE)
		{
			$data['id_member'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_member != '')
			{
				$param['id_member'] = $id_member;
			}
			elseif ($name != '')
			{
				$param['name'] = $name;
			}
			elseif ($email != '')
			{
				$param['email'] = $email;
			}
			elseif ($username != '')
			{
				$param['username'] = $username;
			}
			elseif ($idcard_number != '')
			{
				$param['idcard_number'] = $idcard_number;
			}
			elseif ($phone_number != '')
			{
				$param['phone_number'] = $phone_number;
			}
			elseif ($member_number != '')
			{
				$param['member_number'] = $member_number;
			}
			elseif ($member_card != '')
			{
				$param['member_card'] = $member_card;
			}
			else
			{
				$param['short_code'] = $short_code;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				// hitung point
				$point = $this->member_point_model->lists_count(array('id_member' => $row->id_member, 'status' => 2));
				
				// get provinsi
				$kota = $this->kota_model->info(array('id_kota' => $row->id_kota));
				
				$data = array(
					'id_member' => $row->id_member,
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
					'photo' => $row->photo,
					'point' => intval($point),
					'status' => intval($row->status),
					'member_number' => $row->member_number,
					'member_card' => $row->member_card,
					'short_code' => $row->short_code,
					'notes' => $row->notes,
					'approved_date' => $row->approved_date,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'provinsi' => $kota->row()->provinsi,
					'kota' => array(
						'id_kota' => $row->id_kota,
						'kota' => $row->kota,
						'price' => intval($row->price)
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_member'] = 'Not Found';
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
		$idcard_type = filter(intval(trim($this->get('idcard_type'))));
		$gender = filter(trim($this->get('gender')));
		$marital_status = filter(trim($this->get('marital_status')));
		$religion = filter(intval(trim($this->get('religion'))));
		$shirt_size = filter(trim($this->get('shirt_size')));
		$status = filter(intval(trim($this->get('status'))));
		$q = filter(trim($this->get('q')));
		$new_member = filter($this->get('new_member'));
		
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
		
		if (in_array($order, $this->config->item('default_member_order')) && ($order == TRUE))
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
		
		if (in_array($idcard_type, $this->config->item('default_member_idcard_type')) && ($idcard_type == TRUE))
		{
			$idcard_type = $idcard_type;
		}
		
		if (in_array($religion, $this->config->item('default_member_religion')) && ($religion == TRUE))
		{
			$religion = $religion;
		}
		
		if (in_array($status, $this->config->item('default_member_status')) && ($status == TRUE))
		{
			$status = $status;
		}
		
		if (in_array($gender, $this->config->item('default_member_gender')) && ($gender == TRUE))
		{
			$gender = $gender;
		}
		
		if (in_array($marital_status, $this->config->item('default_member_marital_status')) && ($marital_status == TRUE))
		{
			$marital_status = $marital_status;
		}
		
		if (in_array($shirt_size, $this->config->item('default_member_shirt_size')) && ($shirt_size == TRUE))
		{
			$shirt_size = $shirt_size;
		}
		
		$param = array();
		$param2 = array();
		if ($q == TRUE)
		{
			$param['q'] = $q;
			$param2['q'] = $q;
		}
		if ($idcard_type == TRUE)
		{
			$param['idcard_type'] = $idcard_type;
			$param2['idcard_type'] = $idcard_type;
		}
		if ($religion == TRUE)
		{
			$param['religion'] = $religion;
			$param2['religion'] = $religion;
		}
		if ($status == TRUE)
		{
			$param['status'] = $status;
			$param2['status'] = $status;
		}
		if ($gender != '')
		{
			$param['gender'] = $gender;
			$param2['gender'] = $gender;
		}
		if ($marital_status != '')
		{
			$param['marital_status'] = $marital_status;
			$param2['marital_status'] = $marital_status;
		}
		if ($shirt_size != '')
		{
			$param['shirt_size'] = $shirt_size;
			$param2['shirt_size'] = $shirt_size;
		}
		if ($new_member != '')
		{
			$param['new_member'] = $new_member;
			$param2['new_member'] = $new_member;
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
					'id_member' => $row->id_member,
					'id_kota' => $row->id_kota,
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
					'photo' => $row->photo,
					'status' => intval($row->status),
					'notes' => $row->notes,
					'member_number' => $row->member_number,
					'member_card' => $row->member_card,
					'short_code' => $row->short_code,
					'approved_date' => $row->approved_date,
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
	
	function send_invalid_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id = filter($this->post('id_member'));
		$email_content = $this->post('email_content');
		
		$data = array();
		if ($id == FALSE)
		{
			$data['id_member'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($email_content == FALSE)
		{
			$data['email_content'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_member' => $id));
			
			if ($query->num_rows() > 0)
			{
				// update short code
				$short_code = md5($id.$query->row()->birth_place);
				
				$param = array();
				$param['short_code'] = $short_code;
				$query2 = $this->the_model->update($id, $param);
				
				// send email
				$content = array();
				$content['member_name'] = ucwords($query->row()->name);
				$content['email'] = $query->row()->email;
				$content['short_code'] = $short_code;
				$content['email_content'] = $email_content;
				
				$send_email = email_member_invalid($content);
				
				if ($send_email)
				{
					$data['send_email'] = 'success';
					$validation = 'ok';
					$code = 200;
				}
				else
				{
					$data['send_email'] = 'failed';
					$validation = 'error';
					$code = 400;
				}
			}
			else
			{
				$data['member_card'] = 'not found';
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
	
	function send_recovery_password_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$email = filter(trim(strtolower($this->post('email'))));
		$member_card = filter(trim(strtoupper($this->post('member_card'))));
		
		$data = array();
		if ($email == FALSE)
		{
			$data['email'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($member_card == FALSE)
		{
			$data['member_card'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (valid_email($email) == FALSE && $email == TRUE)
		{
			$data['email'] = 'wrong format';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('member_card' => $member_card, 'email' => $email));
			
			if ($query->num_rows() > 0)
			{
				// update short code
				$short_code = md5($member_card.$email);
				
				$param = array();
				$param['short_code'] = $short_code;
				$query2 = $this->the_model->update($query->row()->id_member, $param);
				
				// send email
				$content = array();
				$content['member_name'] = ucwords($query->row()->name);
				$content['email'] = $email;
				$content['short_code'] = $short_code;
				$send_email = email_recovery_password($content);
				
				if ($send_email)
				{
					$data['send_email'] = 'success';
					$validation = 'ok';
					$code = 200;
				}
				else
				{
					$data['send_email'] = 'failed';
					$validation = 'error';
					$code = 400;
				}
			}
			else
			{
				$data['member_card'] = 'not found';
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
	
	function update_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_member = filter($this->post('id_member'));
		$id_kota = filter($this->post('id_kota'));
		$id_admin = filter($this->post('id_admin'));
		$name = filter(trim(strtolower($this->post('name'))));
		$email = filter(trim(strtolower($this->post('email'))));
		$username = filter(trim(strtolower($this->post('username'))));
		$password = filter(trim($this->post('password')));
		$idcard_type = filter(trim(intval($this->post('idcard_type'))));
		$idcard_number = filter(trim($this->post('idcard_number')));
		$idcard_photo = filter(trim(strtolower($this->post('idcard_photo'))));
		$idcard_address = filter(trim($this->post('idcard_address')));
		$shipment_address = filter(trim($this->post('shipment_address')));
		$postal_code = filter(trim($this->post('postal_code')));
		$gender = filter(trim($this->post('gender')));
		$phone_number = filter(trim($this->post('phone_number')));
		$birth_place = filter(trim(strtolower($this->post('birth_place'))));
		$birth_date = filter(trim($this->post('birth_date')));
		$marital_status = filter(trim($this->post('marital_status')));
		$occupation = filter(trim(strtolower($this->post('occupation'))));
		$religion = filter(trim(intval($this->post('religion'))));
		$shirt_size = filter(trim($this->post('shirt_size')));
		$photo = filter(trim(strtolower($this->post('photo'))));
		$resi = filter(trim($this->post('resi')));
		$status = filter(trim(intval($this->post('status'))));
		$member_number = filter(trim($this->post('member_number')));
		$member_card = filter(trim(strtoupper($this->post('member_card'))));
		$notes = filter(trim($this->post('notes')));
		$approved_date = filter(trim($this->post('approved_date')));
		$transfer_date = filter(trim($this->post('transfer_date')));
		$transfer_photo = filter(trim(strtolower($this->post('transfer_photo'))));
		$account_name = filter(trim(strtolower($this->post('account_name'))));
		$other_information = filter(trim($this->post('other_information')));
		
		$data = array();
		if ($id_member == FALSE)
		{
			$data['id_member'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (valid_email($email) == FALSE && $email == TRUE)
		{
			$data['email'] = 'wrong format';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($idcard_type, $this->config->item('default_member_idcard_type')) == FALSE && $idcard_type == TRUE)
		{
			$data['idcard_type'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($gender, $this->config->item('default_member_gender')) == FALSE && $gender == TRUE)
		{
			$data['gender'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($marital_status, $this->config->item('default_member_marital_status')) == FALSE && $marital_status == TRUE)
		{
			$data['marital_status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($religion, $this->config->item('default_member_religion')) == FALSE && $religion == TRUE)
		{
			$data['religion'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($shirt_size, $this->config->item('default_member_shirt_size')) == FALSE && $shirt_size == TRUE)
		{
			$data['shirt_size'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_member_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_member' => $id_member));
			
			if ($query->num_rows() > 0)
			{
				$query2 = lists_member_transfer($query->row());
				$param = array();
				
				if ($id_kota == TRUE)
				{
					$param['id_kota'] = $id_kota;
				}
				
				if ($name == TRUE)
				{
					$param['name'] = $name;
				}
				
				if ($email == TRUE)
				{
					$param['email'] = $email;
					
					// bisa ditambah send email
				}
				
				if ($username == TRUE)
				{
					$param['username'] = $username;
				}
				
				if ($password == TRUE)
				{
					$param['password'] = md5($password);
					
					// update short code
					$short_code = md5($password.$email);
					
					$param2 = array();
					$param2['short_code'] = $short_code;
					$this->the_model->update($query->row()->id_member, $param2);
					
					// send email
					$content = array();
					$content['member_name'] = ucwords($query->row()->name);
					$content['email'] = $email;
					$content['short_code'] = $short_code;
					$send_email = email_reset_password($content);
					
					if ($send_email)
					{
						$data['send_reset_password'] = 'success';
					}
					else
					{
						$data['send_reset_password'] = 'failed';
					}
				}
				
				if ($idcard_type == TRUE)
				{
					$param['idcard_type'] = $idcard_type;
				}
				
				if ($idcard_number == TRUE)
				{
					$param['idcard_number'] = $idcard_number;
				}
				
				if ($idcard_photo == TRUE)
				{
					$param['idcard_photo'] = $idcard_photo;
				}
				
				if ($idcard_address == TRUE)
				{
					$param['idcard_address'] = $idcard_address;
				}
				
				if ($shipment_address == TRUE)
				{
					$param['shipment_address'] = $shipment_address;
				}
				
				if ($postal_code == TRUE)
				{
					$param['postal_code'] = $postal_code;
				}
				
				if ($gender != '')
				{
					$param['gender'] = $gender;
				}
				
				if ($phone_number == TRUE)
				{
					$param['phone_number'] = $phone_number;
				}
				
				if ($birth_place == TRUE)
				{
					$param['birth_place'] = $birth_place;
				}
				
				if ($birth_date == TRUE)
				{
					$param['birth_date'] = $birth_date;
				}
				
				if ($marital_status != '')
				{
					$param['marital_status'] = $marital_status;
				}
				
				if ($occupation == TRUE)
				{
					$param['occupation'] = $occupation;
				}
				
				if ($religion == TRUE)
				{
					$param['religion'] = $religion;
				}
				
				if ($shirt_size != '')
				{
					$param['shirt_size'] = $shirt_size;
				}
				
				if ($photo == TRUE)
				{
					$param['photo'] = $photo;
				}
				
				if ($member_number != '')
				{
					$param['member_number'] = $member_number;
				}
				
				if ($member_card == TRUE)
				{
					$param['member_card'] = $member_card;
				}
				
				if ($resi == TRUE)
				{
					$param['resi'] = $resi;
				}
				
				if ($notes == TRUE)
				{
					$param['notes'] = $notes;
				}
				
				if ($status == TRUE)
				{
					// Jika status diubah manual, maka ada beberapa info yang harus diubah
					if ($status == 1 || $status == 5) // awaiting review / invalid
					{
						// kosongin username, password, member number, member card
						$param['username'] = '-';
						$param['password'] = '-';
						$param['member_number'] = 0;
						$param['member_card'] = '-';
						$param['approved_date'] = '';
						
						// delete member transfer (jika ada)
						if ($query2 != FALSE)
						{
							foreach ($query2 as $row)
							{
								$this->member_transfer_model->delete($row->id_member_transfer);
							}
						}
					}
					elseif ($status == 2) // awaiting transfer
					{
						// kosongin username, password, member number, member card
						$param['username'] = '-';
						$param['password'] = '-';
						$param['member_number'] = 0;
						$param['member_card'] = '-';
						$param['approved_date'] = '';
							
						// update member transfer
						if ($query2 != FALSE)
						{
							foreach ($query2 as $row)
							{
								$param3 = array();
								$param3['date'] = '0000-00-00';
								$param3['photo'] = '-';
								$param3['account_name'] = '-';
								$param3['other_information'] = '-';
								$param3['resi'] = '-';
								$param3['status'] = 1;
								$param3['updated_date'] = date('Y-m-d H:i:s');
								$this->member_transfer_model->update($row->id_member_transfer, $param3);
							}
						}
						else
						{
							// ambil unique code dan update valuenya (+1)
							$unique_code = get_update_unique_code();
							
							// create member transfer
							$param3 = array();
							$param3['id_member'] = $query->row()->id_member;
							$param3['total'] = $this->config->item('registration_fee') + $unique_code + $query->row()->price;
							$param3['type'] = 1;
							$param3['status'] = 1;
							$param3['created_date'] = date('Y-m-d H:i:s');
							$param3['updated_date'] = date('Y-m-d H:i:s');
							$this->member_transfer_model->create($param3);
						}
					}
					elseif ($status == 3) // awaiting approval
					{
						// kosongin username, password, member number, member card
						$param['username'] = '-';
						$param['password'] = '-';
						$param['member_number'] = 0;
						$param['member_card'] = '-';
						$param['approved_date'] = '';
						
						if ($query2 != FALSE)
						{
							foreach ($query2 as $row)
							{
								$param4 = array();
								$param4['date'] = $transfer_date;
								$param4['photo'] = $transfer_photo;
								$param4['account_name'] = $account_name;
								$param4['other_information'] = $other_information;
								$param4['status'] = 2;
								$param4['updated_date'] = date('Y-m-d H:i:s');
								$this->member_transfer_model->update($row->id_member_transfer, $param4);
							}
						}
						else
						{
							// ambil unique code dan update valuenya (+1)
							$unique_code = get_update_unique_code();
							
							// create member transfer
							$param3 = array();
							$param3['id_member'] = $query->row()->id_member;
							$param3['total'] = $this->config->item('registration_fee') + $unique_code + $query->row()->price;
							$param3['type'] = 1;
							$param3['status'] = 2;
							$param4['created_date'] = date('Y-m-d H:i:s');
							$param4['updated_date'] = date('Y-m-d H:i:s');
							$this->member_transfer_model->create($param3);
						}
						
					}
					elseif ($status == 4) // approved
					{
						// isi username, password, member number, member card
						//$param2 = array();
						//$param2['name'] = $name;
						//$param2['birth_date'] = $birth_date;
						//$param2['gender'] = $gender;
						//
						//$generate_username = generate_username((object) $param2);
						//$get_member_number = get_member_number();
						//$get_member_card = get_member_card((object) $param2, $id_admin);
						//
						//if ($get_member_card != FALSE)
						//{
							$param['username'] = $username;
							$param['member_number'] = $member_number;
							$param['member_card'] = $member_card;
							$param['approved_date'] = date('Y-m-d H:i:s');
						//}
					}
					
					$param['status'] = $status;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_member, $param);
					
					if ($update == TRUE)
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
				$data['id_member'] = 'not found';
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
	
	// Dipakai untuk login karena butuh member card & password (required) 
	function valid_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$member_card = filter(trim(strtoupper($this->post('member_card'))));
		$password = filter(trim($this->post('password')));
		
		$data = array();
		if ($member_card == FALSE)
		{
			$data['member_card'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($password == FALSE)
		{
			$data['password'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('member_card' => $member_card));
			
			if ($query->num_rows() > 0)
			{
				$check_pass = $query->row()->password;
				$pass = md5($password);
				
				if ($check_pass == $pass)
				{
					$data = $query->row();
					$validation = 'ok';
					$code = 200;
				}
				else
				{
					$data['valid'] = 'no!';
					$validation = 'error';
					$code = 400;
				}
			}
			else
			{
				$data['username'] = 'not found';
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
	
	// Dipakai untuk recovery password butuh email, no member & status(required) 
	function valid_recovery_password_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$email = filter(trim(strtolower($this->post('email'))));
		$member_card = filter(trim(strtoupper($this->post('member_card'))));
		$status = filter(trim($this->post('status')));
		
		$data = array();
		if ($email == FALSE)
		{
			$data['email'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($member_card == FALSE)
		{
			$data['member_card'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($status == FALSE)
		{
			$data['status'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['email'] = $email;
			$param['member_card'] = $member_card;
			$param['status'] = $status;
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$data['valid'] = 'yes!';
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['valid'] = 'no!';
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
