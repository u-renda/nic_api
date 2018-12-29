<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Forms_detail_model extends CI_Model {

    var $table = 'forms_detail';
	var $table_id = 'id_forms_detail';
    
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
        if (isset($param['id_forms_detail']) == TRUE)
        {
            $where += array('id_forms_detail' => $param['id_forms_detail']);
        }
        
        $this->db->select('id_forms_detail, '.$this->table.'.id_forms, '.$this->table.'.title, '.$this->table.'.description, '.$this->table.'.photo, 
							order_number, '.$this->table.'.created_date, '.$this->table.'.updated_date, forms.title AS forms_title, 
							forms.description AS forms_description, forms.photo AS forms_photo, status');
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
        if (isset($param['order_number']) == TRUE)
        {
            $where += array('order_number' => $param['order_number']);
        }
        
        $this->db->select('id_forms_detail, id_forms, title, description, photo, order_number, created_date, updated_date');
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
        if (isset($param['order_number']) == TRUE)
        {
            $where += array('order_number' => $param['order_number']);
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