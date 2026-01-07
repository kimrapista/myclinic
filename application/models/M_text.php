<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_text extends CI_Model
{

	function __construct(){ 
		$this->load->database(); 
	}


	public function Patient_Appointment($MRID = NULL){ 

		if( $MRID == NULL ){
			$_POST += json_decode(file_get_contents('php://input'), true);
			$MRID = $_POST['MRID'];
		}

		$data = array(
			'err' => '',
			'suc' => ''
		);


		$sql = $this->db->query("SELECT M.ID, M.CLINICID, P.FIRSTNAME,P.MIDDLENAME,P.LASTNAME,P.MOBILENO, 
			M.CHECKUPDATE, M.APPOINTMENTDATE, M.APPOINTMENTDESCRIPTION,
			S.NAME AS SUBCLINICNAME, C.CLINICNAME, C.BLAST, S.LOCATION

			FROM patients P 
			INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
			INNER JOIN clinics C ON C.ID = M.CLINICID
			LEFT JOIN subclinic S ON S.ID = M.APPOINTMENTSUBCLINICID
			WHERE M.ID =? AND M.APPOINTMENT='Y' AND DATE(M.APPOINTMENTDATE) >= DATE(?)
			LIMIT 1",array($MRID, date('Y-m-d',time())))->row();

		if( $sql ){

			if( $sql->BLAST == 'Y' ){			

				$sql->MOBILENO = preg_replace('/\D/', '', $sql->MOBILENO);

				if( strlen($sql->MOBILENO) == 11 ){

					$msg = "Good day! Mr/Ms ".trim($sql->LASTNAME)
					.", Reminding you of your appointment on ".date('M d,Y',strtotime($sql->APPOINTMENTDATE))
					." at ".trim($sql->CLINICNAME).", ".trim($sql->SUBCLINICNAME).' '.trim($sql->LOCATION);

					if( ! empty($sql->APPOINTMENTDESCRIPTION)   ){
						$msg .= "\n\rPlease bring ".trim($sql->APPOINTMENTDESCRIPTION);
					}


					if( $sql->CLINICID == 1 ){
						$msg .= "\n\rFor other information please contact Rose: 09177003391";
					}
					else if( $sql->CLINICID == 8 ){
						$msg .= "\n\rFor any concerns, Please text the official clinic number 09171790438";
					}
					
					$msg .= "\n\r\n\r[System generated]";


					if( date('Y-m-d', strtotime($sql->APPOINTMENTDATE)) == date('Y-m-d',time()) ){

						$dateAppoint = date('Y-m-d', strtotime($sql->APPOINTMENTDATE));
					}
					else{
						$dateAppoint = date('Y-m-d', strtotime('-1 day', strtotime($sql->APPOINTMENTDATE)));
					}



					$sms = array(
						'CLIENT' 		=> 'CLINIC',
						'TITLE'			=> 'MEDICAL RECORD',
						'HEADERID'		=> $sql->ID,
						'MOBILENO' 		=> $sql->MOBILENO, 
						'BODY'			=> $msg,
						'DATETOPROCESS' => $dateAppoint,
						'DATECREATED'	=> date('Y-m-d H:i:s', time()),
						'STATUS'    	=> 'QUEUE',
						'PRIORITYLEVEL'=> 2
					);

					// use key 'http' even if you send the request to https://...

					$options = array(
						'http' => array(
							'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
							'method'  => 'POST',
							'content' => http_build_query($sms)
						)
					); 


					if( @fsockopen("www.example.com",80) ){


						$url = 'http://sms.solarestech.com/api/insert';


						$context  = stream_context_create($options);
						$result = file_get_contents($url, false, $context);

						if ($result === FALSE) { 
							$data['err'] .= 'error';
						}
						else{
							$data['suc'] .= 'success';
						}
					}
					else{
						$data['err'] .= 'error';
					}
				}
			}
		}

		return $data;
	}



	public function Patient_Rescheduled(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data = array(
			'err' => '',
			'suc' => ''
		);

		$sql = $this->db->query("SELECT MR.ID
			FROM sms_rescheduled S
			INNER JOIN patients P ON P.ID = S.PATIENTID
			INNER JOIN medicalrecords MR  ON MR.PATIENTID = P.ID

			WHERE S.ID=? AND DATE(MR.APPOINTMENTDATE) >= ? ", array(
				$_POST['ID'],
				date('Y-m-d', time())
			))->row();

		if( $sql ){
			$this->Patient_Appointment($sql->ID);
		}


		$ID = $_POST['ID'];
		$TITLE = $_POST['TITLE'];
		$MOBILENO = $_POST['MOBILENO'];
		$MESSAGE = $_POST['MESSAGE'];
		

		$MOBILENO = preg_replace('/\D/', '', $MOBILENO);

		if( strlen($MOBILENO) == 11 ){

			$sms = array(
				'CLIENT' 		=> 'CLINIC',
				'TITLE'			=> $TITLE,
				'HEADERID'		=> $ID,
				'MOBILENO' 		=> $MOBILENO, 
				'BODY'			=> $MESSAGE,
				'DATETOPROCESS' => date('Y-m-d H:i:s', time()),
				'DATECREATED'	=> date('Y-m-d H:i:s', time()),
				'STATUS'    	=> 'QUEUE',
				'PRIORITYLEVEL' => 1
			);


			// use key 'http' even if you send the request to https://...

			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($sms)
				)
			); 


			if( @fsockopen("www.example.com",80) ){

				$url = 'http://sms.solarestech.com/api/insert';

				$context  = stream_context_create($options);

				$result = file_get_contents($url, false, $context);


				if ($result === FALSE) { 
					$data['err'] .= 'SEND FAILED';
				}
				else{
					$data['suc'] .= 'SUCCESS';
				}
			}
			else{
				$data['err'] .= 'SEND FAILED';
			}
		}
		else{
			$data['err'] .= 'INCORRECT MOBILE NUMBER';
		}

		return $data;
	}


	public function Online_Appointment_Code($ID){
		

		$sql = $this->db->query("SELECT P.ID, P.VERIFIED, P.VERIFIEDCODE, P.MOBILENO,
		 		S.USERID, U.LINK

				FROM schedules S 
				INNER JOIN pre_appoint P ON P.SCHEDULEID = S.ID
				INNER JOIN users U ON U.ID = S.USERID
				WHERE P.ID=? AND P.CANCELLED='N' ",array($ID))->row();		

		if( $sql ){

			$sql->MOBILENO = preg_replace('/\D/', '', $sql->MOBILENO);

			if( strlen($sql->MOBILENO) == 11 ){

				$link = base_url((is_null($sql->LINK) || empty($sql->LINK) ? $sql->USERID : $sql->LINK));

				$sms = array(
					'CLIENT' 		=> 'CLINIC',
					'TITLE'			=> 'ONLINE APPOINTMENT CODE_'.date('Y-m-d_H_i_s',time()),
					'HEADERID'		=> $sql->ID,
					'MOBILENO' 		=> $sql->MOBILENO, 
					'BODY'			=> "Good day! Your verification Code:".$sql->VERIFIEDCODE.". Visit this link to confirm your code:\r\n".$link,
					'DATETOPROCESS' => date('Y-m-d H:i:s', time()),
					'DATECREATED'	=> date('Y-m-d H:i:s', time()),
					'STATUS'    	=> 'QUEUE',
					'PRIORITYLEVEL' => 1
				);


				// use key 'http' even if you send the request to https://...

				$options = array(
					'http' => array(
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($sms)
					)
				); 

				if( @fsockopen("www.example.com",80) ){

					$url = 'http://sms.solarestech.com/api/insert';


					$context  = stream_context_create($options);
					$result = file_get_contents($url, false, $context);

					if ($result === FALSE) { 
						// failed send
					}
				}
			}
		}
	}




	public function Clinic_Appointment_Reminders(){

		$sql = $this->db->query("SELECT * FROM clinics WHERE CANCELLED='N'")->result();


		foreach ($sql as $key => $value) {
			
			$mr = $this->db->query("SELECT COUNT(ID) as TOTAL_APPOINTMENT
				FROM medicalrecords 
				WHERE CLINICID=? AND APPOINTMENT='Y' AND DATE(APPOINTMENTDATE)=? 
				AND CANCELLED='N'",array($value->ID, date('Y-m-d',strtotime('+1 day'))))->row();


			$mobiles = explode(',', $value->SMSNOS);

			foreach ($mobiles as $key1 => $value1) {

				$MOBILENO = trim(preg_replace('/\D/', '', $value1));

				if( !empty($MOBILENO) ){

					if( $mr ){
						$total = $mr->TOTAL_APPOINTMENT;
					}
					else{
						$total = 0;
					}
					
					$msg = "There are ".$total." expected appointments tomorrow for ". $value->CLINICNAME ." clinic.";
		
					
					$sms = array(
						'CLIENT' 		=> 'CLINIC',
						'TITLE'			=> 'CLINIC APPOINTMENT REMINDERS '.date('Y-m-d_H_i_s',time()) . $key1,
						'HEADERID'		=> $value->ID,
						'MOBILENO' 		=> $MOBILENO, 
						'BODY'			=> $msg,
						'DATETOPROCESS' => date('Y-m-d H:i:s', time()),
						'DATECREATED'	=> date('Y-m-d H:i:s', time()),
						'STATUS'    	=> 'QUEUE',
						'PRIORITYLEVEL' => 2
					);
	
	
					// use key 'http' even if you send the request to https://...
	
					$options = array(
						'http' => array(
							'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
							'method'  => 'POST',
							'content' => http_build_query($sms)
						)
					); 
	
					if( @fsockopen("www.example.com",80) ){
	
						$url = 'http://sms.solarestech.com/api/insert';
	
	
						$context  = stream_context_create($options);
						$result = file_get_contents($url, false, $context);
	
						if ($result === FALSE) { 
							// failed send
						}
					}
				}
			}
		}

	}


}
?>