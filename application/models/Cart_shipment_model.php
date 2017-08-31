<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cart_shipment_model extends CI_Model {

    var $table = 'cart_shipment';
	var $table_id = 'id_cart_shipment';
    
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
        if (isset($param['id_cart_shipment']) == TRUE)
        {
            $where += array('id_cart_shipment' => $param['id_cart_shipment']);
        }
        if (isset($param['unique_code']) == TRUE)
        {
            $where += array('unique_code' => $param['unique_code']);
        }
        
        $this->db->select('id_cart_shipment, '.$this->table.'.id_kota, shipment_address, postal_code,
						  unique_code, total, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, kota, price');
        $this->db->from($this->table);
        $this->db->join('kota', $this->table.'.id_kota = kota.id_kota', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        
        $this->db->select('id_cart_shipment, id_kota, shipment_address, postal_code, unique_code,
						  total, created_date, updated_date');
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