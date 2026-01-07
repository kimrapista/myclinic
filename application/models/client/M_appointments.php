<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_appointments extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	public function Search_Appointments()
	{

		$_POST += json_decode(file_get_contents('php://input'), true);

        $DATEFROM = date('Y-m-d',strtotime($_POST['DATEFROM']));
        $DATETO = date('Y-m-d',strtotime($_POST['DATETO']));

        $SEARCH = trim(preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['SEARCH']));

        $FROM = $_POST['FROM'];
        $TO = $_POST['TO'];


        $sql = $this->db->query("SELECT MR.ID, MR.PATIENTID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, 
            MR.CHECKUPDATE, MR.AGE, MR.CHEIFCOMPLAINT, MR.FINDINGS, MR.DIAGNOSIS, 
            MR.APPOINTMENT, MR.APPOINTMENTDATE, MR.APPOINTMENTDESCRIPTION, 
            U.NAME AS CREATEDNAME, 
            H.NAME AS HMONAME,
            S.NAME AS FROMCLINIC, 
            S1.NAME AS APPOINTCLINIC

            FROM patients P 
            INNER JOIN medicalrecords MR  ON MR.PATIENTID = P.ID
            INNER JOIN subclinic S ON S.ID = MR.SUBCLINICID
            LEFT JOIN users U ON U.ID = MR.CREATEDBY
            LEFT JOIN subclinic S1 ON S1.ID = MR.APPOINTMENTSUBCLINICID
            LEFT JOIN hmo H ON H.ID = MR.HMOID
            WHERE MR.CLINICID = ?
            AND ( concat(P.FIRSTNAME,' ',P.LASTNAME) like ? OR concat(P.LASTNAME,' ',P.FIRSTNAME) like ?)
            AND APPOINTMENT = 'Y'
            AND DATE(MR.APPOINTMENTDATE) BETWEEN ? AND ?
            AND P.CANCELLED = 'N' 
            AND MR.CANCELLED = 'N'
            ORDER BY MR.APPOINTMENTDATE DESC, MR.CREATEDTIME DESC
            LIMIT ?,? ",
            array(
                $this->session->CLINICID, 
                '%'.$SEARCH.'%',
                '%'.$SEARCH.'%',
                $DATEFROM,
                $DATETO,
                $FROM,
                $TO
            ))->result();

        foreach ($sql as $key => $v) {
            $v->TOTAL_LAB = $this->db->query("SELECT COUNT(ML.ID) AS CNT
                FROM mr_laboratory ML 
                INNER JOIN laboratory L ON L.ID = ML.LABORATORYID
                WHERE ML.MEDICALRECORDID = ? AND ML.CANCELLED='N' ",array($v->ID))->row()->CNT;
        }

        return $sql;
	}


}




?>