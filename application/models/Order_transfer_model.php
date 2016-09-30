<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Order_transfer_model extends CI_Model {

    var $table = 'order_transfer';
	var $table_id = 'id_order_transfer';
    
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
        if (isset($param['id_order_transfer']))
        {
            $where += array('id_order_transfer' => $param['id_order_transfer']);
        }
        
        $this->db->select('id_order_transfer, '.$this->table.'.id_order, total, date, photo,
						  account_name, other_information, resi, '.$this->table.'.status,
						  '.$this->table.'.created_date, '.$this->table.'.updated_date, name');
        $this->db->from($this->table);
		$this->db->join('order', $this->table.'.id_order = order.id_order', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['status']))
        {
            $where += array('status' => $param['status']);
        }
        
        $this->db->select('id_order_transfer, id_order, total, date, photo, account_name,
						  other_information, resi, status, created_date, updated_date');
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
        if (isset($param['status']))
        {
            $where += array('status' => $param['status']);
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
