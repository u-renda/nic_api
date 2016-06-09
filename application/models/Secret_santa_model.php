<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Secret_santa_model extends CI_Model {

    var $table = 'secret_santa';
    var $id = 'id_secret_santa';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    function create($param)
    {
        $this->db->set($this->id, 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		return $query;
    }
    
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $query = $this->db->delete($this->table);
        return $query;
    }
    
    function info($param)
    {
        $where = array();
        if ( ! empty($param['id_secret_santa']))
        {
            $where += array('id_secret_santa' => $param['id_secret_santa']);
        }
        if ( ! empty($param['name']))
        {
            $where += array('name' => $param['name']);
        }
        if (isset($param['status']))
        {
            $where += array('status' => $param['status']);
        }
        
        $this->db->select('id_secret_santa, name, chosen_id, status, created_date, updated_date');
        $this->db->from($this->table);
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
        if ( ! empty($param['not_id_secret_santa']))
        {
            $where += array('id_secret_santa !=' => $param['not_id_secret_santa']);
        }
        if (isset($param['chosen']))
        {
            $where += array('chosen' => $param['chosen']);
        }
        
        $this->db->select('id_secret_santa, name, chosen_id, status, created_date, updated_date');
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
        if ( ! empty($param['not_id_secret_santa']))
        {
            $where += array('id_secret_santa !=' => $param['not_id_secret_santa']);
        }
        if (isset($param['chosen']))
        {
            $where += array('chosen' => $param['chosen']);
        }
        
        $this->db->select($this->id);
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->count_all_results();
        return $query;
    }
    
    function update($id, $param)
    {
        $this->db->where($this->id, $id);
        $query = $this->db->update($this->table, $param);
        return $query;
    }
}
