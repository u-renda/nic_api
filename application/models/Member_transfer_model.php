<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_transfer_model extends CI_Model {

    var $table = 'member_transfer';
	var $table_id = 'id_member_transfer';
    
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
        if (isset($param['id_member_transfer']) == TRUE)
        {
            $where += array('id_member_transfer' => $param['id_member_transfer']);
        }
        if (isset($param['total']) == TRUE)
        {
            $where += array('total' => $param['total']);
        }
        
        $this->db->select('id_member_transfer, '.$this->table.'.id_member, total, date,
						  '.$this->table.'.photo, account_name, other_information, type, resi,
						  '.$this->table.'.status, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, '.$this->table.'.name,
						  member.name AS member_name');
        $this->db->from($this->table);
		$this->db->join('member', $this->table.'.id_member = member.id_member', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['q']) == TRUE)
        {
            $where += array('total LIKE ' => '%'.$param['q'].'%');
        }
        if (isset($param['type']) == TRUE)
        {
            $where += array('type' => $param['type']);
        }
        if (isset($param['id_member']) == TRUE)
        {
            $where += array($this->table.'.id_member' => $param['id_member']);
        }
        
        $this->db->select('id_member_transfer, id_member, total, date, photo, account_name,
						  other_information, type, created_date, updated_date, resi, status, name');
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
        if (isset($param['q']) == TRUE)
        {
            $where += array('total LIKE ' => '%'.$param['q'].'%');
        }
        if (isset($param['type']) == TRUE)
        {
            $where += array('type' => $param['type']);
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
