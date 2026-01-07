<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_rtc extends CI_Model
{
	
	function __construct(){ 

		$this->load->database(); 
   }
   

   public function New_Session(){

      $_POST += json_decode(file_get_contents('php://input'), true);

		$data = array();
		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');

		if ($this->form_validation->run()){

			$this->db->update('vc_rooms' ,array(
				'DONE' => 'Y'
			), array('USERID' => $this->input->post('USERID')));

         $this->db->insert('vc_rooms',array(
				'USERID' => $this->input->post('USERID'),
				'PATIENTID' => $this->input->post('PATIENTID'),
				'LOCALCODE' => $this->input->post('CODE'),
				'DONE' => 'N'
         ));
		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}


	public function Check_Session(){

      $_POST += json_decode(file_get_contents('php://input'), true);

		$data = array();
		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('USERID','USER', 'trim|required');
		$this->form_validation->set_rules('PATIENTID','PATIENT', 'trim');

		if ($this->form_validation->run()){

			$sql = $this->db->query("SELECT PATIENTID, LOCALCODE, REMOTECODE 
				FROM vc_rooms 
				WHERE PATIENTID=? AND DONE='N'
				LIMIT 1", array(
					$this->input->post('PATIENTID')
				))->row();

			

			$data['suc']['VC'] = $sql;
		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}


	public function Remote_Session(){

      $_POST += json_decode(file_get_contents('php://input'), true);

		$data = array();
		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('USERID','USER', 'trim|required');
		$this->form_validation->set_rules('PATIENTID','PATIENT', 'trim');
		$this->form_validation->set_rules('REMOTECODE','REMOTE CODE', 'trim');

		if ($this->form_validation->run()){

			$sql = $this->db->query("SELECT ID 
				FROM vc_rooms 
				WHERE PATIENTID=? AND DONE='N'
				ORDER BY ID DESC
				LIMIT 1", array(
					$this->input->post('PATIENTID')
				))->row();

			if( $sql ){

				$this->db->update('vc_rooms',array(
					'REMOTECODE' => $this->input->post('REMOTECODE')
				),array('ID' => $sql->ID));
			}
		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}


	public function End_Session(){

      $_POST += json_decode(file_get_contents('php://input'), true);

		$data = array();
		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('PATIENTID','PATIENT', 'trim|required');

		if ($this->form_validation->run()){

			$this->db->update('vc_rooms',array(
				'DONE' => 'Y'
			),array('PATIENTID' => $this->input->post('PATIENTID')));
		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}

}
?>