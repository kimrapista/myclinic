<?php



defined('BASEPATH') OR exit('No direct script access allowed');



class Settings extends CI_Controller {





	public function index($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = '') {	



		if( ! isset($this->session->POSITION) ) {



			if( $this->input->is_ajax_request() ){

				echo 'RELOGIN';

			} 

			else {

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





		//-------------------specialist--------------------------------

		if ( $i1 === 'specialist' && $i2 === 'index' && empty($e3) ) 

		{

			$this->load->model('admin/m_specialist');

			echo json_encode( $this->m_specialist->Index());

		}

		

		else if ( $i1 === 'specialist' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ) {



			$this->load->model('admin/m_specialist');

			echo json_encode( $this->m_specialist->Form_Data($i3) );

		}

		else if ( $i1 === 'specialist' && $i2 === 'submit-form' && empty($e3) ) {



			$this->load->model('admin/m_specialist');

			echo json_encode( $this->m_specialist->Submit_Form() );

		}



		//-------------------subspecialty--------------------------------

		else if ( $i1 === 'subspecialty' && $i2 === 'index' && empty($e3) ) 

		{

			$this->load->model('admin/m_subspecialty');

			echo json_encode( $this->m_subspecialty->Index());

		}

		

		else if ( $i1 === 'subspecialty' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ) {



			$this->load->model('admin/m_subspecialty');

			echo json_encode( $this->m_subspecialty->Form_Data($i3) );

		}

		else if ( $i1 === 'subspecialty' && $i2 === 'submit-form' && empty($e3) ) {



			$this->load->model('admin/m_subspecialty');

			echo json_encode( $this->m_subspecialty->Submit_Form() );

		}



		// users--------

		else if ( $i1 === 'users' && $i2 === 'index' && empty($e3) ){



			$this->load->model('admin/m_users');

			echo json_encode( $this->m_users->Index());

		}

		else if ( $i1 === 'users' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ){



			$this->load->model('admin/m_users');

			echo json_encode($this->m_users->Form_Data($i3));

		}

		else if ( $i1 === 'users'  && $i2 === 'submit-form' && empty($e3) ) {



			$this->load->model('admin/m_users');

			echo json_encode($this->m_users->Submit_Form());

		} 
		else if ( $i1 === 'users'  && $i2 === 'submit-active' && empty($e3) ) {



			$this->load->model('admin/m_users');

			echo json_encode($this->m_users->Submit_Active());

		}

		else if ( $i1 === 'users'  && $i2 === 'submit-reset-password' && empty($e3) ) {



			$this->load->model('admin/m_users');

			echo json_encode($this->m_users->Submit_Reset_Password());

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





		// users--------

		if ( $i1 === 'users' && $i2 === 'index' && empty($e3) ){



			$this->load->model('client/m_users');

			echo json_encode( $this->m_users->Index());

		}

		else if ( $i1 === 'users' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ){



			$this->load->model('client/m_users');

			echo json_encode($this->m_users->Form_Data($i3));

		}

		else if ( $i1 === 'users'  && $i2 === 'submit-form' && empty($e3) ) {



			$this->load->model('client/m_users');

			echo json_encode($this->m_users->Submit_Form());

		}

		else if ( $i1 === 'users'  && $i2 === 'submit-active' && empty($e3) ) {



			$this->load->model('client/m_users');

			echo json_encode($this->m_users->Submit_Active());

		}

		else if ( $i1 === 'users'  && $i2 === 'submit-reset-password' && empty($e3) ) {



			$this->load->model('client/m_users');

			echo json_encode($this->m_users->Submit_Reset_Password());

		}





		

		//----------------services-----------------

		else if ( $i1 === 'services' && $i2 === 'index' && empty($e3) ) 

		{

			$this->load->model('client/m_services');

			echo json_encode( $this->m_services->Index());

		}

		else if ( $i1 === 'services' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ) {



			$this->load->model('client/m_services');

			echo json_encode( $this->m_services->Form_Data($i3) );

		}

		else if ( $i1 === 'services' && $i2 === 'submit-form' && empty($e3) ) {



			$this->load->model('client/m_services');

			echo json_encode( $this->m_services->Submit_Form() );

		}





		//----------------Discounts----------------

		else if ( $i1 ==='discounts' && $i2 === 'index' && empty($e3) ) 

		{

			$this->load->model('client/m_discounts');

			echo json_encode( $this->m_discounts->Index());

		}	

		else if ( $i1 === 'discounts' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ) {



			$this->load->model('client/m_discounts');

			echo json_encode( $this->m_discounts->Form_Data($i3) );

		}

		else if ( $i1 === 'discounts' && $i2 === 'submit-form' && empty($e3) ) {



			$this->load->model('client/m_discounts');

			echo json_encode( $this->m_discounts->Submit_Form() );

		}





		//-------------------HMO--------------------------------

		else if ( $i1 === 'hmo' && $i2 === 'index' && empty($e3) ) 

		{

			$this->load->model('client/m_hmo');

			echo json_encode( $this->m_hmo->Index());

		}

		

		else if ( $i1 === 'hmo' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ) {



			$this->load->model('client/m_hmo');

			echo json_encode( $this->m_hmo->Form_Data($i3) );

		}

		else if ( $i1 === 'hmo' && $i2 === 'submit-form' && empty($e3) ) {



			$this->load->model('client/m_hmo');

			echo json_encode( $this->m_hmo->Submit_Form() );

		}







		//------------------- Prescriptions --------------------------------

		else if ( $i1 === 'medicines' && $i2 === 'index' && empty($e3) ) 

		{

			$this->load->model('client/m_medicines');

			echo json_encode( $this->m_medicines->Index());

		}

		

		else if ( $i1 === 'medicines' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ) {



			$this->load->model('client/m_medicines');

			echo json_encode( $this->m_medicines->Form_Data($i3) );

		}

		else if ( $i1 === 'medicines' && $i2 === 'submit-form' && empty($e3) ) {



			$this->load->model('client/m_medicines');

			echo json_encode( $this->m_medicines->Submit_Form() );

		}







		//------------------- Instruction --------------------------------

		else if ( $i1 === 'instructions' && $i2 === 'index' && empty($e3) ) 

		{

			$this->load->model('client/m_instructions');

			echo json_encode( $this->m_instructions->Index());

		}

		

		else if ( $i1 === 'instructions' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ) {



			$this->load->model('client/m_instructions');

			echo json_encode( $this->m_instructions->Form_Data($i3) );

		}

		else if ( $i1 === 'instructions' && $i2 === 'submit-form' && empty($e3) ) {



			$this->load->model('client/m_instructions');

			echo json_encode( $this->m_instructions->Submit_Form() );

		}





		//-------------------Laboratory--------------------------------

		else if ( $i1 === 'laboratory' && $i2 === 'index' && empty($e3) ) 

		{

			$this->load->model('client/m_laboratory');

			echo json_encode( $this->m_laboratory->Index());

		}

		

		else if ( $i1 === 'laboratory' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ) {



			$this->load->model('client/m_laboratory');

			echo json_encode( $this->m_laboratory->Form_Data($i3) );

		}

		else if ( $i1 === 'laboratory' && $i2 === 'submit-form' && empty($e3) ) {



			$this->load->model('client/m_laboratory');

			echo json_encode( $this->m_laboratory->Submit_Form() );

		}





		//------------------Lab Montoring------------------------------

		else if ( $i1 === 'lab_monitoring' && $i2 === 'index' && empty($e3) ){

			$this->load->model('client/m_lab_monitoring');
			echo json_encode( $this->m_lab_monitoring->Index());
		}
		else if ( $i1 === 'lab_monitoring' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ) {

			$this->load->model('client/m_lab_monitoring');
			echo json_encode( $this->m_lab_monitoring->Form_Data($i3) );
		}

		else if ( $i1 === 'lab_monitoring' && $i2 === 'submit-form' && empty($e3) ) {

			$this->load->model('client/m_lab_monitoring');
			echo json_encode( $this->m_lab_monitoring->Submit_Form() );
		}



		//-------------------Specialist--------------------------------

		else if ( $i1 === 'specialist' && $i2 === 'index' && empty($e3) ) 

		{

			$this->load->model('client/m_specialist');

			echo json_encode( $this->m_specialist->Index());

		}


		//--------------------Procedures----------------------------------------

		else if ( $i1 === 'procedures' && $i2 === 'index' && empty($e3) ){

			$this->load->model('client/m_procedures');
			echo json_encode( $this->m_procedures->Index());
		}
		else if ( $i1 === 'procedures' && $i2 === 'form-data' && is_numeric($i3) && empty($e4) ) {

			$this->load->model('client/m_procedures');
			echo json_encode( $this->m_procedures->Form_Data($i3) );
		}

		else if ( $i1 === 'procedures' && $i2 === 'submit-form' && empty($e3) ) {

			$this->load->model('client/m_procedures');
			echo json_encode( $this->m_procedures->Submit_Form() );
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