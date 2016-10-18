<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    var $table = 'admin';
	var $table_id = 'id_admin';
    
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
        if (isset($param['id_admin']))
        {
            $where += array('id_admin' => $param['id_admin']);
        }
        if (isset($param['username']))
        {
            $where += array('username' => $param['username']);
        }
        if (isset($param['password']))
        {
            $where += array('password' => $param['password']);
        }
        if (isset($param['email']))
        {
            $where += array('email' => $param['email']);
        }
        if (isset($param['name']))
        {
            $where += array('name' => $param['name']);
        }
        if (isset($param['twitter']))
        {
            $where += array('twitter' => $param['twitter']);
        }
        if (isset($param['admin_initial']))
        {
            $where += array('admin_initial' => $param['admin_initial']);
        }
        
        $this->db->select('id_admin, username, email, admin_initial, name, password, photo, admin_role,
                          admin_group, position, twitter, created_date, updated_date');
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['admin_role']))
        {
            $where += array('admin_role' => $param['admin_role']);
        }
        if (isset($param['admin_group']))
        {
            $where += array('admin_group' => $param['admin_group']);
        }
        
        $this->db->select('id_admin, username, email, admin_initial, name, password, photo, admin_role,
                          admin_group, position, twitter, created_date, updated_date');
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
        if (isset($param['admin_role']))
        {
            $where += array('admin_role' => $param['admin_role']);
        }
        if (isset($param['admin_group']))
        {
            $where += array('admin_group' => $param['admin_group']);
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