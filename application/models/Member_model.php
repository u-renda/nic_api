<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_model extends CI_Model {

    var $table = 'member';
    
    public function __construct()
    {
        parent::__construct();
    }
	
	function chart_registered($param)
	{
		$this->db->select('month(approved_date) as month, COUNT(*) as total');
		$this->db->from($this->table);
		$this->db->where('status', $param['status']);
		$this->db->where('year(approved_date)', $param['year']);
		$this->db->group_by('month(approved_date)');
		$query = $this->db->get();
        return $query;
	}
    
    function create($param)
    {
        $this->db->set('id_member', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		$id = $this->db->insert_id();
		return $id;
    }
    
    function delete($id)
    {
        $this->db->where('id_member', $id);
        $query = $this->db->delete($this->table);
        return $query;
    }
    
    function info($param)
    {
        $where = array();
        if (isset($param['id_member']))
        {
            $where += array('id_member' => $param['id_member']);
        }
        if (isset($param['name']))
        {
            $where += array('name' => $param['name']);
        }
        if (isset($param['email']))
        {
            $where += array('email' => $param['email']);
        }
        if (isset($param['username']))
        {
            $where += array('username' => $param['username']);
        }
        if (isset($param['idcard_number']))
        {
            $where += array('idcard_number' => $param['idcard_number']);
        }
        if (isset($param['phone_number']))
        {
            $where += array('phone_number' => $param['phone_number']);
        }
        if (isset($param['member_number']))
        {
            $where += array('member_number' => $param['member_number']);
        }
        if (isset($param['member_card']))
        {
            $where += array('member_card' => $param['member_card']);
        }
        
        $this->db->select('id_member, '.$this->table.'.id_kota, name, email, username, password,
						  idcard_type, idcard_number, idcard_photo, idcard_address,
						  shipment_address, postal_code, gender, phone_number, birth_place,
						  birth_date, marital_status, occupation, religion, shirt_size, photo,
						  '.$this->table.'.status, member_number, member_card, approved_date,
						  '.$this->table.'.created_date, '.$this->table.'.updated_date,
						  kota, price, kota.created_date AS kota_created_date,
						  kota.updated_date AS kota_updated_date, notes');
        $this->db->from($this->table);
		$this->db->join('kota', $this->table.'.id_kota = kota.id_kota', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        $or_where = array();
        if (isset($param['q']))
        {
            $where += array('name LIKE ' => '%'.$param['q'].'%');
            $or_where += array('email LIKE ' => '%'.$param['q'].'%');
            $or_where += array('member_card LIKE ' => '%'.$param['q'].'%');
        }
        if (isset($param['shirt_size']))
        {
            $where += array('shirt_size' => $param['shirt_size']);
        }
        if (isset($param['religion']))
        {
            $where += array('religion' => $param['religion']);
        }
        if (isset($param['marital_status']))
        {
            $where += array('marital_status' => $param['marital_status']);
        }
        if (isset($param['status']))
        {
            $where += array('status' => $param['status']);
        }
        if (isset($param['gender']))
        {
            $where += array('gender' => $param['gender']);
        }
        if (isset($param['idcard_type']))
        {
            $where += array('idcard_type' => $param['idcard_type']);
        }
        
        $this->db->select('id_member, id_kota, name, email, username, idcard_type, idcard_number,
						  idcard_photo, idcard_address, shipment_address, postal_code, gender,
						  birth_place, birth_date, marital_status, occupation, religion, shirt_size,
						  photo, status, member_number, member_card, approved_date, created_date,
						  updated_date, password, phone_number, notes');
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
        if (isset($param['q']))
        {
            $where += array('name LIKE ' => '%'.$param['q'].'%');
            $or_where += array('email LIKE ' => '%'.$param['q'].'%');
            $or_where += array('username LIKE ' => '%'.$param['q'].'%');
            $or_where += array('idcard_number LIKE ' => '%'.$param['q'].'%');
            $or_where += array('member_card LIKE ' => '%'.$param['q'].'%');
        }
        if (isset($param['shirt_size']))
        {
            $where += array('shirt_size' => $param['shirt_size']);
        }
        if (isset($param['religion']))
        {
            $where += array('religion' => $param['religion']);
        }
        if (isset($param['marital_status']))
        {
            $where += array('marital_status' => $param['marital_status']);
        }
        if (isset($param['status']))
        {
            $where += array('status' => $param['status']);
        }
        if (isset($param['gender']))
        {
            $where += array('gender' => $param['gender']);
        }
        if (isset($param['idcard_type']))
        {
            $where += array('idcard_type' => $param['idcard_type']);
        }
        
        $this->db->select('id_member');
        $this->db->from($this->table);
        $this->db->where($where);
        $this->db->or_where($or_where);
        $query = $this->db->count_all_results();
        return $query;
    }
    
    function update($id, $param)
    {
        $this->db->where('id_member', $id);
        $query = $this->db->update($this->table, $param);
        return $query;
    }
}