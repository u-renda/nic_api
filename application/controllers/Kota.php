<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Kota extends REST_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->model('kota_model');
    }
	
	function create_post()
	{
		$this->benchmark->mark('code_start');
		$validation = 'ok';
		
		$id_provinsi = filter($this->post('id_provinsi'));
		$kota = filter(trim($this->post('kota')));
		$price = filter(trim($this->post('price')));
		
		$data = array();
		if ($id_provinsi == FALSE)
		{
			$data['id_provinsi'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($kota == FALSE)
		{
			$data['kota'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ( ! isset($price))
		{
			$data['price'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if (check_kota_name($kota, $id_provinsi) == FALSE && $kota == TRUE)
		{
			$data['kota'] = 'already exist';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			$param['id_provinsi'] = $id_provinsi;
			$param['kota'] = $kota;
			$param['price'] = intval($price);
			$param['created_date'] = date('Y-m-d H:i:s');
			$param['updated_date'] = date('Y-m-d H:i:s');
			$query = $this->kota_model->create($param);
			
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
		
        $id_kota = filter($this->post('id_kota'));
        
		$data = array();
        if ($id_kota == FALSE)
		{
			$data['id_kota'] = 'required';
			$validation = "error";
			$code = 400;
		}
        
        if ($validation == "ok")
		{
            $query = $this->kota_model->info(array('id_kota' => $id_kota));
			
			if ($query->num_rows() > 0)
			{
                $delete = $this->kota_model->delete($id_kota);
				
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
				$data['id_kota'] = 'not found';
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
		
		$id_kota = filter($this->get('id_kota'));
		$kota = filter(trim($this->get('kota')));
		
		$data = array();
		if ($id_kota == FALSE && $kota == FALSE)
		{
			$data['id_kota'] = 'required (kota)';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$param = array();
			if ($id_kota != '')
			{
				$param['id_kota'] = $id_kota;
			}
			else
			{
				$param['kota'] = $kota;
			}
			
			$query = $this->kota_model->info($param);
			
			if ($query->num_rows() > 0)
			{
				$row = $query->row();
				
				$data = array(
					'id_kota' => $row->id_kota,
					'id_provinsi' => $row->id_provinsi,
					'kota' => $row->kota,
					'price' => intval($row->price),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'provinsi' => array(
						'provinsi' => $row->provinsi,
						'created_date' => $row->provinsi_created_date,
						'updated_date' => $row->provinsi_updated_date
					)
				);
				
				$validation = 'ok';
				$code = 200;
			}
			else
			{
				$data['id_kota'] = 'not found (kota)';
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
		$id_provinsi = filter($this->get('id_provinsi'));
		
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
		
		if (in_array($order, $this->config->item('default_kota_order')) && ($order == TRUE))
		{
			$order = $order;
		}
		else
		{
			$order = 'kota';
		}
		
		if (in_array($sort, $this->config->item('default_sort')) && ($sort == TRUE))
		{
			$sort = $sort;
		}
		else
		{
			$sort = 'asc';
		}
		
		$param = array();
		$param2 = array();
		if ($q == TRUE)
		{
			$param['q'] = $q;
			$param2['q'] = $q;
		}
        if ($id_provinsi != '')
        {
            $param['id_provinsi'] = $id_provinsi;
            $param2['id_provinsi'] = $id_provinsi;
        }
		
		$param['limit'] = $limit;
		$param['offset'] = $offset;
		$param['order'] = $order;
		$param['sort'] = $sort;
		
		$query = $this->kota_model->lists($param);
		$total = $this->kota_model->lists_count($param2);
		
		$data = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$data[] = array(
					'id_kota' => $row->id_kota,
					'kota' => $row->kota,
					'price' => intval($row->price),
					'created_date' => $row->created_date,
					'updated_date' => $row->updated_date,
					'provinsi' => array(
						'id_provinsi' => $row->id_provinsi,
						'name' => $row->provinsi
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
		
		$id_kota = filter($this->post('id_kota'));
		$id_provinsi = filter($this->post('id_provinsi'));
		$kota = filter(trim($this->post('kota')));
		$price = filter(trim($this->post('price')));
		
		$data = array();
		if ($id_kota == FALSE)
		{
			$data['id_kota'] = 'required';
			$validation = 'error';
			$code = 400;
		}
		
		if ($validation == 'ok')
		{
			$query = $this->kota_model->info(array('id_kota' => $id_kota));
			
			if ($query->num_rows() > 0)
			{
				$param = array();
				if ($id_provinsi == TRUE)
				{
					$param['id_provinsi'] = $id_provinsi;
				}
				
				if ($kota == TRUE)
				{
					$param['kota'] = $kota;
				}
				
				if (isset($price))
				{
					$param['price'] = $price;
				}
				
				if ($param == TRUE)
				{
					$param['updated_date'] = date('Y-m-d H:i:s');
					$update = $this->kota_model->update($id_kota, $param);
					
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
				$data['id_kota'] = 'not found';
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
