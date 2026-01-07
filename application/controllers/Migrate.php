<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', '1000');
ini_set('memory_limit','500M');

class Migrate extends CI_Controller {

	function __construct(){ parent::__construct(); }
	

	public function index($i1='',$i2='',$i3='',$i4='',$i5='',$i6='',$i7='') {

        if( isset($this->session->USERID) ) {

            if( $i1 == 'patients' && !empty($i2)  && is_numeric($i3) ){
				$this->Romero_Patient_Migrate($i2, $i3);
			}
            else if( $i1 == 'records' && !empty($i2) && is_numeric($i3)  ){
				$this->Romero_Record_Migrate($i2, $i3);
			}
            else{
                echo 'invalid parameters';
            }
        }
        else{
            echo 'invalid request';
        }
	}



    private function Romero_Patient_Migrate($fileName, $number){
        echo date('Y-m-d H:i:s',time()).'<br/>';
        
		$path = dirname(__FILE__).'/migrate/';
		$xml = simplexml_load_file($path.$fileName.$number.'.xml');


		$datas = array();
		foreach ($xml as $key =>  $value) {
			
			if( $key == 'PATIENTS'){

				foreach ($value as $key1 => $value1) {
					$datas[] = (object)array(
						'ID' => $value1->attributes()->ID,
						'DATE_REGISTERED' => date('Y-m-d',strtotime($value1->attributes()->DATE_REGISTERED)),
						'LAST_NAME' => $value1->attributes()->LAST_NAME,
						'FIRST_NAME' => $value1->attributes()->FIRST_NAME,
						'MIDDLE_NAME' => $value1->attributes()->MIDDLE_NAME,
						'GENDER' => $value1->attributes()->GENDER,
						'DOB' => date('Y-m-d',strtotime($value1->attributes()->DOB)),
						'MARRITAL_STATUS' => $value1->attributes()->MARRITAL_STATUS,
						'RELIGION' => $value1->attributes()->RELIGION,
						'ADDRESS_1' => $value1->attributes()->ADDRESS_1,
						'EMAIL' => $value1->attributes()->EMAIL,
						'HOME_PHONE' => $value1->attributes()->HOME_PHONE,
						'MOBILE_PHONE' => $value1->attributes()->MOBILE_PHONE,
						'EMERGENCY_CONTACT_PERSON' => $value1->attributes()->EMERGENCY_CONTACT_PERSON,
						'PHONE_NO' => $value1->attributes()->PHONE_NO,
						'MOBILE_NO' => $value1->attributes()->MOBILE_NO
					);				
				}

			}
		}
        
        

        if( count($datas) > 0 ){

            $insertData = array();
            $patData = $this->db->query("SELECT TEMPID FROM patients WHERE CLINICID=? AND TEMPID > 0 AND CANCELLED='N'", array($this->session->CLINICID))->result();

            foreach ($datas as $key => $value) {     
                
                $found = FALSE;

				for ($i=0; $i < count($patData); $i++) { 
					if( !$found && $patData[$i]->TEMPID == $value->ID){ $found = TRUE; break; }
				}

                if( !$found ){

                    if( count($insertData) == 150 ){ $this->db->insert_batch('patients', $insertData); $insertData = array(); }

                    $patData[] = (object)array('TEMPID' =>  $value->ID);

                    $insertData[] = array(
                        'TEMPID' => $value->ID,
                        'CLINICID' => $this->session->CLINICID,
                        'DATEREG' => $value->DATE_REGISTERED,
                        'LASTNAME' => $value->LAST_NAME,
                        'FIRSTNAME' => $value->FIRST_NAME,
                        'MIDDLENAME' => $value->MIDDLE_NAME,
                        'SEX' => $value->GENDER,
                        'DOB' => $value->DOB,
                        'CIVILSTATUS' => $value->MARRITAL_STATUS,
                        'RELIGION' => $value->RELIGION,
                        'STREETNO' => substr($value->ADDRESS_1, 0 , 50),
                        'ADDRESS' => substr($value->ADDRESS_1, 0 , 100),
                        'EMAIL' => $value->EMAIL,
                        'PHONENO' => $value->HOME_PHONE,
                        'MOBILENO' => $value->MOBILE_PHONE,
                        'EMERGENCYCONTACT' => $value->EMERGENCY_CONTACT_PERSON,
                        'EMERGENCYPHONENO' => $value->PHONE_NO,
                        'EMERGENCYMOBILENO' => $value->MOBILE_NO,
                        'CREATEDBY' => $this->session->USERID,
                        'CREATEDTIME' => date('Y-m-d H:i:s',time()),
                        'CANCELLED'=> 'N'
                    );                    
                }                
            }

            if( count($insertData) > 0 ){ $this->db->insert_batch('patients', $insertData); }
        }

        echo date('Y-m-d H:i:s',time()).'<br/>';

		//redirect(base_url('migrate/patients/romero_patient/'.($number + 5000)));
	}


