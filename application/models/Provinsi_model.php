<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Provinsi_model extends CI_Model {

    var $table = 'provinsi';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    function create($param)
    {
        $this->db->set('id_provinsi', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		return $query;
    }
    
    function delete($id)
    {
        $this->db->where('id_provinsi', $id);
        $query = $this->db->delete($this->table);
        return $query;
    }
    
    function info($param)
    {
        $where = array();
        if (isset($param['id_provinsi']))
        {
            $where += array('id_provinsi' => $param['id_provinsi']);
        }
        if (isset($param['provinsi']))
        {
            $where += array('provinsi' => $param['provinsi']);
        }
        
        $this->db->select('id_provinsi, provinsi, created_date, updated_date');
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['q']))
        {
            $where += array('provinsi LIKE ' => '%'.$param['q'].'%');
        }
        
        $this->db->select('id_provinsi, provinsi, created_date, updated_date');
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
        if (isset($param['q']))
        {
            $where += array('provinsi LIKE ' => '%'.$param['q'].'%');
        }
        
        $this->db->select('id_provinsi');
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->count_all_results();
        return $query;
    }
    
    function update($id, $param)
    {
        $this->db->where('id_provinsi', $id);
        $query = $this->db->update($this->table, $param);
        return $query;
    }
}