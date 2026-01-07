<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//assistant

class M_services extends CI_Model
{
	function __construct(){ $this->load->database(); }


	public function Index()
	{
		$sql = $this->db->query("SELECT * FROM services where CLINICID=? ",array($this->session->CLINICID))->result();
		return $sql;
	}

	public function Form_Data($id) {

		if( (int)$id == 0 ) {

			$data = array(
				'TOKEN' 		=> $this->m_utility->tokenRequest(0),
				'ID' 			=> 0,			
				'NAME' 		=> '',
				'PRICE' 		=> 0,
				'AUTOADD'  	=> 'N',
				'CLINICID' 	=> $this->session->CLINICID
			);

			return $data;

		}
		else {

			$sql = $this->db->query("SELECT *  FROM services where ID=? AND CLINICID=?  LIMIT 1",array($id,$this->session->CLINICID))->row();	

			if( isset($sql)){

				$sql->TOKEN 	= $this->m_utility->tokenRequest($sql->ID);
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
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('NAME','NAME','trim|required');
		$this->form_validation->set_rules('PRICE','PRICE','trim|required');
		$this->form_validation->set_rules('AUTOADD','AUTO ADD','trim');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

				$data['err'] = $this->Check_Redundant($ID,$this->input->post('NAME'),$this->session->CLINICID);

				if( $ID > 0 && empty($data['err']) ){

					$this->db->update('services', array(	
						'NAME'     => $this->input->post('NAME'),
						'PRICE'    => $this->input->post('PRICE'),
						'AUTOADD'  => (Boolean)$this->input->post('AUTOADD') ? 'Y' : 'N',
					), array('ID' => $ID));

				}
				else if ( (int)$ID === 0 && empty($data['err']) ) {

					$this->db->insert('services', array(
						'CLINICID'	=> $this->session->CLINICID,
						'NAME'    	=> $this->input->post('NAME')	,				
						'PRICE'    	=> $this->input->post('PRICE'),
						'AUTOADD'  	=> (Boolean)$this->input->post('AUTOADD') ? 'Y' : 'N'
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

		$sql = $this->db->query("SELECT * FROM services WHERE ID !=? AND NAME=? AND CLINICID=? LIMIT 1",array($ID,$NAME,$CLINICID))->row();

		if( isset($sql) ){
			return 'Service Name is already registered.';
		}

		return '';
	}



}
?>
