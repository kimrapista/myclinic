<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_laboratory extends CI_Model
{
	function __construct(){ $this->load->database(); }


	public function Index()
	{

		$sql = $this->db->query("SELECT * FROM laboratory where CLINICID=? ORDER BY NAME",array($this->session->CLINICID))->result();
		return $sql;
	}

	public function Form_Data($id) {

		if( (int)$id == 0 ) {

			$data = array(
				'TOKEN' 	=> $this->m_utility->tokenRequest(0),
				'CLINICID' 	=>  $this->session->CLINICID,
				'ID' 		=> 0,			
				'NAME'		=> '',
				'TEMPLATE'	=> '',
				'AUTOADD'	=> 'N',
				'CANCELLED'	=> 'N'
			);

			return $data;

		}
		else {

			
			$sql = $this->db->query("SELECT *  FROM laboratory where ID=? AND CLINICID=?  LIMIT 1",array($id,$this->session->CLINICID))->row();	

			if( isset($sql) ){	

				$sql->TOKEN = $this->m_utility->tokenRequest($sql->ID);
				return $sql;
			}
		}
	}


	public function Submit_Form(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err'] = '';	
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','Token', 'trim|required');
		$this->form_validation->set_rules('NAME','Name','trim|required');
		$this->form_validation->set_rules('TEMPLATE','Template Field','trim');
		$this->form_validation->set_rules('CANCELLED','Active','trim');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

				$data['err'] = $this->Check_Redundant($ID,$this->input->post('NAME'),$this->input->post('TEMPLATE'),$this->session->CLINICID);

				if( $ID > 0 && empty($data['err']) ){

					$this->db->update('laboratory', array(	
						'NAME'    		=> $this->input->post('NAME'),
						'TEMPLATE'    	=> $this->input->post('TEMPLATE'),
						'CANCELLED'    => (Boolean)$this->input->post('CANCELLED') ? 'Y' : 'N',
						'UPDATEDBY'		=> $this->session->userid,
						'UPDATEDTIME'	=> date('Y-m-d H:i:s',time())
					), array('ID' => $ID) );

				} 
				else if ( (int)$ID === 0 && empty($data['err']) ) {

					$this->db->insert('laboratory', array(
						'CLINICID' 		=> $this->session->CLINICID,
						'NAME'    		=> $this->input->post('NAME'),
						'TEMPLATE'    => $this->input->post('TEMPLATE'),
						'AUTOADD'    	=> 'N',
						'CANCELLED'    => 'N',
						'CREATEDBY'		=> $this->session->userid,
						'CREATEDTIME'	=> date('Y-m-d H:i:s',time())
					));

					$data['suc']['NEWID'] = $this->db->insert_id();
				}

			}
			else {

				$data['err'] .=' Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}



	private function Check_Redundant($ID,$NAME,$DETAIL,$CLINICID){

		$sql = $this->db->query("SELECT * FROM laboratory WHERE ID !=? AND NAME=? AND TEMPLATE=? AND CLINICID=? LIMIT 1",array($ID,$NAME,$DETAIL,$CLINICID))->row();

		if( isset($sql) ){
			return 'Name is already registered.';
		}

		return '';
	}




}
?>
