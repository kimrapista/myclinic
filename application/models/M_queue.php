<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_queue extends CI_Model
{
	
	function __construct(){ $this->load->database(); }



	public function Advisory(){

		if( isset($this->session->CLINICID) ){
			
			$sql = $this->db->query("SELECT * FROM advisory 
				WHERE (CLINICID=? OR CLINICID IS NULL OR CLINICID = 0) AND POST='Y' AND POSTDATE <= ? AND CANCELLED='N' 
				ORDER BY CREATEDTIME DESC",array($this->session->CLINICID, date('Y-m-d H:i:s',time())))->result();

			foreach ($sql as $key => $value) {
				$value->AVATAR = base_url('assets/css/images/logo.png');

				$value->HASH = $this->m_utility->passwordHash($value->TITLE.$value->BODY.$value->LINK.$value->POST.$value->POSTDATE);
			}

			return $sql;	
		}
		else{
			
			return array();
		}
	}



	public function Index(){

		$date = new DateTime();

		$sql = $this->db->query("SELECT PQ.ID,PQ.DATEQUEUE,PQ.PRIORITYNO,PQ.PATIENTID,concat(P.LASTNAME,', ',P.FIRSTNAME,' ',P.MIDDLENAME) as NAME,PQ.WAITING,PQ.SERVING,PQ.PAID,PQ.ACTIVE
			FROM patients P 
			INNER JOIN patient_queue PQ ON PQ.PATIENTID = P.ID
			WHERE  DATE(PQ.DATEQUEUE)=? AND PQ.ACTIVE=1 AND P.CLINICID = ?
			ORDER BY PQ.PRIORITYNO ASC",array($date->format('Y-m-d'),$this->session->CLINICID))->result();

		return $sql;
	}	


	public function Patient_Queue($patientid){

		$data['err'] = '';
		$data['suc'] = array();

		$sql = $this->db->query("SELECT PRIORITYNO from patient_queue where DATE(DATEQUEUE)=? AND CLINICID=? AND PATIENTID=? AND ACTIVE=1 LIMIT 1 ",array(date('Y-m-d',time()),$this->session->CLINICID,$patientid))->row();

		if( !isset($sql) ){

			$sql = $this->db->query("SELECT MAX(PRIORITYNO) AS COUNT from patient_queue where DATE(DATEQUEUE)=? AND CLINICID=? AND ACTIVE=1 LIMIT 1 ",array(date('Y-m-d',time()),$this->session->CLINICID))->row();

			$this->db->insert('patient_queue',array(
				'CLINICID'	 => $this->session->CLINICID,	
				'DATEQUEUE'  => date('Y-m-d H:i:s',time()),
				'PRIORITYNO' => ($sql->COUNT + 1),
				'PATIENTID'  => $patientid,
				'WAITING'    => 1,
				'SERVING'    => 0,
				'PAID'       => 0,
				'ACTIVE'    => 1
			));

			$data['suc'] = '<div>Queued & Priority No. '.($sql->COUNT + 1).'</div>';
		} 
		else{
			$data['err'] = '<div>Already Queued No. '. $sql->PRIORITYNO.'</div>';
		}

		return $data;
	}


	public function Queue_Tomorrow($patientid){

		$data['err']= '';
		$data['suc']=array();

		$date = new DateTime();
		$date->add(new DateInterval('P1D'));

		$sql = $this->db->query("SELECT PRIORITYNO from patient_queue where DATE(DATEQUEUE)=? AND CLINICID=? AND PATIENTID=? AND ACTIVE=1 LIMIT 1 ",array($date->format('Y-m-d'),$this->session->CLINICID,$patientid))->row();

		if( !isset($sql) ){

			$sql = $this->db->query("SELECT MAX(PRIORITYNO) AS COUNT from patient_queue where DATE(DATEQUEUE)=? AND CLINICID=? AND ACTIVE=1 LIMIT 1 ",array($date->format('Y-m-d'),$this->session->CLINICID))->row();

			$this->db->insert('patient_queue',array(
				'CLINICID'   => $this->session->CLINICID,
				'DATEQUEUE'  => $date->format('Y-m-d H:i:s'),
				'PRIORITYNO' => ($sql->COUNT + 1),
				'PATIENTID'  => $patientid,
				'WAITING'    => 1,
				'SERVING'    => 0,
				'PAID'       => 0,
				'ACTIVE'    => 1
			));

			$data['suc'] = '<div>Queued for tomorrow</div>';
		} 
		else{
			$data['err'] = '<div>Already queued for tomorrow.</div>';
		}

		return $data;
	}



	public function patient_remove($ID){
		$this->db->update('patient_queue',array('ACTIVE'=>0),array('ID'=>$ID));
	}


	public function patient_priority(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$ID = $_POST['ID'];
		$PRIORITYNO = $_POST['PNO'];

		$this->db->update('patient_queue',array('PRIORITYNO'=>$PRIORITYNO),array('ID'=>$ID));
	}


	public function Patient_Served($PATIENTID,$DATE){

		$sql = $this->db->query("SELECT ID from patient_queue where DATE(DATEQUEUE)=? AND PATIENTID=? AND ACTIVE=1 LIMIT 1 ",array(date('Y-m-d',strtotime($DATE)),$PATIENTID))->row();
		
		if(isset($sql)){
			$this->db->update('patient_queue',array('SERVING'=>1),array('ID'=>$sql->ID));
		}

	}


	public function Patient_Paid($PATIENTID,$DATE){

		$sql = $this->db->query("SELECT ID from patient_queue where DATE(DATEQUEUE)=? AND PATIENTID=? AND ACTIVE=1 LIMIT 1 ",array(date('Y-m-d',strtotime($DATE)),$PATIENTID))->row();
		
		if(isset($sql)){
			$this->db->update('patient_queue',array('PAID'=>1),array('ID'=>$sql->ID));
		}

	}


}

?>