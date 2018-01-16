<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model {

    var $table = 'order';
	var $table_id = 'id_order';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    function create($param)
    {
        $this->db->set($this->table_id, 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		$id = $this->db->insert_id();
		return $id;
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
        if (isset($param['id_order']) == TRUE)
        {
            $where += array('id_order' => $param['id_order']);
        }
        
        $this->db->select('id_order, '.$this->table.'.id_cart_total, '.$this->table.'.id_member,
						  '.$this->table.'.name, phone, '.$this->table.'.email,
						  '.$this->table.'.status, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, unique_code, total,
						  member.name AS member_name');
        $this->db->from($this->table);
		$this->db->join('member', $this->table.'.id_member = member.id_member', 'left');
		$this->db->join('cart_total', $this->table.'.id_cart_total = cart_total.id_cart_total', 'left');
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
        
        $this->db->select('id_order, id_cart_total, id_member, name, phone, email, status,
						  created_date, updated_date');
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
