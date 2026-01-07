<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_schedules extends CI_Model
{

	function __construct(){ $this->load->database(); }


	public function Index() {

		return array(
			'SCHEDULES' => $this->Schedule_Day(),
			'APPOINTMENTS' => $this->Appointments_Day_Summary(),
			'NOTES' => $this->Notes(),
			'NOSCHEDULES' => $this->No_Schedule_Day_Patients_Summary(),
			'CLINICS' => $this->clinic_summary()
		);
	} 
 
	private function clinic_summary(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$DATE = $_POST['monthDate'];

		$app = $this->db->query("SELECT S1.NAME AS APPOINTCLINIC, COUNT(MR.ID) AS TOTAL
			FROM medicalrecords MR 
			LEFT JOIN subclinic S1 ON S1.ID = MR.APPOINTMENTSUBCLINICID
			WHERE MR.CLINICID = ?
			AND MR.APPOINTMENT = 'Y'
			AND MONTH(APPOINTMENTDATE) = ?
			AND YEAR(APPOINTMENTDATE) = ?
			AND MR.CANCELLED = 'N' 
			GROUP BY S1.NAME",
			array(
				$this->session->CLINICID,
				date('m', strtotime($DATE)),
				date('Y', strtotime($DATE))
			))->result();

		return $app;
	}


	public function Appointments_Day_Summary(){
		
		$_POST += json_decode(file_get_contents('php://input'), true);

		$DATE = $_POST['monthDate'];

		// $inject = ($this->session->POSITION == 'BRANCH ASSISTANT') ? "  " : " AND CREATEDBY= ".$this->session->USERID;

		if( $this->session->POSITION == 'BRANCH ADMINISTRATOR' || $this->session->POSITION == 'BRANCH CONSULTANT' || $this->session->POSITION == 'BRANCH ASSISTANT' ){
			$inject = " ";
		}
		else{
			$inject = " AND CREATEDBY= ".$this->session->USERID;
		}

		$sql = $this->db->query("SELECT APPOINTMENTDATE, COUNT(ID) AS TOTAL
			FROM medicalrecords
			WHERE CLINICID=?
			AND APPOINTMENT='Y'
			AND MONTH(APPOINTMENTDATE) = ?
			AND YEAR(APPOINTMENTDATE) = ?
			AND CANCELLED='N'
			$inject
			GROUP BY APPOINTMENTDATE
			ORDER BY APPOINTMENTDATE " ,
			array(
				$this->session->CLINICID,
				date('m', strtotime($DATE)),
				date('Y', strtotime($DATE))
			))->result();
		
		return $sql;
	}



	public function Appointments_Day(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$DAY = $_POST['DayDate'];

		// $inject = ($this->session->POSITION == 'BRANCH ASSISTANT') ? "  " : " AND MR.CREATEDBY= ".$this->session->USERID;

		if( $this->session->POSITION == 'BRANCH ADMINISTRATOR' || $this->session->POSITION == 'BRANCH CONSULTANT' || $this->session->POSITION == 'BRANCH ASSISTANT' ){
			$inject = " ";
		}
		else{
			$inject = " AND MR.CREATEDBY= ".$this->session->USERID;
		}

		$app = $this->db->query("SELECT MR.ID, MR.PATIENTID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, P.MOBILENO,
            MR.CHECKUPDATE, MR.AGE, MR.CHEIFCOMPLAINT, MR.FINDINGS, MR.DIAGNOSIS,
            MR.APPOINTMENTDATE, MR.APPOINTMENTDESCRIPTION, MR.APPOINTMENTSERVED,
				U.NAME AS CREATEDNAME, 
            S.NAME AS FROMCLINIC,
            S1.NAME AS APPOINTCLINIC,
				(SELECT COUNT(ML.ID) FROM mr_laboratory ML WHERE ML.MEDICALRECORDID=MR.ID AND ML.CANCELLED='N' LIMIT 1) AS TOTAL_LAB

			FROM patients P
			INNER JOIN medicalrecords MR  ON MR.PATIENTID = P.ID 
			INNER JOIN subclinic S ON S.ID = MR.SUBCLINICID
			LEFT JOIN users U ON U.ID = MR.CREATEDBY
			LEFT JOIN subclinic S1 ON S1.ID = MR.APPOINTMENTSUBCLINICID
			WHERE MR.CLINICID = ?
			AND MR.APPOINTMENT = 'Y'
			AND DATE(MR.APPOINTMENTDATE) = ?
			AND P.CANCELLED = 'N'
			AND MR.CANCELLED = 'N'
			$inject
			ORDER BY P.LASTNAME, P.FIRSTNAME ",
			array(
				$this->session->CLINICID,
				date('Y-m-d', strtotime($DAY))
			))->result();

      return $app;
	}
	

	public function No_Schedule_Day_Patients_Summary(){

		$_POST += json_decode(file_get_contents('php://input'), true);
			
		$DAY = $_POST['monthDate'];

		$sql = $this->db->query("SELECT P.RESCHEDULETIME, COUNT(ID) AS TOTAL
			FROM pre_appoint P
			WHERE P.CLINICID=? 
			AND P.SCHEDULEID IS NULL
			AND MONTH(P.RESCHEDULETIME) = ?
			AND YEAR(P.RESCHEDULETIME) = ?
			GROUP BY P.RESCHEDULETIME
			ORDER BY P.RESCHEDULETIME ",
			array(
				$this->session->CLINICID,
				date('m', strtotime($DAY)),
				date('Y', strtotime($DAY))
			))->result();

		return $sql;
	}


	public function No_Schedule_Day_Patients(){

		$_POST += json_decode(file_get_contents('php://input'), true);
			
		$DAY = $_POST['DayDate'];

		$sql = $this->db->query("SELECT P.ID, P.SCHEDULEID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, P.DOB, P.CIVILSTATUS, P.MOBILENO, P.COMPLAINT, P.VERIFIED, P.CREATEDTIME, 
			P.ACKNOWLEDGED, P.ACKNOWLEDGEDTIME, P.CANCELLED, P.CANCELLEDTIME, P.BLOCKED, P.RESCHEDULETIME, P.PATIENTID

			FROM pre_appoint P
			WHERE P.CLINICID=? 
			AND P.SCHEDULEID IS NULL
			AND DATE(P.RESCHEDULETIME) = ?
			ORDER BY P.RESCHEDULETIME ",
			array(
				$this->session->CLINICID,
				date('Y-m-d', strtotime($DAY))
			))->result();

		foreach ($sql as $key => $value) {
			$value->AGE = $this->m_utility->ageCompute($value->DOB,date('Y-m-d',time()));
		}

		return $sql;
	}
	


	public function Notes(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$DATE = $_POST['monthDate'];


		$inject = $this->session->POSITION == 'BRANCH ASSISTANT' ? "  " : " AND N.USERID= ".$this->session->USERID;

		$sql = $this->db->query("SELECT N.ID, N.USERID, N.NOTETIME, N.REMARKS, 
				U.NAME
			FROM notes N 
			INNER JOIN users U ON U.ID = N.USERID
			WHERE  MONTH(N.NOTETIME) = ?AND YEAR(N.NOTETIME) = ? 
			$inject
			ORDER BY N.NOTETIME " ,
			array(
				date('m', strtotime($DATE)),
				date('Y', strtotime($DATE))
			))->result();

		return $sql;

	}


	public function Schedule_Day($ID = NULL){

		if( $ID == NULL ){

			$_POST += json_decode(file_get_contents('php://input'), true);
			$DATE = $_POST['monthDate'];

		
			// $inject = $this->session->POSITION == 'BRANCH ASSISTANT' ? "  " : " AND S.USERID= ".$this->session->USERID;

			if( $this->session->POSITION == 'BRANCH ADMINISTRATOR' || $this->session->POSITION == 'BRANCH CONSULTANT' || $this->session->POSITION == 'BRANCH ASSISTANT' ){
				$inject = " ";
			}
			else{
				$inject = " AND S.USERID= ".$this->session->USERID;
			}

			$sql = $this->db->query("SELECT S.ID, S.SDATETIME, S.MAXPATIENT, S.REMARKS, S.USERID,
				SC.NAME AS SUBCLINICNAME,
				U.NAME AS DOCTORNAME,
				SUM(IF(P.VERIFIED='N' AND P.ACKNOWLEDGED='N',1,0)) as UNVERIFIED,
				SUM(IF(P.VERIFIED='Y' AND P.ACKNOWLEDGED='N',1,0)) as UNACKNOWLEDGED,
				SUM(IF(P.ACKNOWLEDGED='Y',1,0)) as ACKNOWLEDGED

			FROM schedules S 
			INNER JOIN subclinic SC ON SC.ID = S.SUBCLINICID
			INNER JOIN users U ON U.ID = S.USERID
			LEFT JOIN pre_appoint P ON P.SCHEDULEID = S.ID AND P.CANCELLED='N' AND P.BLOCKED='N'
			WHERE MONTH(S.SDATETIME) = ?	AND YEAR(S.SDATETIME) = ?
			$inject   
			AND S.CANCELLED='N'
			GROUP BY S.SDATETIME, S.USERID
			ORDER BY S.SDATETIME ", 
			array(
				date('m', strtotime($DATE)),
				date('Y', strtotime($DATE))
			))->result();

			return $sql;
					
		}
		else{

			$sql = $this->db->query("SELECT S.ID, S.SDATETIME, S.MAXPATIENT, S.REMARKS, S.USERID,
				SC.NAME AS SUBCLINICNAME,
				U.NAME AS DOCTORNAME,
				SUM(IF(P.VERIFIED='N' AND P.ACKNOWLEDGED='N',1,0)) as UNVERIFIED,
				SUM(IF(P.VERIFIED='Y' AND P.ACKNOWLEDGED='N',1,0)) as UNACKNOWLEDGED,
				SUM(IF(P.ACKNOWLEDGED='Y',1,0)) as ACKNOWLEDGED

			FROM schedules S 
			INNER JOIN subclinic SC ON SC.ID = S.SUBCLINICID
			INNER JOIN users U ON U.ID = S.USERID
			LEFT JOIN pre_appoint P ON P.SCHEDULEID = S.ID AND P.CANCELLED='N' AND P.BLOCKED='N'
			WHERE S.ID=?
			GROUP BY S.SDATETIME, S.USERID
			ORDER BY S.SDATETIME ", 
			array($ID))->row();

			return $sql;
		}
	}




	public function Schedule_Day_Patients(){

		$_POST += json_decode(file_get_contents('php://input'), true);
			
		$DAY = $_POST['DayDate'];

		$sql = $this->db->query("SELECT P.ID, P.SCHEDULEID, P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, P.DOB, P.CIVILSTATUS, P.MOBILENO, P.COMPLAINT, P.VERIFIED, P.CREATEDTIME, 
			P.ACKNOWLEDGED, P.ACKNOWLEDGEDTIME, P.CANCELLED, P.CANCELLEDTIME, P.BLOCKED, P.RESCHEDULETIME, P.PATIENTID,
			U.NAME AS ACKNOWLEDGEDNAME,
			U1.NAME AS CANCELLEDNAME
			FROM pre_appoint P
			INNER JOIN schedules S ON S.ID = P.SCHEDULEID
			LEFT JOIN users U ON U.ID = P.ACKNOWLEDGEDBY
			LEFT JOIN users U1 ON U1.ID = P.CANCELLEDBY
			WHERE S.CLINICID=? 
			AND DATE(S.SDATETIME) = ? ",
			array(
				$this->session->CLINICID,
				date('Y-m-d', strtotime($DAY))
			))->result();

		foreach ($sql as $key => $value) {
			$value->AGE = $this->m_utility->ageCompute($value->DOB,date('Y-m-d',time()));
		}

		return $sql;
	}


	


	
	

	public function Schedule_Form(){

		$_POST += json_decode(file_get_contents('php://input'), true);
		$ID = $_POST['ID'];

		$sql = $this->db->query("SELECT * FROM schedules WHERE ID=? LIMIT 1" ,array($ID))->row();

		return $sql;
	}



   public function Submit_Form(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('SUBCLINICID','Sub clinic','trim|required');
		$this->form_validation->set_rules('SDATETIME','Date & Time','trim');
		$this->form_validation->set_rules('REMARKS','Remarks','trim');
		$this->form_validation->set_rules('MAXPATIENT','Maximum Patient','trim|required');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){


				if( $this->input->post('ID') > 0 ){

					$this->db->update('schedules', array(
						'USERID'       => $this->input->post('USERID'),
						'SUBCLINICID'  => $this->input->post('SUBCLINICID'),
						'SDATETIME'    => date('Y-m-d H:i:s',strtotime($this->input->post('SDATETIME'))),
						'REMARKS'   	=> $this->input->post('REMARKS'),
						'MAXPATIENT'   => $this->input->post('MAXPATIENT'),
                  'UPDATEDBY'    => $this->session->USERID,
                  'UPDATEDTIME'  => date('Y-m-d H:i:s',time())
					), array('ID' => $this->input->post('ID') ));

					$ID = $this->input->post('ID');
				}
				else { 

					$this->db->insert('schedules', array(
						'CLINICID'    	=> $this->session->CLINICID,
						'USERID'       => $this->input->post('USERID'),
						'SUBCLINICID'  => $this->input->post('SUBCLINICID'),
                  'SDATETIME'    => date('Y-m-d H:i:s',strtotime($this->input->post('SDATETIME'))),
						'REMARKS'   	=> $this->input->post('REMARKS'),
						'MAXPATIENT'   => $this->input->post('MAXPATIENT'),
                  'CANCELLED'    => 'N',
                  'CREATEDBY'    => $this->session->USERID,
                  'CREATEDTIME'  => date('Y-m-d H:i:s',time())
					));

					$ID = $this->db->insert_id();
				}

				$data['suc']['SCHEDULE'] = $this->Schedule_Day($ID);

			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}



	public function Submit_Remove(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('ID','Sub clinic','trim|required');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$sql = $this->db->query("SELECT ID FROM pre_appoint WHERE SCHEDULEID=? AND CANCELLED='N' LIMIT 1", array($this->input->post('ID')))->row();

				if( $sql ){
					$data['err'] .= 'Please re-schedule or cancel the patient appointment before cancelling.';
				}
				else{

					$this->db->update('schedules',array(
						'CANCELLED' 	=>'Y',
						'CANCELLEDBY'	=> $this->session->USERID,
						'CANCELLEDTIME'=> date('Y-m-d H:i:s',time())
					), array('ID' => $this->input->post('ID')));
				}

			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}


	public function Submit_Verified_Patient(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('ID','Sub clinic','trim|required');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$this->db->update('pre_appoint',array(
					'VERIFIED' => 'Y',
					'VERIFIEDTIME' => date('Y-m-d H:i:s', time())
				),array('ID'=>$this->input->post('ID')));
			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}


	public function Submit_Patient_Acknowledged(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('ID','Sub clinic','trim|required');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$this->db->update('pre_appoint',array(
					'ACKNOWLEDGED' => 'Y',
					'ACKNOWLEDGEDTIME' => date('Y-m-d H:i:s', time()),
					'ACKNOWLEDGEDBY' => $this->session->USERID
				),array('ID'=>$this->input->post('ID')));

				$sql = $this->db->query("SELECT P.*, S.SDATETIME
					FROM pre_appoint P 
					INNER JOIN schedules S ON S.ID = P.SCHEDULEID
					WHERE P.ID=? LIMIT 1",array($this->input->post('ID')))->row();

				if( $sql ){

					$data['suc']['PATIENTID'] = $this->Check_Patient_Exist($sql->FIRSTNAME,$sql->MIDDLENAME,$sql->LASTNAME,$sql->DOB);

					if( is_null($data['suc']['PATIENTID']) ){

						$this->db->insert('patients', array(
							'CLINICID' => $this->session->CLINICID,
							'DATEREG' => date('Y-m-d H:i:s', strtotime($sql->SDATETIME)),
							'FIRSTNAME' => ucwords(strtolower($sql->FIRSTNAME)),
							'MIDDLENAME' => ucwords(strtolower($sql->MIDDLENAME)),
							'LASTNAME' => ucwords(strtolower($sql->LASTNAME)),
							'DOB' => date('Y-m-d',strtotime($sql->DOB)),
							'SEX' => $sql->SEX,
							'STREETNO' => $sql->STREETNO,
							'CITY' => $sql->CITY,
							'PROVINCE' => $sql->PROVINCE,
							'CIVILSTATUS' => $sql->CIVILSTATUS,
							'MOBILENO' => $sql->MOBILENO,
							'EMAIL' => $sql->EMAIL,
							'REVISIT' => 'N',
							'CREATEDBY' => $this->session->USERID,
							'CREATEDTIME' => date('Y-m-d H:i:s',time()),
							'CANCELLED' => 'N'
						));

						$ID = $this->db->insert_id();

						$this->db->update('pre_appoint',array(
							'PATIENTID' => $ID
						), array('ID'=> $this->input->post('ID')));
					}
					else{
						
						$this->db->update('pre_appoint',array(
							'PATIENTID' => $data['suc']['PATIENTID']
						), array('ID'=> $this->input->post('ID')));
					}
				}
			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}


	private function Check_Patient_Exist($fname,$mname,$lname,$dob){

		$fname = trim(str_replace('  ','',preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$fname)));
		$mname = trim(str_replace('  ','',preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$mname)));
		$lname = trim(str_replace('  ','',preg_replace('/[^0-9a-zA-Z-ñÑ ]/i','',$lname)));

		$sql = $this->db->query("SELECT ID 
			FROM patients 
			where CLINICID=? AND FIRSTNAME=? AND MIDDLENAME=? AND LASTNAME=? AND DATE(DOB)=? AND CANCELLED = 'N' 
			LIMIT 1",array( $this->session->CLINICID,$fname,$mname,$lname,date('Y-m-d',strtotime($dob))))->row();

		if ( $sql ){ 
			return $sql->ID; 
		}
		else{ 
			return NULL; 
		}
	}


	public function Submit_Patient_Cancelled(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('ID','Pre Appointment ID','trim|required');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$this->db->update('pre_appoint',array(
					'CANCELLED' => 'Y',
					'CANCELLEDTIME' => date('Y-m-d H:i:s', time()),
					'CANCELLEDBY' => $this->session->USERID
				),array('ID'=>$this->input->post('ID')));
			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	} 


	public function Submit_Patient_Recancelled(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('ID','Pre Appointment ID','trim|required');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$this->db->update('pre_appoint',array(
					'CANCELLED' => 'N',
					'CANCELLEDTIME' => NULL,
					'CANCELLEDBY' => NULL
				),array('ID'=>$this->input->post('ID')));
			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	} 



	public function Submit_Patient_Blocked(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('ID','Pre Appointment','trim|required');
		$this->form_validation->set_rules('MOBILENO','Mobile No','trim|required');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$this->db->update('pre_appoint',array(
					'CANCELLED' => 'Y',
					'CANCELLEDTIME' => date('Y-m-d H:i:s', time()),
					'CANCELLEDBY' => $this->session->USERID,
					'BLOCKED' => 'Y'
				),array('ID'=>$this->input->post('ID')));

				$this->db->insert('block_mobile',array(
					'CLINICID' => $this->session->CLINICID,
					'MOBILENO' => $this->input->post('MOBILENO'),
					'CANCELLED' => 'N'
				));
			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	} 



	public function Submit_Patient_Unblocked(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('ID','Pre Appointment','trim|required');
		$this->form_validation->set_rules('MOBILENO','Mobile No','trim|required');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$this->db->update('pre_appoint',array(
					'CANCELLED' => 'N',
					'CANCELLEDTIME' => NULL,
					'CANCELLEDBY' => NULL,
					'BLOCKED' => 'N'
				),array('ID'=>$this->input->post('ID')));

				$this->db->update('block_mobile',array(
					'CANCELLED' => 'Y'
				),array(
					'CLINICID' => $this->session->CLINICID,
					'MOBILENO' => $this->input->post('MOBILENO')
				));
				
			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}




	public function Last_Appointment_Summary(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$DATE = $_POST['DATE'];
		
		// $inject = ($this->session->POSITION == 'BRANCH ASSISTANT') ? "  " : " AND CREATEDBY= ".$this->session->USERID;

		if( $this->session->POSITION == 'BRANCH ADMINISTRATOR' || $this->session->POSITION == 'BRANCH CONSULTANT' || $this->session->POSITION == 'BRANCH ASSISTANT' ){
			$inject = " ";
		}
		else{
			$inject = " AND CREATEDBY= ".$this->session->USERID;
		}

		$sql = $this->db->query("SELECT APPOINTMENTDATE, COUNT(ID) AS TOTAL
			FROM medicalrecords
			WHERE CLINICID=?
			AND APPOINTMENT='Y'
			AND DATE(APPOINTMENTDATE) >= ?
			AND CANCELLED='N'
			$inject
			GROUP BY APPOINTMENTDATE
			ORDER BY APPOINTMENTDATE" ,
			array(
				$this->session->CLINICID,
				date('Y-m-d', strtotime($DATE))
			))->result();

		
		// $inject = ($this->session->POSITION == 'BRANCH ASSISTANT') ? "  " : " AND S.USERID= ".$this->session->USERID;
		
		if( $this->session->POSITION == 'BRANCH ADMINISTRATOR' || $this->session->POSITION == 'BRANCH CONSULTANT' || $this->session->POSITION == 'BRANCH ASSISTANT' ){
			$inject = " ";
		}
		else{
			$inject = " AND S.USERID= ".$this->session->USERID;
		}

		$sql1 = $this->db->query("SELECT S.SDATETIME,
			SUM(IF(P.VERIFIED='N' AND P.ACKNOWLEDGED='N',1,0)) as UNVERIFIED,
			SUM(IF(P.VERIFIED='Y' AND P.ACKNOWLEDGED='N',1,0)) as UNACKNOWLEDGED,
			SUM(IF(P.ACKNOWLEDGED='Y',1,0)) as ACKNOWLEDGED

			FROM schedules S 
			INNER JOIN pre_appoint P ON P.SCHEDULEID = S.ID AND P.CANCELLED='N' AND P.BLOCKED='N'
			WHERE S.CLINICID=?
			AND DATE(S.SDATETIME) >= ?
			AND S.CANCELLED='N'
			$inject
			GROUP BY S.SDATETIME
			ORDER BY S.SDATETIME", 
			array(
				$this->session->CLINICID,
				date('Y-m-d', strtotime($DATE))
			))->result();


		$sql2 = $this->db->query("SELECT P.RESCHEDULETIME, COUNT(ID) AS TOTAL
			FROM pre_appoint P
			WHERE P.CLINICID=? 
			AND P.SCHEDULEID IS NULL
			AND DATE(P.RESCHEDULETIME) >= ?
			GROUP BY P.RESCHEDULETIME
			ORDER BY P.RESCHEDULETIME ",
			array(
				$this->session->CLINICID,
				date('Y-m-d', strtotime($DATE))
			))->result();

			

		// $inject = $this->session->POSITION == 'BRANCH ASSISTANT' ? "  " : " AND USERID= ".$this->session->USERID;
		
		if( $this->session->POSITION == 'BRANCH ADMINISTRATOR' || $this->session->POSITION == 'BRANCH CONSULTANT' || $this->session->POSITION == 'BRANCH ASSISTANT' ){
			$inject = " ";
		}
		else{
			$inject = " AND USERID= ".$this->session->USERID;
		}

		$sql3 = $this->db->query("SELECT NOTETIME, REMARKS
		FROM notes 
		WHERE CLINICID=?  
		AND DATE(NOTETIME) >= ?
		AND REMARKS > ''
		$inject
		ORDER BY NOTETIME ",
		array(
			$this->session->CLINICID,
			date('Y-m-d', strtotime($DATE))
		))->result();
		

		
		return array(
			'APPOINTMENTS' => $sql,
			'SCHEDULES' => $sql1,
			'NOSCHEDULES' => $sql2,
			'NOTES' => $sql3
		);
	}



	public function Submit_Reschedule(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data = array(
			'err' => '',
			'suc' => array() 
		);


		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('DAY[]','Appointments');
			

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				foreach ($this->input->post('DAY[]') as $key => $value) {
					
					foreach ($value['APPOINTMENTS'] as $key => $app) {

						$this->db->update('medicalrecords',array(
							'APPOINTMENTDATE' => date('Y-m-d',strtotime($value['date']))
						),array('ID' => $app['ID']));

						$this->Save_SMS_Rescheduled($app['PATIENTID'], $app['TITLE'], $app['MESSAGE']);
					}
 
					foreach ($value['PATIENTS'] as $key => $pat) {

						$this->db->update('pre_appoint',array(
							'SCHEDULEID' => NULL,
							'RESCHEDULETIME' => date('Y-m-d',strtotime($value['date']))
						),array('ID' => $pat['ID']));

						$this->Save_SMS_Rescheduled($pat['PATIENTID'], $pat['TITLE'], $pat['MESSAGE']);
					}

					foreach ($value['NOSCHEDULES'] as $key => $pat) {

						$this->db->update('pre_appoint',array(
							'SCHEDULEID' => NULL,
							'RESCHEDULETIME' => date('Y-m-d',strtotime($value['date']))
						),array('ID' => $pat['ID']));

						$this->Save_SMS_Rescheduled($pat['PATIENTID'], $pat['TITLE'], $pat['MESSAGE']);
					}
				}			
			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}



	private function Save_SMS_Rescheduled($PATIENTID, $TITLE, $MESSAGE){

		$sql = $this->db->query("SELECT ID 
			FROM sms_rescheduled 
			WHERE PATIENTID=? AND TITLE=? AND SEND='N' AND CANCELLED='N'
			LIMIT 1 ", array(
				$PATIENTID,
				$TITLE
			))->row();

		if( $sql ){
			
			$this->db->update('sms_rescheduled',array(
				'TITLE' => $TITLE,
				'MESSAGE' => $MESSAGE
			), array('ID'=>$sql->ID));
		}
		else{

			$this->db->insert('sms_rescheduled',array(
				'PATIENTID' => $PATIENTID,
				'TITLE' => $TITLE,
				'MESSAGE' => $MESSAGE,
				'SEND' => 'N',
				'SENDTIME' => NULL,
				'CANCELLED' => 'N',
				'CREATEDTIME' => date('Y-m-d H:i:s',time())
			));
		}

	}


	public function SMS_Rescheduled(){

		$sql = $this->db->query("SELECT S.ID, S.TITLE, S.MESSAGE, S.SEND, S.SENDTIME,
			P.FIRSTNAME, P.MIDDLENAME, P.LASTNAME, P.MOBILENO
			FROM sms_rescheduled S 
			INNER JOIN patients P ON P.ID = S.PATIENTID
			WHERE P.CLINICID =? AND S.SEND='N'", array($this->session->CLINICID))->result();

		return $sql;
	}


	public function Submit_SMS_Rescheduled(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data = array(
			'err' => '',
			'suc' => array() 
		);


		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('SMS[]','Appointments');
			

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				foreach ($this->input->post('SMS[]') as $key => $value) {
					
					if( (Boolean)$value['SEND']) {
						$this->db->update('sms_rescheduled',array(
							'SEND' => 'Y',
							'SENDTIME' => date('Y-m-d', time())
						),array('ID' => $value['ID']));
					}

				}			
			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}



	public function Submit_Set_Patient_Scheduled(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data = array(
			'err' => '',
			'suc' => array()
		);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('ID','PRE-APPOINT ID', 'trim|required');
		$this->form_validation->set_rules('SCHEDULEID','Schedule ', 'trim|required');
			

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$this->db->update('pre_appoint',array(
					'SCHEDULEID' => $this->input->post('SCHEDULEID'),
					'RESCHEDULETIME' => NULL
				), array('ID' => $this->input->post('ID')));

			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}


	public function Submit_Note(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err'] = '';
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('USERID','User','trim|required');
		$this->form_validation->set_rules('NOTETIME','Date','trim|required');
		$this->form_validation->set_rules('REMARKS','Remarks','trim');
			

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$sql = $this->db->query("SELECT ID 
				FROM notes 
				WHERE USERID=? AND DATE(NOTETIME)=? 
				LIMIT 1", array(
					$this->input->post('USERID'),
					date('Y-m-d',strtotime($this->input->post('NOTETIME')))
				))->row();

				if( $sql ){

					$this->db->update('notes',array(
						'REMARKS' => $this->input->post('REMARKS')
					), array('ID'=>$sql->ID));
				}
				else{

					$this->db->insert('notes',array(
						'CLINICID' 		=> $this->session->CLINICID,
						'USERID' 		=> $this->input->post('USERID'),
						'NOTETIME' 		=> date('Y-m-d',strtotime($this->input->post('NOTETIME'))),
						'REMARKS' 		=> $this->input->post('REMARKS'),
						'CREATEDTIME' 	=> date('Y-m-d H:i:s', time())
					));
				}

			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}

}
?>
