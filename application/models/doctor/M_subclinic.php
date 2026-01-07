<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_subclinic extends CI_Model
{
	function __construct(){ $this->load->database(); }


	public function Index()
	{

		$sql = $this->db->query("SELECT * FROM subclinic where CLINICID=? ",array($this->session->CLINICID))->result();
		return $sql;
	}
	

	public function Form_Data($id) {

		if( (int)$id == 0 ) {

			$data = array(
				'TOKEN' 	=> $this->m_utility->tokenRequest(0),
				'CLINICID' 	=> $this->session->CLINICID,
				'HOSPITALID'=> NULL,
				'ID' 		=> 0,			
				'NAME' 		=> '',
				'LOCATION'	=> '',
				'HOSPITALS' => $this->Hospitals()
			);

			return $data;

		}
		else {

			
			$sql = $this->db->query("SELECT *  FROM subclinic where ID=? AND CLINICID=?  LIMIT 1",array($id,$this->session->CLINICID))->row();	

			if( isset($sql)){

				$sql->TOKEN 	= $this->m_utility->tokenRequest($sql->ID);
				$sql->HOSPITALS = $this->Hospitals();
				return $sql;
			}
			
		}
	}


	private function Hospitals()
	{
		$data = $this->db->query("SELECT * from hospitals ORDER BY NAME")->result();
		return $data;
	}


	public function Submit_Form(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err'] = '';	
		$data['suc'] = array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('HOSPITALID','Hospital','trim');
		$this->form_validation->set_rules('NAME','Name','trim|required');
		$this->form_validation->set_rules('LOCATION','Location','trim');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

				$data['err'] = $this->Check_Redundant($ID,$this->input->post('NAME'),$this->session->CLINICID);

				if( $ID > 0 && empty($data['err']) ){

					$this->db->update('subclinic', array(	
						'HOSPITALID'=> strtoupper($this->input->post('HOSPITALID')),
						'NAME'    	=> strtoupper($this->input->post('NAME')),						
						'LOCATION'  => strtoupper($this->input->post('LOCATION'))
					), array('ID' => $ID) );

				}
				else if ( (int)$ID === 0 && empty($data['err']) ) {

					$this->db->insert('subclinic', array(
						'CLINICID' 	=> $this->session->CLINICID,	
						'HOSPITALID'=> strtoupper($this->input->post('HOSPITALID')),	
						'NAME'    	=> strtoupper($this->input->post('NAME')),						
						'LOCATION'  => strtoupper($this->input->post('LOCATION'))
					));
				}

			}
			else
			{
				$data['err'] .=' Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}


	private function Check_Redundant($ID,$NAME,$CLINICID){

		$sql = $this->db->query("SELECT * FROM subclinic WHERE ID !=? AND NAME=? AND CLINICID=? LIMIT 1",array($ID,$NAME,$CLINICID))->row();

		if( isset($sql) ){
			return 'Subclinic Name is already registered.';
		}

		return '';
	}




}
?>
