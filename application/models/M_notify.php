<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_notify extends CI_Model
{
	
   function __construct(){ $this->load->database(); }
   

   
   public function Today_Patients(){


      // from appointments
      // $app = $this->db->query("SELECT P.ID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, MR.ID AS MRID
      //    FROM patients P
		// 	INNER JOIN medicalrecords MR  ON MR.PATIENTID = P.ID
		// 	WHERE MR.CLINICID = ?
		// 	AND MR.APPOINTMENT = 'Y' 
		// 	AND DATE(MR.APPOINTMENTDATE) = ?
      //    AND MR.CANCELLED = 'N' 
      //    GROUP BY P.ID ",
		// 	array(
		// 		$this->session->CLINICID,
		// 		date('Y-m-d', time())
      //    ))->result();

      $app = $this->db->query("SELECT P.ID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, MR.ID AS MRID
         FROM patients P
			INNER JOIN medicalrecords MR  ON MR.PATIENTID = P.ID
			WHERE MR.CLINICID = ?
			AND MR.APPOINTMENT = 'Y' 
			AND DATE(MR.APPOINTMENTDATE) = ?
         AND MR.CANCELLED = 'N' 
         GROUP BY P.ID ",
			array(
				$this->session->CLINICID,
				date('Y-m-d', time())
         ))->result();

		$appNow = $this->db->query("SELECT ID as MRID, PATIENTID
         FROM medicalrecords  
			WHERE CLINICID = ?
			AND DATE(CHECKUPDATE) = ?
         AND CANCELLED = 'N' ",
			array(
				$this->session->CLINICID,
				date('Y-m-d', time())
         ))->result();
      
		foreach ($app as $key => $value) {
			$app[$key]->MRID = NULL;

			foreach ($appNow as $key1 => $value1) {
				if( $value1->PATIENTID == $value->ID ){
					$app[$key]->MRID = $value1->MRID;
				}
			}
		}
 
      
         
      // online schedule
      // $sch = $this->db->query("SELECT P.ID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, MR.ID AS MRID
      //    FROM patients P 
      //    INNER JOIN pre_appoint PA ON PA.PATIENTID = P.ID
      //    LEFT JOIN medicalrecords MR ON MR.PATIENTID = P.ID AND MR.PATIENTID = P.ID AND  DATE(MR.CHECKUPDATE) = ? AND MR.CANCELLED='N'
		// 	LEFT JOIN schedules S ON S.ID = PA.SCHEDULEID

		// 	WHERE P.CLINICID=? 
		// 	AND (DATE(S.SDATETIME) = ? OR DATE(PA.RESCHEDULETIME) = ?)
      //    AND PA.ACKNOWLEDGED = 'Y'
      //    AND PA.BLOCKED = 'N'
      //    AND PA.CANCELLED = 'N' 
      //    GROUP BY P.ID ",
		// 	array(
      //       date('Y-m-d', time()),
		// 		$this->session->CLINICID,
      //       date('Y-m-d', time()),
      //       date('Y-m-d', time())
      //    ))->result();
         
      $sch = array();  

         
      // new patients
      $pat = $this->db->query("SELECT P.ID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, MR.ID AS MRID
         FROM patients P
         LEFT JOIN medicalrecords MR ON MR.PATIENTID = P.ID AND DATE(MR.CHECKUPDATE) = ? AND MR.CANCELLED='N'
         WHERE P.CLINICID=? 
			AND DATE(P.DATEREG) = ?
         AND P.CANCELLED = 'N' 
         GROUP BY P.ID",
			array(
            date('Y-m-d', time()),
				$this->session->CLINICID,
            date('Y-m-d', time())
         ))->result();
      

      // REVISIT
      $rev = $this->db->query("SELECT P.ID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, MR.ID AS MRID
         FROM patients P
         INNER JOIN medicalrecords MR ON MR.PATIENTID = P.ID  
         WHERE P.CLINICID=? 
         AND DATE(MR.CHECKUPDATE) = ?
         AND DATE(P.DATEREG) != ? 
         AND P.CANCELLED = 'N' 
         AND MR.CANCELLED='N' ",
         array(
            $this->session->CLINICID,
            date('Y-m-d', time()),
            date('Y-m-d', time())
         ))->result();


      return array(
         'APPOINTMENTS' => $app,
         'SCHEDULES' => $sch,
         'NEWPATIENTS' => $pat,
         'REVISITS' => $rev
      );
   }


}

?>