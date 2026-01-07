<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_patients_query extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	public function list_of_patients()
	{
		$sql = $this->db->query("SELECT ID,DATEREG,LASTNAME,FIRSTNAME,MIDDLENAME, DOB,SEX,MOBILENO,PHONENO,TOTALRECORDS
			FROM patients 
			WHERE CLINICID=? AND CANCELLED = 'N'
			ORDER BY DATEREG DESC
			LIMIT 300",array($this->session->CLINICID));
		$data = $sql->result_array();

		foreach ($data as $key => $v) {
			$data[$key]['NAME'] = $v['LASTNAME'].', '.$v['FIRSTNAME'].' '.$v['MIDDLENAME'];
			$data[$key]['NAME1'] = $v['LASTNAME'].' '.$v['FIRSTNAME'].' '.$v['MIDDLENAME'];
			$data[$key]['NAME2'] = $v['FIRSTNAME'].' '.$v['LASTNAME'];
			unset($data[$key]['FIRSTNAME']);
			unset($data[$key]['LASTNAME']);
			unset($data[$key]['MIDDLENAME']);
		}
		return $data;
	}
	

	public function search_patients()
	{
		$_POST += json_decode(file_get_contents('php://input'), true);

		$search = preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['search']);

		$sql = $this->db->query("SELECT ID,DATEREG,LASTNAME,FIRSTNAME,MIDDLENAME, DOB,SEX,MOBILENO,PHONENO,TOTALRECORDS
			FROM patients 
			WHERE (concat(FIRSTNAME,' ',MIDDLENAME,' ',LASTNAME) like ? 
			OR concat(LASTNAME,' ',FIRSTNAME,' ',MIDDLENAME) like ?
			OR concat(FIRSTNAME,' ',LASTNAME) like ? )
			AND CLINICID=?
			AND CANCELLED = 'N'
			ORDER BY LASTNAME ASC, FIRSTNAME ASC
			LIMIT 300",array($search.'%',$search.'%',$search.'%',$this->session->CLINICID));

		$data = $sql->result_array();

		foreach ($data as $key => $v) {
			$data[$key]['NAME'] = $v['LASTNAME'].', '.$v['FIRSTNAME'].' '.$v['MIDDLENAME'];
			$data[$key]['NAME1'] = $v['LASTNAME'].' '.$v['FIRSTNAME'].' '.$v['MIDDLENAME'];
			$data[$key]['NAME2'] = $v['FIRSTNAME'].' '.$v['LASTNAME'];
			unset($data[$key]['FIRSTNAME']);
			unset($data[$key]['LASTNAME']);
			unset($data[$key]['MIDDLENAME']);
		}
		return $data;
	}



	public function edit_patient_data($id)
	{

		if( (int)$id === 0 )
		{

			$data = array(
				'token' => $this->my_utilities->token(0),
				'url' => base_url('patients/submit_patient'),
				'newData' => true,
				'DATEREG' => date('m/d/Y',time()),
				'FIRSTNAME' => '',
				'MIDDLENAME' => '',
				'LASTNAME' => '',
				'DOB' => '',
				'SEX' => 'MALE',
				'BLOODTYPE' => '',
				'POB' => '',
				'NATIONALITY' => '',
				'RELIGION' => '',
				'OCCUPATION' => '',
				'STREETNO' => '',
				'CITY' => '',
				'PROVINCE' => '',
				'PHONENO' => '',
				'MOBILENO' => '',
				'EMAIL' => '',
				'PICTURE' => '',
				'EMERGENCYCONTACT' => '',
				'EMERGENCYADDRESS' => '',
				'EMERGENCYPHONENO' => '',
				'EMERGENCYMOBILENO' => '',
				'MEDASTHMA' => 'N',
				'MEDDM' => 'N',
				'MEDHPN' => 'N',
				'MEDIHD' => 'N',
				'MEDSMOKER' => 'N',
				'MEDOTHERS' => '',
				'CANCELLED' => 'N'
			);

		}
		else
		{

			$sql = $this->db->query("SELECT * 
				FROM patients
				where ID =? AND CLINICID=?
				LIMIT 1",array($id,$this->session->CLINICID));
			
			$data = $sql->row_array();
			$data['token'] = $this->my_utilities->token($id);
			$data['url'] = base_url('patients/submit_patient');
			$data['newData'] = false;
			$data['DATEREG'] = date('m/d/Y',strtotime($data['DATEREG']));
			$data['DOB'] = date('m/d/Y',strtotime($data['DOB']));
		}


		return $data;

	}




	public function patient_information($id)
	{
		$sql = $this->db->query("SELECT *
			FROM patients 
			WHERE ID = ? AND CLINICID=?
			LIMIT 1",array($id,$this->session->CLINICID));

		$data = $sql->row_array();
		$data['token'] = $this->my_utilities->token($id);

		$sql1 = $this->db->query("SELECT ID,BRANCH,CHECKUPDATE,CHEIFCOMPLAINT,APPOINTMENT,APPOINTMENTDATE,AGE,FINDINGS,DIAGNOSIS
			From medicalrecords 
			WHERE PATIENTID=? AND CLINICID=? and CANCELLED='N' 
			LIMIT 50",array($id,$this->session->CLINICID));

		$data['MEDICALS'] = $sql1->result_array();

		if(  $data['TOTALRECORDS'] != count($data['MEDICALS']) )
			$this->db->update('patients',array('TOTALRECORDS'=>count($data['MEDICALS'])),array('ID'=>$id));

		return $data;
	}






}




?>