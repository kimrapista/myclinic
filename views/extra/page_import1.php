<section class="container">
	
	<?php

		// if( (int)$startIn >= 0 ){

		// 	$limit = 10000;
		// 	$cnt = 0;

		// 	$insertData = array();

		// 	$file = fopen(base_url('planning/data/medicalrecords.CSV'),"r");

		// 	while(! feof($file)  && $cnt < ($startIn + $limit) )
		// 	{
				
		// 		$data = fgetcsv($file);

		// 		if( $cnt >= $startIn && $cnt < ($startIn + $limit) ){

		// 			$insertData[]= array(
		// 				'ID' => $data[0],
		// 				'PATIENTID' => $data[1],
		// 				'CHECKUPDATE' => date('Y-m-d',strtotime($data[2])),
		// 				'APPOINTMENT' => 'N',
		// 				'APPOINTMENTDATE' => date('Y-m-d',time()),
		// 				'AGE' => $data[3],
		// 				'REFFEREDBY' => $data[4],
		// 				'MEDASTHMA' => trim($data[5]) != '' ? 'Y':'N',
		// 				'MEDDM' => trim($data[6]) != '' ? 'Y':'N',
		// 				'MEDHPN' => trim($data[7]) != '' ? 'Y':'N',
		// 				'MEDIHD' => trim($data[8]) != '' ? 'Y':'N',
		// 				'MEDSMOKER' => trim($data[9]) != '' ? 'Y':'N',
		// 				'MEDOTHERS' => trim($data[10]),
		// 				'REFOD' => trim($data[11]),
		// 				'REFOS' => trim($data[12]),
		// 				'REFADD' => trim($data[13]),
		// 				'REFPD' => trim($data[14]),
		// 				'CHEIFCOMPLAINT' => trim($data[15]),
		// 				'FINDINGS' => trim($data[16]),
		// 				'DIAGNOSIS' => trim($data[17]),
		// 				'MEDICATION' => trim($data[18]),
		// 				'HMOID' => 0,
		// 				'COMPANY' => trim($data[19]),
		// 				'PAYMODE' => trim($data[20]),
		// 				'AMOUNT' => $data[21],
		// 				'AMOUNTCHANGE' => 0,
		// 				'CREATEDBY'=>$data[22],
		// 				'CREATEDTIME' => trim($data[23]) != ''? date('Y-m-d H:i:s',strtotime($data[23])) : '',
		// 				'UPDATEDBY' => $data[24],
		// 				'UPDATEDTIME' => trim($data[25]) != ''? date('Y-m-d H:i:s',strtotime($data[25])) : '',
		// 				'CANCELLEDBY' => $data[26],
		// 				'CANCELLEDTIME' => trim($data[27]) != ''? date('Y-m-d H:i:s',strtotime($data[27])) : '',
		// 				'CANCELLED' => 'N',
		// 			);

		// 		}
				

		// 		$cnt +=1;
		// 	}
		// 	fclose($file);

		// 	$this->db->insert_batch('medicalrecords',$insertData);
		// }


		if( (int)$startIn >= 0 ){

			$limit = 10000;
			$cnt = 0;

			$insertData = array();

			$file = fopen(base_url('importdataclient/patients.CSV'),"r");


			while(! feof($file)  && $cnt < ($startIn + $limit) )
			{
				
				$data = fgetcsv($file);

				if( $cnt >= $startIn && $cnt < ($startIn + $limit) ){

					$insertData[]= array(
						'ID' => $data[0],
						'TEMPID' => $data[0],
						'DATEREG' => date('Y-m-d',strtotime($data[1])),
						'FIRSTNAME' => trim($data[2]),
						'MIDDLENAME' => trim($data[3]),
						'LASTNAME' => trim($data[4]),
						'DOB' => date('Y-m-d',strtotime($data[5])),
						'SEX' => trim(strtoupper($data[6])),
						'BLOODTYPE' => trim($data[7]),
						'POB' => trim($data[8]),
						'NATIONALITY' => trim($data[9]),
						'RELIGION' => trim($data[10]),
						'OCCUPATION' => trim($data[11]),
						'STREETNO' => trim($data[12]),
						'CITY' => trim($data[13]),
						'PROVINCE' => trim($data[14]),
						'PHONENO' => trim($data[15]),
						'MOBILENO' => trim($data[16]),
						'EMAIL' => trim($data[17]),
						'PICTURE' => trim($data[18]),
						'EMERGENCYNAME' => trim($data[19]),
						'EMERGENCYADDRESS' => trim($data[20]),
						'EMERGENCYPHONENO' => trim($data[21]),
						'EMERGENCYMOBILENO' => trim($data[22]),
						'MEDASTHMA' => trim($data[23]) != '' ? 'Y':'N',
						'MEDDM' => trim($data[24]) != '' ? 'Y':'N',
						'MEDHPN' => trim($data[25]) != '' ? 'Y':'N',
						'MEDIHD' => trim($data[26]) != '' ? 'Y':'N',
						'MEDSMOKER' => trim($data[27]) != '' ? 'Y':'N',
						'MEDOTHERS' => trim($data[28]),
						'CREATEDBY'=>$data[29],
						'CREATEDTIME' => trim($data[30]) != ''? date('Y-m-d H:i:s',strtotime($data[30])) : '',
						'UPDATEDBY' => $data[31],
						'UPDATEDTIME' => trim($data[32]) != ''? date('Y-m-d H:i:s',strtotime($data[32])) : '',
						'CANCELLEDBY' => $data[33],
						'CANCELLEDTIME' => trim($data[34]) != ''? date('Y-m-d H:i:s',strtotime($data[34])) : '',
						'CANCELLED' => 'N',
					);

				}
				

				$cnt +=1;
			}
			fclose($file);

			$this->db->insert_batch('patients',$insertData);
		}


	?>

</section>