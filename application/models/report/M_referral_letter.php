<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_referral_letter extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	public function index($id){

		if( isset($this->session->CLINICID) ){


			if( $this->session->CLINICID == 1 ){
				$this->load->model('report/m_referral_letter_1');
				$this->m_referral_letter_1->Index($id);
			}
			else if( $this->session->CLINICID == 2) {
				$this->load->model('report/m_referral_letter_2');
				$this->m_referral_letter_2->Index($id);
			}
			else if( $this->session->CLINICID == 4) {
				$this->load->model('report/m_referral_letter_4');
				$this->m_referral_letter_4->Index($id);
			}
			else if( $this->session->CLINICID == 5) {
				$this->load->model('report/m_referral_letter_5');
				$this->m_referral_letter_5->Index($id);
			}
			else if( $this->session->CLINICID == 8 ){
				$this->load->model('report/m_referral_letter_8');
				$this->m_referral_letter_8->Index($id);
			}
			else if( $this->session->CLINICID == 11) {
				$this->load->model('report/m_referral_letter_11');
				$this->m_referral_letter_11->Index($id);
			}
		
			else{

				$this->load->model('report/m_referral_letter_default');
				$this->m_referral_letter_default->Index($id);	
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