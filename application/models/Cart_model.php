<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cart_model extends CI_Model {

    var $table = 'cart';
	var $table_id = 'id_cart';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    function create($param)
    {
        $this->db->set($this->table_id, 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		return $query;
    }
    
    function delete($id)
    {
        $this->db->where($this->table_id, $id);
        $query = $this->db->delete($this->table);
        return $query;
    }
    
    function info($param)
    {
        $where = array();
        if (isset($param['id_cart']) == TRUE)
        {
            $where += array('id_cart' => $param['id_cart']);
        }
        
        $this->db->select('id_cart, '.$this->table.'.id_product, '.$this->table.'.quantity,
						  unique_code, total, '.$this->table.'.status, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, name');
        $this->db->from($this->table);
        $this->db->join('product', $this->table.'.id_product = product.id_product', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['status']) == TRUE)
        {
            $where += array('status' => $param['status']);
        }
        if (isset($param['id_member']) == TRUE)
        {
            $where += array('id_member' => $param['id_member']);
        }
        
        $this->db->select('id_cart, id_product, quantity, unique_code, total, status, created_date,
						  updated_date, id_member');
        $this->db->from($this->table);
        $this->db->where($where);
        $this->db->order_by($param['order'], $param['sort']);
        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        return $query;
    }
    
    function lists_count($param)
    {
        $where = array();
        if (isset($param['status']) == TRUE)
        {
            $where += array('status' => $param['status']);
        }
        if (isset($param['id_member']) == TRUE)
        {
            $where += array('id_member' => $param['id_member']);
        }
        
        $this->db->select($this->table_id);
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->count_all_results();
        return $query;
    }
    
    function update($id, $param)
    {
        $this->db->where($this->table_id, $id);
        $query = $this->db->update($this->table, $param);
        return $query;
    }
}