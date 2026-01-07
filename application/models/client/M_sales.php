<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_sales extends CI_Model
{
	
	function __construct(){ $this->load->database(); }

	public function Clinics(){

    	$_POST += json_decode(file_get_contents('php://input'), true);

    	$DATEFROM = date('Y-m-d',strtotime($_POST['DATEFROM']));
		$DATETO = date('Y-m-d',strtotime($_POST['DATETO']));

		
		$inject = ($this->session->POSITION == 'BRANCH ASSISTANT' || $this->session->POSITION == 'BRANCH ADMINISTRATOR') ? "  " : " AND M.CREATEDBY= ".$this->session->USERID;

    	$sql = $this->db->query("SELECT M.SUBCLINICID, S.NAME, 
				COUNT(M.ID) AS RECORDS, 
				SUM(M.GROSSAMOUNT) AS GROSSAMOUNT,
				SUM(M.DISCOUNTAMOUNT) AS DISCOUNTAMOUNT,
				SUM(M.NETPAYABLES) AS NETPAYABLES
			
			FROM medicalrecords M 
			INNER JOIN subclinic S ON S.ID = M.SUBCLINICID
			WHERE M.CLINICID = ? 
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
			AND M.CANCELLED = 'N'
			$inject
			GROUP BY S.ID
			ORDER BY NETPAYABLES DESC, S.NAME ", 
			array(
				$this->session->CLINICID,
				$DATEFROM, 
				$DATETO
			))->result();

		return $sql; 
	}


	public function HMO(){

    	$_POST += json_decode(file_get_contents('php://input'), true);

    	$DATEFROM = date('Y-m-d',strtotime($_POST['DATEFROM']));
		$DATETO = date('Y-m-d',strtotime($_POST['DATETO']));
		
		$inject = ($this->session->POSITION == 'BRANCH ASSISTANT' || $this->session->POSITION == 'BRANCH ADMINISTRATOR') ? "  " : " AND M.CREATEDBY= ".$this->session->USERID;

    	$sql = $this->db->query("SELECT H.ID, H.NAME,
				COUNT(M.ID) AS RECORDS,
				SUM(IF(M.HMORECEIVED = 'Y', 1, 0)) AS RECEIVED_COUNT, 
				SUM(IF(M.HMORECEIVED != 'Y', 1, 0)) AS NOT_RECEIVED_COUNT 
			
			FROM medicalrecords M 
			INNER JOIN hmo H ON H.ID = M.HMOID
			WHERE M.CLINICID = ? 
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
			AND M.CANCELLED = 'N'
			$inject
			GROUP BY H.ID
			ORDER BY RECORDS DESC, H.NAME ", 
			array(
				$this->session->CLINICID,
				$DATEFROM,
				$DATETO
			))->result();

    	return $sql;
	}


	public function PhilHealth(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$DATEFROM = date('Y-m-d',strtotime($_POST['DATEFROM']));
	  	$DATETO = date('Y-m-d',strtotime($_POST['DATETO']));
	  
	  	$inject = ($this->session->POSITION == 'BRANCH ASSISTANT' || $this->session->POSITION == 'BRANCH ADMINISTRATOR') ? "  " : " AND M.CREATEDBY= ".$this->session->USERID;

		$sql = $this->db->query("SELECT CONCAT(P.LASTNAME,', ',P.FIRSTNAME,' ',P.MIDDLENAME) AS NAME,
			M.CHECKUPDATE, M.PHILHEALTH, M.PHILHEALTHRECEIVED, M.PHILHEALTHCHEQUENO, M.PHILHEALTHCHEQUEDATE, M.PHILHEALTHAMOUNT
			
		  FROM patients P 
		  INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
		  WHERE M.CLINICID = ? 
		  AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
		  AND M.CANCELLED = 'N' AND M.PHILHEALTH = 'Y'
		  $inject
		  ORDER BY M.CHECKUPDATE, P.LASTNAME", 
		  array(
			  $this->session->CLINICID,
			  $DATEFROM,
			  $DATETO
		  ))->result();

		return $sql;
  	}
	

	public function Services(){

      	$_POST += json_decode(file_get_contents('php://input'), true);

      	$DATEFROM = date('Y-m-d',strtotime($_POST['DATEFROM']));
		$DATETO = date('Y-m-d',strtotime($_POST['DATETO']));
		
		$inject = ($this->session->POSITION == 'BRANCH ASSISTANT' || $this->session->POSITION == 'BRANCH ADMINISTRATOR') ? "  " : " AND M.CREATEDBY= ".$this->session->USERID;

      	$sql = $this->db->query("SELECT M.SUBCLINICID, S.NAME, SUM(MS.QUANTITY) AS QUANTITY, SUM(MS.AMOUNT) AS AMOUNT
			FROM medicalrecords M 
			INNER JOIN  mr_services MS ON MS.MEDICALRECORDID = M.ID
			INNER JOIN services S ON S.ID = MS.SERVICEID
			INNER JOIN subclinic SC ON SC.ID = M.SUBCLINICID
			WHERE M.CLINICID = ? 
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
			AND M.CANCELLED = 'N'
			AND MS.CANCELLED = 'N'
			$inject
			GROUP BY M.SUBCLINICID, S.NAME
			ORDER BY SC.NAME, AMOUNT DESC, S.NAME", 
			array(
				$this->session->CLINICID,
				$DATEFROM,
				$DATETO
			))->result();

      	return $sql;
	}
	

	public function Patients(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$DATEFROM = date('Y-m-d',strtotime($_POST['DATEFROM']));
	  $DATETO = date('Y-m-d',strtotime($_POST['DATETO']));
	  
	  $FROM = $_POST['FROM'];
	  $TO = $_POST['TO'];
	  
	  $inject = ($this->session->POSITION == 'BRANCH ASSISTANT' || $this->session->POSITION == 'BRANCH ADMINISTRATOR') ? "  " : " AND M.CREATEDBY= ".$this->session->USERID;

		$sql = $this->db->query("SELECT M.ID,M.PATIENTID, M.CHECKUPDATE, M.NETPAYABLES, M.PAIDAMOUNT, M.CHEQUEAMOUNT, M.HMOAMOUNT, M.PHILHEALTHAMOUNT, M.PHILHEALTH,
		  CONCAT(P.LASTNAME,', ',P.FIRSTNAME,' ',P.MIDDLENAME) AS NAME,
		  S.NAME AS FROMCLINIC,
		  H.NAME AS HMONAME

		  FROM patients P 
		  INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
		  INNER JOIN subclinic S ON S.ID = M.SUBCLINICID
		  LEFT JOIN hmo H ON H.ID = M.HMOID
		  WHERE M.CLINICID = ? 
		  AND DATE(M.CHECKUPDATE) BETWEEN ? AND ? AND (M.HMOID > 0 OR M.PHILHEALTH = 'Y'  OR M.NETPAYABLES > 0)
		  AND M.CANCELLED = 'N'
		  $inject
		  ORDER BY M.CHECKUPDATE DESC, P.LASTNAME, M.NETPAYABLES 
		  LIMIT ?,?",  
		  array(
			  $this->session->CLINICID,
			  $DATEFROM,
			  $DATETO,
			  $FROM,
			  $TO
		  ))->result();

		return $sql;
	}

  
  	public function Patients_No_Charges(){

	  	$_POST += json_decode(file_get_contents('php://input'), true);

	  	$DATEFROM = date('Y-m-d',strtotime($_POST['DATEFROM']));
		$DATETO = date('Y-m-d',strtotime($_POST['DATETO']));
	
	
		$inject = ($this->session->POSITION == 'BRANCH ASSISTANT' || $this->session->POSITION == 'BRANCH ADMINISTRATOR') ? "  " : " AND M.CREATEDBY= ".$this->session->USERID;

	  	$sql = $this->db->query("SELECT M.ID, M.PATIENTID, M.CHECKUPDATE,			
			CONCAT(P.LASTNAME,', ',P.FIRSTNAME,' ',P.MIDDLENAME) AS NAME,
			S.NAME AS FROMCLINIC

			FROM patients P 
			INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
			INNER JOIN subclinic S ON S.ID = M.SUBCLINICID
			WHERE M.CLINICID = ? 
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ? AND (M.HMOID=0 OR M.HMOID IS NULL) AND (M.PHILHEALTH = 'N' OR M.PHILHEALTH IS NULL) AND (M.NETPAYABLES=0 OR M.NETPAYABLES IS NULL) AND (M.AMOUNT =0 OR M.AMOUNT IS NULL)
			AND M.CANCELLED = 'N'
			$inject
			ORDER BY M.CHECKUPDATE DESC, P.LASTNAME ",  
			array(
				$this->session->CLINICID,
				$DATEFROM,
				$DATETO
			))->result();

	  return $sql;
  	}



	public function Index() {

		$_POST += json_decode(file_get_contents('php://input'), true);

		$search = preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['text']);
		$dateFrom = date('Y-m-d',strtotime($_POST['dateFrom']));
		$dateTo = date('Y-m-d',strtotime($_POST['dateTo']));

		$inject = ($this->session->POSITION == 'BRANCH ASSISTANT' || $this->session->POSITION == 'BRANCH ADMINISTRATOR') ? "  " : " AND M.CREATEDBY= ".$this->session->USERID;
      
		$SUMMARY = $this->db->query("SELECT COUNT(M.ID) AS MRCOUNT, SUM(M.NETPAYABLES) AS NETPAYABLES, S.NAME AS FROMCLINIC
			
			FROM patients P 
			INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
			INNER JOIN subclinic S ON S.ID = M.SUBCLINICID
			WHERE M.CLINICID = ? 
			AND M.CREATEDBY=?
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
			AND S.NAME LIKE ?
			AND M.CANCELLED = 'N'
			$inject
			GROUP BY S.NAME
			ORDER BY S.NAME", 
			array(
				$this->session->CLINICID,
				$this->session->userid,
				$dateFrom,
				$dateTo,
				'%'.$search.'%',
			))->result();


		$DETAIL = $this->db->query("SELECT M.ID,M.PATIENTID, M.CHECKUPDATE,M.NETPAYABLES,			
			CONCAT(P.LASTNAME,', ',P.FIRSTNAME,' ',P.MIDDLENAME) AS NAME, M.ID,M.PATIENTID,
			S.NAME AS FROMCLINIC

			FROM patients P 
			INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
			INNER JOIN subclinic S ON S.ID = M.SUBCLINICID
			WHERE M.CLINICID = ? 
			AND M.CREATEDBY=?
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
			AND S.NAME LIKE ?
			AND M.CANCELLED = 'N'
			$inject
			ORDER BY M.CHECKUPDATE, M.ID", 
			array(
				$this->session->CLINICID,
				$this->session->userid,
				$dateFrom,
				$dateTo,
				'%'.$search.'%',
			))->result();

		
		$SERVICES = $this->db->query("SELECT S.NAME, SUM(MS.QUANTITY) AS QUANTITY, SUM(MS.AMOUNT) AS AMOUNT
			FROM medicalrecords M 
			INNER JOIN  mr_services MS ON MS.MEDICALRECORDID = M.ID
			INNER JOIN services S ON S.ID = MS.SERVICEID
			INNER JOIN subclinic SC ON SC.ID = M.SUBCLINICID
			WHERE M.CLINICID = ? 
			AND M.CREATEDBY = ?
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
			AND SC.NAME LIKE ?
			AND M.CANCELLED = 'N'
			AND MS.CANCELLED = 'N'
			$inject
			GROUP BY S.NAME
			ORDER BY S.NAME", 
			array(
				$this->session->CLINICID,
				$this->session->userid,
				$dateFrom,
				$dateTo,
				'%'.$search.'%',
			))->result();
		

		return array(
			'SUMMARY' => $SUMMARY,
			'DETAIL' => $DETAIL,
			'SERVICES' => $SERVICES
		);

	}




}
?>