<?php

defined('BASEPATH') OR exit('No direct script access allowed');
 
class Dashboard extends CI_Controller {

 
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

		if ( $i1 === 'doctors' && empty($e2) ) {

			$this->load->model('admin/m_dashboard');
			echo json_encode( $this->m_dashboard->Doctors() );
		}
		else if ( $i1 === 'clinics' && empty($e2) ) {
			
			$this->load->model('admin/m_dashboard');
			echo json_encode( $this->m_dashboard->Clinics() );
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


		if ( $i1 === 'summary' && empty($e2) )
		{
			$this->load->model('client/m_dashboard');
			echo json_encode( $this->m_dashboard->Summary() );
		}
		else if ( $i1 === 'patients-chart' && empty($e2) )
		{
			$this->load->model('client/m_dashboard');
			echo json_encode( $this->m_dashboard->Patients_Chart() );
		}
		else if ( $i1 === 'month-net-chart' && empty($e2) )
		{
			$this->load->model('client/m_dashboard');
			echo json_encode( $this->m_dashboard->Month_Net_Chart() );
		}
		else if ( $i1 === 'month-served-chart' && empty($e2) )
		{
			$this->load->model('client/m_dashboard');
			echo json_encode( $this->m_dashboard->Month_Served_Chart() );
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