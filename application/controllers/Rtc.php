<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Rtc extends CI_Controller {


	public function index($i1='',$i2='',$i3=''){
		
		$e1=$i1.$i2.$i3;
		$e2=$i2.$i3;
		$e3=$i3;
 
 
		if ( $i1 === 'new-session'  && empty($e2) ) {

			$this->load->model('m_rtc');
			echo json_encode($this->m_rtc->New_Session());
		}
		else if ( $i1 === 'check-session'  && empty($e2) ) {

			$this->load->model('m_rtc');
			echo json_encode($this->m_rtc->Check_Session());
		}
		else if ( $i1 === 'remote-session'  && empty($e2) ) {

			$this->load->model('m_rtc');
			echo json_encode($this->m_rtc->Remote_Session());
		}
		else if ( $i1 === 'end-session'  && empty($e2) ) {

			$this->load->model('m_rtc');
			echo json_encode($this->m_rtc->End_Session());
		}
		
 
	}

}


?>