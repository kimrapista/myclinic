<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// assistant

class M_dashboard extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	public function Summary_Info(){ 


		$sql = $this->db->query("SELECT count(ID) as total 
			FROM patients 
			where DATE(DATEREG)=? AND CLINICID=? AND CANCELLED='N' 
			limit 1",array(date('Y-m-d',time()),$this->session->CLINICID))->row();

		$sql1 = $this->db->query("SELECT count(ID) as total 
			FROM patients 
			WHERE CLINICID=? AND CANCELLED='N' 
			LIMIT 1",array($this->session->CLINICID))->row();


		$sql2 = $this->db->query("SELECT COUNT(P.ID) as total
			FROM medicalrecords MR
			INNER JOIN patients P ON P.ID = MR.PATIENTID
			WHERE  DATE(MR.CHECKUPDATE)=? AND MR.CLINICID=? AND DATE(P.DATEREG) < ? AND MR.CANCELLED='N'
			LIMIT 1",array(date('Y-m-d',time()),$this->session->CLINICID,date('Y-m-d',time())))->row();


		$sql3 = $this->db->query("SELECT count(CHECKUPDATE) as total
			FROM medicalrecords
			WHERE APPOINTMENT='Y' AND DATE(APPOINTMENTDATE) = ? AND CLINICID=? AND CANCELLED='N'
			LIMIT 1",array(date('Y-m-d',time()),$this->session->CLINICID))->row();

		$sql4 = $this->db->query("SELECT count(CHECKUPDATE) as total
			FROM medicalrecords
			WHERE APPOINTMENT='Y' AND DATE(APPOINTMENTDATE) > ? AND CLINICID=? AND CANCELLED='N'
			LIMIT 1",array(date('Y-m-d',time()),$this->session->CLINICID))->row();


		$sql5 = $this->db->query("SELECT count(ID) as served, SUM(GROSSAMOUNT) as servicesAmt, SUM(DISCOUNTAMOUNT) as discountAmt, SUM(PAIDAMOUNT) as cashAmt, SUM(HMOAMOUNT) as hmoAmt
			FROM medicalrecords MR
			WHERE  DATE(CHECKUPDATE)=? AND CLINICID=? AND CANCELLED='N'
			LIMIT 1",array(date('Y-m-d',time()),$this->session->CLINICID))->row();
		
			
		return array(
			'newPatient' => (int)$sql->total,
			'totalPatient' => (int)$sql1->total,
			'appointToday' => (int)$sql3->total,
			'appointUpcoming' => (int)$sql4->total,
			'returnee' => (int)$sql2->total,
			'served' => (int)$sql5->served,
			'servicesAmount' => (float)$sql5->servicesAmt,
			'discountAmount' => (float)$sql5->discountAmt,
			'hmoAmount' => (float)$sql5->hmoAmt,
			'cashAmount' => (float)$sql5->cashAmt
		);

	}


	public function Monthly_Info(){

		$month = date('m',time());
		$year = date('Y',time());
		$labels = array();
		$served = array();
		$services = array();
		$discounts = array();
		$cash = array();
		$hmo = array();
		$collectables = array(); 


		$data = $this->db->query("SELECT CHECKUPDATE, COUNT(ID) as served, SUM(NETPAYABLES + DISCOUNTAMOUNT) as servicesAmt, SUM(DISCOUNTAMOUNT) as discountAmt,SUM(PAIDAMOUNT) as cashAmt, SUM(HMOAMOUNT) as hmoAmt
			From medicalrecords
			WHERE CLINICID=? AND MONTH(CHECKUPDATE)=? AND YEAR(CHECKUPDATE)=? AND CANCELLED = 'N'
			GROUP by DATE(CHECKUPDATE)
			ORDER by CHECKUPDATE ASC ",
			array(
				$this->session->CLINICID,
				$month,
				$year
			))->result();
		

		foreach ($data as $key => $v) {

			$labels[] = date('m/d/y',strtotime($v->CHECKUPDATE));
			$served[] = $v->served;
			$services[] = $v->servicesAmt;
			$discounts[] = $v->discountAmt;
			$hmo[] = $v->hmoAmt;
			$cash[] = $v->cashAmt;
			$collectables[] = $v->servicesAmt - $v->discountAmt;
		}

		
		return array(
			'labels'=>$labels,
			'served'=>$served,
			'services'=>$services,
			'discounts'=>$discounts,
			'hmo' =>$hmo,
			'cash'=>$cash,
			'collectables'=>$collectables
		);

	}

}

?>