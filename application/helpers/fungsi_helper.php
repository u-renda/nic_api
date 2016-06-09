<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('check_admin_email'))
{
    function check_admin_email($email)
	{
        $CI =& get_instance();
        $CI->load->model('admin_model');
        
		$query = $CI->admin_model->info(array('email' => $email));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_admin_initial'))
{
    function check_admin_initial($admin_initial)
	{
        $CI =& get_instance();
        $CI->load->model('admin_model');
        
		$query = $CI->admin_model->info(array('admin_initial' => $admin_initial));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_admin_name'))
{
    function check_admin_name($name)
	{
        $CI =& get_instance();
        $CI->load->model('admin_model');
        
		$query = $CI->admin_model->info(array('name' => $name));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_kota_name'))
{
    function check_kota_name($kota, $id_provinsi)
	{
        $CI =& get_instance();
        $CI->load->model('kota_model');
        
		$query = $CI->kota_model->info(array('kota' => $kota, 'id_provinsi' => $id_provinsi));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_member_email'))
{
    function check_member_email($email)
	{
        $CI =& get_instance();
        $CI->load->model('member_model');
        
		$query = $CI->member_model->info(array('email' => $email, 'status' => 4));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_member_idcard_number'))
{
    function check_member_idcard_number($idcard_number)
	{
        $CI =& get_instance();
        $CI->load->model('member_model');
        
		$query = $CI->member_model->info(array('idcard_number' => $idcard_number));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_member_name'))
{
    function check_member_name($name)
	{
        $CI =& get_instance();
        $CI->load->model('member_model');
        
		$query = $CI->member_model->info(array('name' => $name, 'status' => 4));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_member_phone_number'))
{
    function check_member_phone_number($phone_number)
	{
        $CI =& get_instance();
        $CI->load->model('member_model');
        
		$query = $CI->member_model->info(array('phone_number' => $phone_number));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_member_username'))
{
    function check_member_username($username)
	{
        $CI =& get_instance();
        $CI->load->model('member_model');
        
		$query = $CI->member_model->info(array('username' => $username));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_nav_menu_title'))
{
    function check_nav_menu_title($title)
	{
        $CI =& get_instance();
        $CI->load->model('nav_menu_model');
        
		$query = $CI->nav_menu_model->info(array('title' => $title));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_post_slug'))
{
    function check_post_slug($slug)
	{
        $CI =& get_instance();
        $CI->load->model('post_model');
        
		$query = $CI->post_model->info(array('slug' => $slug));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_post_title'))
{
    function check_post_title($title)
	{
        $CI =& get_instance();
        $CI->load->model('post_model');
        
		$query = $CI->post_model->info(array('title' => $title));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_preferences_key'))
{
    function check_preferences_key($key)
	{
        $CI =& get_instance();
        $CI->load->model('preferences_model');
        
		$query = $CI->preferences_model->info(array('key' => $key));
		
		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('check_provinsi_name'))
{
    function check_provinsi_name($provinsi)
	{
        $CI =& get_instance();
        $CI->load->model('post_model');

		$query = $CI->provinsi_model->info(array('provinsi' => $provinsi));

		if ($query->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
    }
}

if ( ! function_exists('filter'))
{
    function filter($param)
    {
        $CI =& get_instance();

        $result = $CI->db->escape_str($param);
        return $result;
    }
}

if ( ! function_exists('valid_email'))
{
	function valid_email($email)
	{
		if ( !preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i", $email) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}
