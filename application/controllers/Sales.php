<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends CI_Controller {
 

	public function index($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = '') 
	{	
		if( ! isset($this->session->POSITION) ){

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

		//-------------------- MEDICAL RECORDS -------------------------------
		if (  $i1 === 'clinics' && empty($e2) ) {

			$this->load->model('client/m_sales');
			echo json_encode( $this->m_sales->Clinics() );
		}
		else if (  $i1 === 'hmo' && empty($e2) ) {

			$this->load->model('client/m_sales');
			echo json_encode( $this->m_sales->HMO() );
		}
        else if (  $i1 === 'hmo_posting' && empty($e2) ) {

			$this->load->model('client/m_sales');
			echo json_encode( $this->m_sales->HMO_POSTING() );
		}
		else if (  $i1 === 'services' && empty($e2) ) {
			
			$this->load->model('client/m_sales');
			echo json_encode( $this->m_sales->Services() );
		}
		else if (  $i1 === 'patients' && empty($e2) ) {
			
			$this->load->model('client/m_sales');
			echo json_encode( $this->m_sales->Patients() );
		}
		else if (  $i1 === 'patients-no-charges' && empty($e2) ) {
			
			$this->load->model('client/m_sales');
			echo json_encode( $this->m_sales->Patients_No_Charges() );
		}
		else if (  $i1 === 'philhealth' && empty($e2) ) {
			
			$this->load->model('client/m_sales');
			echo json_encode( $this->m_sales->PhilHealth() );
		}
		//-------------------- MEDICAL RECORDS REPORT-------------------------------
		else if( $i1 === 'sales-summary-report' && empty($e2) ){

			$this->load->model('report/sales/m_sales_summary');
			$this->m_sales_summary->PDF();
		}
		else if( $i1 === 'sales-detail-report' && empty($e2) ){

			$this->load->model('report/sales/m_sales_detail');
			$this->m_sales_detail->PDF();
		}
		else if( $i1 === 'sales-detail-philhealth-report' && empty($e2) ){

			$this->load->model('report/sales/M_sales_detail_philhealth');
			$this->M_sales_detail_philhealth->PDF();
		}
		else if( $i1 === 'sales-hmo-report' && empty($e2) ){

			$this->load->model('report/sales/M_sales_hmo');
			$this->M_sales_hmo->PDF();
		}

		else if( $i1 === 'sales-hmo-posting-report' && empty($e2) ){

			$this->load->model('report/sales/M_sales_hmo');
			$this->M_sales_hmo->PDF1();
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
