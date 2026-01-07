<?php
 
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends CI_Controller {

 
	public function index($i1 = '', $i2 = '', $i3 = '') 
	{	
      $e1 = $i1 . $i2 . $i3;
		$e2 = $i2 . $i3;
		$e3 = $i3;
      

      // home page
      if( $i1 == 'search-doctor' && empty($e2) ){

         $this->load->model('m_landing');
         echo json_encode( $this->m_landing->Search_Doctor() );
      }
      else if( $i1 == 'clinics-location' && empty($e2) ){

         $this->load->model('m_landing');
         echo json_encode( $this->m_landing->Clinics_Location() );
      }
      else if( $i1 == 'map-clinic-schedules' && empty($e2) ){

         $this->load->model('m_landing');
         echo json_encode( $this->m_landing->Map_Clinic_Schedules() );
      }
      
      // -------- online appointment -----------
      else if( $i1 == 'doctor-profile' && empty($e2) ){

         $this->load->model('m_landing');
         echo json_encode( $this->m_landing->Doctor_Profile() );
      }
      else if( $i1 == 'doctor-schedules' && empty($e2) ){

         $this->load->model('m_landing');
         echo json_encode( $this->m_landing->Doctor_Schedules() );
      }
      else if( $i1 == 'submit-pre-appointment' && empty($e2) ){

         $this->load->model('m_landing');
         echo json_encode( $this->m_landing->Submit_Pre_Appointment() );
      }
      else if( $i1 == 'submit-verify-code' && empty($e2) ){

         $this->load->model('m_landing');
         echo json_encode( $this->m_landing->Submit_Verify_Code() );
      }
      else if( $i1 == 'submit-resend-code' && empty($e2) ){

         $this->load->model('m_landing');
         echo json_encode( $this->m_landing->Submit_Resend_Code() );
      }


      // -------- jitsi check patient -----------
      else if( $i1 == 'submit-jitsi-check-patient' && empty($e2) ){

         $this->load->model('m_landing');
         echo json_encode( $this->m_landing->Submit_Jitsi_Check_Patient() );
      }


      else {
				
         if( $this->input->is_ajax_request() ){
            echo 'RELOGIN';
         } 
         else {
            redirect(base_url('404-invalid-page'));
         }
      }


   }


}
?>