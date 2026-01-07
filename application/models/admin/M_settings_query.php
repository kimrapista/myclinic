<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_settings_query extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	public function list_of_services()
	{
		$sql = $this->db->query("SELECT * FROM services where CLINICID=? ORDER BY NAME ASC",array($this->session->CLINICID));
		return $sql->result_array();
	}


	public function list_of_discounts()
	{
		$sql = $this->db->query("SELECT * FROM discounts where CLINICID=? ORDER BY NAME ASC",array($this->session->CLINICID));
		return $sql->result_array();
	}

	public function list_of_hmo()
	{
		$sql = $this->db->query("SELECT * FROM hmo where CLINICID=? ORDER BY NAME ASC",array($this->session->CLINICID));
		return $sql->result_array();
	}

	public function list_of_medicines()
	{
		$sql = $this->db->query("SELECT * FROM medicines where CLINICID=? ORDER BY NAME ASC",array($this->session->CLINICID));
		return $sql->result_array();
	}



	public function list_of_users(){

		$sql = $this->db->query("SELECT ID,NAME,POSITION,USERNAME,CANCELLED
			FROM users
			where ID != ? AND CLINICID=? ",array($this->session->userid,$this->session->CLINICID));

		return $sql->result();
	}



	public function user_data($id){

		if( (int)$id === 0 ){

			$data = array(
				'TOKEN' => $this->my_utilities->token(0),
				'url' => base_url('settings/submit-user'),
				'newData' => TRUE,
				'NAME' => '',
				'JOBTITLE' => '',
				'POSITION' => '',
				'USERNAME' => '',
				'CANCELLED' => 'N'
			);

			return $data;
		}
		else if ( (int)$id > 0 ){

			$sql = $this->db->query("SELECT NAME,JOBTITLE,POSITION,USERNAME,CANCELLED
				FROM users 
				WHERE ID=?  AND CLINICID=?
				LIMIT 1",array($id,$this->session->CLINICID));

			$data = $sql->row_array();

			$data['TOKEN'] = $this->my_utilities->token($id);
			$data['url'] = base_url('settings/submit-user');
			$data['newData'] = FALSE;

			return $data;

		}

	}



}




?>