<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medical_prescription extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	public function index($id, $return = FALSE){

		if( isset($this->session->CLINICID) ){ 
			
			
			if( $this->session->CLINICID == 1 ){
				$this->load->model('report/m_medical_prescription_1');
				return $this->m_medical_prescription_1->Index($id, $return);
			}
			else if( $this->session->CLINICID == 2) {
				$this->load->model('report/m_medical_prescription_2');
				return $this->m_medical_prescription_2->Index($id, $return);
			}
			else if( $this->session->CLINICID == 4) {
				$this->load->model('report/m_medical_prescription_4');
				return $this->m_medical_prescription_4->Index($id, $return);
			}
			else if( $this->session->CLINICID == 5) {
				$this->load->model('report/m_medical_prescription_5');
				return $this->m_medical_prescription_5->Index($id, $return);
			}
			else if( $this->session->CLINICID == 8 ){
				$this->load->model('report/M_medical_prescription_wm_8');
				$this->M_medical_prescription_wm_8->Index($id, $return); 
			}
			else if( $this->session->CLINICID == 11) {
				$this->load->model('report/m_medical_prescription_11');
				return $this->m_medical_prescription_11->Index($id, $return);
			}
			else if( $this->session->CLINICID == 12 ){
				$this->load->model('report/m_medical_prescription_12');
				return $this->m_medical_prescription_12->Index($id, $return);
			}
			else if( $this->session->CLINICID == 16 ){
				$this->load->model('report/M_medical_prescription_16');
				return $this->M_medical_prescription_16->Index($id, $return);
			}
			else { 
				$this->load->model('report/m_medical_prescription_default');
				return $this->m_medical_prescription_default->Index($id, $return);	
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