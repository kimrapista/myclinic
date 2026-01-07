<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Ma_preregistration_q extends CI_Model
{	
	function __construct(){ $this->load->database(); }


	public function index()
	{
		$id = $this->session->CLINICID;
		$sql = $this->db->query("SELECT p.ID,concat(p.FIRSTNAME,' ', p.MIDDLENAME,' ', p.LASTNAME) as name, concat(p.NOSTREET,' ',p.BARANGAY,' ',p.CITY,' ',p.PROVINCE) as address, c.CLINICNAME, c.DOCTORNAME,p.POST,p.SEX FROM admitted_patient p,clinics c where p.CLINICID = c.ID and p.ADMITTED = 'Y' and p.CANCELLED = 'N' and c.ID = '$id'")->result();
		return $sql;
	}

	public function adminIndex()
	{
		$id = $this->session->userid;
		$sql = $this->db->query("SELECT p.ID,concat(p.FIRSTNAME,' ', p.MIDDLENAME,' ', p.LASTNAME) as name, concat(p.NOSTREET,' ',p.BARANGAY,' ',p.CITY,' ',p.PROVINCE) as address, c.CLINICNAME, c.DOCTORNAME,p.POST,p.SEX FROM admitted_patient p,clinics c where p.CLINICID = c.ID and p.CANCELLED = 'N' and p.ADMITTED = 'Y'")->result();
		return $sql;
	}

	public function edit_preregistration_data($id)
	{
		if( (int)$id === 0 )
		{
			$data = array(
				'TOKEN' 				=> $this->m_utility->tokenRequest(),
				'URL' 					=> base_url('preregistration/submit_preregistration'),
				'newData' 				=> true,
				'ID' 					=> 0,
				'DATEREG' 				=> date('m/d/Y',time()),
				'FIRSTNAME' 			=> '',
				'MIDDLENAME' 			=> '',
				'LASTNAME' 				=> '',
				'DOB' 					=> date('m/d/Y',time()),
				'SEX' 					=> 'MALE',
				'CIVILSTATUS' 			=> '',
				'BLOODTYPE' 			=> '',
				'POB' 					=> '',
				'NATIONALITY' 			=> '',
				'RELIGION' 				=> '',
				'OCCUPATION' 			=> '',
				'STREETNO' 				=> '',
				'CITY' 					=> '',
				'PROVINCE' 				=> '',
				'TELEPHONENO' 			=> '',
				'EMERGENCYNAME' 		=> '',
				'EMERGENCYADDRESS' 		=> '',
				'EMERGENCYTELPHONENO' 	=> '',
				'EMERGENCYRELATION' 	=> '',
				'ADMITTED' 				=> '',
				'ALLERGIES' 			=> '',
				'SCNO' 					=> '',
				'DATEISSUE' 			=> date('m/d/y',time()),
				'POST' 					=> 'N',
				'SPOUSENAME' 			=> '',
				'SPOUSEADDRESS' 		=> '',
				'SPOUSETELPHONENO' 		=> '',
				'FATHERNAME' 			=> '',
				'MOTHERNAME' 			=> '',
				'MOTHERADDRESS' 		=> '',
				'MOTHERTELPHONENO' 		=> ''
			);
		}
		else
		{
			$data = $this->db->query("SELECT *  FROM admitted_patient where ID =? LIMIT 1",array($id))->row_array();
			$data['TOKEN'] 		= $this->m_utility->tokenRequest($id);
			$data['URL'] 		= base_url('preregistration/submit_preregistration');
			$data['DATEREG'] 	= date('m/d/Y',strtotime($data['DATEREG']));
			$data['DOB'] 		= date('m/d/Y',strtotime($data['DOB']));
		}
		return $data;
	}
	public function admitPatient($id)
	{
		$data = $this->db->query("SELECT *  FROM patients where ID =? AND CLINICID=? LIMIT 1",array($id,$this->session->CLINICID))->row_array();
		$data['TOKEN'] 		= $this->m_utility->tokenRequest($id);
		$data['URL'] 		= base_url('preregistration/admitPatient');
		$data['DATEREG'] 	= date('m/d/Y',strtotime($data['DATEREG']));
		$data['DOB'] 		= date('m/d/Y',strtotime($data['DOB']));
		return $data;
	}
}
?>