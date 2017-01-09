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

if ( ! function_exists('check_product_name'))
{
    function check_product_name($name)
	{
        $CI =& get_instance();
        $CI->load->model('product_model');
        
		$query = $CI->product_model->info(array('name' => $name));
		
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
        $CI->load->model('provinsi_model');

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

if ( ! function_exists('email_req_transfer'))
{
	function email_req_transfer($param, $unique_code)
	{
		$CI =& get_instance();
		$CI->load->model('preferences_model');
		$param += email_requirement();
		
		$query = $CI->preferences_model->info(array('key' => 'email_req_transfer'));
		
		if ($query->num_rows() > 0)
		{
			$registration_fee = $CI->config->item('registration_fee');
			$delivery_cost = $param['price'];
			$email_content = $query->row()->value;
			
			$param['subject'] = 'Status Pendaftaran NIC';
			$param['member_name'] = ucwords($param['name']);
			$param['registration_fee'] = number_format($registration_fee, 0, '', '.');
			$param['delivery_cost'] = number_format($delivery_cost, 0, '', '.');
			$param['unique_code'] = $unique_code;
			$param['total_transfer'] = number_format($registration_fee + $delivery_cost + $unique_code, 0, '', '.');
			$param['link_web_transfer'] = $CI->config->item('link_web_transfer');
		}
		
		$send = email_send($param, $email_content);
		return $send;
	}
}

if ( ! function_exists('email_requirement'))
{
	function email_requirement()
	{
		$CI =& get_instance();
		$CI->load->library('email');
		
		$config['useragent'] = 'NEZindaCLUB';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['protocol'] = 'sendmail';
		$CI->email->initialize($config);
		
		// Jika ada tambahan param yang mau ditambahin di email (misal: email admin, alamat, dll)
		$param = array();
		return $param;
	}
}

if ( ! function_exists('email_send'))
{
	function email_send($param, $email_content)
	{
		$CI =& get_instance();
		
		foreach ($param as $key => $value)
		{
			$k = "{" . $key . "}";
			$email_content = str_replace($k, $value, $email_content);
		}
		
		$CI->email->from($CI->config->item('email_admin'), $CI->config->item('title'));
		$CI->email->to($param['email']);
		$CI->email->subject($param['subject']);
		$CI->email->message($email_content);
		
		$send = $CI->email->send();
		return $send;
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

if ( ! function_exists('generate_username'))
{
	function generate_username($param)
	{
		$CI =& get_instance();
		
		$username = str_replace(" ", "", ucwords($param->name));
		$username = substr($username, 0, 8);
		$username .= date('md', strtotime($param->birth_date));
		return $username;
	}
}

if ( ! function_exists('get_member_card'))
{
	function get_member_card($param, $id_admin)
	{
		$CI =& get_instance();
		$CI->load->model('admin_model');
		$CI->load->model('member_model');
		$code_member_gender = $CI->config->item('code_member_gender');
		$query = $CI->admin_model->info(array('id_admin' => $id_admin));
		
		if ($query->num_rows() > 0)
		{
			$birth_date = date('my', strtotime($param->birth_date));
			$gender = $code_member_gender[$param->gender];
			$initial = $query->row()->admin_initial;
			$year = date('y');
			
			$data = $birth_date.$gender.'W'.$initial.$year.$param->get_member_number;
			return $data;
		}
		else
		{
			return FALSE;
		}
	}
}

if ( ! function_exists('get_member_number'))
{
	function get_member_number()
	{
		$CI =& get_instance();
		$CI->load->model('member_model');
		
		$param2 = array();
		$param2['order'] = 'member_number';
		$param2['sort'] = 'desc';
		$param2['limit'] = 1;
		$param2['offset'] = 0;
		$query = $CI->member_model->lists($param2);
		
		if ($query->num_rows() > 0)
		{
			$member_number = $query->row()->member_number + 1;
			$new_number = str_pad($member_number, 5, 0, STR_PAD_LEFT);
			return $new_number;
		}
		else
		{
			return FALSE;
		}
	}
}

if ( ! function_exists('get_update_unique_code'))
{
	function get_update_unique_code()
	{
		$CI =& get_instance();
		$CI->load->model('preferences_model');
		
		$query = $CI->preferences_model->info(array('key' => 'unique_trf_id'));
		
		if ($query->num_rows() > 0)
		{
			$id = $query->row()->id_preferences;
			$unique_code = $query->row()->value;
			$new_value = $unique_code + 1;
			
			// update valuenya (+1)
			$query2 = $CI->preferences_model->update($id, array('value' => $new_value));
			
			if ($query2 == TRUE)
			{
				return $unique_code;
			}
			else
			{
				return FALSE;
			}
		}
	}
}

if ( ! function_exists('lists_member_transfer'))
{
	function lists_member_transfer($param, $type = 1)
	{
		$CI =& get_instance();
		$CI->load->model('member_transfer_model');
		
		$param2 = array();
		$param2['sort'] = 'desc';
		$param2['order'] = 'created_date';
		$param2['offset'] = 0;
		$param2['limit'] = 20;
		$param2['type'] = $type;
		$param2['id_member'] = $param->id_member;
		$query = $CI->member_transfer_model->lists($param2);
		
		if ($query->num_rows() > 0)
		{
			return $query->result();
		}
		else
		{
			return FALSE;
		}
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
