<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reindex_model extends CI_Model {

	var $old;
	
    public function __construct()
    {
        parent::__construct();
		$this->old = $this->load->database('old', TRUE);
    }
	
	function admin($param)
	{
		$this->db->set('id_admin', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert('admin', $param);
		return $query;
	}
	
	function events($param)
	{
		$this->db->set('id_events', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert('events', $param);
		return $query;
	}
	
	function faq($param)
	{
		$this->db->set('id_faq', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert('faq', $param);
		return $query;
	}
	
	function kota($param)
	{
		$this->db->set('id_kota', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert('kota', $param);
		return $query;
	}
	
	function kota_info($param)
	{
		$this->db->select('*');
        $this->db->from('kota');
		$this->db->where('kota', $param['kota']);
        $query = $this->db->get();
        return $query;
	}
	
	function member($param)
	{
		$this->db->set('id_member', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert('member', $param);
		return $query;
	}
	
	function member_info($param)
	{
		$this->db->select('*');
        $this->db->from('member');
		$this->db->where('name', $param['name']);
        $query = $this->db->get();
        return $query;
	}
	
	function member_transfer($param)
	{
		$this->db->set('id_member_transfer', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert('member_transfer', $param);
		return $query;
	}
	
	function nav_menu($param)
	{
		$this->db->set('id_nav_menu', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert('nav_menu', $param);
		return $query;
	}
	
	function nav_menu_info($param)
	{
		$this->db->select('*');
        $this->db->from('nav_menu');
		$this->db->where('title', $param['title']);
        $query = $this->db->get();
        return $query;
	}
	
	function nav_user($param)
	{
		$this->db->set('id_nav_user', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert('nav_user', $param);
		return $query;
	}
    
    function old_admin_lists($param)
    {
        $this->old->select('*');
        $this->old->from('Nic_admin');
        $this->old->where('cron', '');
		$this->old->limit($param['limit'], $param['offset']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_admin_update($id, $param)
    {
        $this->old->where('admin_id', $id);
        $query = $this->old->update('Nic_admin', $param);
        return $query;
    }
    
    function old_events_lists($param)
    {
        $this->old->select('*');
        $this->old->from('Nic_events');
        $this->old->where('cron', '');
		$this->old->limit($param['limit'], $param['offset']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_events_update($id, $param)
    {
        $this->old->where('event_id', $id);
        $query = $this->old->update('Nic_events', $param);
        return $query;
    }
    
    function old_faq_lists($param)
    {
        $this->old->select('*');
        $this->old->from('Nic_faq');
        $this->old->where('cron', '');
		$this->old->limit($param['limit'], $param['offset']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_faq_update($id, $param)
    {
        $this->old->where('faq_id', $id);
        $query = $this->old->update('Nic_faq', $param);
        return $query;
    }
    
    function old_kota_lists($param)
    {
        $this->old->select('*');
        $this->old->from('Delivery_cost');
        $this->old->where('cron', '');
		$this->old->limit($param['limit'], $param['offset']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_kota_update($id, $param)
    {
        $this->old->where('city_id', $id);
        $query = $this->old->update('Delivery_cost', $param);
        return $query;
    }
    
    function old_member_info($param)
    {
        $this->old->select('*');
        $this->old->from('Nic_member');
		$this->old->where('acct_id', $param['acct_id']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_member_lists($param)
    {
        $this->old->select('*');
        $this->old->from('Nic_member');
        $this->old->where('cron', '');
		$this->old->limit($param['limit'], $param['offset']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_member_transfer_lists($param)
    {
        $this->old->select('*');
        $this->old->from('Nic_transfer');
        $this->old->where('cron', '');
		$this->old->limit($param['limit'], $param['offset']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_member_transfer_update($id, $param)
    {
        $this->old->where('trf_id', $id);
        $query = $this->old->update('Nic_transfer', $param);
        return $query;
    }
    
    function old_member_update($id, $param)
    {
        $this->old->where('acct_id', $id);
        $query = $this->old->update('Nic_member', $param);
        return $query;
    }
    
    function old_nav_menu_info($param)
    {
        $this->old->select('*');
        $this->old->from('nav_menu_old');
		$this->old->where('nav_menu_id', $param['nav_menu_id']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_nav_menu_lists($param)
    {
        $this->old->select('*');
        $this->old->from('nav_menu_old');
        $this->old->where('cron', '');
		$this->old->limit($param['limit'], $param['offset']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_nav_menu_update($id, $param)
    {
        $this->old->where('id_nav_menu', $id);
        $query = $this->old->update('nav_menu_old', $param);
        return $query;
    }
    
    function old_nav_user_lists($param)
    {
        $this->old->select('*');
        $this->old->from('nav_user_old');
        $this->old->where('cron', '');
		$this->old->limit($param['limit'], $param['offset']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_nav_user_update($id, $param)
    {
        $this->old->where('nav_user_id', $id);
        $query = $this->old->update('nav_user_old', $param);
        return $query;
    }
    
    function old_post_archived_lists($param)
    {
        $this->old->select('*');
        $this->old->from('Nic_archive');
        $this->old->where('cron', '');
		$this->old->limit($param['limit'], $param['offset']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_post_archived_update($id, $param)
    {
        $this->old->where('post_id', $id);
        $query = $this->old->update('Nic_archive', $param);
        return $query;
    }
    
    function old_post_info($param)
    {
        $this->old->select('*');
        $this->old->from('Nic_post');
		$this->old->where('post_id', $param['post_id']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_post_lists($param)
    {
        $this->old->select('*');
        $this->old->from('Nic_post');
        $this->old->where('cron', '');
		$this->old->limit($param['limit'], $param['offset']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_post_update($id, $param)
    {
        $this->old->where('post_id', $id);
        $query = $this->old->update('Nic_post', $param);
        return $query;
    }
    
    function old_provinsi_info($param)
    {
        $this->old->select('*');
        $this->old->from('Ind_provinces');
		$this->old->where('prov_id', $param['prov_id']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_preferences_lists($param)
    {
        $this->old->select('*');
        $this->old->from('Nic_preferences');
        $this->old->where('cron', '');
		$this->old->limit($param['limit'], $param['offset']);
        $query = $this->old->get();
        return $query;
    }

    function old_preferences_update($id, $param)
    {
        $this->old->where('pref_id', $id);
        $query = $this->old->update('Nic_preferences', $param);
        return $query;
    }

    function old_provinsi_lists($param)
    {
        $this->old->select('*');
        $this->old->from('Ind_provinces');
        $this->old->where('cron', '');
		$this->old->limit($param['limit'], $param['offset']);
        $query = $this->old->get();
        return $query;
    }
    
    function old_provinsi_update($id, $param)
    {
        $this->old->where('prov_id', $id);
        $query = $this->old->update('Ind_provinces', $param);
        return $query;
    }
	
	function post($param)
	{
		$this->db->set('id_post', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert('post', $param);
		return $query;
	}
	
	function post_archived($param)
	{
		$this->db->set('id_post_archived', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert('post_archived', $param);
		return $query;
	}
	
	function post_info($param)
	{
		$this->db->select('*');
        $this->db->from('post');
		$this->db->where('title', $param['title']);
        $query = $this->db->get();
        return $query;
	}
	
	function preferences($param)
	{
		$this->db->set('id_preferences', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert('preferences', $param);
		return $query;
	}

	function provinsi($param)
	{
		$this->db->set('id_provinsi', 'UUID_SHORT()', FALSE);
		$query = $this->db->insert('provinsi', $param);
		return $query;
	}
	
	function provinsi_info($param)
	{
		$this->db->select('*');
        $this->db->from('provinsi');
		$this->db->where('provinsi', $param['provinsi']);
        $query = $this->db->get();
        return $query;
	}
}