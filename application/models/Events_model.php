<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Events_model extends CI_Model {

    var $table = 'events';
	var $table_id = 'id_events';
    
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
        if (isset($param['id_events']))
        {
            $where += array('id_events' => $param['id_events']);
        }
        if (isset($param['id_post']))
        {
            $where += array($this->table.'.id_post' => $param['id_post']);
        }
        if (isset($param['title']))
        {
            $where += array($this->table.'.title' => $param['title']);
        }
        if (isset($param['date']))
        {
            $where += array('date' => $param['date']);
        }
        
        $this->db->select('id_events, '.$this->table.'.id_post, date, '.$this->table.'.title,
						  '.$this->table.'.status, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, slug, content, media, media_type, type,
						  post.status as post_status, is_event, post.created_date AS post_created_date,
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
        if (isset($param['q']))
        {
            $where += array('title LIKE ' => '%'.$param['q'].'%');
        }
        
        $this->db->select('id_events, id_post, date, title, status, created_date, updated_date');
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
            $where += array('title LIKE ' => '%'.$param['q'].'%');
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