<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Forms_question_model extends CI_Model {

    var $table = 'forms_question';
	var $table_id = 'id_forms_question';
    
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
        if (isset($param['id_forms_question']) == TRUE)
        {
            $where += array('id_forms_question' => $param['id_forms_question']);
        }
        
        $this->db->select('id_forms_question, '.$this->table.'.id_forms, question, '.$this->table.'.description, is_required, answer_type, 
							'.$this->table.'.created_date, '.$this->table.'.updated_date, title, forms.description AS forms_description, photo, status');
        $this->db->from($this->table);
		$this->db->join('forms', $this->table.'.id_forms = forms.id_forms', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['id_forms']) == TRUE)
        {
            $where += array('id_forms' => $param['id_forms']);
        }
        if (isset($param['is_required']) == TRUE)
        {
            $where += array('is_required' => $param['is_required']);
        }
        if (isset($param['answer_type']) == TRUE)
        {
            $where += array('answer_type' => $param['answer_type']);
        }
        
        $this->db->select('id_forms_question, id_forms, question, description, is_required, answer_type, created_date, updated_date');
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
        if (isset($param['id_forms']) == TRUE)
        {
            $where += array('id_forms' => $param['id_forms']);
        }
        if (isset($param['is_required']) == TRUE)
        {
            $where += array('is_required' => $param['is_required']);
        }
        if (isset($param['answer_type']) == TRUE)
        {
            $where += array('answer_type' => $param['answer_type']);
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