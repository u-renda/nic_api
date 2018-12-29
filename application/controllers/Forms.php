<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Forms extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('forms_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$title = filter(trim(strtolower($this->post('title'))));
		$description = filter(trim($this->post('description')));
		$detail = filter(trim($this->post('detail')));
		$status = filter(trim(strtolower($this->post('status'))));
		$photo = filter($this->post('photo'));
		
		$data = array();
		if ($title == FALSE)
		{
			$data['title'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($description == FALSE)
		{
			$data['description'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($detail == FALSE)
		{
			$data['detail'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($status == FALSE)
		{
			$data['status'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_forms_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
		    $url_title = url_title(strtolower($title));
			
			if (check_post_slug($url_title) == FALSE)
			{
				$counter = random_string('numeric',5);
				$slug = url_title(strtolower(''.$title.'-'.$counter.''));
			}
			else
			{
				$slug = $url_title;
			}
			
			$param = array();
			$param['title'] = $title;
			$param['slug'] = $slug;
			$param['description'] = $description;
			$param['detail'] = $detail;
			$param['status'] = $status;
			$param['photo'] = $photo;
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
		
        $id_forms = filter($this->post('id_forms'));
        
		$data = array();
        if ($id_forms == FALSE)
		{
			$data['id_forms'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_forms' => $id_forms));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id_forms);
				
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
				$data['id_forms'] = 'not found';
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
		
		$id_forms = filter($this->get('id_forms'));
		$slug = filter(trim(strtolower($this->get('slug'))));
		
		$data = array();
		if ($id_forms == FALSE && $slug == FALSE)
		{
			$data['id_forms'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_forms != '')
			{
				$param['id_forms'] = $id_forms;
			}
			else
			{
				$param['slug'] = $slug;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_forms' => $row->id_forms,
					'title' => $row->title,
					'slug' => $row->slug,
					'description' => $row->description,
					'detail' => $row->detail,
					'status' => $row->status,
					'photo' => $row->photo,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_forms'] = 'Not Found';
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
		
		if (in_array($order, $this->config->item('default_forms_order')) && ($order == TRUE))
		{
			$order = $order;
		}
		else
		{
			$order = 'title';
		}
		
		if (in_array($sort, $this->config->item('default_sort')) && ($sort == TRUE))
		{
			$sort = $sort;
		}
		else
		{
			$sort = 'asc';
		}
		
		if (in_array($status, $this->config->item('default_forms_status')) && ($status == TRUE))
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
					'id_forms' => $row->id_forms,
					'title' => $row->title,
					'slug' => $row->slug,
					'description' => $row->description,
					'detail' => $row->detail,
					'status' => $row->status,
					'photo' => $row->photo,
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
		
		$id_forms = filter($this->post('id_forms'));
		$title = filter(trim(strtolower($this->post('title'))));
		$description = filter(trim($this->post('description')));
		$detail = filter(trim($this->post('detail')));
		$status = filter(trim(strtolower($this->post('status'))));
		$photo = filter(trim(strtolower($this->post('photo'))));
		
		$data = array();
		if ($id_forms == FALSE)
		{
			$data['id_forms'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_forms_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_forms' => $id_forms));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($title == TRUE)
				{
					$param['title'] = $title;
				}
				
				if ($description == TRUE)
				{
					$param['description'] = $description;
				}
				
				if ($detail == TRUE)
				{
					$param['detail'] = $detail;
				}
				
				if ($status == TRUE)
				{
					$param['status'] = $status;
				}
				
				if ($photo == TRUE)
				{
					$param['photo'] = $photo;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_forms, $param);
					
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
				$data['id_forms'] = 'not found';
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
