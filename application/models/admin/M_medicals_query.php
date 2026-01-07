<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medicals_query extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	public function list_of_medical_records()
	{

		$_POST += json_decode(file_get_contents('php://input'), true);

		$search = preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['text']);
		$dateFrom = date('Y-m-d',strtotime($_POST['dateFrom']));
		$dateTo = date('Y-m-d',strtotime($_POST['dateTo']));
			

		$sql = $this->db->query("SELECT MR.ID, MR.PATIENTID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, P.SEX, MR.CHECKUPDATE, MR.AGE, MR.CHEIFCOMPLAINT, MR.FINDINGS, MR.DIAGNOSIS, MR.BRANCH 
			FROM patients P 
			INNER JOIN medicalrecords MR  ON MR.PATIENTID = P.ID
			WHERE (concat(P.FIRSTNAME,' ',P.MIDDLENAME,' ',P.LASTNAME) like ? 
			OR concat(P.LASTNAME,' ',P.FIRSTNAME,' ',P.MIDDLENAME) like ?
			OR concat(P.FIRSTNAME,' ',P.LASTNAME) like ? )
			AND DATE(MR.CHECKUPDATE) between ? AND ?
			AND MR.CLINICID=?
			AND P.CANCELLED = 'N'
			ORDER BY MR.CHECKUPDATE DESC,LASTNAME ASC
			LIMIT 500",
			array($search.'%',$search.'%',$search.'%',$dateFrom,$dateTo,$this->session->CLINICID));

		return $sql->result_array();
	}



	public function edit_medical_record_data($medid,$patientid)
	{

		if( $medid == 0 )
		{

			$patient = $this->getPatient($patientid);
			$medothers ='';

			if( $patient->TOTALRECORDS > 0 ){
				$sql = $this->db->query("SELECT MEDOTHERS FROM medicalrecords WHERE PATIENTID=? ORDER BY CHECKUPDATE DESC,ID DESC LIMIT 1",array($patient->ID));
				if( $sql->row() ){ $medothers = $sql->row()->MEDOTHERS; }
			}
			
			$data = array(
				'TOKEN' => $this->my_utilities->token(array(0,$patient->ID)),
				'URL' => base_url('medical/submit-medical-record'),
				'NEWDATA' => TRUE,
				'PATIENT' => $patient,
				'BRANCH' => '',
				'CHECKUPDATE' => date('m/d/Y',time()),
				'APPOINTMENT' => 'N',
				'APPOINTMENTDATE' => date('m/d/Y',time()),
				'AGE' => $this->my_utilities->ageCompute($patient->DOB,date('Y-m-d',time())),
				'REFFEREDBY' => '',
				'MEDOTHERS' => $medothers,
				'REFOD' => '',
				'REFOS' => '',
				'REFADD' => '',
				'REFPD' => '',
				'CHEIFCOMPLAINT' => '',
				'FINDINGS' => '',
				'DIAGNOSIS' => '',
				'MEDICATION' => '',
				'REMARKS' => '',
				'HMOID' => 0,
				'COMPANY' => '',
				'PAYMODE' => 'CASH',
				'AMOUNT' => 0,
				'AMOUNTCHANGE' => 0,
				'CANCELLED' => 'N',
				'SERVICES' => array(),
				'DISCOUNTS' => array(),
				'IMAGES' => array(),
				'MEDICINES' => array(),
				'DISEASES' => $this->DISEASES(0,$patient->ID),
				'list_services' => $this->list_of_services(),
				'list_discounts' => $this->list_of_discounts(),
				'list_hmo' => $this->list_of_hmo(),
				'list_medicines' => $this->list_of_medicines()
			);

		}
		else if( $medid > 0 )
		{

			$sql = $this->db->query("SELECT *
				FROM medicalrecords
				where ID =? AND CLINICID=?
				LIMIT 1",array($medid,$this->session->CLINICID));

			$data = $sql->row_array();
			$data['TOKEN'] = $this->my_utilities->token(array($medid,$data['PATIENTID']));
			$data['URL'] = base_url('medical/submit-medical-record');
			$data['NEWDATA'] = false;

			$data['CHECKUPDATE'] = date('m/d/Y',strtotime($data['CHECKUPDATE']));
			$data['APPOINTMENTDATE'] = date('m/d/Y',strtotime($data['APPOINTMENTDATE']));

			$data['PATIENT'] = $this->getPatient($data['PATIENTID']);
			$data['IMAGES'] = $this->IMAGES($medid);
			$data['SERVICES'] = $this->SERVICES($medid);
			$data['DISCOUNTS'] = $this->DISCOUNTS($medid);
			$data['MEDICINES'] = $this->MEDICINES($medid);
			$data['DISEASES'] = $this->DISEASES($medid,$data['PATIENTID']);
			$data['list_services'] = $this->list_of_services();
			$data['list_discounts'] = $this->list_of_discounts();
			$data['list_hmo'] = $this->list_of_hmo();
			$data['list_medicines'] = $this->list_of_medicines();

			if( !is_numeric($data['AGE']) ){
				$data['AGE'] = $this->my_utilities->ageCompute($data['PATIENT']->DOB,$data['CHECKUPDATE']);
			}
			else{
				$data['AGE'] = (int)$data['AGE'];
			}

		}


		return $data;

	}


	private function getPatient($patientid){

		$sql=$this->db->query("SELECT ID,DOB,FIRSTNAME,LASTNAME,MIDDLENAME,TOTALRECORDS FROM patients WHERE ID=? LIMIT 1",array($patientid));
		return $sql->row();
	}


	private function SERVICES($medid){
		$sql = $this->db->query("SELECT MRS.ID, MRS.SERVICEID, MRS.QUANTITY, MRS.CANCELLED, S.NAME, S.PRICE
			From mr_services MRS 
			INNER JOIN services S ON S.ID = MRS.SERVICEID 
			WHERE MRS.MEDICALRECORDID=? AND MRS.CANCELLED = 'N' ",array($medid));
		
		$data= $sql->result_array();
		foreach ($data as $key => $value) { $data[$key]['EDIT'] = false; }
		return $data;
	}


	private function DISCOUNTS($medid){
		$sql = $this->db->query("SELECT MRD.ID, MRD.DISCOUNTID, MRD.AMOUNT, MRD.CANCELLED, D.NAME, D.PERCENTAGE 
			From mr_discounts MRD 
			INNER JOIN discounts D ON D.ID = MRD.DISCOUNTID
			WHERE MRD.MEDICALRECORDID=? AND MRD.CANCELLED = 'N' ",array($medid));
		
		$data= $sql->result_array();
		foreach ($data as $key => $value) { $data[$key]['EDIT'] = false; }
		return $data;
	}

	private function  IMAGES($medid){
		$sql = $this->db->query("SELECT ID, IMAGEPATH, CANCELLED From mr_images WHERE MEDICALRECORDID=? AND CANCELLED = 'N' ",array($medid));
		return $sql->result();	
	}


	private function MEDICINES($medid){
		$sql = $this->db->query("SELECT ID,MEDICINEID,FREQUENCY,INSTRUCTION,CANCELLED
			From mr_medicines
			WHERE MEDICALRECORDID=? AND CANCELLED = 'N' ",array($medid));
		$data = $sql->result_array();
		foreach ($data as $key => $v) { $data[$key]['EDIT'] = FALSE; }

		return $data;
	}


	private function DISEASES($medid,$patientid){

		$data = array();

		$sql1 = $this->db->query("SELECT * FROM diseases WHERE DISABLED = 0",array());
		$diseases = $sql1->result_array();

		
		$sql = $this->db->query("SELECT mrd.ID,mrd.DISEASEID,d.NAME,mrd.ACTIVE 
			FROM mr_diseases mrd 
			INNER JOIN diseases d ON d.ID = mrd.DISEASEID
			WHERE mrd.MEDICALRECORDID=? ",array($medid));
		$data = $sql->result_array();

		foreach ($data as $key => $v) { 
			$data[$key]['ACTIVE'] = (boolean) $v['ACTIVE'];
			$data[$key]['EDIT'] = FALSE; 
		}


		// Try to Insert for new diseases to old medical record disease list
		foreach($diseases as $key => $v){
			
			$found = false;
			foreach ($data as $key1 => $v1) { if($v['ID'] === $v1['DISEASEID']) { $found = true; break;} }

			if( !$found){
				$data[] = array('ID'=>0, 'DISEASEID'=>$v['ID'], 'NAME'=>$v['NAME'], 'ACTIVE'=>FALSE, 'EDIT'=>FALSE);
			}
		}

		if( $medid == 0 ){ 
			// For new medical record try to get previous medical history and to inherit

			$sql2 = $this->db->query("SELECT ID FROM medicalrecords  WHERE PATIENTID=? ORDER BY CHECKUPDATE DESC,ID DESC LIMIT 1",array($patientid));

			if( $sql2->row() ){
				$medid = $sql2->row()->ID;

				$sql13 = $this->db->query("SELECT mrd.DISEASEID
					FROM mr_diseases mrd 
					INNER JOIN diseases d ON d.ID = mrd.DISEASEID
					WHERE mrd.MEDICALRECORDID=? and ACTIVE = 1",array($medid));
				$olddata = $sql13->result_array();

				foreach ($olddata as $key => $v) {
					$found = false;
					foreach ($data as $key1 => $v1) {
						if($v['DISEASEID'] === $v1['DISEASEID'] ){
							$data[$key1]['ACTIVE'] = TRUE;
							$data[$key1]['EDIT'] = TRUE;
							break;
						}
					}
				}
			} 
		}


		


		return $data;
	}


	private function list_of_services(){
		$sql = $this->db->query("SELECT * FROM services where CLINICID=? ",array($this->session->CLINICID));
		return $sql->result_array();
	}

	private function list_of_discounts(){
		$sql = $this->db->query("SELECT * FROM discounts where CLINICID=? ",array($this->session->CLINICID));
		return $sql->result_array();
	}

	private function list_of_hmo(){
		$sql = $this->db->query("SELECT * FROM hmo where CLINICID=? ",array($this->session->CLINICID));
		return $sql->result_array();
	}

	private function list_of_medicines(){
		$sql = $this->db->query("SELECT * FROM medicines where CLINICID=? ",array($this->session->CLINICID));
		return $sql->result_array();
	}



}




?>