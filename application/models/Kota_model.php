<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kota_model extends CI_Model {

    var $table = 'kota';
    var $table_id = 'id_kota';
    
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
        if (isset($param['id_kota']))
        {
            $where += array('id_kota' => $param['id_kota']);
        }
        if (isset($param['kota']))
        {
            $where += array('kota' => $param['kota']);
        }
        
        $this->db->select('id_kota, '.$this->table.'.id_provinsi, kota, price,
						  '.$this->table.'.created_date, '.$this->table.'.updated_date,
						  provinsi, provinsi.created_date AS provinsi_created_date,
						  provinsi.updated_date AS provinsi_updated_date');
        $this->db->from($this->table);
		$this->db->join('provinsi', $this->table.'.id_provinsi = provinsi.id_provinsi', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['q']))
        {
            $where += array('kota LIKE ' => '%'.$param['q'].'%');
        }
        if (isset($param['id_provinsi']))
        {
            $where += array('id_provinsi' => $param['id_provinsi']);
        }
        
        $this->db->select('id_kota, id_provinsi, kota, price, created_date, updated_date');
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
            $where += array('kota LIKE ' => '%'.$param['q'].'%');
        }
        if (isset($param['id_provinsi']))
        {
            $where += array('id_provinsi' => $param['id_provinsi']);
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