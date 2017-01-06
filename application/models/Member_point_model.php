<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_point_model extends CI_Model {

    var $table = 'member_point';
	var $table_id = 'id_member_point';
    
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
        if (isset($param['id_member_point']) == TRUE)
        {
            $where += array('id_member_point' => $param['id_member_point']);
        }
        
        $this->db->select('id_member_point, '.$this->table.'.id_member, '.$this->table.'.id_events,
						  '.$this->table.'.status, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, name, title');
        $this->db->from($this->table);
		$this->db->join('member', $this->table.'.id_member = member.id_member', 'left');
		$this->db->join('events', $this->table.'.id_events = events.id_events', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['id_events']) == TRUE)
        {
            $where += array('id_events' => $param['id_events']);
        }
        if (isset($param['id_member']) == TRUE)
        {
            $where += array('id_member' => $param['id_member']);
        }
        if (isset($param['status']) == TRUE)
        {
            $where += array('status' => $param['status']);
        }
        
        $this->db->select('id_member_point, id_member, id_events, status, created_date, updated_date');
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
        if (isset($param['id_events']) == TRUE)
        {
            $where += array('id_events' => $param['id_events']);
        }
        if (isset($param['id_member']) == TRUE)
        {
            $where += array('id_member' => $param['id_member']);
        }
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