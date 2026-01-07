<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medical_record extends CI_Model
{
	
	function __construct(){ $this->load->database(); }

//	change new model
	function index($id){


		if( isset($this->session->CLINICID) ){

			if( $this->session->CLINICID == 12 ){
				$this->load->model('report/M_medical_record_12');
				$this->M_medical_record_12->index($id);
			}
			else {

				$this->load->model('report/M_medical_record_default');
				$this->M_medical_record_default->Index($id);	
			}


		} else {

			if( $this->input->is_ajax_request() ){
				echo 'RELOGIN';
			} else {
				redirect(base_url());
			}
		}

	}

}
?>