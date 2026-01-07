<?php

defined('BASEPATH') OR exit('No direct script access allowed');





class M_users extends CI_Model

{

	function __construct(){ $this->load->database(); }



	public function Index()

	{



		$sql = $this->db->query("SELECT U.ID,U.NAME,U.JOBTITLE, U.POSITION,U.USERNAME,U.CANCELLED, C.CLINICNAME,U.ISADDSERVICES,U.ISDISPLAYSERVICESAMOUNT

			FROM users U 

			LEFT JOIN clinics C ON C.ID = U.CLINICID 

			WHERE U.CLINICID=?=0 ",

			array($this->session->CLINICID))->result();



		return $sql;

	}



	public function Form_Data($id) {



		if( (int)$id == 0 ) {



			$data = array(

				'TOKEN' 	=> $this->m_utility->tokenRequest(),

				'ID' 		=> 0,			

				'NAME' 		=> '',

				'JOBTITLE'	=> '',

				'CLINICID'	=> 0,

				'POSITION'	=> '',

				'CANCELLED' => 'N'

			);



			return $data;



		}

		else {



			$data = $this->db->query("SELECT *  FROM users where ID = ?", array($id))->row();



			if( isset($data) ) {



				$data->TOKEN 	= $this->m_utility->tokenRequest($data->ID);



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

		$this->form_validation->set_rules('CLINICID','CLINICNAME','trim');

		$this->form_validation->set_rules('POSITION','User Position','trim|required');

		$this->form_validation->set_rules('USERNAME','Username','trim|required|min_length[4]');





		if ($this->form_validation->run() ){



			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){



				$ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));



				$data['err'] = $this->Check_Redundant($ID, $this->input->post('USERNAME'));



				if( $ID > 0 && empty($data['err']) ){



					$this->db->update('users', array(

						'NAME'         	=> $this->input->post('NAME'),

						'JOBTITLE'     	=> $this->input->post('JOBTITLE'),

						'CLINICID'    	=> $this->input->post('CLINICID'),

						'POSITION'     	=> $this->input->post('POSITION'),

						'AUTHORIZATION' => (Boolean)$this->input->post('AUTHORIZATION') ? 'Y' : 'N',

						'EDITMR' 		=> (Boolean)$this->input->post('EDITMR') ? 'Y' : 'N',

						'ISADDSERVICES' 		=> (Boolean)$this->input->post('ISADDSERVICES') ? 'Y' : 'N',

						'ISDISPLAYSERVICESAMOUNT' 		=> (Boolean)$this->input->post('ISDISPLAYSERVICESAMOUNT') ? 'Y' : 'N',

						'UPDATEDBY'    	=> $this->session->USERID,

						'UPDATEDTIME'  	=> date('Y-m-d H:i:s',time())

					),

					array('ID'=>$ID) );



					

				}

				else if ( empty($data['err']) ) {



					$this->db->insert('users',array(

						'NAME'         	=> $this->input->post('NAME'),

						'JOBTITLE'     	=> $this->input->post('JOBTITLE'),

						'CLINICID'    	=> $this->input->post('CLINICID'),

						'POSITION'     	=> $this->input->post('POSITION'),

						'AUTHORIZATION' 	=> (Boolean)$this->input->post('AUTHORIZATION') ? 'Y' : 'N',

						'EDITMR' 			=> (Boolean)$this->input->post('EDITMR') ? 'Y' : 'N',

						'ISADDSERVICES' 		=> (Boolean)$this->input->post('ISADDSERVICES') ? 'Y' : 'N',

						'ISDISPLAYSERVICESAMOUNT' 		=> (Boolean)$this->input->post('ISDISPLAYSERVICESAMOUNT') ? 'Y' : 'N',

						'USERNAME'     	=> $this->input->post('USERNAME'),

						'USERPASSWORD' 	=> $this->m_utility->passwordHash('123'),

						'CREATEDBY'    	=> $this->session->USERID,

						'CREATEDTIME'  	=> date('Y-m-d H:i:s',time()),

						'AVATAR'				=> '',

						'CANCELLED'    	=> 'N'

					));



					$ID = $this->db->insert_id();

				}



				$data['suc']['ID'] = $ID;



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







	public function Submit_Active(){



		$_POST += json_decode(file_get_contents('php://input'), true);



		$this->load->library('form_validation');

		$this->form_validation->set_rules('ID','ID', 'trim|required');

		$this->form_validation->set_rules('ACTIVE','Active','trim');



		if ($this->form_validation->run() ){

 

			// cancelled act as Active

			$this->db->update('users', 

				array('CANCELLED'=> ((Boolean)$this->input->post('ACTIVE') ? 'N' : 'Y') ),

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

