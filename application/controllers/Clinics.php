<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Clinics extends CI_Controller {


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

		 
		if ( $i1 === 'index' && empty($e2) ) 
		{
			$this->load->model('admin/m_clinics');
			echo json_encode( $this->m_clinics->Index() );
		}
		else if ( $i1 === 'form-data' && empty($e2) )
		{
			$this->load->model('admin/m_clinics');
			echo json_encode( $this->m_clinics->Form_Data() );
		}
		else if ( $i1 === 'submit-form' && empty($e2) )
		{
			$this->load->model('admin/m_clinics');
			echo json_encode($this->m_clinics->Submit_Form());
		}
		else if ( $i1 === 'report-forms' && empty($e2) )
		{
			$this->load->model('admin/m_clinics');
			echo json_encode( $this->m_clinics->Report_Forms() );
		}
		else if ( $i1 === 'submit-report-forms' && empty($e2) )
		{
			$this->load->model('admin/m_clinics');
			echo json_encode($this->m_clinics->Submit_Report_Forms());
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

	
 

	private function client($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = ''){

		$e1 = $i1 . $i2 . $i3 . $i4 . $i5;
		$e2 = $i2 . $i3 . $i4 . $i5;
		$e3 = $i3 . $i4 . $i5;
		$e4 = $i4 . $i5;
		$e5 = $i5;

		if ( $i1 === 'index' && empty($e2) ) 
		{
			$this->load->model('client/m_clinic');
			echo json_encode( $this->m_clinic->Index() );
		}
		else if ( $i1 === 'form-data'  && empty($e2) )
		{
			$this->load->model('client/m_clinic');
			echo json_encode( $this->m_clinic->Form_Data() );
		}
		else if ( $i1 === 'submit-form' && empty($e2) )
		{
			$this->load->model('client/m_clinic');
			echo json_encode($this->m_clinic->Submit_Form());
		}
		else if ( $i1 === 'subclinic' && $i2 == 'index' && empty($e3) ) {

			$this->load->model('client/m_subclinic');
			echo json_encode( $this->m_subclinic->Index() );
		}
		else if ( $i1 === 'subclinic' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ) {

			$this->load->model('client/m_subclinic');
			echo json_encode( $this->m_subclinic->Form_Data($i3) );
		}
		else if ( $i1 === 'subclinic' && $i2 === 'submit-form' && empty($e3) ) {

			$this->load->model('client/m_subclinic');
			echo json_encode( $this->m_subclinic->Submit_Form() );
		}
		else if ( $i1 === 'subclinic' && $i2 === 'submit-time' && empty($e3) ) {

			$this->load->model('client/m_subclinic');
			echo json_encode( $this->m_subclinic->Submit_Time() );
		}
		else if ( $i1 === 'subclinic' && $i2 === 'submit-set-map' && empty($e3) ) {

			$this->load->model('client/m_subclinic');
			echo json_encode( $this->m_subclinic->Submit_Set_Map() );
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