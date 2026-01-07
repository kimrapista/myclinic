<section class="container">
	
	<?php
		$startIn = $this->uri->segment(2);
		if( (int)$startIn >= 0 ){

			$limit = 10000;
			$cnt = 0;

			$insertData = array();
			$sql = "select ID,TEMPID,FIRSTNAME,LASTNAME,MIDDLENAME,DOB from patients where p.CLINICID  = 8 and TEMPID > 0";
			$querymedical = $this->db->query($sql);
			$file = fopen(base_url('importdata/datapatient.CSV'),"r");

			foreach ($querymedical->result() as $key) {
				$ID = $key->ID;
				$TEMPID = $key->TEMPID;
				$FIRSTNAME = $key->FIRSTNAME;
				$MIDDLENAME = $key->MIDDLENAME;
				$LASTNAME 	= $key->LASTNAME;
				$DATEOFBIRTH	= $key->DOB;
			
			while(! feof($file)  && $cnt < ($startIn + $limit) )
			{
				
				$data = fgetcsv($file);

				if( $cnt >= $startIn && $cnt < ($startIn + $limit) ){
					if($FIRSTNAME = trim(isset($data[2])?$data[2]:'') && $LASTNAME = trim(isset($data[4])?$data[4]:'') && $MIDDLENAME = trim(isset($data[3])?$data[3]:'') && $DATEOFBIRTH = date('Y-m-d',strtotime(isset($data[6])?$data[6]:'')))
					{
						$insertData[]= array(
						'TEMPID'				=> $data[0],
						'ID'					=> $ID,
					);
					}

				}
			}
			}
				$cnt +=1;
			
			fclose($file);
			if(count($insertData) > 0){
				$this->db->update_batch('patients',$insertData,'ID');
			}
			
		}
	?>

</section>