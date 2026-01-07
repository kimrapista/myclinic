<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medical_instruction extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	public function index($id, $return = FALSE){

		if( isset($this->session->CLINICID) ){ 
			
			
			if( $this->session->CLINICID == 8 ){
				$this->load->model('report/M_medical_instruction_wm_8');
				$this->M_medical_instruction_wm_8->Index($id, $return); 
			}
			else { 
				$this->load->model('report/M_medical_instruction_wm_8');
				return $this->M_medical_instruction_wm_8->Index($id, $return);	
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