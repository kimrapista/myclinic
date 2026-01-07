<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Preregistration extends CI_Controller {


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
		else if ( $this->session->POSITION === 'ADMINISTRATOR' ) 
		{
			$this->admin($i1,$i2,$i3,$i4,$i5);
		}	
		else if ( $this->session->POSITION === 'BRANCH ADMINISTRATOR' || $this->session->POSITION === 'BRANCH CONSULTANT' || $this->session->POSITION === 'BRANCH RESIDENT')
		{
			$this->doctor($i1,$i2,$i3,$i4,$i5);
		}	
		else if ( $this->session->POSITION === 'BRANCH ASSISTANT' )
		{
			$this->assistant($i1,$i2,$i3,$i4,$i5);
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

	private function admin($i1 = '',$i2 = '',$i3 = '' , $i4 = '', $i5 = ''){
		$e1 = $i1 . $i2 . $i3 . $i4 . $i5;
		$e2 = $i2 . $i3 . $i4 . $i5;
		$e3 = $i3 . $i4 . $i5;
		$e4 = $i4 . $i5;
		$e5 = $i5;

		if ( empty($e1) )
		{
			
			$this->load->view('header',array('active'=>'preregistration'));
			$this->load->view('preregistration/pageas_preregistration');
			$this->load->view('footer');
		}
		else if (  $i1 === 'index'  && empty($e2) ) 
		{
			$this->load->model('preregistration/ma_preregistration_q');
			echo json_encode( $this->ma_preregistration_q->adminIndex());
		}
		else if ( $i1 === 'search-medical-records' && empty($e2) ) 
		{
			$this->load->model('medicals/ma_medicals_q');
			echo json_encode( $this->ma_medicals_q->search_medical_records() );
		}
		else if ( $i1 === 'new' && empty($e2) )
		{
			$this->load->view('header',array('active'=>'preregistration'));
			$this->load->view('preregistration/forma_preregistration' , array('id'=>0));
			$this->load->view('footer');
		}
		else if ( $i1 === 'edit' && is_numeric($i2) && empty($e3) )
		{
			$this->load->view('header',array('active'=>'preregistration'));
			$this->load->view('preregistration/formas_preregistration' , array('id'=>$i2));
			$this->load->view('footer');
		}
		else if ( $i1 === 'edit_preregistration_data' && is_numeric($i2) && empty($e3) )
		{
			$this->load->model('preregistration/ma_preregistration_q');
			echo json_encode( $this->ma_preregistration_q->edit_preregistration_data($i2) );
		}
		else if ( $i1 === 'submit_preregistration' && empty($e2) )
		{
			$this->load->model('preregistration/ma_preregistration');
			$this->ma_preregistration->submit_preregistration();
		}
		else{
			
			if( $this->input->is_ajax_request() ){
				echo 'RELOGIN';
			} else {
				redirect(base_url());
			}
		}
	}
	


	//---------------------preregistration---------------------------------- 



	private function doctor($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = ''){

		$e1 = $i1 . $i2 . $i3 . $i4 . $i5;
		$e2 = $i2 . $i3 . $i4 . $i5;
		$e3 = $i3 . $i4 . $i5;
		$e4 = $i4 . $i5;
		$e5 = $i5;

		if ( empty($e1) )
		{
			$this->load->view('headera',array('active'=>'preregistration'));
			$this->load->view('preregistration/pagea_preregistration');
			$this->load->view('footera');
		}
		else if (  $i1 === 'index' && empty($e2) ) 
		{
			$this->load->model('preregistration/ma_preregistration_q');
			echo json_encode( $this->ma_preregistration_q->index() );
		}
		else if ( $i1 === 'search-medical-records' && empty($e2) ) 
		{
			$this->load->model('medicals/ma_medicals_q');
			echo json_encode( $this->ma_medicals_q->search_medical_records() );
		}
		else if ( $i1 === 'new' && empty($e2) )
		{
			$this->load->view('headera',array('active'=>'preregistration'));
			$this->load->view('preregistration/forma_preregistration' , array('id'=>0));
			$this->load->view('footera');
		}
		else if ( $i1 === 'admitPatient' && is_numeric($i2) && empty($e3) )
		{
			$this->load->view('headera',array('active'=>'preregistration'));
			$this->load->view('preregistration/formas_preregistration' , array('id'=>$i2));
			$this->load->view('footera');
		}
		else if ( $i1 === 'form-preregistration' && is_numeric($i2) && empty($e3) )
		{
			$this->load->model('preregistration/ma_preregistration_q');
			echo json_encode( $this->ma_preregistration_q->admitPatient($i2) );
		}
		else if ( $i1 === 'admitPatient' && empty($e2))
		{
			$this->load->model('preregistration/ma_preregistration');
			$this->ma_preregistration->admitPatient();
		}
		else if ( $i1 === 'edit' && is_numeric($i2) && empty($e3) )
		{
			$this->load->view('headera',array('active'=>'preregistration'));
			$this->load->view('preregistration/forma_preregistration' , array('id'=>$i2));
			$this->load->view('footera');
		}
		else if ( $i1 === 'edit_preregistration_data' && is_numeric($i2) && empty($e3) )
		{
			$this->load->model('preregistration/ma_preregistration_q');
			echo json_encode( $this->ma_preregistration_q->edit_preregistration_data($i2) );
		}
		else if ( $i1 === 'submit_preregistration' && empty($e2) )
		{
			$this->load->model('preregistration/ma_preregistration');
			$this->ma_preregistration->submit_preregistration();
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