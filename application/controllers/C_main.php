<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class C_main extends CI_Controller {





	function __construct(){ parent::__construct(); }





	public function index($i1='',$i2='',$i3='',$i4='',$i5='',$i6='',$i7='') {



		// Empty parameter purposes 

		$e1=$i1.$i2.$i3.$i4.$i5.$i6.$i7;

		$e2=$i2.$i3.$i4.$i5.$i6.$i7;

		$e3=$i3.$i4.$i5.$i6.$i7;

		$e4=$i4.$i5.$i6.$i7;

		$e5=$i5.$i6.$i7;

		$e6=$i6.$i7;

		$e7=$i7;

		



		if( $i1 == 'phpinfo-viewing-cds' && empty($e2) ){

			echo phpinfo();

		}

		// delete temp file
		else if( $i1 == 'temp-delete' && !empty($i2) ){

			$realPath = realpath('').'/temp_files_pdf/';

			if( file_exists($realPath.$i2) ){
				unlink( $realPath. $i2);
				clearstatcache();
			}
		}

		else if( ($i1 == '403-forbidden' && empty($e2) ) || ($i1 == 'clinics' && $i2 == '403-forbidden' && empty($e3)) ){

			$this->load->view('page_403');
		}

		else if( $i1 == '404-invalid-page'  && empty($e2) ){			

			$this->load->view('page_404');

		}
		else if( $i1 == 'report-mini-mr-billing' && is_numeric($i2) && is_numeric($i3) && empty($e4) ){				

			$this->load->model('report/M_billing_report');
			$this->M_billing_report->Mini_Billing($i2, $i3);
		}


		else if( isset($this->session->USERID) ) {



			if ( $i1 === 'signout' && empty($e2) ) {

				$this->session->sess_destroy();

				redirect(base_url('signin'));

			}

			else if ( $this->session->POSITION === 'ADMINISTRATOR') {



				$this->load->view('admin/header');

			}

			else {

				

				$this->load->view('client/header');

			}

		}

		else  { 





			//------------------- landing pages



			if( empty($e1) ){



				$this->load->view('page_landing',array('TOKEN'=> $this->m_utility->tokenRequest()));

			}

			

			else if( (is_numeric($i1) || preg_match('/@[a-zA-Z0-9]/i', $i1) )  && empty($e2) ){



				$this->load->model('m_landing');

				$data = $this->m_landing->Doctor_Page_Info($i1);

				

				if( $data  ){

					$this->load->view('page_online_appoint',array(

						'TOKEN'=> $this->m_utility->tokenRequest($i1),

						'data' => $data					

					));

				}

				else{

					redirect(base_url('404-invalid-page'));

				}				

			}

			

			// video call jitsi

			else if( (is_numeric($i1) || preg_match('/@[a-zA-Z0-9]/i', $i1) ) && $i2 == 'video-call'  && empty($e3) ){



				$this->load->model('m_landing');

				$data = $this->m_landing->Doctor_Page_Info($i1);

				

				if( $data  ){

					$this->load->view('page_jitsi',array('data'=> $data));

				}

				else{

					redirect(base_url('404-invalid-page'));

				}				

			}

			





			// ------------------ sign in ---------------

			

			else if ( $i1 === 'signin' && empty($e2) ) {



				$this->load->view('page_login',array('TOKEN'=> $this->m_utility->tokenRequest()));

			}

			else if ( $i1 === 'submit-login' && empty($e2) ) {

				

				$this->load->model('m_login');

				echo json_encode( $this->m_login->Submit_Login() );

			} 

			else {

				

				if( $this->input->is_ajax_request() ){

					echo 'RELOGIN';

				} 

				else {

					redirect(base_url('404-invalid-page'));

				}

			}



		}

	}



}

