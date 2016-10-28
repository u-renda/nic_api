<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Product extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('product_detail_model');
		$this->load->model('product_image_model');
		$this->load->model('product_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$name = filter(trim($this->post('name')));
		$image = filter(trim($this->post('image')));
		$price_public = filter(trim($this->post('price_public')));
		$price_member = filter(trim($this->post('price_member')));
		$description = filter(trim($this->post('description')));
		$quantity = filter(trim($this->post('quantity')));
		$status = filter(trim($this->post('status')));
		$other_photo = $this->post('other_photo');
		$size = filter(trim($this->post('size')));
		$colors = filter(trim($this->post('colors')));
		$material = filter(trim($this->post('material')));
		
		$data = array();
		if ($name == FALSE)
		{
			$data['name'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($image == FALSE)
		{
			$data['image'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($price_public == FALSE)
		{
			$data['price_public'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($price_member == FALSE)
		{
			$data['price_member'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($description == FALSE)
		{
			$data['description'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($quantity == FALSE)
		{
			$data['quantity'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($status == '')
		{
			$data['status'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_product_name($name) == FALSE && $name == TRUE)
		{
			$data['name'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if (in_array($status, $this->config->item('default_product_status')) == FALSE && $status == TRUE)
		{
			$data['status'] = 'wrong value';
			$validation = 'error';
			$code = 400;
		}
		
		if ( ! is_array($other_photo) && $other_photo != '')
		{
			$data['other_photo'] = 'use other_photo[]';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['name'] = $name;
			$param['image'] = $image;
			$param['price_public'] = intval($price_public);
			$param['price_member'] = intval($price_member);
			$param['description'] = $description;
			$param['quantity'] = intval($quantity);
			$param['status'] = intval($status);
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->the_model->create($param);
			
			if ($query != 0 || $query != '')
			{
				if ($other_photo != '')
				{
					foreach ($other_photo as $key => $val)
					{
						// insert image ke product image
						$param2 = array();
						$param2['id_product'] = $query;
						$param2['image'] = $val;
						$param2['status'] = 1;
						$param2['created_date'] = date('Y-m-d H:i:s');
						$param2['updated_date'] = date('Y-m-d H:i:s');
						$query2 = $this->product_image_model->create($param2);
					}
				}
				
				if ($size == TRUE || $colors == TRUE || $material == TRUE)
				{
					// insert detail ke product detail
					$param3 = array();
					$param3['id_product'] = $query;
					$param3['size'] = $size;
					$param3['colors'] = $colors;
					$param3['material'] = $material;
					$param3['created_date'] = date('Y-m-d H:i:s');
					$param3['updated_date'] = date('Y-m-d H:i:s');
					$query3 = $this->product_detail_model->create($param3);
				}
					
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
		
        $id = filter($this->post('id_product'));
        
		$data = array();
        if ($id == FALSE)
		{
			$data['id_product'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_product' => $id));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id);
				
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
				$data['id_product'] = 'not found';
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
		
		$id_product = filter($this->get('id_product'));
		
		$data = array();
		if ($id_product == FALSE)
		{
			$data['id_product'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_product != '')
			{
				$param['id_product'] = $id_product;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_product' => $row->id_product,
					'name' => $row->name,
					'image' => $row->image,
					'price_public' => intval($row->price_public),
					'price_member' => intval($row->price_member),
					'description' => $row->description,
					'quantity' => intval($row->quantity),
					'status' => intval($row->status),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date
				);
				
				$query2 = $this->product_detail_model->info(array('id_product' => $id_product));
				
				if ($query2->num_rows() > 0)
				{
					$row2 = $query2->row();
					$data['detail'] = array(
						'id_product_detail' => $row2->id_product_detail,
						'size' => $row2->size,
						'colors' => $row2->colors,
						'material' => $row2->material
					);
				}
				else
				{
					$data['detail'] = array(
						'id_product_detail' => '-',
						'size' => '-',
						'colors' => '-',
						'material' => '-'
					);
				}
				
				// Get images
				$param2 = array();
				$param2['order'] = 'created_date';
				$param2['sort'] = 'desc';
				$param2['limit'] = 20;
				$param2['offset'] = 0;
				$param2['id_product'] = $id_product;
				$query3 = $this->product_image_model->lists($param2);
				
				$data['other_image'] = array();
				if ($query3->num_rows() > 0)
				{
					foreach ($query3->result() as $row3)
					{
						$temp = array();
						$temp['id_product_image'] = $row3->id_product_image;
						$temp['image'] = $row3->image;
						$data['other_image'][] = $temp;
					}
				}
				
                $validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_preferences'] = 'not found (key)';
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
		
		if (in_array($order, $this->config->item('default_product_order')) && ($order == TRUE))
		{
			$order = $order;
		}
		else
		{
			$order = 'name';
		}
		
		if (in_array($sort, $this->config->item('default_sort')) && ($sort == TRUE))
		{
			$sort = $sort;
		}
		else
		{
			$sort = 'asc';
		}
		
		if (in_array($status, $this->config->item('default_product_status')) && ($status == TRUE))
		{
			$status = $status;
		}
		
		$param = array();
		$param2 = array();
		if ($status != '')
		{
			$param['status'] = intval($status);
			$param2['status'] = intval($status);
		}
		if ($q == TRUE)
		{
			$param['q'] = $q;
			$param2['q'] = $q;
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
					'id_product' => $row->id_product,
					'name' => $row->name,
					'image' => $row->image,
					'price_public' => intval($row->price_public),
					'price_member' => intval($row->price_member),
					'description' => $row->description,
					'quantity' => intval($row->quantity),
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
}
