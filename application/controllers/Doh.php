<?php

defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set('Asia/Manila');





class Doh extends CI_Controller { 



	public function __construct(){ 

		parent::__construct();

		$this->load->database(); 

	}





	public function index($i1 = '', $i2 = '', $i3 = '', $i4 = '', $i5 = '') 

	{	



		$e1 = $i1 . $i2 . $i3 . $i4 . $i5;

		$e2 = $i2 . $i3 . $i4 . $i5;

		$e3 = $i3 . $i4 . $i5;

		$e4 = $i4 . $i5;

		$e5 = $i5;





		//------------------- PhilHealth --------------------------------

		if ( $i1 === 'icd' && empty($e2) ) 

		{

			echo json_encode($this->ICD());

		}

		else if ( $i1 === 'rvs' && empty($e2) ) 

		{

			echo json_encode($this->RVS());

		}

		else if( $i1 == 'top-icd' && ! empty($i2) && is_numeric($i3) && is_numeric($i4) && empty($e5) ){



			$this->Top_ICD( strtoupper($i2), $i3, $i4);

		}	



	}





	private function Top_ICD($HOSPITALCODE, $PERIODM, $PERIODY){



		

		$q = $this->db->query("SELECT D.icd10cat AS CATEGORY, COUNT(MR.ID) as TOTAL

			FROM hospitals H 

			INNER JOIN clinics C ON C.HOSPITALID = H.ID 

			INNER JOIN medicalrecords MR ON MR.CLINICID = C.ID 

			INNER JOIN doh_icd D ON D.icd10code = MR.ICD_CODE  

			WHERE H.CODE = ?  AND MONTH(MR.CHECKUPDATE) = ? AND YEAR(MR.CHECKUPDATE) = ? AND MR.CANCELLED = 'N'

			GROUP BY D.icd10cat

			ORDER BY TOTAL DESC

			LIMIT 10 ",array($HOSPITALCODE,$PERIODM,$PERIODY))->result();



		echo json_encode($q);



		



	} 





	private function ICD(){



		$sql = $this->db->query("SELECT icd10code AS ITEMCODE, icd10desc AS ITEMDESCRIPTION FROM doh_icd ORDER BY icd10code")->result();

		return $sql;



	}





	private function RVS(){



		$sql = $this->db->query("SELECT itemcode AS ITEMCODE, itemdescription AS ITEMDESCRIPTION FROM ph_icd WHERE codetype='RVS' ORDER BY itemcode")->result();



		return $sql;

	}











}





?>