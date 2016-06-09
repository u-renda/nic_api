<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_transfer_model extends CI_Model {

    var $table = 'member_transfer';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    function create($param)
    {
        $this->db->set('id_member_transfer', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		return $query;
    }
    
    function delete($id)
    {
        $this->db->where('id_member_transfer', $id);
        $query = $this->db->delete($this->table);
        return $query;
    }
    
    function info($param)
    {
        $where = array();
        if (isset($param['id_member_transfer']))
        {
            $where += array('id_member_transfer' => $param['id_member_transfer']);
        }
        if (isset($param['id_member']))
        {
            $where += array($this->table.'.id_member' => $param['id_member']);
        }
        if (isset($param['type']))
        {
            $where += array($this->table.'.type' => $param['type']);
        }
        
        $this->db->select('id_member_transfer, '.$this->table.'.id_member, total, date,
						  '.$this->table.'.photo, account_name, other_information, type,
						  '.$this->table.'.created_date, '.$this->table.'.updated_date, name,
						  email, username, idcard_type, idcard_number, idcard_photo,
						  idcard_address, shipment_address, postal_code, gender, phone_number,
						  birth_place, birth_date, marital_status, occupation, religion,
						  shirt_size, member.photo as member_photo,
						  member.status AS member_status, member_number, member_card,
						  approved_date, member.created_date AS member_created_date,
						  member.updated_date AS member_updated_date, '.$this->table.'.status');
        $this->db->from($this->table);
		$this->db->join('member', $this->table.'.id_member = member.id_member', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['q']))
        {
            $where += array('total LIKE ' => '%'.$param['q'].'%');
        }
        if (isset($param['type']))
        {
            $where += array('type' => $param['type']);
        }
        
        $this->db->select('id_member_transfer, id_member, total, date, photo, account_name,
						  other_information, type, created_date, updated_date');
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
            $where += array('total LIKE ' => '%'.$param['q'].'%');
        }
        if (isset($param['type']))
        {
            $where += array('type' => $param['type']);
        }
        
        $this->db->select('id_member_transfer');
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->count_all_results();
        return $query;
    }
    
    function update($id, $param)
    {
        $this->db->where('id_member_transfer', $id);
        $query = $this->db->update($this->table, $param);
        return $query;
    }
}
