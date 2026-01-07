<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notify extends CI_Controller {


	public function index($i1='',$i2='',$i3='',$i4='',$i5='',$i6='',$i7=''){
		
		$e1=$i1.$i2.$i3.$i4.$i5.$i6.$i7;
		$e2=$i2.$i3.$i4.$i5.$i6.$i7;
		$e3=$i3.$i4.$i5.$i6.$i7;
		$e4=$i4.$i5.$i6.$i7;
		$e5=$i5.$i6.$i7;
		$e6=$i6.$i7;
		$e7=$i7;

 
		if ( $i1 === 'today-patients'  && empty($e2) ) {

			$this->load->model('m_notify');
			echo json_encode($this->m_notify->Today_Patients());
		}


	}

}


?>