    private function Romero_Record_Migrate($fileName, $number){

		echo date('Y-m-d H:i:s',time()).'<br>';

		$path = dirname(__FILE__).'/migrate/';
		$xml = simplexml_load_file($path.$fileName.$number.'.xml');

				
		$datas = array();
		foreach ($xml as $key =>  $value) {
			
			if( $key == 'RECORDS'){

				foreach ($value as $key1 => $value1) {
					$datas[] = (object)array(
						'ID' => $value1->attributes()->ID,
						'DATEDOC' => date('Y-m-d',strtotime($value1->attributes()->DATEDOC)),
						'PATIENTID' => $value1->attributes()->PATIENTID,
						'AGEYEARS' => $value1->attributes()->AGEYEARS,
						'AGEMONTHS' => $value1->attributes()->AGEMONTHS,
						'AGEDAYS' => $value1->attributes()->AGEDAYS,
						'COMPANYID' => $value1->attributes()->COMPANYID,
						'HMOID' => $value1->attributes()->HMOID,
						'COMPLAINTS' => $value1->attributes()->COMPLAINTS,
						'FINDINGS' => $value1->attributes()->FINDINGS,
						'DIAGNOSIS' => $value1->attributes()->DIAGNOSIS,
						'TREATMENT' => $value1->attributes()->TREATMENT,
						'REMARKS' => $value1->attributes()->REMARKS,
						'TOTALCHARGES' => $value1->attributes()->TOTALCHARGES,
						'OPTICAL_OD' => $value1->attributes()->OPTICAL_OD,
						'OPTICAL_OS' => $value1->attributes()->OPTICAL_OS,
						'OPTICAL_ADD' => $value1->attributes()->OPTICAL_ADD,
						'OPTICAL_PD' => $value1->attributes()->OPTICAL_PD,
						'OPTICAL_SPH1' => $value1->attributes()->OPTICAL_SPH1,
						'OPTICAL_SPH2' => $value1->attributes()->OPTICAL_SPH2,
						'OPTICAL_CYCLEAXIS1' => $value1->attributes()->OPTICAL_CYCLEAXIS1,
						'OPTICAL_CYCLEAXIS2' => $value1->attributes()->OPTICAL_CYCLEAXIS2
					);				
				}
			}
		}

        if( count($datas) > 0 ){

			$insertData = array();
			$patData = $this->db->query("SELECT ID, TEMPID FROM patients WHERE CLINICID=? AND TEMPID > 0 AND CANCELLED='N'", array($this->session->CLINICID))->result();

			$mrData = $this->db->query("SELECT TEMPID FROM medicalrecords WHERE CLINICID=? AND TEMPID > 0 AND CANCELLED='N'", array($this->session->CLINICID))->result();
		
			foreach ($datas as $key => $value) {

				$patientID = NULL;	
                
				for ($i=0; $i < count($patData); $i++) { 
					if( $patData[$i]->TEMPID == $value->PATIENTID ){ $patientID = $patData[$i]->ID; break; }
				}

                if( $patientID ){

					$found = FALSE;

					for ($i=0; $i < count($mrData); $i++) { 
						if( !$found && $mrData[$i]->TEMPID == $value->ID){ $found = TRUE; break; }
					}


					if( !$found ){

						if( count($insertData) == 150 ){ $this->db->insert_batch('medicalrecords', $insertData); $insertData = array(); }

						$mrData[] = (object)array('TEMPID' =>  $value->ID);

						$insertData[] = array(
							'PATIENTID' => $patientID,
							'TEMPID' => $value->ID,
							'TEMPPATIENTID' => $value->PATIENTID,

							'CLINICID' => $this->session->CLINICID,
							'SUBCLINICID' => 46,
							'CHECKUPDATE' => $value->DATEDOC,
							'AGE' => $value->AGEYEARS,
							
							'CHEIFCOMPLAINT' => $value->COMPLAINTS,
							'FINDINGS' => $value->FINDINGS,
							'DIAGNOSIS' => $value->DIAGNOSIS,
							'PROCEDURE_DONE' => $value->TREATMENT,
							'REMARKS' => $value->REMARKS,
							
							'REFOD' => $value->OPTICAL_OD,
							'REFOS' => $value->OPTICAL_OS,
							'REFADD' => $value->OPTICAL_ADD,
							'REFPD' => $value->OPTICAL_PD,
							'SPHLEFT' => $value->OPTICAL_SPH1,
							'SPHRIGHT' => $value->OPTICAL_SPH2,
							'CYLAXISLEFT' => $value->OPTICAL_CYCLEAXIS1,
							'CYLAXISRIGHT' => $value->OPTICAL_CYCLEAXIS2,
							
							'PAYMODE' =>  $value->TOTALCHARGES > 0 ? 'CHARGED' : 'NO CHARGE',
							'GROSSAMOUNT' => $value->TOTALCHARGES,
							'NETPAYABLES' => $value->TOTALCHARGES,
							
							
							'CREATEDBY' => $this->session->USERID,
							'CREATEDTIME' => date('Y-m-d H:i:s',time()),
							'CANCELLED'=> 'N'
						); 
					}
				}
			}	

			if( count($insertData) > 0 ){ $this->db->insert_batch('medicalrecords', $insertData); }			

		}

		echo date('Y-m-d H:i:s',time());

		//redirect(base_url('migrate/records/romero_record/'.($number + 5000)));
	}




}