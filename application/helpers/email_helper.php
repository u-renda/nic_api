<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Copyright (C) 2016
 * File: application/helpers/email_helper.php
 * Summary: email_helper
 * First writter:  renda <renda [dot] innovation [at] gmail [dot] com>
 */

if ( ! function_exists('email_member_approved'))
{
	function email_member_approved($param)
	{
		$CI =& get_instance();
		$CI->load->model('preferences_model');
		$param += requirement();
		
		$param['subject'] = 'AGnation - Selamat Bergabung di AGnation!';
		$param['link_reset_password'] = $CI->config->item('link_reset_password').'?c='.$param['short_code'];
		
		// content email
		$query = $CI->preferences_model->info(array('key' => 'email_member_approved'));
		
		$email_content = '';
		if ($query->num_rows() > 0)
		{
			$email_content = $query->row()->value;
		}
		
		$send = send_email($param, $email_content);
		return $send;
	}
}

if ( ! function_exists('email_member_create'))
{
	function email_member_create($param)
	{
		$CI =& get_instance();
		$CI->load->model('preferences_model');
		$param += requirement();
		
		$param['subject'] = 'AGnation - Registrasi Berhasil';
		
		// content email
		$query = $CI->preferences_model->info(array('key' => 'email_register_success'));
		
		$email_content = '';
		if ($query->num_rows() > 0)
		{
			$email_content = $query->row()->value;
		}
		
		$send = send_email($param, $email_content);
		return $send;
	}
}

if ( ! function_exists('email_member_invalid'))
{
	function email_member_invalid($param)
	{
		$CI =& get_instance();
		$param += requirement();
		
		$param['subject'] = 'AGnation - Membership Invalid';
		
		$send = send_email($param, $param['email_content']);
		return $send;
	}
}

if ( ! function_exists('email_member_request_transfer'))
{
	function email_member_request_transfer($param)
	{
		$CI =& get_instance();
		$param += requirement();
		
		$param['subject'] = 'AGnation - Membership Request Transfer';
		
		$send = send_email($param, $param['email_content']);
		return $send;
	}
}

if ( ! function_exists('email_member_transfer_confirmation'))
{
	function email_member_transfer_confirmation($param)
	{
		$CI =& get_instance();
		$CI->load->model('preferences_model');
		$param += requirement();
		
		$param['subject'] = 'AGnation - Konfirmasi Pembayaran Berhasil';
		
		// content email
		$query = $CI->preferences_model->info(array('key' => 'email_trf_confirmation_success'));
		
		$email_content = '';
		if ($query->num_rows() > 0)
		{
			$email_content = $query->row()->value;
		}
		
		$send = send_email($param, $email_content);
		return $send;
	}
}

if ( ! function_exists('email_order_create'))
{
	function email_order_create($param)
	{
		$CI =& get_instance();
		$CI->load->model('preferences_model');
		$param += requirement();
		
		$param['subject'] = 'AGnation - Order Merchandise Berhasil';
		
		// content email
		$query = $CI->preferences_model->info(array('key' => 'email_merch_req_trf'));
		
		$email_content = '';
		if ($query->num_rows() > 0)
		{
			$email_content = $query->row()->value;
		}
		
		$send = send_email($param, $email_content);
		return $send;
	}
}

if ( ! function_exists('email_recovery_password'))
{
	function email_recovery_password($param)
	{
		$CI =& get_instance();
		$CI->load->model('preferences_model');
		$param += requirement();
		
		$param['subject'] = 'AGnation - Recovery Password';
		$param['link_reset_password'] = $CI->config->item('link_reset_password').'?c='.$param['short_code'];
		
		// content email
		$query = $CI->preferences_model->info(array('key' => 'email_recovery_password'));
		
		$email_content = '';
		if ($query->num_rows() > 0)
		{
			$email_content = $query->row()->value;
		}
		
		$send = send_email($param, $email_content);
		return $send;
	}
}

if ( ! function_exists('email_reset_password'))
{
	function email_reset_password($param)
	{
		$CI =& get_instance();
		$CI->load->model('preferences_model');
		$param += requirement();
		
		$param['subject'] = 'AGnation - Reset Password';
		$param['link_reset_password'] = $CI->config->item('link_reset_password').'?c='.$param['short_code'];
		
		// content email
		$query = $CI->preferences_model->info(array('key' => 'email_reset_password'));
		
		$email_content = '';
		if ($query->num_rows() > 0)
		{
			$email_content = $query->row()->value;
		}
		
		$send = send_email($param, $email_content);
		return $send;
	}
}

if( ! function_exists('requirement'))
{
	function requirement()
	{
		$CI =& get_instance();
		$CI->load->library('email');
		$CI->config->load('email_template');
		
		$config['useragent'] = 'theagnation.com';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$CI->email->initialize($config);
		
		$param = array();
		
		return $param;
	}
}

if ( ! function_exists('send_email'))
{
	function send_email($param, $email_content)
	{
		$CI =& get_instance();
		
		foreach ($param as $key => $value)
		{
			$k = "{" . $key . "}";
			$email_content = str_replace($k, $value, $email_content);
		}
		
		$CI->email->from('admin@theagnation.com', 'AGnation');
		$CI->email->to($param['email']);
		$CI->email->subject($param['subject']);
		$CI->email->message('<html><head></head><body style="font-family: Arial; margin: 0px;">'.$email_content.'</body></html>');
		
		$send = $CI->email->send();
		return $send;
	}
}

/* End of file email_helper.php */
/* Location: ./application/helpers/email_helper.php */