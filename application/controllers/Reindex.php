<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Reindex extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('member_model');
		$this->load->model('member_transfer_model');
		$this->load->model('post_model');
		$this->load->model('reindex_model');
    }
	
	// 1
	function admin_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
        
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		
		if ($offset == '' && ! isset($offset))
		{
			$data['offset'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($limit == FALSE)
		{
			$data['limit'] = 'required';
			$validation = 'error';
			$code = 400;
		}
        
		if ($validation == 'ok')
		{
			$param = array();
			$param['limit'] = $limit;
			$param['offset'] = $offset;
			
			$get = $this->reindex_model->old_admin_lists($param);
			
			if ($get->num_rows() > 0)
			{
				$i = 0;
				foreach ($get->result() as $row)
				{
					$param2 = array();
					$param2['username'] = trim(strtolower($row->username));
					$param2['email'] = trim(strtolower($row->email));
					$param2['admin_initial'] = trim(strtoupper($row->admin_initial));
					$param2['name'] = trim(strtolower($row->firstname));
					$param2['password'] = trim($row->password);
					$param2['photo'] = '';
					$param2['admin_role'] = 1;
					$param2['position'] = '';
					$param2['twitter'] = '';
					$param2['admin_group'] = 1;
					$param2['created_date'] = $row->create_date;
					$param2['updated_date'] = $row->update_date;
					$create = $this->reindex_model->admin($param2);
					
					// Update Nic_admin
					$update = $this->reindex_model->old_admin_update($row->admin_id, array('cron' => 'bot'));
					$i++;
				}
				
				$data = "Berhasil. Total = ".$i;
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data = "Selesai";
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
	
	// 11
	function change_image_member_get()
	{
		// Merubah URL image di member
		// Offset-nya diganti terus smp total member terpenuhi
		$this->benchmark->mark('code_start');
		$validation = 'ok';
        
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		
		if ( ! isset($offset))
		{
			$data['offset'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($limit == FALSE)
		{
			$data['limit'] = 'required';
			$validation = 'error';
			$code = 400;
		}
        
		if ($validation == 'ok')
		{
			$param = array();
			$param['limit'] = $limit;
			$param['offset'] = $offset;
			$param['shirt_size'] = '';
			$param['marital_status'] = '';
			$param['gender'] = '';
			$param['order'] = 'created_date';
			$param['sort'] = 'asc';
			
			$get = $this->member_model->lists($param);
			
			if ($get->num_rows() > 0)
			{
				$i = 0;
				foreach ($get->result() as $row)
				{
					$id_member = $row->id_member;
					$idcard_photo = $row->idcard_photo;
					$photo = $row->photo;
					
					$param = array();
					$new_idcard_photo = '';
					$new_photo = '';
					if ($idcard_photo != '')
					{
						$explode = explode('../ext/upload/register/', $idcard_photo);
						
						if (count($explode) > 1)
						{
							$new_idcard_photo = $this->config->item('link_member_photo').$explode[1];
						}
					}
					
					if ($photo != '')
					{
						$explode = explode('../ext/upload/register/', $photo);
						
						if (count($explode) > 1)
						{
							$new_photo = $this->config->item('link_member_photo').$explode[1];
						}
					}					
					
					$param['idcard_photo'] = $new_idcard_photo;
					$param['photo'] = $new_photo;
					$query = $this->member_model->update($id_member, $param);
					$i++;
				}
				
				$data = "Berhasil. Total = ".$i;
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data = "Selesai";
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
	
	// 12
	function change_image_member_transfer_get()
	{
		// Merubah URL image di member_transfer
		// Offset-nya diganti terus smp total terpenuhi
		$this->benchmark->mark('code_start');
		$validation = 'ok';
        
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		
		if ( ! isset($offset))
		{
			$data['offset'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($limit == FALSE)
		{
			$data['limit'] = 'required';
			$validation = 'error';
			$code = 400;
		}
        
		if ($validation == 'ok')
		{
			$param = array();
			$param['limit'] = $limit;
			$param['offset'] = $offset;
			$param['order'] = 'created_date';
			$param['sort'] = 'asc';
			
			$get = $this->member_transfer_model->lists($param);
			
			if ($get->num_rows() > 0)
			{
				$i = 0;
				foreach ($get->result() as $row)
				{
					$id = $row->id_member_transfer;
					$photo = $row->photo;
					
					$param = array();
					$new_photo = '';
					if ($photo != '')
					{
						$explode = explode('../ext/upload/register/', $photo);
						
						if (count($explode) > 1)
						{
							$new_photo = $this->config->item('link_member_photo').$explode[1];
						}
					}					
					
					$param['photo'] = $new_photo;
					$query = $this->member_transfer_model->update($id, $param);
					$i++;
				}
				
				$data = "Berhasil. Total = ".$i;
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data = "Selesai";
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
	
	// 13
	function change_image_post_get()
	{
		// Merubah URL image di post
		// Offset-nya diganti terus smp total terpenuhi
		$this->benchmark->mark('code_start');
		$validation = 'ok';
        
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		
		if ( ! isset($offset))
		{
			$data['offset'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($limit == FALSE)
		{
			$data['limit'] = 'required';
			$validation = 'error';
			$code = 400;
		}
        
		if ($validation == 'ok')
		{
			$param = array();
			$param['limit'] = $limit;
			$param['offset'] = $offset;
			$param['order'] = 'created_date';
			$param['sort'] = 'asc';
			$param['media_type'] = 2;
			$param['is_event'] = '';
			
			$get = $this->post_model->lists($param);
			
			if ($get->num_rows() > 0)
			{
				$i = 0;
				foreach ($get->result() as $row)
				{
					$id = $row->id_post;
					$photo = $row->media;
					
					$param = array();
					$new_photo = '';
					if ($photo != '')
					{
						$explode = explode('../ext/upload/image/', $photo);
						
						if (count($explode) > 1)
						{
							$new_photo = $this->config->item('link_post_photo').$explode[1];
						}
					}					
					
					$param['media'] = $new_photo;
					$query = $this->post_model->update($id, $param);
					$i++;
				}
				
				$data = "Berhasil. Total = ".$i;
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data = "Selesai";
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
	
	// 4
	function events_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
        
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		
		if ( ! isset($offset))
		{
			$data['offset'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($limit == FALSE)
		{
			$data['limit'] = 'required';
			$validation = 'error';
			$code = 400;
		}
        
		if ($validation == 'ok')
		{
			$param = array();
			$param['limit'] = $limit;
			$param['offset'] = $offset;
			
			$get = $this->reindex_model->old_events_lists($param);
			
			if ($get->num_rows() > 0)
			{
				$i = 0;
				foreach ($get->result() as $row)
				{
					$explode = explode(' ', $row->event_date);
					$date = $explode[0];
					
					if ($row->status == 'P')
					{
						$status = 1;
					}
					elseif ($row->status == 'D')
					{
						$status = 0;
					}
					else
					{
						$status = 3;
					}
					
					$get_nic_post = $this->reindex_model->old_post_info(array('post_id' => $row->post_id));
					
					$param2 = array();
					if ($get_nic_post->num_rows() > 0)
					{
						$post_title = trim($get_nic_post->row()->post_title);
						$get_post = $this->reindex_model->post_info(array('title' => $post_title));
						
						if ($get_post->num_rows() > 0)
						{
							$param2['id_post'] = $get_post->row()->id_post;
							$param2['title'] = $get_post->row()->title;
						}
					}
					else
					{
						$param2['id_post'] = 0;
						$param2['title'] = $row->event_title;
					}
					
					$param2['date'] = $date;
					$param2['status'] = $status;
					$param2['created_date'] = $row->create_date;
					$param2['updated_date'] = $row->update_date;
					$create = $this->reindex_model->events($param2);
					$i++;
					
					// Update Nic_event
					$update = $this->reindex_model->old_events_update($row->event_id, array('cron' => 'bot'));
				}
				
				$data = "Berhasil. Total = ".$i;
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data = "Selesai";
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
	
	// 5
	function faq_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
        
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		
		if ( ! isset($offset))
		{
			$data['offset'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($limit == FALSE)
		{
			$data['limit'] = 'required';
			$validation = 'error';
			$code = 400;
		}
        
		if ($validation == 'ok')
		{
			$param = array();
			$param['limit'] = $limit;
			$param['offset'] = $offset;
			
			$get = $this->reindex_model->old_faq_lists($param);
			
			if ($get->num_rows() > 0)
			{
				$i = 0;
				foreach ($get->result() as $row)
				{
					$question = '';
					$answer = '';
					if ($row->faq_category == 'N')
					{
						$category = 2;
					}
					else
					{
						$category = 1;
					}
					
					if ($row->faq_type == 'Q')
					{
						$question = trim(strtolower($row->faq_content));
					}
					else
					{
						$answer = trim(strtolower($row->faq_content));
					}
					
					$param2 = array();
					$param2['category'] = $category;
					$param2['question'] = $question;
					$param2['answer'] = $answer;
					$param2['created_date'] = $row->create_date;
					$param2['updated_date'] = $row->update_date;
					$create = $this->reindex_model->faq($param2);
					$i++;
					
					// Update Nic_faq
					$update = $this->reindex_model->old_faq_update($row->faq_id, array('cron' => 'bot'));
				}
				
				$data = "Berhasil. Total = ".$i;
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data = "Selesai";
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
	
	// 7
	function kota_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
        
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		
		if ( ! isset($offset))
		{
			$data['offset'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($limit == FALSE)
		{
			$data['limit'] = 'required';
			$validation = 'error';
			$code = 400;
		}
        
		if ($validation == 'ok')
		{
			$param = array();
			$param['limit'] = $limit;
			$param['offset'] = $offset;
			
			$get = $this->reindex_model->old_kota_lists($param);
			
			if ($get->num_rows() > 0)
			{
				$i = 0;
				foreach ($get->result() as $row)
				{
					$get_nic_provinsi = $this->reindex_model->old_provinsi_info(array('prov_id' => $row->prov_id));
				
					if ($get_nic_provinsi->num_rows() > 0)
					{
						$name = trim(strtolower($get_nic_provinsi->row()->prov_name));
					
						$get_post = $this->reindex_model->provinsi_info(array('provinsi' => $name));
						$id_provinsi = $get_post->row()->id_provinsi;
						
						if ($row->status == 'X')
						{
							$status = 0;
						}
						else
						{
							$status = 1;
						}
						
						$param2 = array();
						$param2['id_provinsi'] = $id_provinsi;
						$param2['kota'] = trim(strtolower($row->city_name));
						$param2['price'] = trim($row->delivery_cost);
						$param2['status'] = $status;
						$param2['created_date'] = date('Y-m-d H:i:s');
						$param2['updated_date'] = date('Y-m-d H:i:s');
						$create = $this->reindex_model->kota($param2);
						$i++;
						
						// Update Delivery_cost
						$update = $this->reindex_model->old_kota_update($row->city_id, array('cron' => 'bot'));
					}
				}
				
				$data = "Berhasil. Total = ".$i;
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data = "Selesai";
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
	
	// 8
	function member_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
        
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		
		if ( ! isset($offset))
		{
			$data['offset'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($limit == FALSE)
		{
			$data['limit'] = 'required';
			$validation = 'error';
			$code = 400;
		}
        
		if ($validation == 'ok')
		{
			$param = array();
			$param['limit'] = $limit;
			$param['offset'] = $offset;
			
			$get = $this->reindex_model->old_member_lists($param);

			if ($get->num_rows() > 0)
			{
				$i = 0;
				foreach ($get->result() as $row)
				{
                    // masukkan kota 'jakarta, dki jakarta' untuk semua member
					$get_kota = $this->reindex_model->kota_info(array('kota' => 'jakarta, dki jakarta'));

					$id_kota = $get_kota->row()->id_kota;
					
					if ($row->idcard_type == 'ktp')
					{
						$idcard_type = 1;
					}
					elseif ($row->idcard_type == 'sim')
					{
						$idcard_type = 2;
					}
					elseif ($row->idcard_type == 'passport')
					{
						$idcard_type = 3;
					}
					elseif ($row->idcard_type == 'student_id')
					{
						$idcard_type = 4;
					}
					else
					{
						$idcard_type = 5;
					}
					
					if ($row->gender == 'M')
					{
						$gender = 0;
					}
					else
					{
						$gender = 1;
					}
					
					if ($row->marital_status == 'M')
					{
						$marital_status = 1;
					}
					else
					{
						$marital_status = 0;
					}
					
					if ($row->religion == 'Islam')
					{
						$religion = 1;
					}
					elseif ($row->religion == 'Protestan' || $row->religion == 'kristen protest' || $row->religion == 'Kristen')
					{
						$religion = 2;
					}
					elseif ($row->religion == 'Katolik')
					{
						$religion = 3;
					}
					elseif ($row->religion == 'BUDHA' || $row->religion == 'Buddha')
					{
						$religion = 4;
					}
					elseif ($row->religion == 'Hindu')
					{
						$religion = 5;
					}
					elseif ($row->religion == 'Kongfuchu')
					{
						$religion = 6;
					}
					else
					{
						$religion = 7;
					}
					
					if ($row->shirt_size == 'M')
					{
						$shirt_size = 0;
					}
					else
					{
						$shirt_size = 1;
					}
					
					if ($row->reg_status == 'RV')
					{
						$status = 1;
					}
					elseif ($row->reg_status == 'TR')
					{
						$status = 2;
					}
					elseif ($row->reg_status == 'PR')
					{
						$status = 3;
					}
					elseif ($row->reg_status == 'AP')
					{
						$status = 4;
					}
					elseif ($row->reg_status == 'IV')
					{
						$status = 5;
					}
					else
					{
						$status = 6;
					}
					
					$param2 = array();
					$param2['id_kota'] = $id_kota;
					$param2['name'] = trim(strtolower($row->fullname));
					$param2['email'] = trim(strtolower($row->email));
					$param2['username'] = trim($row->username);
					$param2['password'] = trim($row->password);
					$param2['idcard_type'] = $idcard_type;
					$param2['idcard_number'] = trim($row->idcard_no);
					$param2['idcard_photo'] = trim($row->idscan_path);
					$param2['idcard_address'] = trim(strtolower($row->idcard_address));
					$param2['shipment_address'] = trim(strtolower($row->ship_address));
					$param2['postal_code'] = '';
					$param2['gender'] = $gender;
					$param2['phone_number'] = trim($row->phone_no);
					$param2['birth_place'] = trim(strtolower($row->birth_place));
					$param2['birth_date'] = trim($row->birth_date);
					$param2['marital_status'] = $marital_status;
					$param2['occupation'] = trim(strtolower($row->occupation));
					$param2['religion'] = $religion;
					$param2['shirt_size'] = $shirt_size;
					$param2['photo'] = trim($row->photo_path);
					$param2['status'] = $status;
					$param2['member_number'] = trim($row->queue_num);
					$param2['member_card'] = trim(strtoupper($row->member_id));
					$param2['approved_date'] = $row->approved_date;
					$param2['created_date'] = $row->create_date;
					$param2['updated_date'] = $row->update_date;
					$create = $this->reindex_model->member($param2);
					$i++;
						
					// Update Nic_member
					$update = $this->reindex_model->old_member_update($row->acct_id, array('cron' => 'bot'));
				}
				
				$data = "Berhasil. Total = ".$i;
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data = "Selesai";
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
	
	// 9
	function member_transfer_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
        
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		
		if ( ! isset($offset))
		{
			$data['offset'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($limit == FALSE)
		{
			$data['limit'] = 'required';
			$validation = 'error';
			$code = 400;
		}
        
		if ($validation == 'ok')
		{
			$param = array();
			$param['limit'] = $limit;
			$param['offset'] = $offset;
			
			$get = $this->reindex_model->old_member_transfer_lists($param);
			
			if ($get->num_rows() > 0)
			{
				$i = 0;
				foreach ($get->result() as $row)
				{
					$get_nic_member = $this->reindex_model->old_member_info(array('acct_id' => $row->acct_id));
				
					if ($get_nic_member->num_rows() > 0)
					{
						$fullname = trim(strtolower($get_nic_member->row()->fullname));
					
						$get_member = $this->reindex_model->member_info(array('name' => $fullname));
						$id_member = $get_member->row()->id_member;
					
						$explode = explode(' ', $row->tgl_trf);
						$date = $explode[0];
						
						if ($row->must_trf == 0)
						{
							$status = 0;
						}
						else
						{
							$status = 1;
						}
						
						$param2 = array();
						$param2['id_member'] = $id_member;
						$param2['total'] = trim($row->jml_trf);
						$param2['date'] = $date;
						$param2['photo'] = trim($row->trf_path);
						$param2['account_name'] = trim(strtolower($row->pemilik_rek));
						$param2['other_information'] = trim(strtolower($row->cat_tambahan));
						$param2['type'] = 1;
						$param2['status'] = $status;
						$param2['created_date'] = date('Y-m-d H:i:s');
						$param2['updated_date'] = date('Y-m-d H:i:s');
						$create = $this->reindex_model->member_transfer($param2);
						$i++;
						
						// Update Nic_transfer
						$update = $this->reindex_model->old_member_transfer_update($row->trf_id, array('cron' => 'bot'));
					}
				}
				
				$data = "Berhasil. Total = ".$i;
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data = "Selesai";
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
	
	// 2
	function post_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
        
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		
		if ( ! isset($offset))
		{
			$data['offset'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($limit == FALSE)
		{
			$data['limit'] = 'required';
			$validation = 'error';
			$code = 400;
		}
        
		if ($validation == 'ok')
		{
			$param = array();
			$param['limit'] = $limit;
			$param['offset'] = $offset;
			
			$get = $this->reindex_model->old_post_lists($param);
			
			if ($get->num_rows() > 0)
			{
				$i = 0;
				foreach ($get->result() as $row)
				{
					$json_decode = json_decode($row->post_media);
					$media = $json_decode[1];
					
					if ($json_decode[0] == 'image')
					{
						$media_type = 2;
					}
					else
					{
						$media_type = 1;
					}
					
					if ($row->post_type == 'N')
					{
						$type = 2;
					}
					else
					{
						$type = 1;
					}
					
					// Status delete gak diperluin lagi, jadi gak usah dimasukkin
					if ($row->status == 'P')
					{
						$status = 1;
					}
					elseif ($row->status == 'D')
					{
						$status = 2;
					}
					else
					{
						$status = 3;
					}
					
					if ($row->is_event == 'N')
					{
						$event = 0;
					}
					else
					{
						$event = 1;
					}
					
					$param2 = array();
					$param2['title'] = trim($row->post_title);
					$param2['slug'] = url_title(strtolower($param2['title']));
					$param2['content'] = trim(stripcslashes($row->post_content));
					$param2['media'] = trim($media);
					$param2['media_type'] = $media_type;
					$param2['type'] = $type;
					$param2['status'] = $status;
					$param2['is_event'] = $event;
					$param2['created_date'] = $row->create_date;
					$param2['updated_date'] = $row->update_date;
					$create = $this->reindex_model->post($param2);
					
					// Update Nic_post
					$update = $this->reindex_model->old_post_update($row->post_id, array('cron' => 'bot'));
					$i++;
				}
				
				$data = "Berhasil. Total = ".$i;
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data = "Selesai";
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
	
	// 3
	function post_archived_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
        
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		
		if ( ! isset($offset))
		{
			$data['offset'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($limit == FALSE)
		{
			$data['limit'] = 'required';
			$validation = 'error';
			$code = 400;
		}
        
		if ($validation == 'ok')
		{
			$param = array();
			$param['limit'] = $limit;
			$param['offset'] = $offset;
			
			$get = $this->reindex_model->old_post_archived_lists($param);
			
			if ($get->num_rows() > 0)
			{
				$i = 0;
				foreach ($get->result() as $row)
				{
					$get_nic_post = $this->reindex_model->old_post_info(array('post_id' => $row->post_id));
					
					if ($get_nic_post->num_rows() > 0)
					{
						$title = trim(strtolower($get_nic_post->row()->post_title));
						$get_post = $this->reindex_model->post_info(array('title' => $title));
						
						if ($get_post->num_rows() > 0)
						{
							$id_post = $get_post->row()->id_post;
					
							$param2 = array();
							$param2['id_post'] = $id_post;
							$param2['year'] = $row->year;
							$param2['month'] = $row->month;
							$param2['created_date'] = date('Y-m-d H:i:s');
							$param2['updated_date'] = date('Y-m-d H:i:s');
							$create = $this->reindex_model->post_archived($param2);
							$i++;
							
							// Update Nic_archive
							$update = $this->reindex_model->old_post_archived_update($row->post_id, array('cron' => 'bot'));
						}
					}
				}
				
				$data = "Berhasil. Total = ".$i;
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data = "Selesai";
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

    // 10
    function preferences_get()
    {
        $this->benchmark->mark('code_start');
        $validation = 'ok';

        $offset = intval(trim($this->get('offset')));
        $limit = intval(trim($this->get('limit')));

        if ( ! isset($offset))
        {
            $data['offset'] = 'required';
            $validation = 'error';
            $code = 400;
        }

        if ($limit == FALSE)
        {
            $data['limit'] = 'required';
            $validation = 'error';
            $code = 400;
        }

        if ($validation == 'ok')
        {
            $param = array();
            $param['limit'] = $limit;
            $param['offset'] = $offset;

            $get = $this->reindex_model->old_preferences_lists($param);

            if ($get->num_rows() > 0)
            {
                $i = 0;
                foreach ($get->result() as $row)
                {
                    $param2 = array();
                    $param2['key'] = trim(strtolower($row->pref_key));
                    $param2['value'] = trim($row->value);
                    $param2['created_date'] = date('Y-m-d H:i:s');
                    $param2['updated_date'] = date('Y-m-d H:i:s');
                    $create = $this->reindex_model->preferences($param2);
                    $i++;

                    // Update Ind_provinces
                    $update = $this->reindex_model->old_preferences_update($row->pref_id, array('cron' => 'bot'));
                }

                $data = "Berhasil. Total = ".$i;
                $validation = 'ok';
                $code = 200;
            }
            else
            {
                $data = "Selesai";
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
	
	// 6
	function provinsi_get()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
        
		$offset = intval(trim($this->get('offset')));
		$limit = intval(trim($this->get('limit')));
		
		if ( ! isset($offset))
		{
			$data['offset'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($limit == FALSE)
		{
			$data['limit'] = 'required';
			$validation = 'error';
			$code = 400;
		}
        
		if ($validation == 'ok')
		{
			$param = array();
			$param['limit'] = $limit;
			$param['offset'] = $offset;
			
			$get = $this->reindex_model->old_provinsi_lists($param);
			
			if ($get->num_rows() > 0)
			{
				$i = 0;
				foreach ($get->result() as $row)
				{
					$param2 = array();
					$param2['provinsi'] = trim(strtolower($row->prov_name));
					$param2['created_date'] = date('Y-m-d H:i:s');
					$param2['updated_date'] = date('Y-m-d H:i:s');
					$create = $this->reindex_model->provinsi($param2);
					$i++;
						
					// Update Ind_provinces
					$update = $this->reindex_model->old_provinsi_update($row->prov_id, array('cron' => 'bot'));
				}
				
				$data = "Berhasil. Total = ".$i;
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data = "Selesai";
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
