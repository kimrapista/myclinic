<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_Advisory extends CI_Model
{
	function __construct(){ $this->load->database(); }


	public function Index()
	{
		$sql = $this->db->query("SELECT A.*, C.CLINICNAME 
			FROM advisory A 
			LEFT JOIN clinics C ON C.ID = A.CLINICID 
			WHERE A.CANCELLED = 'N' ")->result();

		return $sql;
	} 
 

	public function Form_Data() {

		$_POST += json_decode(file_get_contents('php://input'), true);

		$sql = $this->db->query("SELECT *  FROM advisory where ID=?  LIMIT 1",array($_POST['ID']))->row();	
		
		if( $sql ){	

			$sql->TOKEN = $this->m_utility->tokenRequest($sql->ID);
			return $sql;
		}

	}



	public function Submit_Form(){

		$_POST += json_decode(file_get_contents('php://input'), true);

		$data=array(
			'err' => '',
			'suc' => array()
		);


		$this->load->library('form_validation');
		$this->form_validation->set_rules('TOKEN','token', 'trim|required');
		$this->form_validation->set_rules('CLINICID','CLINIC','trim');
		$this->form_validation->set_rules('TITLE','TITLE','trim');
		$this->form_validation->set_rules('BODY','BODY','trim');
		$this->form_validation->set_rules('LINK','LINK','trim');
		$this->form_validation->set_rules('POST','POST','trim');
		$this->form_validation->set_rules('POSTDATE','POST DATE','trim');
		$this->form_validation->set_rules('CANCELLED','CANCELLED','trim');

		if ($this->form_validation->run()){

			if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

				
				$POST = (Boolean)$this->input->post('POST') ? 'Y' : 'N';

				if( $POST == 'Y' ){
					$POSTDATE = date('Y-m-d H:i:s',strtotime($this->input->post('POSTDATE')));
				}
				else{
					$POSTDATE = NULL;
				}

				if( $this->input->post('ID') > 0 ){

					$this->db->update('advisory', array(	
						'CLINICID'    	=> $this->input->post('CLINICID'),
						'TITLE'    		=> $this->input->post('TITLE'),
						'BODY'    		=> $this->input->post('BODY'),
						'POST'    		=> $POST,
						'POSTDATE'    	=> $POSTDATE,
						'CANCELLED'    	=> (Boolean)$this->input->post('CANCELLED') ? 'Y' : 'N'
					), array('ID' => $this->input->post('ID')) );

					$ID = $this->input->post('ID');
				}
				else {

					$this->db->insert('advisory', array(
						'CLINICID'    	=> $this->input->post('CLINICID'),
						'TITLE'    		=> $this->input->post('TITLE'),
						'BODY'    		=> $this->input->post('BODY'),
						'POST'    		=> $POST,
						'POSTDATE'    	=> $POSTDATE,
						'CANCELLED'    => 'N',
						'CREATEDTIME'	=> date('Y-m-d H:i:s',time())
					));

					$ID = $this->db->insert_id();
				}

				$data['suc']['ID'] = $ID;

			}
			else {

				$data['err'] .='Expired request. Please refresh the page. ';
			}

		}

		$data['err'] .=validation_errors(' ',' ');

		return $data;
	}



}
?>
