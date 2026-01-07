<?php

defined('BASEPATH') OR exit('No direct script access allowed');





class M_subclinic extends CI_Model

{

	function __construct(){ $this->load->database(); }





	public function Index()

	{



		$sql = $this->db->query("SELECT S.ID, S.NAME, S.LOCATION, S.MONTIME, S.TUETIME, S.WEDTIME, S.THUTIME, S.FRITIME, S.SATTIME, S.SUNTIME, S.ISONLINE,

				S.COORDLONG_0, S.COORDLONG_1, S.COORDSHORT_0, S.COORDSHORT_1,

				H.NAME AS HOSPITALNAME

			FROM subclinic S 

			LEFT JOIN hospitals H ON H.ID = S.HOSPITALID

			where S.CLINICID=? 

			ORDER BY NAME ",array($this->session->CLINICID))->result();



		return $sql;

	}

	



	public function Form_Data($id) {



		$sql = $this->db->query("SELECT *  FROM subclinic where ID=? AND CLINICID=?  LIMIT 1",array($id,$this->session->CLINICID))->row();	



		if( isset($sql)){



			$sql->TOKEN = $this->m_utility->tokenRequest($sql->ID);

			return $sql;

		}

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

		$this->form_validation->set_rules('LOCATION','Address','trim');



		if ($this->form_validation->run()){



			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){



				$data['err'] = $this->Check_Redundant($this->input->post('ID'), $this->input->post('NAME'), $this->session->CLINICID);



				if( empty($data['err']) ){



					if( $this->input->post('ID') > 0 ){



						$this->db->update('subclinic', array(	

							'HOSPITALID'=> $this->input->post('HOSPITALID'),

							'NAME'    	=> $this->input->post('NAME'),						

							'LOCATION'  => $this->input->post('LOCATION'),

							'ISSIG'  => (Boolean)$this->input->post('ISSIG') ? 'Y' : 'N'

						), array('ID' => $this->input->post('ID')) );

	

						$ID = $this->input->post('ID');

					}

					else  {

	

						$this->db->insert('subclinic', array(

							'CLINICID' 	=> $this->session->CLINICID,	

							'HOSPITALID'=> $this->input->post('HOSPITALID'),	

							'NAME'    	=> $this->input->post('NAME'),						

							'LOCATION'  => $this->input->post('LOCATION'),

							'ISSIG'  => (Boolean)$this->input->post('ISSIG') ? 'Y' : 'N'

						));

	

						$ID = $this->db->insert_id();

					}

	

					$data['suc']['ID'] = $ID;



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







	public function Submit_Time(){



		$_POST += json_decode(file_get_contents('php://input'), true);



		$this->load->library('form_validation');

		$this->form_validation->set_rules('TOKEN','token', 'trim|required');	



		if ($this->form_validation->run()){



			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){



				foreach ($this->input->post('SUBCLINICS[]') as $key => $value) {

					

					$this->db->update('subclinic', array(

						'MONTIME'  	=> $value['MONTIME'],

						'TUETIME'  	=> $value['TUETIME'],

						'WEDTIME'  	=> $value['WEDTIME'],

						'THUTIME'  	=> $value['THUTIME'],

						'FRITIME'  	=> $value['FRITIME'],

						'SATTIME'  	=> $value['SATTIME'],

						'SUNTIME'  	=> $value['SUNTIME'],

						'ISONLINE'  => (Boolean)$value['ISONLINE'] ? 'Y' : 'N'

					),

					array('ID' => $value['ID']));

				}

			}

		}



	}





	public function Submit_Set_Map(){



		$_POST += json_decode(file_get_contents('php://input'), true);



		$this->load->library('form_validation');

		$this->form_validation->set_rules('TOKEN','token', 'trim|required');

		$this->form_validation->set_rules('ID','ID','trim');		



		if ($this->form_validation->run()){



			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				

				$this->db->update('subclinic', array(

					'COORDLONG_0' => $this->input->post('COORDLONG_0'),

					'COORDLONG_1' => $this->input->post('COORDLONG_1'),

					'COORDSHORT_0' => $this->input->post('COORDSHORT_0'),

					'COORDSHORT_1' => $this->input->post('COORDSHORT_1'),

				),

				array('ID' => $this->input->post('ID')));

			}

		}

	}



}

?>

