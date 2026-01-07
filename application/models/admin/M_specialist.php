<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_specialist extends CI_Model
{
	function __construct(){ $this->load->database(); }


	public function Index()
	{

		$sql = $this->db->query("SELECT * FROM specialist ")->result();
		return $sql;
	}


	public function Form_Data($id) {

		if( (int)$id == 0 ) {

			$data = array(
				'TOKEN' 			=> $this->m_utility->tokenRequest(0),
				'ID' 				=> 0,			
				'SPECIALTY' 		=> '',
				'SPECIALTYGROUP' 	=> NULL,
				'SPECIALTYPRACTICE'	=> '',
				'SPECIALTYTITLE' 	=> '',
				'AGEPATIENT' 		=> NULL,
				'DIAG_THERA' 		=> NULL,
				'SURG_IM' 			=> NULL,
				'ORG_TECH' 			=> NULL
			);

			return $data;

		}
		else {

			
			$sql = $this->db->query("SELECT *  FROM specialist where ID=? LIMIT 1",array($id))->row();	

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
		$this->form_validation->set_rules('SPECIALTY','Specialty','trim|required');
		$this->form_validation->set_rules('SPECIALTYGROUP','Specialty Group','trim');
		$this->form_validation->set_rules('SPECIALTYPRACTICE','Fields of specialty practice','trim');
		$this->form_validation->set_rules('SPECIALTYTITLE','Specialist titles','trim');
		$this->form_validation->set_rules('AGEPATIENT','AGE APTIENT','trim');
		$this->form_validation->set_rules('DIAG_THERA','Diagnostics Or Therapeutic','trim');
		$this->form_validation->set_rules('SURG_IM','Surgical or Interal Medicine','trim');
		$this->form_validation->set_rules('ORG_TECH','Organ based or Technique based','trim');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

				$data['err'] = $this->Check_Redundant($ID,$this->input->post('SPECIALTY'));

				if( $ID > 0 && empty($data['err']) ){

					$this->db->update('specialist', array(	
						'SPECIALTY'    		=> $this->input->post('SPECIALTY'),
						'SPECIALTYGROUP'    => $this->input->post('SPECIALTYGROUP'),
						'SPECIALTYPRACTICE'	=> $this->input->post('SPECIALTYPRACTICE'),
						'SPECIALTYTITLE'    => $this->input->post('SPECIALTYTITLE'),
						'AGEPATIENT'    	=> $this->input->post('AGEPATIENT'),
						'DIAG_THERA'    	=> $this->input->post('DIAG_THERA'),
						'SURG_IM'    		=> $this->input->post('SURG_IM'),
						'ORG_TECH'    		=> $this->input->post('ORG_TECH')
					), array('ID' => $ID) );

				}
				else if ( (int)$ID === 0 && empty($data['err']) ) {

					$this->db->insert('specialist', array(
						'SPECIALTY'    		=> $this->input->post('SPECIALTY'),
						'SPECIALTYGROUP'    => $this->input->post('SPECIALTYGROUP'),
						'SPECIALTYPRACTICE'	=> $this->input->post('SPECIALTYPRACTICE'),
						'SPECIALTYTITLE'    => $this->input->post('SPECIALTYTITLE'),
						'AGEPATIENT'    	=> $this->input->post('AGEPATIENT'),
						'DIAG_THERA'    	=> $this->input->post('DIAG_THERA'),
						'SURG_IM'    		=> $this->input->post('SURG_IM'),
						'ORG_TECH'    		=> $this->input->post('ORG_TECH')
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

		$sql = $this->db->query("SELECT * FROM specialist WHERE ID !=? AND SPECIALTY=?  LIMIT 1",array($ID,$NAME))->row();

		if( isset($sql) ){
			return 'SPECIALTY is already registered.';
		}

		return '';
	}




}
?>
