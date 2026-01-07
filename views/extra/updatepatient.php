<section class="container">
	
	<?php
		$startIn = $this->uri->segment(2);
		if( (int)$startIn >= 0 ){

			$limit = 5000;
			$cnt = 0;

			$insertData = array();
			$sql = "select TEMPID,FIRSTNAME,LASTNAME,MIDDLENAME,DOB,CLINICID from patients where CLINICID  = 8";
			$query = $this->db->query($sql);
			$file = fopen(base_url('importdata/datapatient.CSV'),"r");

			foreach ($query->result() as $key) {
				$TEMPORARYID = $key->TEMPID;
				$FIRST_NAME = $key->FIRSTNAME;
				$LAST_NAME = $key->LASTNAME;
				$MIDDLE_NAME = $key->MIDDLENAME;
				$DATEOFBIRTH = $key->DOB;
			
			while(! feof($file)  && $cnt < ($startIn + $limit) )
			{
				
				$data = fgetcsv($file);

				if( $cnt >= $startIn && $cnt < ($startIn + $limit) ){
					if($TEMPORARYID = $data[0] && $FIRST_NAME = trim(isset($data[2])?$data[2]:'') && $LAST_NAME = trim(isset($data[4])?$data[4]:'') && $MIDDLE_NAME = trim(isset($data[3])?$data[3]:'') && $DATEOFBIRTH = date('Y-m-d',strtotime(isset($data[6])?$data[6]:'')))
					{
						$insertData[]= array(
						'TEMPID'				=> $data[0],
						'PHONENO' 				=> trim(isset($data[28])?$data[28]:''),
						'MOBILENO' 				=> trim(isset($data[29])?$data[29]:''),
					);
					}

				}
			}
			}
				$cnt +=1;
			
			fclose($file);
			if(count($insertData) > 0){
				$this->db->update_batch('patients',$insertData,'TEMPID');
			}
			
		}
	?>

</section>