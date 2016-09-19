<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Preferences_model extends CI_Model {

    var $table = 'preferences';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    function create($param)
    {
        $this->db->set('id_preferences', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		return $query;
    }
    
    function delete($id)
    {
        $this->db->where('id_preferences', $id);
        $query = $this->db->delete($this->table);
        return $query;
    }
    
    function info($param)
    {
        $where = array();
        if (isset($param['id_preferences']))
        {
            $where += array('id_preferences' => $param['id_preferences']);
        }
        if (isset($param['key']))
        {
            $where += array('key' => $param['key']);
        }
        
        $this->db->select('id_preferences, key, value, description, created_date, updated_date');
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['type']))
        {
            $where += array('type' => $param['type']);
        }

        $this->db->select('id_preferences, key, value, description, created_date, updated_date');
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
        if (isset($param['type']))
        {
            $where += array('type' => $param['type']);
        }

        $this->db->select('id_preferences');
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->count_all_results();
        return $query;
    }
    
    function update($id, $param)
    {
        $this->db->where('id_preferences', $id);
        $query = $this->db->update($this->table, $param);
        return $query;
    }
}