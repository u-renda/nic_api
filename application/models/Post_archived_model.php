<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Post_archived_model extends CI_Model {

    var $table = 'post_archived';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    function create($param)
    {
        $this->db->set('id_post_archived', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		return $query;
    }
    
    function delete($id)
    {
        $this->db->where('id_post_archived', $id);
        $query = $this->db->delete($this->table);
        return $query;
    }
    
    function info($param)
    {
        $where = array();
        if (isset($param['id_post_archived']))
        {
            $where += array('id_post_archived' => $param['id_post_archived']);
        }
        
        $this->db->select('id_post_archived, '.$this->table.'.id_post, year, month,
						  '.$this->table.'.created_date, '.$this->table.'.updated_date,
						  title, content, slug, content, media, media_type, type, status,
						  is_event, post.created_date AS post_created_date,
						  post.updated_date AS post_updated_date');
        $this->db->from($this->table);
		$this->db->join('post', $this->table.'.id_post = post.id_post', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
		$where = array();
        if (isset($param['type']))
        {
            $where += array('post.type' => $param['type']);
        }
        if (isset($param['status']))
        {
            $where += array('post.status' => $param['status']);
        }
		
        $this->db->select('id_post_archived, '.$this->table.'.id_post, year, month,
						  '.$this->table.'.created_date, '.$this->table.'.updated_date, title, slug,
						  type, status');
        $this->db->from($this->table);
		$this->db->join('post', $this->table.'.id_post = post.id_post', 'left');
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
            $where += array('post.type' => $param['type']);
        }
        if (isset($param['status']))
        {
            $where += array('post.status' => $param['status']);
        }
		
        $this->db->select('id_post_archived');
        $this->db->from($this->table);
		$this->db->join('post', $this->table.'.id_post = post.id_post', 'left');
		$this->db->where($where);
        $query = $this->db->count_all_results();
		return $query;
    }
    
    function update($id, $param)
    {
        $this->db->where('id_post_archived', $id);
        $query = $this->db->update($this->table, $param);
        return $query;
    }
}