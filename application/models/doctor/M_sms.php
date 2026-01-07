<?php
defined('BASEPATH') OR exit('No direct script access allowed');

 
class M_sms extends CI_Model
{
	function __construct(){ $this->load->database(); }


	public function Index()
	{
		$sql = $this->db->query("SELECT * FROM sms where CLINICID=? ORDER BY CREATEDTIME DESC",array($this->session->CLINICID))->result();
		return $sql;
	}

	public function Form_Data($id) {

		if( (int)$id == 0 ) {

			$data = array(
				'TOKEN' 	 	=> $this->m_utility->tokenRequest(0),
				'ID' 		 	=> 0,			
				'CLINICID' 	 	=> $this->session->CLINICID,
				'MESSAGE' 	 	=> '',
				'SENDDATE' 	 	=> date('Y-m-d', time()),
				'NOPATIENT'  	=> 0,
				'APPOINTMENT'	=> 'N',
				'NEWPATIENT' 	=> 'N', 
				'REVISITPATIENT'=> 'N',
				'POST'  	 	=> 'N',
				'CANCELLED'  	=> 'N'
			);

			return $data;

		}
		else {

			$sql = $this->db->query("SELECT *  FROM sms where ID=? AND CLINICID=?  LIMIT 1",array($id,$this->session->CLINICID))->row();	

			if( isset($sql)){

				$sql->TOKEN 	= $this->m_utility->tokenRequest($sql->ID);
				return $sql;
			}
			
		}
	}


	public function Submit_Form(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err'] = '';	
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('ID','ID','trim|required');
		$this->form_validation->set_rules('CLINICID','Clinic ID','trim|required');
		$this->form_validation->set_rules('MESSAGE','Message','trim|required');
		$this->form_validation->set_rules('SENDDATE','Send Date','trim|required');
		$this->form_validation->set_rules('POST','Post','trim');
		$this->form_validation->set_rules('NOPATIENT','No. Patient','trim');
		$this->form_validation->set_rules('APPOINTMENT','Appointment Patient','trim');
		$this->form_validation->set_rules('NEWPATIENT','New Patient','trim');
		$this->form_validation->set_rules('REVISITPATIENT','Revisit Patient','trim');


		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

				if( $ID > 0 ){

					$this->db->update('sms', array(	
						'MESSAGE'   	=> $this->input->post('MESSAGE'),
						'SENDDATE'  	=> date('Y-m-d', strtotime($this->input->post('SENDDATE'))),
						'NOPATIENT'		=> $this->input->post('NOPATIENT'),
						'APPOINTMENT' 	=> (Boolean)$this->input->post('APPOINTMENT') ? 'Y' : 'N',
						'NEWPATIENT' 	=> (Boolean)$this->input->post('NEWPATIENT') ? 'Y' : 'N',
						'REVISITPATIENT'=> (Boolean)$this->input->post('REVISITPATIENT') ? 'Y' : 'N',
						'POST' 			=> (Boolean)$this->input->post('POST') ? 'Y' : 'N'
					), array('ID' => $ID) );

				}
				else if ( (int)$ID === 0 ) {

					$this->db->insert('sms', array(
						'CLINICID' 	=> $this->session->CLINICID,
						'MESSAGE'   	=> $this->input->post('MESSAGE'),
						'SENDDATE'  	=> date('Y-m-d', strtotime($this->input->post('SENDDATE'))),
						'NOPATIENT'		=> $this->input->post('NOPATIENT'),
						'APPOINTMENT' 	=> (Boolean)$this->input->post('APPOINTMENT') ? 'Y' : 'N',
						'NEWPATIENT' 	=> (Boolean)$this->input->post('NEWPATIENT') ? 'Y' : 'N',
						'REVISITPATIENT'=> (Boolean)$this->input->post('REVISITPATIENT') ? 'Y' : 'N',
						'POST' 			=> (Boolean)$this->input->post('POST') ? 'Y' : 'N',
						'CANCELLED' 	=> 'N',
						'CREATEDTIME'	=> date('Y-m-d H:i:s', time())
					));

					$ID = $this->db->insert_id();
				}

				$data['suc']['TOKEN'] = $this->m_utility->tokenRequest($ID);
				$data['suc']['ID'] = $ID;
			}
			else
			{
				$data['err'] .=' Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}



	public function Patient_Mobile_No(){


		$_POST += json_decode(file_get_contents('php://input'), true);

		$data = array();
		
		$this->load->library('form_validation');


		if( $this->input->post('APPOINTMENT') == 'Y' ){

			$sql = $this->db->query("SELECT P.ID, P.MOBILENO

				FROM patients P 
				INNER JOIN medicalrecords MR  ON MR.PATIENTID = P.ID
				WHERE MR.CLINICID=? AND MR.APPOINTMENT='Y' AND MR.APPOINTMENTDATE >= ? AND P.CANCELLED='N' AND MR.CANCELLED='N' "
				,array(
					$this->session->CLINICID,
					date('Y-m-d',time())
				))->result();

			if( $sql ){

				foreach ($sql as $key => $value) {
					$value->MOBILENO = trim(preg_replace('/\D/', '', $value->MOBILENO));

					if( strlen($value->MOBILENO) == 11 ){

						$data[] = array('PATIENTID' => $value->ID, 'MOBILENO' => $value->MOBILENO);
					}
				}
			}
		}


		if( $this->input->post('NEWPATIENT') == 'Y' ){

			$sql = $this->db->query("SELECT P.ID, P.MOBILENO
				FROM patients P 
				WHERE P.CLINICID=? AND P.CREATEDTIME >=? AND P.CANCELLED='N' "
				,array(
					$this->session->CLINICID,
					date('Y-m-d',strtotime("-6 months"))
				))->result();

			if( $sql ){

				foreach ($sql as $key => $value) {
					$value->MOBILENO = trim(preg_replace('/\D/', '', $value->MOBILENO));

					if( strlen($value->MOBILENO) == 11 ){

						$data[] = array('PATIENTID' => $value->ID, 'MOBILENO' => $value->MOBILENO);
					}
				}
			}
		}


		if( $this->input->post('REVISITPATIENT') == 'Y' ){
			
			$sql = $this->db->query("SELECT P.ID, P.MOBILENO, COUNT(MR.ID) AS MR_TOTAL

				FROM patients P 
				INNER JOIN medicalrecords MR  ON MR.PATIENTID = P.ID
				WHERE MR.CLINICID=? AND MR.CHECKUPDATE <= ? AND P.CANCELLED='N' AND MR.CANCELLED='N' 
				GROUP BY MR.ID 
				HAVING MR_TOTAL >= 2 "
				,array(
					$this->session->CLINICID,
					date('Y-m-d',strtotime("-6 months"))
				))->result();

			if( $sql ){

				foreach ($sql as $key => $value) {
					$value->MOBILENO = trim(preg_replace('/\D/', '', $value->MOBILENO));

					if( strlen($value->MOBILENO) == 11 ){

						$data[] = array('PATIENTID' => $value->ID, 'MOBILENO' => $value->MOBILENO);
					}
				}
			}
		}

		return $data;
	}

}
?>
