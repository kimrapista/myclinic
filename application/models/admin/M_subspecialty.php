<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_subspecialty extends CI_Model
{
	function __construct(){ $this->load->database(); }


	public function Index()
	{

		$sql = $this->db->query("SELECT * FROM subspecialty ")->result();
		return $sql;
	}


	public function Form_Data($id) {

		if( (int)$id == 0 ) {

			$data = array(
				'TOKEN' 	=> $this->m_utility->tokenRequest(0),
				'ID' 		=> 0,			
				'NAME' 		=> ''
			);

			return $data;

		}
		else {

			
			$sql = $this->db->query("SELECT *  FROM subspecialty where ID=? LIMIT 1",array($id))->row();	

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
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('NAME','NAME','trim|required');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

				$data['err'] = $this->Check_Redundant($ID,$this->input->post('NAME'));

				if( $ID > 0 && empty($data['err']) ){

					$this->db->update('subspecialty', array(	
						'NAME'    	=> $this->input->post('NAME')					
					), array('ID' => $ID) );

				}
				else if ( (int)$ID === 0 && empty($data['err']) ) {

					$this->db->insert('subspecialty', array(
						'NAME'   	=> $this->input->post('NAME')	
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



	private function Check_Redundant($ID,$NAME){

		$sql = $this->db->query("SELECT * FROM subspecialty WHERE ID !=? AND NAME=? LIMIT 1",array($ID,$NAME))->row();

		if( isset($sql) ){
			return 'Subspecialty Name is already registered.';
		}

		return '';
	}




}
?>
