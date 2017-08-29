<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Image_album_model extends CI_Model {

    var $table = 'image_album';
    var $table_id = 'id_image_album';
    
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
        if (isset($param['id_image_album']) == TRUE)
        {
            $where += array('id_image_album' => $param['id_image_album']);
        }
        if (isset($param['slug']) == TRUE)
        {
            $where += array('slug' => $param['slug']);
        }
        
        $this->db->select('id_image_album, name, slug, date, created_date, updated_date');
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['q']) == TRUE)
        {
            $where += array('name LIKE ' => '%'.$param['q'].'%');
        }
        
        $this->db->select('id_image_album, name, slug, date, created_date, updated_date');
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
            $where += array('name LIKE ' => '%'.$param['q'].'%');
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