<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms extends CI_Controller {



	public function index($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = '')  {	 

		
		if ( $i1 == 'clinic-appointment-reminders' ) {
			$this->load->model('m_text');
			$this->m_text->Clinic_Appointment_Reminders();
		}

		else if( ! isset($this->session->POSITION) ){ 

			if( $this->input->is_ajax_request() ){
				echo 'RELOGIN';
			} 
			else {
				redirect(base_url());
			}
		}	

		else if ( $this->session->POSITION === 'ADMINISTRATOR' ) {

			$this->admin($i1,$i2,$i3,$i4,$i5);
		}	

		else if ( ! empty($this->session->POSITION) ){
			$this->client($i1,$i2,$i3,$i4,$i5);
		}
		else{

			if( $this->input->is_ajax_request() ){
				echo 'RELOGIN';
			} 
			else {
				redirect(base_url());
			}
		}
	}

 
	private function admin($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = ''){


	}


	private function client($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = ''){


		$e1 = $i1 . $i2 . $i3 . $i4 . $i5;
		$e2 = $i2 . $i3 . $i4 . $i5;
		$e3 = $i3 . $i4 . $i5;
		$e4 = $i4 . $i5;
		$e5 = $i5; 


		if ( $i1 === 'submit-sms-patient-appointment' && empty($e2) ){

			$this->load->model('m_text');
			echo json_encode($this->m_text->Patient_Appointment()); 
		}	

		else if ( $i1 === 'submit-sms-patient-rescheduled' && empty($e2) ) {

			$this->load->model('m_text'); 
			echo json_encode($this->m_text->Patient_Rescheduled()); 
		}
		else{
	
			if( $this->input->is_ajax_request() ){
				echo 'RELOGIN';
			} 
			else {
				redirect(base_url());
			}
		}
	}


}

?>