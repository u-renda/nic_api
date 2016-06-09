<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Post_archived extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('post_archived_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_post = trim(intval($this->post('id_post')));
		$year = trim(intval($this->post('year')));
		$month = trim(intval($this->post('month')));
		
		$data = array();
		if ($id_post == FALSE)
		{
			$data['id_post'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($year == FALSE)
		{
			$data['year'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($month == FALSE)
		{
			$data['month'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['id_post'] = $id_post;
			$param['year'] = $year;
			$param['month'] = $month;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->post_archived_model->create($param);
			
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
		
        $id_post_archived = trim($this->post('id_post_archived'));
        
		$data = array();
        if ($id_post_archived == FALSE)
		{
			$data['id_post_archived'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->post_archived_model->info(array('id_post_archived' => $id_post_archived));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->post_archived_model->delete($id_post_archived);
				
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
				$data['id_post_archived'] = 'not found';
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
		
		$id_post_archived = trim($this->get('id_post_archived'));
		
		$data = array();
		if ($id_post_archived == FALSE)
		{
			$data['id_post_archived'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_post_archived)
			{
				$param['id_post_archived'] = $id_post_archived;
			}
			
			$query = $this->post_archived_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_post_archived' => $row->id_post_archived,
					'id_post' => $row->id_post,
					'year' => intval($row->year),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'post' => array(
						'title' => $row->title,
						'slug' => $row->slug,
						'content' => $row->content,
						'media' => $row->media,
						'media_type' => intval($row->media_type),
						'type' => intval($row->type),
						'status' => intval($row->status),
						'is_event' => intval($row->is_event),
						'created_date' => $row->post_created_date,
						'updated_date' => $row->post_updated_date
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_post_archived'] = 'not found';
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
		$default_order = array("year", "month", "created_date");
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
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;
		
		$query = $this->post_archived_model->lists($param);
		$total = $this->post_archived_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_post_archived' => $row->id_post_archived,
					'id_post' => $row->id_post,
					'year' => intval($row->year),
					'month' => intval($row->month),
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
		
		$id_post_archived = trim(intval($this->post('id_post_archived')));
		$id_post = trim(intval($this->post('id_post')));
		$year = trim(intval($this->post('year')));
		$month = trim(intval($this->post('month')));
		
		$data = array();
		if ($id_post_archived == FALSE)
		{
			$data['id_post_archived'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->post_archived_model->info(array('id_post_archived' => $id_post_archived));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_post == TRUE)
				{
					$param['id_post'] = $id_post;
				}
				
				if ($year == TRUE)
				{
					$param['year'] = $year;
				}
				
				if ($month == TRUE)
				{
					$param['month'] = $month;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->post_archived_model->update($id_post_archived, $param);
					
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
				$data['id_post_archived'] = 'not found';
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
