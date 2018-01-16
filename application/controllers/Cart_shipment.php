<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Cart_shipment extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('cart_shipment_model', 'the_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_kota = filter($this->post('id_kota'));
		$shipment_address = filter(trim($this->post('shipment_address')));
		$postal_code = filter(trim($this->post('postal_code')));
		$unique_code = filter(trim($this->post('unique_code')));
		$total = filter(trim($this->post('total')));
		
		$data = array();
		if ($id_kota == FALSE)
		{
			$data['id_kota'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($total == FALSE)
		{
			$data['total'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($unique_code == FALSE)
		{
			$data['unique_code'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['id_kota'] = $id_kota;
			$param['shipment_address'] = $shipment_address;
			$param['postal_code'] = $postal_code;
			$param['unique_code'] = $unique_code;
			$param['total'] = $total;
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
		
        $id_cart_shipment = filter($this->post('id_cart_shipment'));
        
		$data = array();
        if ($id_cart_shipment == FALSE)
		{
			$data['id_cart_shipment'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->the_model->info(array('id_cart_shipment' => $id_cart_shipment));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->the_model->delete($id_cart_shipment);
				
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
				$data['id_cart_shipment'] = 'not found';
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
		
		$id_cart_shipment = filter($this->get('id_cart_shipment'));
		$unique_code = filter($this->get('unique_code'));
		
		$data = array();
		if ($id_cart_shipment == FALSE && $unique_code == FALSE)
		{
			$data['id_cart_shipment'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_cart_shipment != '')
			{
				$param['id_cart_shipment'] = $id_cart_shipment;
			}
			else
			{
				$param['unique_code'] = $unique_code;
			}
			
			$query = $this->the_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_cart_shipment' => $row->id_cart_shipment,
					'shipment_address' => $row->shipment_address,
					'postal_code' => intval($row->postal_code),
					'unique_code' => $row->unique_code,
					'total' => intval($row->total),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'kota' => array(
						'id_kota' => $row->id_kota,
						'kota' => $row->kota,
						'price' => intval($row->price)
					),
					'provinsi' => array(
						'id_provinsi' => $row->id_provinsi,
						'provinsi' => $row->provinsi
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_cart_shipment'] = 'Not Found';
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
		
		if (in_array($order, $this->config->item('default_cart_shipment_order')) && ($order == TRUE))
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
					'id_cart_shipment' => $row->id_cart_shipment,
					'id_kota' => $row->id_kota,
					'shipment_address' => $row->shipment_address,
					'postal_code' => intval($row->postal_code),
					'unique_code' => $row->unique_code,
					'total' => intval($row->total),
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
		
		$id_cart_shipment = filter($this->post('id_cart_shipment'));
		$id_kota = filter($this->post('id_kota'));
		$shipment_address = filter(trim($this->post('shipment_address')));
		$postal_code = filter(trim($this->post('postal_code')));
		$unique_code = filter(trim($this->post('unique_code')));
		$total = filter(trim($this->post('total')));
		
		$data = array();
		if ($id_cart_shipment == FALSE)
		{
			$data['id_cart_shipment'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->the_model->info(array('id_cart_shipment' => $id_cart_shipment));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_kota == TRUE)
				{
					$param['id_kota'] = $id_kota;
				}
				
				if ($shipment_address == TRUE)
				{
					$param['shipment_address'] = $shipment_address;
				}
				
				if ($postal_code == TRUE)
				{
					$param['postal_code'] = $postal_code;
				}
				
				if ($unique_code == TRUE)
				{
					$param['unique_code'] = $unique_code;
				}
				
				if ($total == TRUE)
				{
					$param['total'] = $total;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->the_model->update($id_cart_shipment, $param);
					
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
				$data['id_cart_shipment'] = 'not found';
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
