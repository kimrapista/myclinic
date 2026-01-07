<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_users extends CI_Model
{
	function __construct(){ $this->load->database(); }



	public function Index()
	{

		$sql = $this->db->query("SELECT U.ID,U.NAME,U.POSITION,U.USERNAME,U.CANCELLED,
			S.NAME AS SUBCLINIC
			FROM users U 
			LEFT JOIN subclinic S ON S.ID = U.SUBCLINICID
			WHERE U.ID !=? AND U.CLINICID=?
			ORDER BY U.NAME ASC",array($this->session->userid,$this->session->CLINICID))->result();

		return $sql;
	}

	public function Form_Data($id) {

		if( (int)$id == 0 ) {

			$data = array(
				'TOKEN' 	=> $this->m_utility->tokenRequest(),
				'URL' 		=> base_url('settings/users/submit-form'),
				'ID' 		=> 0,			
				'NAME' 		=> '',
				'JOBTITLE'	=> '',
				'POSITION'	=> '',
				'CLINICID' 	=> '',
				'AUTHORIZATION' => 'N',
				'EDITMR'		=> 'N',
				'ISADDSERVICES'		=> 'N',
				'ISADDSERVICESNOAMOUNT'		=> 'N',
				'CANCELLED' => 'N',
				'SUBCLINICID' => NULL,
				'CLINICS'	=> $this->list_of_subclinic()	
			);

			return $data;

		}
		else {

			$data = $this->db->query("SELECT *  FROM users where ID !=? AND ID = ? AND CLINICID=?  LIMIT 1",
				array($this->session->userid,$id,$this->session->CLINICID))->row();

			if( isset($data) ) {

				$data->TOKEN 	= $this->m_utility->tokenRequest($data->ID);
				$data->CLINICS = $this->list_of_subclinic();

				return $data;
			}
			else{

				return array();
			}
		}
	}


	public function Submit_Form(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array();

		$data['err']= '';
		$data['suc']=array();

		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('NAME','Name','trim|required');
		$this->form_validation->set_rules('JOBTITLE','Job Title','trim');
		$this->form_validation->set_rules('SUBCLINICID','Clinic','trim');
		$this->form_validation->set_rules('AUTHORIZATION','Authorization','trim');
		$this->form_validation->set_rules('EDITMR','Edit Previous Medical Record','trim');
		$this->form_validation->set_rules('ISADDSERVICES','Add Services','trim');
		$this->form_validation->set_rules('ISADDSERVICESNOAMOUNT','Add services No Amount','trim');
		$this->form_validation->set_rules('POSITION','User Position','trim|required');
		$this->form_validation->set_rules('USERNAME','Username','trim|required|min_length[4]');
		$this->form_validation->set_rules('CANCELLED','Active User','trim');


		if ($this->form_validation->run() ){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				$ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));


				$data['err'] = $this->Check_Redundant($ID,$this->input->post('USERNAME'));


				if( $ID > 0 && empty($data['err']) ){

					$this->db->update('users', array(
						'NAME' 			=> $this->input->post('NAME'),
						'JOBTITLE' 		=> $this->input->post('JOBTITLE'),
						'SUBCLINICID' 	=> $this->input->post('SUBCLINICID'),
						'POSITION' 		=> $this->input->post('POSITION'),
						'AUTHORIZATION' => (Boolean)$this->input->post('AUTHORIZATION') ? 'Y' : 'N',
						'EDITMR' 		=> (Boolean)$this->input->post('EDITMR') ? 'Y' : 'N',
						'ISADDSERVICES' 		=> (Boolean)$this->input->post('ISADDSERVICES') ? 'Y' : 'N',
						'ISADDSERVICESNOAMOUNT' 		=> (Boolean)$this->input->post('ISADDSERVICESNOAMOUNT') ? 'Y' : 'N',
						'USERNAME' 		=> $this->input->post('USERNAME'),
						'UPDATEDBY' 	=> $this->session->userid,
						'UPDATEDTIME' 	=> date('Y-m-d H:i:s',time()),
						'CANCELLED' 	=> (Boolean)$this->input->post('CANCELLED') ? 'Y' : 'N'
					),
					array('ID'=>$ID) );

					
				}
				else if ( (int)$ID === 0 && empty($data['err']) ) {

					$this->db->insert('users',array(
						'CLINICID' 		=> $this->session->CLINICID,
						'NAME' 			=> $this->input->post('NAME'),
						'JOBTITLE' 		=> $this->input->post('JOBTITLE'),
						'SUBCLINICID' 	=> $this->input->post('SUBCLINICID'),
						'POSITION' 		=> $this->input->post('POSITION'),
						'AUTHORIZATION' => (Boolean)$this->input->post('AUTHORIZATION') ? 'Y' : 'N',
						'EDITMR' 		=> (Boolean)$this->input->post('EDITMR') ? 'Y' : 'N',
						'ISADDSERVICES' 		=> (Boolean)$this->input->post('ISADDSERVICES') ? 'Y' : 'N',
						'ISADDSERVICESNOAMOUNT' 		=> (Boolean)$this->input->post('ISADDSERVICESNOAMOUNT') ? 'Y' : 'N',
						'USERNAME' 		=> $this->input->post('USERNAME'),
						'USERPASSWORD' 	=> $this->m_utility->passwordHash('123'),
						'CREATEDBY' 	=> $this->session->userid,
						'CREATEDTIME' 	=> date('Y-m-d H:i:s',time()),
						'CANCELLED' 	=> (Boolean)$this->input->post('CANCELLED') ? 'Y' : 'N'
					));
				}


			}
			else {

				$data['err'] .='Expired request. Please refresh the page.';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}



	private function Check_Redundant($ID,$NAME){

		$sql = $this->db->query("SELECT * FROM users WHERE ID !=? AND USERNAME=? LIMIT 1",array($ID,$NAME))->row();

		if( isset($sql) ){
			return 'Username is already registered.';
		}

		return '';
	}


	private function list_of_subclinic()
	{
		$sql = $this->db->query("SELECT * FROM subclinic where CLINICID=? order by NAME ",array($this->session->CLINICID))->result();
		return $sql;
	}



	public function Submit_Active(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('ID','ID', 'trim|required');
		$this->form_validation->set_rules('CANCELLED','CANCELLED','trim');

		if ($this->form_validation->run() ){

			$this->db->update('users', 
				array('CANCELLED'=> ((Boolean)$this->input->post('CANCELLED') ? 'N' : 'Y') ),
				array('ID' => $this->input->post('ID'))
			);
		}
	}


	public function Submit_Reset_Password(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$this->load->library('form_validation');
		$this->form_validation->set_rules('ID','ID', 'trim|required');

		if ($this->form_validation->run() ){

			$this->db->update('users', 
				array('USERPASSWORD' => $this->m_utility->passwordHash('123')),
				array('ID' => $this->input->post('ID'))
			);
		}
	}

}
?>
