<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Faq_model extends CI_Model {

    var $table = 'faq';
    var $table_id = 'id_faq';
    
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
        if (isset($param['id_faq']) == TRUE)
        {
            $where += array('id_faq' => $param['id_faq']);
        }
        
        $this->db->select('id_faq, category, question, answer, created_date, updated_date');
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        $or_where = array();
        if (isset($param['q']) == TRUE)
        {
            $where += array('question LIKE ' => '%'.$param['q'].'%');
            $or_where += array('answer LIKE ' => '%'.$param['q'].'%');
        }
        if (isset($param['category']) == TRUE)
        {
            $where += array('category' => $param['category']);
        }
        
        $this->db->select('id_faq, category, question, answer, created_date, updated_date');
        $this->db->from($this->table);
        $this->db->where($where);
        $this->db->or_where($or_where);
        $this->db->order_by($param['order'], $param['sort']);
        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
        return $query;
    }
    
    function lists_count($param)
    {
        $where = array();
        $or_where = array();
        if (isset($param['q']) == TRUE)
        {
            $where += array('question LIKE ' => '%'.$param['q'].'%');
            $where += array('answer LIKE ' => '%'.$param['q'].'%');
        }
        if (isset($param['category']) == TRUE)
        {
            $where += array('category' => $param['category']);
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