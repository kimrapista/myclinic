	<?php
	$FROM = (int)$FROM;
	$TO = (int)$TO;

	$CLINICID = NULL;
	$CLINICID = $this->session->CLINICID;

	$SUBCLINICID = NULL;
	$SUBCLINICID = 8;


	if( (int)$FROM >= 0 && $CLINICID != NULL && $SUBCLINICID != NULL ){


		// ------------------------- PATIENT
		// $line = 0;
		// $DATAS = array();

		// $file = fopen(base_url('importdataclient/NEWPAT.CSV'),"r");

		// while(! feof($file) && $FROM <= $TO )
		// {

		// 	$data = fgetcsv($file);

		// 	if( $line >= $FROM && $line <= $TO ){

		// 		if( is_numeric($data[0]) ){

		// 			$DATAS[]= array(
		// 				'CLINICID' => $CLINICID,
		// 				'TEMPID' => $data[0],
		// 				'DATEREG' => date('Y-m-d',strtotime($data[1])),
		// 				'LASTNAME' => trim($data[2]),
		// 				'FIRSTNAME' => trim($data[3]),
		// 				'MIDDLENAME' => trim($data[4]),
		// 				'SEX' => trim(strtoupper($data[6])),
		// 				'DOB' => date('Y-m-d',strtotime($data[7])),
		// 				'CIVILSTATUS' => trim($data[8]),
		// 				'RELIGION' => trim($data[9]),
		// 				'STREETNO' => trim($data[11]),
		// 				'CITY' => trim($data[12]),
		// 				'ZIPCODE' => trim($data[14]),
		// 				'PHONENO' => trim($data[16]),
		// 				'MOBILENO' => trim($data[17]),
		// 				'EMERGENCYCONTACT' => trim($data[18]),
		// 				'EMERGENCYADDRESS' => trim($data[19]),
		// 				'EMERGENCYPHONENO' => trim($data[20]),
		// 				'EMERGENCYMOBILENO' => trim($data[21]),
		// 				'CANCELLED' => trim($data[23]),
		// 				'CREATEDBY'=>$data[24],
		// 				'CREATEDTIME' => trim($data[25]) != ''? date('Y-m-d H:i:s',strtotime($data[25])) : '',
		// 				'UPDATEDBY' => $data[26],
		// 				'UPDATEDTIME' => trim($data[27]) != ''? date('Y-m-d H:i:s',strtotime($data[27])) : '',
		// 				'CANCELLEDBY' => $data[28],
		// 				'CANCELLEDTIME' => trim($data[29]) != ''? date('Y-m-d H:i:s',strtotime($data[29])) : ''
		// 			);
		// 		}
		// 	}

		// 	$line += 1;
		// }
		// fclose($file);

		// $INSERTPATIENT = array();

		// foreach ($DATAS as $key => $value) {

		// 	$sql = $this->db->query("SELECT ID 
		// 		FROM patients 
		// 		WHERE CLINICID=? AND FIRSTNAME=? AND MIDDLENAME=? AND LASTNAME=? AND DOB=? LIMIT 1",
		// 		array(
		// 			$CLINICID,
		// 			$value['FIRSTNAME'],
		// 			$value['MIDDLENAME'],
		// 			$value['LASTNAME'],
		// 			$value['DOB']
		// 		))->row();


		// 	if( ! count($sql) ){
		// 		$INSERTPATIENT [] = $value;
		// 	}
		// }


		// if( count($INSERTPATIENT) )
		// 	$this->db->insert_batch('patients',$INSERTPATIENT);



		// ---------------- MEDICAL RECORD


		// $line = 0;
		// $DATAS = array();

		// $file = fopen(base_url('importdataclient/NEWMR.CSV'),"r");



		// while(! feof($file) && $FROM <= $TO )
		// {

		// 	$data = fgetcsv($file);

		// 	if( $line >= $FROM && $line <= $TO ){

		// 		if( is_numeric($data[0]) ){

		// 			// bp
		// 			if( !empty($data[11]) ){
		// 				$bp = explode('/', trim($data[11]));

		// 				if( isset($bp[0]) )
		// 					$syst = $bp[0];

		// 				if( isset($bp[1]) )
		// 					$dias = $bp[1];
		// 			}
		// 			else{
		// 				$syst = '';
		// 				$dias = '';
		// 			}

		// 			$DATAS[]= array(
		// 				'CLINICID' => $CLINICID,
		// 				'SUBCLINICID' => $SUBCLINICID,
		// 				'TEMPID' => $data[0],

		// 				'CHECKUPDATE' => date('Y-m-d',strtotime($data[1])),
		// 				'PATIENTID' => $data[2],
		// 				'TEMPPATIENTID' => $data[2],
		// 				// DOB
		// 				// MARTIALSTATUS
		// 				// GENDER
		// 				'AGE' => $data[6],
		// 				// AGEMONT
		// 				// AGEDA
		// 				// COMPANY
		// 				// HMOID
		// 				'BP_SYSTOLIC' => $syst,
		// 				'BP_DIASTOLIC' => $dias,
		// 				'WEIGHT' => trim($data[12]),
		// 				'HEIGHT' => trim($data[13]),
		// 				'TEMPERATURE' => trim($data[14]),
		// 				'CHEIFCOMPLAINT' => trim($data[15]),
		// 				'DIAGNOSIS' => trim($data[16]),
		// 				'MEDICATION' => trim($data[17]),

		// 				// REMARKS
		// 				// TOTALCHA
		// 				// PHICC
		// 				// COMPCHA
		// 				// HMOCHAR
		// 				'CANCELLED' => trim($data[23]),				
		// 				'CREATEDBY'=>$data[24],
		// 				'CREATEDTIME' => trim($data[25]) != ''? date('Y-m-d H:i:s',strtotime($data[23])) : '',
		// 				'UPDATEDBY' => $data[26],
		// 				'UPDATEDTIME' => trim($data[27]) != ''? date('Y-m-d H:i:s',strtotime($data[25])) : '',
		// 				'CANCELLEDBY' => $data[28],
		// 				'CANCELLEDTIME' => trim($data[29]) != ''? date('Y-m-d H:i:s',strtotime($data[27])) : '',
		// 				'FINDINGS' => trim($data[30]),
		// 				//LMP
		// 				'RESPIRATORY' => trim($data[32])
		// 			);

		// 		}
		// 	}


		// 	$line += 1;
		// }

		// fclose($file);

		// $INSERTMR = array();

		// foreach ($DATAS as $key => $value) {

		// 	$sql = $this->db->query("SELECT ID 
		// 		FROM medicalrecords 
		// 		WHERE CLINICID=? AND SUBCLINICID=? AND TEMPID =? AND TEMPPATIENTID=? AND CHEIFCOMPLAINT=? LIMIT 1",
		// 		array(
		// 			$CLINICID,
		// 			$SUBCLINICID,
		// 			$value['TEMPID'],
		// 			$value['TEMPPATIENTID'],
		// 			$value['CHEIFCOMPLAINT']
		// 		))->row();


		// 	if( ! count($sql) ){
		// 		$INSERTMR [] = $value;
		// 	}
		// }


		// if( count($INSERTMR) )
		// 	$this->db->insert_batch('medicalrecords',$INSERTMR);


		// ----------------- LABORATORY

		$line = 0;
		$DATAS = array();

		$file = fopen(base_url('importdataclient/NEWLAB.CSV'),"r");



		while(! feof($file) && $FROM <= $TO )
		{

			$data = fgetcsv($file);

			if( $line >= $FROM && $line <= $TO ){

				if( is_numeric($data[0]) ){

					$DATAS[]= array(
						'CLINICID' => $CLINICID,
						'TEMPMRID' => $data[1],
						'LABORATORYID' => 12,
						'TEMPLATERESULT' => trim($data[2]),
						'CANCELLED' => 'N',
						'CREATEDBY' => 22,
						'CREATEDTIME' => trim($data[4]) != ''? date('Y-m-d H:i:s',strtotime($data[4])) : ''
					);

				}
			}

			$line += 1;
		}

		fclose($file);


		$INSERTLAB = array();

		foreach ($DATAS as $key => $value) {

			$sql = $this->db->query("SELECT ID FROM medicalrecords WHERE CLINICID=? AND TEMPID=? LIMIT 1", array($CLINICID,$value['TEMPMRID']))->row();


			if( $sql ){

				$sql1 = $this->db->query("SELECT ID FROM mr_laboratory WHERE CLINICID=? AND TEMPMRID =? AND LTRIM(RTRIM(TEMPLATERESULT))=? LIMIT 1",
					array(
						$CLINICID,
						$value['TEMPMRID'],
						$value['TEMPLATERESULT']
					))->row();


				if( ! count($sql1) ){

					$value['MEDICALRECORDID'] = $sql->ID;

					$INSERTLAB [] = $value;
				}
			}
		
		}


		if( count($INSERTLAB) )
			$this->db->insert_batch('mr_laboratory',$INSERTLAB);



		$FROM += 100;
		$TO = $FROM + 100;

		echo 'done';
		echo count($DATAS).'-'.count($INSERTLAB).'<br>';
		echo anchor(base_url('import/'.$FROM.'/'.$TO),'next');

		// echo '<pre>'; print_r($DATAS); echo '</pre>';

	}




