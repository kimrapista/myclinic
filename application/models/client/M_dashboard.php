<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_dashboard extends CI_Model
{
	
   function __construct(){ $this->load->database(); }
   
   public function Summary(){

      $newPat = $this->db->query("SELECT COUNT(ID) as TOTAL,
            SUM(IF( UPPER(TRIM(SEX)) = 'MALE',1,0)) AS NEWMALE,
            SUM(IF( UPPER(TRIM(SEX)) = 'FEMALE',1,0)) AS NEWFEMALE
			FROM patients 
			where CLINICID=? AND DATE(DATEREG)=? AND CANCELLED='N' 
         LIMIT 1",array(
            $this->session->CLINICID, 
            date('Y-m-d',time())
            ))->row();
         
      $monthPat = $this->db->query("SELECT COUNT(ID) as MONTHPAT 
			FROM patients 
			WHERE CLINICID=? AND MONTH(DATEREG)=? AND YEAR(DATEREG)=? AND CANCELLED='N' 
         LIMIT 1",array(
            $this->session->CLINICID,
            date('m',time()),
            date('Y',time())
            ))->row();

		$totalPat = $this->db->query("SELECT COUNT(ID) as TOTAL 
			FROM patients 
			WHERE CLINICID=? AND CANCELLED='N' 
         LIMIT 1",array($this->session->CLINICID))->row();


      $todayAppoint = $this->db->query("SELECT COUNT(CHECKUPDATE) as TOTAL
         FROM medicalrecords
         WHERE APPOINTMENT='Y' AND DATE(APPOINTMENTDATE) = ? AND CLINICID=? AND CANCELLED='N'
         LIMIT 1",array(date('Y-m-d',time()),$this->session->CLINICID))->row();

      $monthAppoint = $this->db->query("SELECT COUNT(CHECKUPDATE) as TOTAL
         FROM medicalrecords
         WHERE CLINICID=? AND APPOINTMENT='Y' AND DATE(APPOINTMENTDATE) > ? AND MONTH(APPOINTMENTDATE)=? AND YEAR(APPOINTMENTDATE)=? AND  CANCELLED='N'
         LIMIT 1",array(
            $this->session->CLINICID,
            date('Y-m-d',time()),
            date('m',time()),
            date('Y',time()) 
            ))->row();

      $totalAppoint = $this->db->query("SELECT COUNT(CHECKUPDATE) as TOTAL
         FROM medicalrecords
         WHERE CLINICID=? AND APPOINTMENT='Y' AND DATE(APPOINTMENTDATE) > ? AND  CANCELLED='N'
         LIMIT 1",array(
            $this->session->CLINICID,
            date('Y-m-d',time())
            ))->row();

      
      $todayMR = $this->db->query("SELECT COUNT(ID) as TOTAL, SUM(GROSSAMOUNT) as GROSSAMOUNT, SUM(DISCOUNTAMOUNT) as DISCOUNTAMOUNT, SUM(NETPAYABLES) as NETPAYABLES,
               SUM(IF( HMOID > 0, 1, 0)) AS TODAYHMO
			FROM medicalrecords MR
			WHERE  CLINICID=? AND DATE(CHECKUPDATE)=? AND  CANCELLED='N'
			LIMIT 1",array(
            $this->session->CLINICID,
            date('Y-m-d',time())
            ))->row();
		
		$monthMR = $this->db->query("SELECT COUNT(ID) as TOTAL, SUM(GROSSAMOUNT) as GROSSAMOUNT, SUM(DISCOUNTAMOUNT) as DISCOUNTAMOUNT, SUM(NETPAYABLES) as NETPAYABLES,
            SUM(IF( HMOID > 0, 1, 0)) AS MONTHHMO
			FROM medicalrecords MR
			WHERE  CLINICID=? AND MONTH(CHECKUPDATE)=? AND YEAR(CHECKUPDATE)=? AND  CANCELLED='N'
			LIMIT 1",array(
            $this->session->CLINICID,
            date('m',time()),
            date('Y',time())            
            ))->row();        
      
      return array(
         'NEWPAT' => $newPat->TOTAL,
         'NEWMALE' => $newPat->NEWMALE,
         'NEWFEMALE' => $newPat->NEWFEMALE,
         'MONTHPAT' => $monthPat->MONTHPAT,
         'TOTALPAT' => $totalPat->TOTAL,
         'TODAYAPPOINT' => $todayAppoint->TOTAL,
         'MONTHAPPOINT' =>$monthAppoint->TOTAL,
         'TOTALAPPOINT' => $totalAppoint->TOTAL,
         'TODAYMR'=> $todayMR->TOTAL,
         'TODAYHMO'=> $todayMR->TODAYHMO,
         'GROSSAMOUNT' => $todayMR->GROSSAMOUNT,
         'DISCOUNTAMOUNT' => $todayMR->DISCOUNTAMOUNT,
         'NETPAYABLES' => $todayMR->NETPAYABLES,
         'MONTHMR'=> $monthMR->TOTAL,
         'MONTHHMO'=> $monthMR->MONTHHMO,
         'MONTHGROSSAMOUNT' => $monthMR->GROSSAMOUNT,
         'MONTHDISCOUNTAMOUNT' => $monthMR->DISCOUNTAMOUNT,
         'MONTHNETPAYABLES' => $monthMR->NETPAYABLES
      );

   }

   public function Patients_Chart(){

      $sql = $this->db->query("SELECT P.DATEREG, COUNT(P.ID) AS TOTAL
         FROM patients P 
         WHERE P.CLINICID =? 
         AND P.DATEREG BETWEEN ? AND ? 
         AND P.CANCELLED = 'N'
         GROUP BY DATE(P.DATEREG)
         HAVING TOTAL > 0
         ORDER BY P.DATEREG "
         ,array(
            $this->session->CLINICID,
            date('Y-m-d',strtotime("-1 month", time())),
            date('Y-m-d', time())
         ))->result();

      return $sql;
   }


   public function Month_Net_Chart(){

      // date('Y-m-d',strtotime("-2 month", time())),
      $sql = $this->db->query("SELECT M.CHECKUPDATE, SUM(M.NETPAYABLES) AS TOTALNET
         FROM medicalrecords M 
         WHERE M.CLINICID =? 
         AND M.CHECKUPDATE BETWEEN ? AND ? 
         AND M.CANCELLED = 'N'
         GROUP BY DATE(M.CHECKUPDATE)
         HAVING TOTALNET > 0
         ORDER BY M.CHECKUPDATE "
         ,array(
            $this->session->CLINICID,
            date('Y-m-d',strtotime("-3 month", time())),
            date('Y-m-d', time())
         ))->result();

      return $sql;
   }


   public function Month_Served_Chart(){

      // date('Y-m-d',strtotime("-1 month", time())),
      $sql = $this->db->query("SELECT M.CHECKUPDATE, COUNT(M.ID) AS TOTAL
         FROM medicalrecords M 
         WHERE M.CLINICID =? 
         AND M.CHECKUPDATE BETWEEN ? AND ? 
         AND M.CANCELLED = 'N'
         GROUP BY DATE(M.CHECKUPDATE)
         HAVING TOTAL > 0
         ORDER BY M.CHECKUPDATE "
         ,array(
            $this->session->CLINICID,
            date('Y-m-d',strtotime("-2 month", time())),
            date('Y-m-d', time())
         ))->result();

      return $sql;
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