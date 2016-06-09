<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Faq extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('faq_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$category = filter(trim($this->post('category')));
		$question = filter(trim($this->post('question')));
		$answer = filter(trim($this->post('answer')));
		
		$data = array();
		if ($category == FALSE)
		{
			$data['category'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($question == FALSE)
		{
			$data['question'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($answer == FALSE)
		{
			$data['answer'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($category, $this->config->item('default_faq_category')) == FALSE && $category == TRUE)
		{
			$data['category'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['category'] = intval($category);
			$param['question'] = $question;
			$param['answer'] = $answer;
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->faq_model->create($param);
			
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
		
        $id_faq = filter($this->post('id_faq'));
        
		$data = array();
        if ($id_faq == FALSE)
		{
			$data['id_faq'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->faq_model->info(array('id_faq' => $id_faq));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->faq_model->delete($id_faq);
				
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
				$data['id_faq'] = 'not found';
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
		
		$id_faq = filter($this->get('id_faq'));
		
		$data = array();
		if ($id_faq == FALSE)
		{
			$data['id_faq'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_faq != '')
			{
				$param['id_faq'] = $id_faq;
			}
			
			$query = $this->faq_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_faq' => $row->id_faq,
					'category' => intval($row->category),
					'question' => $row->question,
					'answer' => $row->answer,
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_faq'] = 'not found';
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
		$category = filter(trim($this->get('category')));
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
		
		if (in_array($order, $this->config->item('default_faq_order')) && ($order == TRUE))
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
		
		if (in_array($category, $this->config->item('default_faq_category')) && ($category == TRUE))
		{
			$category = $category;
		}
		
		$param = array();
		$param2 = array();
		if ($q == TRUE)
		{
			$param['q'] = $q;
			$param2['q'] = $q;
		}
		if ($category == TRUE)
		{
			$param['category'] = intval($category);
			$param2['category'] = intval($category);
		}
		
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;
		
		$query = $this->faq_model->lists($param);
		$total = $this->faq_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_faq' => $row->id_faq,
					'category' => intval($row->category),
					'question' => $row->question,
					'answer' => $row->answer,
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
		
		$id_faq = filter($this->post('id_faq'));
		$category = filter(trim($this->post('category')));
		$question = filter(trim($this->post('question')));
		$answer = filter(trim($this->post('answer')));
		
		$data = array();
		if ($id_faq == FALSE)
		{
			$data['id_faq'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($category, $this->config->item('default_faq_category')) == FALSE && $category == TRUE)
		{
			$data['category'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->faq_model->info(array('id_faq' => $id_faq));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($category == TRUE)
				{
					$param['category'] = intval($category);
				}
				
				if ($question == TRUE)
				{
					$param['question'] = $question;
				}
				
				if ($answer == TRUE)
				{
					$param['answer'] = $answer;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->faq_model->update($id_faq, $param);
					
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
				$data['id_faq'] = 'not found';
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
