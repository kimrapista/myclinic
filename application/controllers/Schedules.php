<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Schedules extends CI_Controller {


	public function index($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = '') {	

		if( ! isset($this->session->POSITION) ) {

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
		else if ( ! empty($this->session->POSITION) )
		{
			$this->client($i1,$i2,$i3,$i4,$i5);
		}
		else{

			if( $this->input->is_ajax_request() ){
				echo 'RELOGIN';
			} else {
				redirect(base_url());
			}
		}

   }

   private function admin($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = ''){

		$e1 = $i1 . $i2 . $i3 . $i4 . $i5;
		$e2 = $i2 . $i3 . $i4 . $i5;
		$e3 = $i3 . $i4 . $i5;
		$e4 = $i4 . $i5;
      $e5 = $i5;
      
   }


   private function client($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = ''){

		$e1 = $i1 . $i2 . $i3 . $i4 . $i5;
		$e2 = $i2 . $i3 . $i4 . $i5;
		$e3 = $i3 . $i4 . $i5;
		$e4 = $i4 . $i5;
      $e5 = $i5;
      
      if( $i1 == 'search-schedules' && empty($e2) ){

         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Index() );
		}
		else if( $i1 == 'appointments-day' && empty($e2) ){

         $this->load->model('client/m_schedules');
			echo json_encode($this->m_schedules->Appointments_Day());
		}
		else if( $i1 == 'schedule-day-patients' && empty($e2) ){

         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Schedule_Day_Patients() );
		}
		else if( $i1 == 'no-schedule-day-patients' && empty($e2) ){

         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->No_Schedule_Day_Patients() );
		}
		else if( $i1 == 'schedule-form' && empty($e2) ){

         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Schedule_Form() );
		}
		else if( $i1 == 'last-appointment-summary' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Last_Appointment_Summary() );
		}
		else if( $i1 == 'sms-rescheduled' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->SMS_Rescheduled() );
      }
		

      else if( $i1 == 'submit-form' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Submit_Form() );
		}
		else if( $i1 == 'submit-remove' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Submit_Remove() );
		}
		else if( $i1 == 'submit-verified-patient' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Submit_Verified_Patient() );
		}
		else if( $i1 == 'submit-patient-acknowledged' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Submit_Patient_Acknowledged() );
		}
		else if( $i1 == 'submit-patient-cancelled' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Submit_Patient_Cancelled() );
		}
		else if( $i1 == 'submit-patient-recancelled' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Submit_Patient_Recancelled() );
		}
		else if( $i1 == 'submit-patient-blocked' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Submit_Patient_Blocked() );
		}
		else if( $i1 == 'submit-patient-unblocked' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Submit_Patient_Unblocked() );
		}
		

		else if( $i1 == 'sending-reschedule-message' && empty($e2) ){
         
         $this->load->model('m_text');
			echo json_encode( $this->m_text->SMS_Reschedule() );
		}
		else if( $i1 == 'submit-reschedules' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Submit_Reschedule() );
		}
	
		else if( $i1 == 'submit-set-patient-scheduled' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Submit_Set_Patient_Scheduled() );
		}


		else if( $i1 == 'submit-note' && empty($e2) ){
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Submit_Note() );
		}


		else if( $i1 == 'submit-sms-rescheduled' && empty($e2) ){ 
         
         $this->load->model('client/m_schedules');
			echo json_encode( $this->m_schedules->Submit_SMS_Rescheduled() );
		}

      else{

			if( $this->input->is_ajax_request() ){
				echo 'RELOGIN';
			} else {
				redirect(base_url());
			}
		}
   }
   
}