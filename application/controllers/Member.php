<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Member extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('member_model');
		$this->load->model('member_point_model');
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
		$query = $this->member_model->chart_registered(array('year' => $year, 'status' => 4));
		
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
		$name = filter(trim(strtolower($this->post('name'))));
		$email = filter(trim(strtolower($this->post('email'))));
		$username = filter(trim(strtolower($this->post('username'))));
		$password = filter(trim($this->post('password')));
		$idcard_type = filter(trim($this->post('idcard_type')));
		$idcard_number = filter(trim($this->post('idcard_number')));
		$idcard_photo = filter(trim(strtolower($this->post('idcard_photo'))));
		$idcard_address = filter(trim($this->post('idcard_address')));
		$shipment_address = filter(trim($this->post('shipment_address')));
		$postal_code = filter(trim($this->post('postal_code')));
		$gender = filter(trim($this->post('gender')));
		$phone_number = filter(trim($this->post('phone_number')));
		$birth_place = filter(trim($this->post('birth_place')));
		$birth_date = filter(trim($this->post('birth_date')));
		$marital_status = filter(trim($this->post('marital_status')));
		$occupation = filter(trim($this->post('occupation')));
		$religion = filter(trim($this->post('religion')));
		$shirt_size = filter(trim($this->post('shirt_size')));
		$photo = filter(trim(strtolower($this->post('photo'))));
		$status = filter(trim($this->post('status')));
		$member_number = filter(trim($this->post('member_number')));
		$member_card = filter(trim($this->post('member_card')));
		$approved_date = filter(trim($this->post('approved_date')));
		
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
		
		if ($marital_status == '')
		{
			$data['marital_status'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($occupation == FALSE)
		{
			$data['occupation'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($religion == FALSE)
		{
			$data['religion'] = 'required';
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
			if ($password == TRUE)
			{
				$password = md5($password);
			}
			
			$param = array();
			$param['id_kota'] = $id_kota;
			$param['name'] = $name;
			$param['email'] = $email;
			$param['username'] = $username;
			$param['password'] = $password;
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
			$param['marital_status'] = $marital_status;
			$param['occupation'] = $occupation;
			$param['religion'] = $religion;
			$param['shirt_size'] = $shirt_size;
			$param['photo'] = $photo;
			$param['status'] = $status;
			$param['member_card'] = $member_card;
			$param['member_number'] = $member_number;
			$param['approved_date'] = $approved_date;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->member_model->create($param);
			
			if ($query != 0 || $query != '')
			{
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
            $query = $this->member_model->info(array('id_member' => $id_member));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->member_model->delete($id_member);
				
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
		$idcard_number = filter(trim(intval($this->get('idcard_number'))));
		$phone_number = filter(trim(intval($this->get('phone_number'))));
		$member_number = filter(trim(intval($this->get('member_number'))));
		$member_card = filter(trim(strtoupper($this->get('member_card'))));
		
		$data = array();
		if ($id_member == FALSE && $name == FALSE && $email == FALSE && $username == FALSE
			&& $idcard_number == FALSE && $phone_number == FALSE && $member_number == FALSE
			&& $member_card == FALSE)
		{
			$data['id_member'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_member)
			{
				$param['id_member'] = $id_member;
			}
			elseif ($name)
			{
				$param['name'] = $name;
			}
			elseif ($email)
			{
				$param['email'] = $email;
			}
			elseif ($username)
			{
				$param['username'] = $username;
			}
			elseif ($idcard_number)
			{
				$param['idcard_number'] = $idcard_number;
			}
			elseif ($phone_number)
			{
				$param['phone_number'] = $phone_number;
			}
			elseif ($member_number)
			{
				$param['member_number'] = $member_number;
			}
			else
			{
				$param['member_card'] = $member_card;
			}
			
			$query = $this->member_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				// hitung point
				$point = $this->member_point_model->lists_count(array('id_member' => $row->id_member));
				
				$data = array(
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
					'point' => intval($point),
					'status' => intval($row->status),
					'member_number' => $row->member_number,
					'member_card' => $row->member_card,
					'approved_date' => $row->approved_date,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'kota' => array(
						'kota' => $row->kota,
						'price' => intval($row->price),
						'created_date' => $row->kota_created_date,
						'updated_date' => $row->kota_updated_date
					)
				);
				
				$validation = 'ok';
				$code = 200;
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
		if (isset($gender))
		{
			$param['gender'] = $gender;
			$param2['gender'] = $gender;
		}
		if (isset($marital_status))
		{
			$param['marital_status'] = $marital_status;
			$param2['marital_status'] = $marital_status;
		}
		if (isset($shirt_size))
		{
			$param['shirt_size'] = $shirt_size;
			$param2['shirt_size'] = $shirt_size;
		}
		
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;
		
		$query = $this->member_model->lists($param);
		$total = $this->member_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				// hitung point
				$point = $this->member_point_model->lists_count(array('id_member' => $row->id_member));
				
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
					'point' => intval($point),
					'status' => intval($row->status),
					'member_number' => $row->member_number,
					'member_card' => $row->member_card,
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
	
	function update_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_member = filter($this->post('id_member'));
		$id_kota = filter($this->post('id_kota'));
		$name = filter(trim(strtolower($this->post('name'))));
		$email = filter(trim(strtolower($this->post('email'))));
		$username = filter(trim(strtolower($this->post('username'))));
		$password = filter(trim($this->post('password')));
		$idcard_type = filter(trim(intval($this->post('idcard_type'))));
		$idcard_number = filter(trim(intval($this->post('idcard_number'))));
		$idcard_photo = filter(trim(strtolower($this->post('idcard_photo'))));
		$idcard_address = filter(trim(strtolower($this->post('idcard_address'))));
		$shipment_address = filter(trim(strtolower($this->post('shipment_address'))));
		$postal_code = filter(trim(intval($this->post('postal_code'))));
		$gender = filter(trim($this->post('gender')));
		$phone_number = filter(trim($this->post('phone_number')));
		$birth_place = filter(trim(strtolower($this->post('birth_place'))));
		$birth_date = filter(trim($this->post('birth_date')));
		$marital_status = filter(trim($this->post('marital_status')));
		$occupation = filter(trim(strtolower($this->post('occupation'))));
		$religion = filter(trim(intval($this->post('religion'))));
		$shirt_size = filter(trim($this->post('shirt_size')));
		$photo = filter(trim(strtolower($this->post('photo'))));
		$status = filter(trim(intval($this->post('status'))));
		$member_number = filter(trim(intval($this->post('member_number'))));
		$member_card = filter(trim(strtoupper($this->post('member_card'))));
		
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
			$query = $this->member_model->info(array('id_member' => $id_member));
			
			if ($query->num_rows() > 0)
			{
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
					$param['password'] = $password;
					
					// bisa ditambah send email
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
				
				if (isset($gender))
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
				
				if (isset($marital_status))
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
				
				if (isset($shirt_size))
				{
					$param['shirt_size'] = $shirt_size;
				}
				
				if ($photo == TRUE)
				{
					$param['photo'] = $photo;
				}
				
				if ($status == TRUE)
				{
					$param['status'] = $status;
				}
				
				if ($member_number == TRUE)
				{
					$param['member_number'] = $member_number;
				}
				
				if ($member_card == TRUE)
				{
					$param['member_card'] = $member_card;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->member_model->update($id_member, $param);
					
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
	
	// Dipakai untuk login karena butuh username & password (required) 
	function valid_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$username = filter(trim($this->post('username')));
		$password = filter(trim($this->post('password')));
		
		$data = array();
		if ($username == FALSE)
		{
			$data['username'] = 'required';
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
			$query = $this->member_model->info(array('username' => $username));
			
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
}
