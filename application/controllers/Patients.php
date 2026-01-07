<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Patients extends CI_Controller {


	public function index($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = '') 
	{	
		
		if( ! isset($this->session->POSITION) )
		{	
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


		$this->load->view('header');
		$this->load->view('error_404');
		$this->load->view('footer');

	}


	private function client($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = ''){

		$e1 = $i1 . $i2 . $i3 . $i4 . $i5;
		$e2 = $i2 . $i3 . $i4 . $i5;
		$e3 = $i3 . $i4 . $i5;
		$e4 = $i4 . $i5;
		$e5 = $i5;


		//-------------------- PATIENTS -------------------------------
		if ( $i1 === 'search-patients' && empty($e2) ) 
		{
			$this->load->model('client/m_patients');
			echo json_encode( $this->m_patients->Search_Patients() );
		}
		else if ( $i1 === 'form-data' && is_numeric($i2) && empty($e3) )
		{
			$this->load->model('client/m_patients');
			echo json_encode( $this->m_patients->Form_Data($i2) );
		}
		else if ( $i1 === 'submit-patient' && empty($e2) )
		{
			$this->load->model('client/m_patients');
			$this->m_patients->Submit_Patient();
		}

		//  ------------- SELECTED PATIENT WITH RECORDS -------------------
		else if ( $i1 === 'patient-information' && is_numeric($i2) && empty($e3) ) 
		{
			$this->load->model('client/m_patients');
			echo json_encode( $this->m_patients->Patient_Information($i2) );
		}
		else if ( $i1 === 'patient-medical-record'  && empty($e2) ) 
		{
			$this->load->model('client/m_patients');
			echo json_encode( $this->m_patients->Patient_Medical_Record() );
		}
		else if ( $i1 === 'medical-record-preview'  && empty($e2) ) 
		{
			$this->load->model('client/m_patients');
			echo json_encode( $this->m_patients->Medical_Record_Preview() );
		}
		else if ( $i1 === 'medical-record-preview-laboratory'  && empty($e2) ) 
		{
			$this->load->model('client/m_patients');
			echo json_encode( $this->m_patients->Medical_Record_Preview_Laboratory() );
		}
		else if ( $i1 === 'submit-remove-patient' && empty($e2) )
		{
			$this->load->model('client/m_patients');
			echo json_encode( $this->m_patients->Submit_Remove_Patient());
		}
		else if ( $i1 === 'submit-remove-record' && empty($e2) )
		{
			$this->load->model('client/m_patients');
			echo json_encode( $this->m_patients->Submit_Remove_Record());
		}
		else if ( $i1 === 'do_upload' && empty($e2)) 
		{
			$this->load->model('doctor/m_patients');
			$this->m_patients->Submit_Patient_Picture();
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