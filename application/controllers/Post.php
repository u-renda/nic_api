<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Post extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->helper('fungsi');
		$this->load->model('post_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$title = filter(trim(strtolower($this->post('title'))));
		$content = filter(trim($this->post('content')));
		$media = filter(trim($this->post('media')));
		$media_type = filter(trim(intval($this->post('media_type'))));
		$type = filter(trim(intval($this->post('type'))));
		$status = filter(trim(intval($this->post('status'))));
		$is_event = filter(trim($this->post('is_event')));
		$created_date = filter(trim($this->post('created_date')));
		
		$data = array();
		if ($title == FALSE)
		{
			$data['title'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($content == FALSE)
		{
			$data['content'] = 'required';
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
		
		if ( ! isset($is_event))
		{
			$data['is_event'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($media_type, $this->config->item('default_post_media_type')) == FALSE && $media_type == TRUE)
		{
			$data['media_type'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($type, $this->config->item('default_post_type')) == FALSE && $type == TRUE)
		{
			$data['type'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_post_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($is_event, $this->config->item('default_post_is_event')) == FALSE && $is_event == TRUE)
		{
			$data['is_event'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$url_title = url_title($title);
			
			if (check_post_slug($url_title) == FALSE)
			{
				$counter = random_string('numeric',5);
				$slug = url_title(strtolower(''.$title.'-'.$counter.''));
			}
			else
			{
				$slug = url_title($title);
			}

            if ($created_date != FALSE)
            {
                $created_date = $created_date;
            }
            else
            {
                $created_date = date('Y-m-d H:i:s');
            }
		
			$param = array();
			$param['title'] = $title;
			$param['slug'] = $slug;
			$param['content'] = $content;
			$param['media'] = $media;
			$param['media_type'] = $media_type;
			$param['type'] = $type;
			$param['status'] = $status;
			$param['is_event'] = $is_event;
			$param['created_date'] = $created_date;
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->post_model->create($param);
			
			if ($query > 0)
			{
				$data['id_post'] = $query;
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
		
        $id_post = filter($this->post('id_post'));
        
		$data = array();
        if ($id_post == FALSE)
		{
			$data['id_post'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->post_model->info(array('id_post' => $id_post));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->post_model->delete($id_post);
				
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
				$data['id_post'] = 'not found';
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
		
		$id_post = filter($this->get('id_post'));
		$title = filter(trim(strtolower($this->get('title'))));
		$slug = filter(trim(strtolower($this->get('slug'))));
		
		$data = array();
		if ($id_post == FALSE && $title == FALSE && $slug == FALSE)
		{
			$data['id_post'] = 'required (title/slug)';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_post)
			{
				$param['id_post'] = $id_post;
			}
			elseif ($title)
			{
				$param['title'] = $title;
			}
			else
			{
				$param['slug'] = $slug;
			}
			
			$query = $this->post_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_post' => $row->id_post,
					'title' => $row->title,
					'slug' => $row->slug,
					'content' => $row->content,
					'media' => $row->media,
					'media_type' => intval($row->media_type),
					'type' => intval($row->type),
					'status' => intval($row->status),
					'is_event' => intval($row->is_event),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_post'] = 'not found (title/slug)';
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
		$media_type = filter(intval(trim($this->get('media_type'))));
		$type = filter(intval(trim($this->get('type'))));
		$status = filter(intval(trim($this->get('status'))));
		$is_event = filter(trim($this->get('is_event')));
		$media_not = filter(trim($this->get('media_not')));
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
		
		if (in_array($order, $this->config->item('default_post_order')) && ($order == TRUE))
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
		
		if (in_array($media_type, $this->config->item('default_post_media_type')) && ($media_type == TRUE))
		{
			$media_type = $media_type;
		}
		
		if (in_array($type, $this->config->item('default_post_type')) && ($type == TRUE))
		{
			$type = $type;
		}
		
		if (in_array($status, $this->config->item('default_post_status')) && ($status == TRUE))
		{
			$status = $status;
		}
		
		if (in_array($is_event, $this->config->item('default_post_is_event')) && ($is_event == TRUE))
		{
			$is_event = $is_event;
		}
		
		$param = array();
		$param2 = array();
		if ($q == TRUE)
		{
			$param['q'] = $q;
			$param2['q'] = $q;
		}
		if ($media_type == TRUE)
		{
			$param['media_type'] = $media_type;
			$param2['media_type'] = $media_type;
		}
		if ($type == TRUE)
		{
			$param['type'] = $type;
			$param2['type'] = $type;
		}
		if ($status == TRUE)
		{
			$param['status'] = $status;
			$param2['status'] = $status;
		}
		if ($is_event != '')
		{
			$param['is_event'] = $is_event;
			$param2['is_event'] = $is_event;
		}
		if ($media_not == TRUE)
		{
			$param['media_not'] = $media_not;
			$param2['media_not'] = $media_not;
		}
		
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;
		
		$query = $this->post_model->lists($param);
		$total = $this->post_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_post' => $row->id_post,
					'title' => $row->title,
					'slug' => $row->slug,
					'content' => $row->content,
					'media' => $row->media,
					'media_type' => intval($row->media_type),
					'type' => intval($row->type),
					'status' => intval($row->status),
					'is_event' => intval($row->is_event),
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
		
		$id_post = filter($this->post('id_post'));
		$title = filter(trim(strtolower($this->post('title'))));
		$content = filter(trim($this->post('content')));
		$media = filter(trim($this->post('media')));
		$media_type = filter(trim(intval($this->post('media_type'))));
		$type = filter(trim(intval($this->post('type'))));
		$status = filter(trim(intval($this->post('status'))));
		$is_event = filter(trim($this->post('is_event')));
		$created_date = filter(trim($this->post('created_date')));
		
		$data = array();
		if ($id_post == FALSE)
		{
			$data['id_post'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($media_type, $this->config->item('default_post_media_type')) == FALSE && $media_type == TRUE)
		{
			$data['media_type'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($type, $this->config->item('default_post_type')) == FALSE && $type == TRUE)
		{
			$data['type'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_post_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($is_event, $this->config->item('default_post_is_event')) == FALSE && $is_event == TRUE)
		{
			$data['is_event'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->post_model->info(array('id_post' => $id_post));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($title == TRUE)
				{
					$param['title'] = $title;
				}
				
				if ($content == TRUE)
				{
					$param['content'] = $content;
				}
				
				if ($media == TRUE)
				{
					$param['media'] = $media;
				}
				
				if ($media_type == TRUE)
				{
					$param['media_type'] = $media_type;
				}
				
				if ($type == TRUE)
				{
					$param['type'] = $type;
				}
				
				if ($status == TRUE)
				{
					$param['status'] = $status;
				}
				
				if ($created_date == TRUE)
				{
					$param['created_date'] = $created_date;
				}
				
				if (isset($is_event) && $is_event == TRUE)
				{
					$param['is_event'] = $is_event;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->post_model->update($id_post, $param);
					
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
				$data['id_post'] = 'not found';
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
