<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Forms_answer_model extends CI_Model {

    var $table = 'forms_answer';
	var $table_id = 'id_forms_answer';
    
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
        if (isset($param['id_forms_answer']) == TRUE)
        {
            $where += array('id_forms_answer' => $param['id_forms_answer']);
        }
        
        $this->db->select('id_forms_answer, '.$this->table.'.id_forms_question, '.$this->table.'.id_forms_user, answer, '.$this->table.'.created_date, 
							'.$this->table.'.updated_date, id_forms, question, description, is_required, answer_type, is_member, id_card, name, email, phone');
        $this->db->from($this->table);
		$this->db->join('forms_question', $this->table.'.id_forms_question = forms_question.id_forms_question', 'left');
		$this->db->join('forms_user', $this->table.'.id_forms_user = forms_user.id_forms_user', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['id_forms_question']) == TRUE)
        {
            $where += array('id_forms_question' => $param['id_forms_question']);
        }
        if (isset($param['id_forms_user']) == TRUE)
        {
            $where += array('id_forms_user' => $param['id_forms_user']);
        }
        
        $this->db->select('id_forms_answer, id_forms_question, id_forms_user, answer, created_date, updated_date');
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
        if (isset($param['id_forms_question']) == TRUE)
        {
            $where += array('id_forms_question' => $param['id_forms_question']);
        }
        if (isset($param['id_forms_user']) == TRUE)
        {
            $where += array('id_forms_user' => $param['id_forms_user']);
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