<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Post_model extends CI_Model {

    var $table = 'post';
	var $table_id = 'id_post';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    function create($param)
    {
        $this->db->set($this->table_id, 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		$id_post = $this->db->insert_id();
		return $id_post;
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
        if (isset($param['id_post']) == TRUE)
        {
            $where += array('id_post' => $param['id_post']);
        }
        if (isset($param['title']) == TRUE)
        {
            $where += array('title' => $param['title']);
        }
        if (isset($param['slug']) == TRUE)
        {
            $where += array('slug' => $param['slug']);
        }
        
        $this->db->select('id_post, title, slug, content, media, media_type, type, status, is_event,
						  created_date, updated_date');
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
            $where += array('title LIKE ' => '%'.$param['q'].'%');
        }
        if (isset($param['type']) == TRUE)
        {
            $where += array('type' => $param['type']);
        }
        if (isset($param['status']) == TRUE)
        {
            $where += array('status' => $param['status']);
        }
        if (isset($param['media_type']) == TRUE)
        {
            $where += array('media_type' => $param['media_type']);
        }
        if (isset($param['media_not']) == TRUE)
        {
            $where += array('media != ' => '');
        }
        if (isset($param['is_event']) == TRUE)
        {
            $where += array('is_event' => $param['is_event']);
        }
        
        $this->db->select('id_post, title, slug, content, media, media_type, type, status, is_event,
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
        if (isset($param['q']) == TRUE)
        {
            $where += array('title LIKE ' => '%'.$param['q'].'%');
        }
        if (isset($param['type']) == TRUE)
        {
            $where += array('type' => $param['type']);
        }
        if (isset($param['status']) == TRUE)
        {
            $where += array('status' => $param['status']);
        }
        if (isset($param['media_type']) == TRUE)
        {
            $where += array('media_type' => $param['media_type']);
        }
        if (isset($param['media_not']) == TRUE)
        {
            $where += array('media != ' => '');
        }
        if (isset($param['is_event']) == TRUE)
        {
            $where += array('is_event' => $param['is_event']);
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