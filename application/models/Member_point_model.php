<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Member_point_model extends CI_Model {

    var $table = 'member_point';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    function create($param)
    {
        $this->db->set('id_member_point', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert($this->table, $param);
		return $query;
    }
    
    function delete($id)
    {
        $this->db->where('id_member_point', $id);
        $query = $this->db->delete($this->table);
        return $query;
    }
    
    function info($param)
    {
        $where = array();
        if (isset($param['id_member_point']))
        {
            $where += array('id_member_point' => $param['id_member_point']);
        }
        if (isset($param['id_member']))
        {
            $where += array($this->table.'.id_member' => $param['id_member']);
        }
        if (isset($param['id_events']))
        {
            $where += array($this->table.'.id_events' => $param['id_events']);
        }
        
        $this->db->select('id_member_point, '.$this->table.'.id_member,
						  '.$this->table.'.id_events, poin, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, name, email, username, idcard_type,
						  idcard_number, idcard_photo, idcard_address, shipment_address,
						  postal_code, gender, phone_number, birth_place, birth_date,
						  marital_status, occupation, religion, shirt_size, photo, status,
						  member_number, member_card, approved_date,
						  member.created_date AS member_created_date,
						  member.updated_date AS member_updated_date, title, date,
						  events.created_date AS events_created_date,
						  events.updated_date AS events_updated_date');
        $this->db->from($this->table);
		$this->db->join('member', $this->table.'.id_member = member.id_member', 'left');
		$this->db->join('events', $this->table.'.id_events = events.id_events', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query;
    }
    
    function lists($param)
    {
        $where = array();
        if (isset($param['id_events']))
        {
            $where += array('id_events' => $param['id_events']);
        }
        if (isset($param['id_member']))
        {
            $where += array('id_member' => $param['id_member']);
        }
        if (isset($param['status']))
        {
            $where += array('status' => $param['status']);
        }
        
        $this->db->select('id_member_point, id_member, '.$this->table.'.id_events,
						  '.$this->table.'.status, '.$this->table.'.created_date,
						  '.$this->table.'.updated_date, title, date');
        $this->db->from($this->table);
		$this->db->join('events', $this->table.'.id_events = events.id_events', 'left');
        $this->db->where($where);
        $this->db->order_by($param['order'], $param['sort']);
        $this->db->limit($param['limit'], $param['offset']);
        $query = $this->db->get();
		return $query;
    }
    
    function lists_count($param)
    {
        $where = array();
        if (isset($param['id_events']))
        {
            $where += array('id_events' => $param['id_events']);
        }
        if (isset($param['id_member']))
        {
            $where += array('id_member' => $param['id_member']);
        }
        if (isset($param['status']))
        {
            $where += array('status' => $param['status']);
        }
        
        $this->db->select('id_member_point');
        $this->db->from($this->table);
        $this->db->where($where);
        $query = $this->db->count_all_results();
        return $query;
    }
    
    function update($id, $param)
    {
        $this->db->where('id_member_poin', $id);
        $query = $this->db->update($this->table, $param);
        return $query;
    }
}