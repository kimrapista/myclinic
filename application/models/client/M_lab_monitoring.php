<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class M_lab_monitoring extends CI_Model {

	function __construct(){ $this->load->database(); }


	public function Index(){

		$sql = $this->db->query("SELECT ID, GROUPNAME, NAME, SORT, INDENT, ISACTIVE, CANCELLED 
			FROM lab_monitoring
			where CLINICID=? 
			ORDER BY GROUPNAME, SORT ",array($this->session->CLINICID))->result();

		return $sql;
	}


	public function Form_Data($id) {

		if( (int)$id == 0 ) {

			$data = array(
				'TOKEN' 	=> $this->m_utility->tokenRequest(0),
				'ID' 		=> 0,	
				'GROUPNAME'	=> '',
				'NAME' 		=> '',
				'SORT' 		=> 0,
				'INDENT' 	=> 0,
				'ISACTIVE'	=> 'Y',
				'CANCELLED'	=> 'N'
			);

			return $data; 

		}
		else {			

			$sql = $this->db->query("SELECT *  FROM lab_monitoring where ID=? AND CLINICID=?  LIMIT 1",array($id,$this->session->CLINICID))->row();	

			if( isset($sql) ){	
				$sql->TOKEN = $this->m_utility->tokenRequest($sql->ID);
				return $sql;

			}
		}
	}



	public function Submit_Form(){


		$_POST += json_decode(file_get_contents('php://input'), true);

		$data = array();
		$data['err'] = '';	
		$data['suc'] = array();


		$this->load->library('form_validation');

		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('GROUPNAME','GROUP NAME','trim|required');
		$this->form_validation->set_rules('NAME','NAME','trim|required');
		$this->form_validation->set_rules('SORT','SORT','trim');
		$this->form_validation->set_rules('INDENT','INDENT','trim');
		$this->form_validation->set_rules('ISACTIVE','Active','trim');
		$this->form_validation->set_rules('CANCELLED','Active','trim');


		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

				$data['err'] = $this->Check_Redundant($ID, $this->session->CLINICID, $this->input->post('GROUPNAME'), $this->input->post('NAME'));


				if( empty($data['err']) ){

					$FORM = array(						
						'GROUPNAME' => $this->input->post('GROUPNAME'),
						'NAME'   	=> $this->input->post('NAME'),
						'SORT'   	=> $this->input->post('SORT'),
						'INDENT'   	=> $this->input->post('INDENT'),
						'ISACTIVE'	=> (Boolean)$this->input->post('ISACTIVE') ? 'Y' : 'N'
					);

					if( $ID > 0 ){
		
						$this->db->update('lab_monitoring', $FORM, array('ID' => $ID) );	
					}	
					else {
						
						$FORM['CLINICID'] = $this->session->CLINICID;
						$FORM['CANCELLED'] = 'N';
	
						$this->db->insert('lab_monitoring', $FORM);						
	
						$ID = $this->db->insert_id();	
					}
				}

				$data['suc']['ID'] = $ID;
			}

			else{			

				$data['err'] .=' Expired request. Please refresh the page. ';
			}
		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}





	private function Check_Redundant($ID,$CLINICID, $GNAME, $NAME){

		$sql = $this->db->query("SELECT * FROM lab_monitoring WHERE ID !=? AND CLINICID=? AND GROUPNAME=? AND NAME=? LIMIT 1",array($ID,$CLINICID, $GNAME,$NAME))->row();

		if( isset($sql) ){
			return 'Name is already registered.';
		}

		return '';
	}



}

?>

