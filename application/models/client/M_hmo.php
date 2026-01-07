<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_hmo extends CI_Model
{
	function __construct(){ $this->load->database(); }


	public function Index()
	{

		$sql = $this->db->query("SELECT ID,NAME,ADDRESS,MOBILE,PHONENO FROM hmo 
			where CLINICID=? AND CANCELLED='N'
			ORDER BY NAME ",array($this->session->CLINICID))->result();
		return $sql;
	}

	public function Form_Data($id) {

		if( (int)$id == 0 ) {

			$data = array(
				'TOKEN' 	=> $this->m_utility->tokenRequest(0),
				'ID' 		=> 0,			
				'NAME' 		=> '',		
				'ADDRESS'	=> '',
				'PHONENO'	=> '',
				'MOBILE'	=> ''		
			);

			return $data;

		}
		else {

			
			$sql = $this->db->query("SELECT *  FROM hmo where ID=? AND CLINICID=?  LIMIT 1",array($id,$this->session->CLINICID))->row();	

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
		$this->form_validation->set_rules('ADDRESS','ADDRESS','trim');
		$this->form_validation->set_rules('PHONENO','PHONE NO','trim');
		$this->form_validation->set_rules('MOBILE','MOBILE NO','trim');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

				$data['err'] = $this->Check_Redundant($ID,$this->input->post('NAME'),$this->session->CLINICID);

				if( $ID > 0 && empty($data['err']) ){

					$this->db->update('hmo', array(	
						'NAME' 			=> $this->input->post('NAME'),
						'ADDRESS' 		=> $this->input->post('ADDRESS'),
						'PHONENO' 		=> $this->input->post('PHONENO'),
						'MOBILE' 		=> $this->input->post('MOBILE'),
						'UPDATEDBY' 	=> $this->session->USERID,
						'UPDATEDTIME'	=> date('Y-m-d h:i:s', time())				
					), array('ID' => $ID) );

				}
				else if ( empty($data['err']) ) {

					$this->db->insert('hmo', array(
						'CLINICID' 		=> $this->session->CLINICID,		
						'NAME' 			=> $this->input->post('NAME'),
						'ADDRESS' 		=> $this->input->post('ADDRESS'),
						'PHONENO' 		=> $this->input->post('PHONENO'),
						'MOBILE' 		=> $this->input->post('MOBILE'),
						'CANCELLED' 	=> 'N',
						'CREATEDBY' 	=> $this->session->USERID,
						'CREATEDTIME'	=> date('Y-m-d h:i:s', time())
					));

					$ID = $this->db->insert_id();
				}

				$data['suc']['ID'] = $ID;

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

		$sql = $this->db->query("SELECT * FROM hmo WHERE ID !=? AND NAME=? AND CLINICID=? LIMIT 1",array($ID,$NAME,$CLINICID))->row();

		if( isset($sql) ){
			return 'HMO Name is already registered.';
		}

		return '';
	}




}
?>
