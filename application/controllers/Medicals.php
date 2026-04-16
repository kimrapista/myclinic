<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Medicals extends CI_Controller {



	

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





		if( $i1 == 'sample-dynamic-report' && empty($e2) ) {

			$this->load->model('report/m_dynamic_report');

			$this->m_dynamic_report->Report();

		}

		else{

			

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



		//-------------------- MEDICAL RECORDS -------------------------------

		if ( $i1 === 'search-medical-records' && empty($e2) ) 
		{

			$this->load->model('client/m_medicals');
			echo json_encode( $this->m_medicals->Search_Medical_Records() );
		}
		else if ( $i1 === 'search-summary' && empty($e2) ) 
		{
			$this->load->model('client/m_medicals');
			echo json_encode( $this->m_medicals->Search_Summary() );

		}

		else if ( $i1 === 'mr-images' && is_numeric($i2) && empty($e3) ) 

		{

			$this->load->model('client/m_medicals');

			echo json_encode( $this->m_medicals->Images($i2) );

		}

		else if ( $i1 === 'form-data' && is_numeric($i2) && is_numeric($i3) && empty($e4) )

		{

			$this->load->model('client/m_medicals');

			echo json_encode( $this->m_medicals->Form_Data($i2,$i3));

		}

		else if ( $i1 === 'get-latest-prescription' && is_numeric($i2) && is_numeric($i3) && empty($e4) )

		{

			$this->load->model('client/m_medicals');

			echo json_encode( $this->m_medicals->Get_Latest_Prescription($i2,$i3));

		}

		else if ( $i1 === 'submit-form' && empty($e2) ) {			

			$this->load->model('client/m_medicals');
			$this->m_medicals->Submit_Form();
		}

		else if ( $i1 === 'set-holder' && empty($e2) ) {			

			$this->load->model('client/m_medicals');
			$this->m_medicals->Set_Holder();
		}

		else if ( $i1 === 'clear-holder' && empty($e2) ) {			

			$this->load->model('client/m_medicals');
			$this->m_medicals->Clear_Holder();
		}

		else if ( $i1 === 'heartbeat-holder' && empty($e2) ) {			

			$this->load->model('client/m_medicals');
			$this->m_medicals->Heartbeat_Holder();
		}

		else if( $i1 == 'submit-upload' && is_numeric($i2) && empty($i3)){

			$this->load->model('client/m_medicals');
			echo json_encode($this->m_medicals->Submit_Upload($i2));
		}

		else if( $i1 == 'submit-upload-laboratory' && is_numeric($i2)  && is_numeric($i3) && empty($i4)){

			$this->load->model('client/m_medicals');
			echo json_encode($this->m_medicals->Submit_Laboratory_Upload($i2, $i3));
		}



		else if( $i1 == 'email-prescription' && empty($i2)){
			$this->load->model('M_email');			
			echo json_encode($this->M_email->Send_Email_Prescription());
		}


		else if( $i1 == 'email-certificate' && empty($i2)){
			$this->load->model('M_email');			
			echo json_encode($this->M_email->Send_Email_Certificate());
		}



		//-------------------- MEDICAL RECORDS REPORT-------------------------------

		else if ( $i1 === 'report' && is_numeric($i2) && $i3 === 'medical-record' && empty($e4)) 

		{

			$this->load->model('report/m_medical_record');

			$this->m_medical_record->index($i2);

		}

		else if ( $i1 === 'report' && is_numeric($i2) && $i3 === 'medical-certificate' && empty($e4)) 

		{

			$this->load->model('report/m_medical_certificate');

			$this->m_medical_certificate->index($i2);

		}

		else if ( $i1 === 'report' && is_numeric($i2) && $i3 === 'medical-prescription' && empty($e4) ) {



			$this->load->model('report/m_medical_prescription'); 

			$this->m_medical_prescription->index($i2);

		}
		else if ( $i1 === 'report' && is_numeric($i2) && $i3 === 'medical-prescription' && $i4 == 'output-data' && empty($e5) ) {

			$this->load->model('report/m_medical_prescription');
			$this->m_medical_prescription->index($i2, TRUE); 
		}

		else if ( $i1 === 'report' && is_numeric($i2) && $i3 === 'referral-letter' && empty($e4) ) {



			$this->load->model('report/m_referral_letter');

			$this->m_referral_letter->index($i2);

		}

		else if ( $i1 === 'report' && is_numeric($i2) && $i3 === 'clearance-letter' && empty($e4) ) {



			$this->load->model('report/m_clearance_letter');

			$this->m_clearance_letter->index($i2);

		}
		else if ( $i1 === 'report' && is_numeric($i2) && $i3 === 'instruction-letter' && empty($e4) ) {

			$this->load->model('report/M_medical_instruction');
			$this->M_medical_instruction->index($i2);
		}
		
		else if ( $i1 === 'report' && is_numeric($i2) && $i3 === 'pirani-report' && empty($e4) ) {

			$this->load->model('report/M_pirani_report');
			$this->M_pirani_report->index($i2);
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

