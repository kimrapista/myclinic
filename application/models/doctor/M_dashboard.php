<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// doctor

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
		
		$sql6 = $this->db->query("SELECT UPPER(TRIM(SEX)) as labels, COUNT(ID) AS total
					FROM patients 
					WHERE CLINICID=? AND CANCELLED='N'
					GROUP BY SEX",array($this->session->CLINICID))->result();

		$sql7 = $this->db->query("SELECT D.icd10code as ITEMCODE,  D.icd10desc AS ITEMDESCRIPTION, count(M.ID) as TOTAL
					FROM medicalrecords M 
					INNER JOIN doh_icd D ON D.icd10code = M.ICD_CODE
					WHERE M.CLINICID=? AND M.CANCELLED='N' 
					GROUP BY M.ICD_CODE",array($this->session->CLINICID))->result();

		$sql8 = $this->db->query("SELECT D.itemcode as ITEMCODE,  D.itemdescription AS ITEMDESCRIPTION, count(M.ID) as TOTAL
					FROM medicalrecords M 
					INNER JOIN ph_icd D ON D.itemcode = M.RVS_CODE
					WHERE M.CLINICID=? AND M.CANCELLED='N' 
					GROUP BY M.RVS_CODE",array($this->session->CLINICID))->result();


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
			'cashAmount' => (float)$sql5->cashAmt,
			'sex' => $sql6,
			'icd' => $sql7,
			'rvs' => $sql8
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



	public function Yearly_Info(){
			
		$labels = array();
		$netincome = array();
	

		$data = $this->db->query("SELECT MR.CHECKUPDATE, SUM(MR.NETPAYABLES) as NETPAYABLES
			From medicalrecords MR
			WHERE MR.CLINICID=? AND MR.CANCELLED = 'N'
			GROUP by MONTH(MR.CHECKUPDATE), YEAR(MR.CHECKUPDATE)
			ORDER by MR.CHECKUPDATE ASC ",
			array(
				$this->session->CLINICID
			))->result();

		// arrange date sequence and remove redundant date
		foreach ($data as $key => $v) {

			$exist = false;
			foreach ($labels as $k1 => $v1) {
				if ( $v1 == date('Y-m',strtotime($v->CHECKUPDATE)) ){ $exist = true; break; }
			}
			if( !$exist ) $labels[] = date('Y-m',strtotime($v->CHECKUPDATE));
		}

	
		sort($labels);

		foreach ($labels as $key => $v) {

	
			if (!isset($netincome[$key])) 
				$netincome[$key] = 0;

			foreach ($data as $key1 => $v1) {

				if ( $v == date('Y-m',strtotime($v1->CHECKUPDATE)) ){

					$netincome[$key] += $v1->NETPAYABLES; 
				}
			}

		}

		return array(
			'labels' => $labels,
			'netincome' => $netincome
		);

	}


	function Medical_Record(){

		$data = $this->db->query("SELECT CHECKUPDATE, count(ID) as total
			From medicalrecords
			WHERE CANCELLED = 'N' AND CLINICID=?
			GROUP BY MONTH(CHECKUPDATE),YEAR(CHECKUPDATE)
			Order by YEAR(CHECKUPDATE) ASC, MONTH(CHECKUPDATE) ASC ",array($this->session->CLINICID))->result();

		
		$labels = array();
		$values = array();

		foreach ($data as $key => $v) {
			$labels[] = date('M y',strtotime($v->CHECKUPDATE));
			$values[] = $v->total;
		}


		return array($labels,$values);
	}


	function Patient_Age(){

		
		$sql = $this->db->query("SELECT AGE , count(ID) as age_count
			FROM medicalrecords 
			WHERE CLINICID=? AND CANCELLED = 'N' AND CAST(AGE AS UNSIGNED) BETWEEN 0 AND 130
			GROUP BY CAST(AGE AS UNSIGNED)
			ORDER BY CAST(AGE AS UNSIGNED) ASC",array($this->session->CLINICID))->result();
		


		$labels = array('0-10','11-20','21-30','31-40','41-60','61-80','81+');
		$values = array(0,0,0,0,0,0,0);

		$totalAge = 0;
		foreach ($sql as $key => $v) {
			
			if( $v->AGE >= 81 ){ $values[6] += $v->age_count; }
			else if( $v->AGE >= 61 ){ $values[5] += $v->age_count; }
			else if( $v->AGE >= 41 ){ $values[4] += $v->age_count; }
			else if( $v->AGE >= 31 ){ $values[3] += $v->age_count; }
			else if( $v->AGE >= 21 ){ $values[2] += $v->age_count; }
			else if( $v->AGE >= 11 ){ $values[1] += $v->age_count; }
			else if( $v->AGE >= 0 ){ $values[0] += $v->age_count; }

			$totalAge += $v->age_count;
		}

		foreach ($values as $key => $v) {
			if( $v > 0 )
				$values[$key] = round(($v/$totalAge) *100,2); 
		}


		return array(
			'labels'=> $labels,
			'values'=> $values
		);
	}


}

?>