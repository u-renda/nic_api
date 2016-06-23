<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Events extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('events_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_post = filter($this->post('id_post'));
		$title = filter(trim($this->post('title')));
		$date = filter(trim($this->post('date')));
		$status = filter(trim($this->post('status')));
		
		$data = array();
		if ($title == FALSE)
		{
			$data['title'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($date == FALSE)
		{
			$data['date'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($status == FALSE)
		{
			$data['status'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_events_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_post == TRUE)
			{
				$param['id_post'] = $id_post;
			}
			
			$param['title'] = $title;
			$param['date'] = $date;
			$param['status'] = intval($status);
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->events_model->create($param);
			
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
		
        $id_events = filter($this->post('id_events'));
        
		$data = array();
        if ($id_events == FALSE)
		{
			$data['id_events'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->events_model->info(array('id_events' => $id_events));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->events_model->delete($id_events);
				
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
				$data['id_events'] = 'not found';
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
		
		$id_events = filter($this->get('id_events'));
		$id_post = filter($this->get('id_post'));
		$title = filter(trim($this->get('title')));
		$date = filter(trim($this->get('date')));
		
		$data = array();
		if ($id_events == FALSE && $id_post == FALSE && $title == FALSE && $date == FALSE)
		{
			$data['id_events'] = 'required (id_post/title/date)';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_events != '')
			{
				$param['id_events'] = $id_events;
			}
			elseif ($id_post != '')
			{
				$param['id_post'] = $id_post;
			}
			elseif ($title != '')
			{
				$param['title'] = $title;
			}
			else
			{
				$param['date'] = $date;
			}
			
			$query = $this->events_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_events' => $row->id_events,
					'date' => $row->date,
					'title' => $row->title,
					'status' => intval($row->status),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'post' => array(
						'id_post' => $row->id_post,
						'slug' => $row->slug,
						'content' => $row->content,
						'media' => $row->media,
						'media_type' => intval($row->media_type),
						'type' => intval($row->type),
						'status' => intval($row->post_status),
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
				$data['id_events'] = 'not found (id_post/title/date)';
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
		
		if (in_array($order, $this->config->item('default_events_order')) && ($order == TRUE))
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
		if ($q == TRUE)
		{
			$param['q'] = $q;
			$param2['q'] = $q;
		}
		
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;
		
		$query = $this->events_model->lists($param);
		$total = $this->events_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_events' => $row->id_events,
					'id_post' => $row->id_post,
					'date' => $row->date,
					'title' => $row->title,
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
		
		$id_events = filter($this->post('id_events'));
		$id_post = filter($this->post('id_post'));
		$title = filter(trim($this->post('title')));
		$date = filter(trim($this->post('date')));
		$status = filter(trim($this->post('status')));
		
		$data = array();
		if ($id_events == FALSE)
		{
			$data['id_events'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_events_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->events_model->info(array('id_events' => $id_events));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_post == TRUE)
				{
					$param['id_post'] = $id_post;
				}
				
				if ($title == TRUE)
				{
					$param['title'] = $title;
				}
				
				if ($date == TRUE)
				{
					$param['date'] = $date;
				}
				
				if ($status == TRUE)
				{
					$param['status'] = intval($status);
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->events_model->update($id_events, $param);
					
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
				$data['id_events'] = 'not found';
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
