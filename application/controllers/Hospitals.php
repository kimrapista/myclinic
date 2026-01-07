<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hospitals extends CI_Controller {


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
			$this->load->model('admin/m_hospitals');
			echo json_encode( $this->m_hospitals->Index() );
		}
		else if ( $i1 === 'form-data' && is_numeric($i2) && empty($e3) )
		{
			$this->load->model('admin/m_hospitals');
			echo json_encode( $this->m_hospitals->Form_Data($i2) );
		}
		else if ( $i1 === 'submit-form' && empty($e2) )
		{
			$this->load->model('admin/m_hospitals');
			echo json_encode($this->m_hospitals->Submit_Form());
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
			$this->load->model('client/m_hospitals');
			echo json_encode( $this->m_hospitals->Index() );
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

}

?>