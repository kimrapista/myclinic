<section class="container">
	
	<?php

		$sql = $this->db->query("SELECT ID FROM patients p WHERE TOTALRECORDS < 0  LIMIT 10 ",array());

		$update = array();
		foreach ($sql->result() as $key => $v) {
			
			$sql1 = $this->db->query("SELECT count(ID) as total FROM medicalrecords WHERE PATIENTID=? AND CANCELLED='N' LIMIT 1",array($v->ID));
			
			$update[] = array('ID'=> $v->ID,'TOTALRECORDS'=> $sql1->row()->total);
		}


		$this->db->update_batch('patients',$update,'ID');

	?>


</section>