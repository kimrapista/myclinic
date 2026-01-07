<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// doctor

class M_sales extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	public function index()
	{

		$_POST += json_decode(file_get_contents('php://input'), true);

		$search = preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$_POST['text']);
		$dateFrom = date('Y-m-d',strtotime($_POST['dateFrom']));
		$dateTo = date('Y-m-d',strtotime($_POST['dateTo']));


	
		
		$SUMMARY = $this->db->query("SELECT COUNT(M.ID) AS MRCOUNT, SUM(M.NETPAYABLES) AS NETPAYABLES, S.NAME AS FROMCLINIC
			
			FROM patients P 
			INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
			INNER JOIN subclinic S ON S.ID = M.SUBCLINICID
			WHERE M.CLINICID = ? 
			AND M.CREATEDBY=?
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
			AND S.NAME LIKE ?
			AND M.CANCELLED = 'N'
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



	private function Fixer_Laboratory(){


		$sql = $this->db->query("SELECT M.ID, M.PATIENTID, M.TEMPPATIENTID
			FROM medicalrecords M 
			WHERE M.CLINICID=? AND M.TEMPPATIENTID > 0 AND M.PATIENTID = M.TEMPPATIENTID
			LIMIT 100 ", array($this->session->CLINICID))->result();

		$updateData = array();

		foreach ($sql as $key => $v) {
			
			$sql1 = $this->db->query("SELECT P.ID FROM patients P WHERE P.TEMPID =? LIMIT 1", array($v->TEMPPATIENTID))->row();

			if($sql1){
				$updateData[] = array('ID'=>$v->ID,'PATIENTID'=>$sql1->ID);
			}
		}

		if( count($updateData))
			$this->db->update_batch('medicalrecords',$updateData, 'ID');



		$sql = $this->db->query("SELECT M.ID , M.LABORATORY, M.XRAY, M.ECG, M.ULTRASOUND
			FROM medicalrecords  M
			WHERE M.CLINICID=? 
			AND (
			(TRIM(M.LABORATORY) != '' AND M.LABORATORY IS NOT NULL) OR   
			(TRIM(M.XRAY) != '' AND M.LABORATORY IS NOT NULL) OR  
			(TRIM(M.ECG) != '' AND M.LABORATORY IS NOT NULL) OR
			(TRIM(M.ULTRASOUND) != ''  AND M.LABORATORY IS NOT NULL)
			)
			AND 0 = (SELECT COUNT(ML.ID) AS CNT FROM mr_laboratory ML WHERE ML.MEDICALRECORDID=M.ID AND ML.CANCELLED='N' LIMIT 1 )
			LIMIT 100 " ,array($this->session->CLINICID))->result();

		$data = array();

		foreach ($sql as $key => $v) {

			$v->LABORATORY 	= is_null($v->LABORATORY) ? '' : trim($v->LABORATORY);
			$v->XRAY 		= is_null($v->XRAY) ? '' : trim($v->XRAY);
			$v->ECG 		= is_null($v->ECG) ? '' : trim($v->ECG);
			$v->ULTRASOUND 	= is_null($v->ULTRASOUND) ? '' : trim($v->ULTRASOUND);


			if(  ! empty($v->LABORATORY) ){

				array_push($data,array(
					'MEDICALRECORDID'=> $v->ID,
					'LABORATORYID' => 7,
					'TEMPLATERESULT' => $v->LABORATORY,
					'CANCELLED' => 'N',
					'CREATEDBY' => 0,
					'CREATEDTIME' => date('Y-m-d H:i:s',time())
				));
			}

			if(  ! empty($v->XRAY) || ! empty($v->ULTRASOUND) ){

				array_push($data,array(
					'MEDICALRECORDID'=> $v->ID,
					'LABORATORYID' => 11,
					'TEMPLATERESULT' => $v->XRAY."\r\n\r\n".$v->ULTRASOUND,
					'CANCELLED' => 'N',
					'CREATEDBY' => 0,
					'CREATEDTIME' => date('Y-m-d H:i:s',time())
				));
			}


			if(  ! empty($v->ECG) ){

				array_push($data,array(
					'MEDICALRECORDID'=> $v->ID,
					'LABORATORYID' => 13,
					'TEMPLATERESULT' => $v->ECG,
					'CANCELLED' => 'N',
					'CREATEDBY' => 0,
					'CREATEDTIME' => date('Y-m-d H:i:s',time())
				));
			}


		}

		if( count($data) > 0 )
			$this->db->insert_batch('mr_laboratory',$data);

	}



	private function Fixer_Netpayables(){

		// SERVICES
		$data = array();
		//AND M.GROSSAMOUNT = 0
		$q = $this->db->query("SELECT M.ID, SUM( ROUND(MS.QUANTITY * MS.UNITPRICE, 2) ) AS GROSSAMOUNT
			FROM medicalrecords M  
			INNER JOIN mr_services MS ON MS.MEDICALRECORDID = M.ID 
			INNER JOIN services S ON S.ID = MS.SERVICEID
			WHERE M.CLINICID=? AND M.CANCELLED='N' AND MS.CANCELLED='N' 
			GROUP BY M.ID" , array($this->session->CLINICID))->result();

		foreach ($q as $key => $v) {
			
			$data[] = array( 'ID' => $v->ID, 'GROSSAMOUNT'=> $v->GROSSAMOUNT );
		}

		if( count($data) > 0 )  $this->db->update_batch('medicalrecords', $data, 'ID');


		// DISCOUNTS
		$data = array();
		//AND M.DISCOUNTAMOUNT = 0
		$q = $this->db->query("SELECT M.ID, M.GROSSAMOUNT, 
			SUM(IF(D.PERCENTAGE = 'Y', D.AMOUNT,0)) AS DISCPERCENT, 
			SUM(IF(D.PERCENTAGE = 'N', D.AMOUNT,0)) AS DISCAMOUNT 

			FROM medicalrecords M  
			INNER JOIN mr_discounts DS ON DS.MEDICALRECORDID = M.ID 
			INNER JOIN discounts D ON D.ID = DS.DISCOUNTID
			WHERE M.CLINICID=? AND M.CANCELLED='N' AND DS.CANCELLED='N' 
			GROUP BY M.ID" , array($this->session->CLINICID))->result();

		foreach ($q as $key => $v) {
			
			if( $v->DISCPERCENT > 0 ){
				$discounts = round( $v->GROSSAMOUNT * ($v->DISCPERCENT/100), 2);
			} else {
				$discounts = 0;
			}

			$discounts += $v->DISCAMOUNT;

			$data[] = array( 'ID' => $v->ID, 'DISCOUNTAMOUNT'=> $discounts);
		}

		if( count($data) > 0 ) $this->db->update_batch('medicalrecords', $data, 'ID');


		// NETPAYABLES
		$data = array();
		//AND M.NETPAYABLES = 0
		$q = $this->db->query("SELECT M.ID, M.GROSSAMOUNT, M.DISCOUNTAMOUNT, M.NETPAYABLES, M.AMOUNT
			FROM medicalrecords M  
			WHERE M.CLINICID=? AND M.CANCELLED='N'  " , array($this->session->CLINICID))->result();

		foreach ($q as $key => $v) {
			
			$v->NETPAYABLES = $v->GROSSAMOUNT - $v->DISCOUNTAMOUNT;

			if( $v->NETPAYABLES == 0 && $v->AMOUNT > 0 ){
				$v->NETPAYABLES = $v->AMOUNT;
			}

			$data[] = array( 'ID' => $v->ID, 'NETPAYABLES'=> $v->NETPAYABLES);
		}

		if( count($data) > 0 )  $this->db->update_batch('medicalrecords', $data, 'ID');


		// SUBCLINIC

		$data = array();
		// AND (M.SUBCLINICID = 0 OR M.SUBCLINICID IS NULL) 
		$q = $this->db->query("SELECT M.ID, M.SUBCLINICID, S.ID AS SUBID
			FROM medicalrecords M  
			LEFT JOIN subclinic S ON S.NAME = M.BRANCH AND S.CLINICID = M.CLINICID
			WHERE M.CLINICID=? AND M.CANCELLED='N' AND (M.BRANCH IS NOT NULL OR M.BRANCH != '')" ,
			array($this->session->CLINICID))->result();


		foreach ($q as $key => $v) {

			if( $v->SUBID > 0 ){
				$data[] = array( 'ID' => $v->ID, 'SUBCLINICID'=> $v->SUBID);
			} else{
				$data[] = array( 'ID' => $v->ID, 'SUBCLINICID'=> $this->session->SUBCLINICID);
			}
		}

		if( count($data) > 0 )  $this->db->update_batch('medicalrecords', $data, 'ID');
	}


}




?>