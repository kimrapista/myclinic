<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_appointments extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	public function Index()
	{

		$_POST += json_decode(file_get_contents('php://input'), true);

		$search = preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['text']);
		$dateFrom = date('Y-m-d',strtotime($_POST['dateFrom']));
		$dateTo = date('Y-m-d',strtotime($_POST['dateTo']));

		
		$sql = $this->db->query("SELECT MR.ID, MR.PATIENTID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, P.SEX, 
			MR.CHECKUPDATE, MR.AGE, MR.CHEIFCOMPLAINT, MR.FINDINGS, MR.DIAGNOSIS, MR.APPOINTMENT,MR.APPOINTMENTDATE,MR.APPOINTMENTDESCRIPTION, 
			U.NAME AS CREATEDNAME, S.NAME AS FROMCLINIC, S1.NAME AS APPOINTCLINIC

			FROM patients P 
			INNER JOIN medicalrecords MR  ON MR.PATIENTID = P.ID
			INNER JOIN users U ON U.ID = MR.CREATEDBY
			LEFT JOIN subclinic S ON S.ID = MR.SUBCLINICID
			LEFT JOIN subclinic S1 ON S1.ID = MR.APPOINTMENTSUBCLINICID
			WHERE MR.CLINICID = ?
			AND ( concat(P.FIRSTNAME,' ',P.MIDDLENAME,' ',P.LASTNAME) like ? 
			OR concat(P.LASTNAME,' ',P.FIRSTNAME,' ',P.MIDDLENAME) like ?
			OR concat(P.FIRSTNAME,' ',P.LASTNAME) like ? )
			AND APPOINTMENT = 'Y'
			AND DATE(MR.APPOINTMENTDATE) between ? AND ?
			AND P.CANCELLED = 'N'
			AND MR.CANCELLED = 'N'
			ORDER BY MR.APPOINTMENTDATE, MR.CHECKUPDATE, LASTNAME ASC", 
			array(
				$this->session->CLINICID,
				'%'.$search.'%',
				'%'.$search.'%',
				'%'.$search.'%',
				$dateFrom,
				$dateTo
			))->result_array();


		return $sql;
	}


}




?>