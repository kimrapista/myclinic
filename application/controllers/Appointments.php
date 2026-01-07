<?php
 
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointments extends CI_Controller {

 
	public function index($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = '') 
	{	
		if( ! isset($this->session->POSITION) ){

			if( $this->input->is_ajax_request() ){
				echo 'RELOGIN';
			} else {
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
		else
		{
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


		//-------------------- APPOINTMENTS -------------------------------
		if ( $i1 === 'index' && empty($e2) ) 
		{
			$this->load->model('admin/m_appointments');
			echo json_encode( $this->m_appointments->Index());
		}
		else if ( $i1 === 'retext' && is_numeric($i2) && empty($e3) ) 
		{
			$this->load->model('m_text');
			$this->m_text->Text_Patient($i2);
		}
		else{
			
			if( $this->input->is_ajax_request() ){
				echo 'RELOGIN';
			} else {
				redirect(base_url());
			}
		}

	}


	private function client($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = ''){

		$e1 = $i1 . $i2 . $i3 . $i4 . $i5;
		$e2 = $i2 . $i3 . $i4 . $i5;
		$e3 = $i3 . $i4 . $i5;
		$e4 = $i4 . $i5;
		$e5 = $i5;

		//-------------------- APPOINTMENTS -------------------------------
		if ( $i1 === 'search-appontments' && empty($e2) ) 
		{
			$this->load->model('client/m_appointments');
			echo json_encode( $this->m_appointments->Search_Appointments());
		}
		else if ( $i1 === 'appointment-report' && empty($e2) ) 
		{
			$this->load->model('report/appointment/m_appointment_report');
			$this->m_appointment_report->PDF();
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
?>