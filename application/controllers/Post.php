<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Post extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->helper('fungsi');
		$this->load->model('events_model');
		$this->load->model('post_archived_model');
		$this->load->model('post_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$title = filter(trim($this->post('title')));
		$content = trim($this->post('content', FALSE));
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
		
		if ($is_event == '')
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
			$query = $this->the_model->create($param);
			
			if ($query > 0)
			{
				// save to archived
				$explode = explode('-', $created_date);
				
				$param2 = array();
				$param2['id_post'] = $query;
				$param2['year'] = $explode[0];
				$param2['month'] = $explode[1];
				$param2['created_date'] = $created_date;
				$param2['updated_date'] = date('Y-m-d H:i:s');
				$query2 = $this->post_archived_model->create($param2);
				
				if ($is_event == 1)
				{
					// save to event
					$param3 = array();
					$param3['id_post'] = $query;
					$param3['title'] = $title;
					$param3['date'] = $created_date;
					$param3['status'] = 1;
					$param2['created_date'] = date('Y-m-d H:i:s');
					$param2['updated_date'] = date('Y-m-d H:i:s');
					$query3 = $this->events_model->create($param3);
				}
				
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
            $query = $this->the_model->info(array('id_post' => $id_post));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id_post);
				
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
			$data['id_post'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_post != '')
			{
				$param['id_post'] = $id_post;
			}
			elseif ($title != '')
			{
				$param['title'] = $title;
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
		
		$query = $this->the_model->lists($param);
		$total = $this->the_model->lists_count($param2);
		
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
		$title = filter(trim($this->post('title')));
		$content = trim($this->post('content', FALSE));
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
			$query = $this->the_model->info(array('id_post' => $id_post));
			
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
				
				if ($is_event != '')
				{
					$param['is_event'] = $is_event;
					
					// check event
					$query2 = $this->events_model->info(array('id_post' => $id_post));
					
					if ($query2->num_rows() > 0)
					{
						if ($is_event == 0)
						{
							// delete event
							$query3 = $this->events_model->delete($query2->row()->id_events);
						}
					}
					else
					{
						if ($is_event == 1)
						{
							// create event
							$param2 = array();
							$param2['id_post'] = $id_post;
							$param2['title'] = $query->row()->title;
							$param2['date'] = $query->row()->created_date;
							$param2['status'] = 1;
							$param2['created_date'] = $query->row()->created_date;
							$param2['updated_date'] = date('Y-m-d H:i:s');
							$query4 = $this->events_model->create($param2);
						}
					}
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_post, $param);
					
					if ($update)
					{
						// update archived
						$query5 = $this->post_archived_model->info(array('id_post' => $id_post));
						$id_post_archived = $query5->row()->post->id_post_archived;
						$explode = explode('-', $created_date);
						
						$param3 = array();
						$param3['created_date'] = $created_date;
						$param3['year'] = $explode[0];
						$param3['month'] = $explode[1];
						$query6 = $this->post_archived_model->update($id_post_archived, $param3);
						
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
