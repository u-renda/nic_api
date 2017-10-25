<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Post_archived extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('post_archived_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_post = filter($this->post('id_post'));
		$year = filter(trim(intval($this->post('year'))));
		$month = filter(trim(intval($this->post('month'))));
		
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
		
        $id_post_archived = filter($this->post('id_post_archived'));
        
		$data = array();
        if ($id_post_archived == FALSE)
		{
			$data['id_post_archived'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_post_archived' => $id_post_archived));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id_post_archived);
				
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
		
		$id_post_archived = filter($this->get('id_post_archived'));
		$id_post = filter($this->get('id_post'));
		
		$data = array();
		if ($id_post_archived == FALSE && $id_post == FALSE)
		{
			$data['id_post_archived'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_post_archived != '')
			{
				$param['id_post_archived'] = $id_post_archived;
			}
			else
			{
				$param['id_post'] = $id_post;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_post_archived' => $row->id_post_archived,
					'year' => intval($row->year),
					'month' => intval($row->month),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'post' => array(
						'id_post' => $row->id_post,
						'title' => $row->title,
						'slug' => $row->slug
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
		
		$status = filter(intval(trim($this->get('status'))));
		$type = filter(intval(trim($this->get('type'))));
		$offset = filter(intval(trim($this->get('offset'))));
		$limit = filter(intval(trim($this->get('limit'))));
		$order = filter(trim($this->get('order')));
		$sort = filter(trim($this->get('sort')));
		
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
		
		if (in_array($order, $this->config->item('default_post_archived_order')) && ($order == TRUE))
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
		
		$param = array();
		$param2 = array();
		if ($status == TRUE)
		{
			$param['status'] = $status;
			$param2['status'] = $status;
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
		
		$query = $this->the_model->lists($param);
		$total = $this->the_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_post_archived' => $row->id_post_archived,
					'year' => intval($row->year),
					'month' => intval($row->month),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'post' => array(
						'id_post' => $row->id_post,
						'title' => $row->title,
						'slug' => $row->slug
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
		
		$id_post_archived = filter($this->post('id_post_archived'));
		$id_post = filter($this->post('id_post'));
		$year = filter(trim(intval($this->post('year'))));
		$month = filter(trim(intval($this->post('month'))));
		
		$data = array();
		if ($id_post_archived == FALSE)
		{
			$data['id_post_archived'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_post_archived' => $id_post_archived));
			
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
					$update = $this->the_model->update($id_post_archived, $param);
					
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
