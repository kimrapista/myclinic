<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Queue extends CI_Controller {


	public function index($i1='',$i2='',$i3='',$i4='',$i5='',$i6='',$i7=''){
		
		$e1=$i1.$i2.$i3.$i4.$i5.$i6.$i7;
		$e2=$i2.$i3.$i4.$i5.$i6.$i7;
		$e3=$i3.$i4.$i5.$i6.$i7;
		$e4=$i4.$i5.$i6.$i7;
		$e5=$i5.$i6.$i7;
		$e6=$i6.$i7;
		$e7=$i7;


		if ( $i1 === 'advisory'  && empty($e2) ) {

			$this->load->model('m_queue');
			echo json_encode($this->m_queue->Advisory($i2));
		}
		else if ( $i1 === 'index' && empty($e2) ) {

			$this->load->model('m_queue');
			echo json_encode($this->m_queue->Index());
		}
		else if ( $i1 === 'patient-queue' && is_numeric($i2) && empty($e3) ) {

			$this->load->model('m_queue');
			echo json_encode($this->m_queue->Patient_Queue($i2));
		}
		else if ( $i1 === 'patient-remove' && is_numeric($i2) && empty($e3) ) {

			$this->load->model('m_queue');
			$this->m_queue->patient_remove($i2);
		}
		else if ( $i1 === 'patient-priority' && empty($e2) ) {

			$this->load->model('m_queue');
			$this->m_queue->patient_priority();
		}
		else if ( $i1 === 'patient-tomorrow' && is_numeric($i2) && empty($e3) ) {

			$this->load->model('m_queue');
			echo json_encode($this->m_queue->Queue_Tomorrow($i2));
		}

	}

}


?>