<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {


	public function index($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = '') 
	{	

 
		if( ! isset($this->session->POSITION) ){ 

			if( $i1 == 'submit-relogin' ) {

				$this->load->model('client/m_account');
				echo json_encode( $this->m_account->Submit_Relogin() );
			}
			else if( $this->input->is_ajax_request() ){
				echo 'RELOGIN';
			} 
			else {
				redirect(base_url());
			}
		}
		else if ( $this->session->POSITION === 'ADMINISTRATOR' ) {

			$this->client($i1,$i2,$i3,$i4,$i5);
		}	
		else if ( ! empty($this->session->POSITION) ) {

			$this->client($i1,$i2,$i3,$i4,$i5);
		}
		else {

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

		// -------------------- ACCOUNT ----------------------------
		if ( $i1 === 'my-account' && empty($e2) ) {

			$this->load->model('m_account');
			echo json_encode( $this->m_account->My_Account() );
		}
		else if ( $i1 === 'submit-username' && empty($e2) ) {

			$this->load->model('m_account');
			echo json_encode($this->m_account->Submit_Username());
		}
		else if ( $i1 === 'submit-password' && empty($e2) ) {

			$this->load->model('m_account');
			echo json_encode($this->m_account->Submit_Password());
		}
		else if ( $i1 === 'submit-account' && empty($e2) ) {

			$this->load->model('m_account');
			echo json_encode($this->m_account->Submit_Account());
		}
		else if ( $i1 === 'submit-online-profile' && empty($e2) ) {

			$this->load->model('m_account');
			echo json_encode($this->m_account->Submit_Online_Profile());
		}
		else if ( $i1 === 'submit-upload' && empty($e2) ) {

			$this->load->model('m_account');
			echo json_encode($this->m_account->Submit_Upload());
		}
		else if ( $i1 === 'submit-crop' && empty($e2) ) {

			$this->load->model('m_account');
			echo json_encode($this->m_account->Submit_Crop());
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