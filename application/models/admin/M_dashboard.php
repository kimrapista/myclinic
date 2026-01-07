<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_dashboard extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	public function Clinics(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$DATEFROM = date('Y-m-d',strtotime($_POST['DATEFROM']));
		$DATETO = date('Y-m-d',strtotime($_POST['DATETO']));

		$sql = $this->db->query("SELECT MR.CLINICID, COUNT(MR.ID) AS SERVED, SUM(MR.NETPAYABLES) AS NETPAYABLES, 
			C.CLINICNAME
			
			FROM clinics C 
			INNER JOIN medicalrecords MR ON MR.CLINICID = C.ID 
			WHERE DATE(MR.CHECKUPDATE) BETWEEN ? AND ? AND MR.CANCELLED='N'
			GROUP BY C.ID ",  array( 
				date('Y-m-d', strtotime($DATEFROM)),
				date('Y-m-d', strtotime($DATETO)) 
			))->result();


		return $sql;

	}


	public function Doctors(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$DATEFROM = date('Y-m-d',strtotime($_POST['DATEFROM']));
		$DATETO = date('Y-m-d',strtotime($_POST['DATETO']));

		$sql = $this->db->query("SELECT MR.CLINICID, COUNT(MR.ID) AS SERVED, SUM(MR.NETPAYABLES) AS NETPAYABLES,
			C.CLINICNAME , 
			U.NAME AS DOCTORNAME
			
			FROM clinics C 
			INNER JOIN medicalrecords MR ON MR.CLINICID = C.ID 
			LEFT JOIN users U ON U.ID = MR.CREATEDBY
			WHERE DATE(MR.CHECKUPDATE) BETWEEN ? AND ? AND MR.CANCELLED='N'
			GROUP BY U.ID,C.ID",  array( 
				date('Y-m-d', strtotime($DATEFROM)),
				date('Y-m-d', strtotime($DATETO)) 
			))->result();


		return $sql;

	}





}

?>