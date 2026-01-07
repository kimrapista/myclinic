<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// admin


class M_hospitals extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


    public function Index()
    {

        $sql = $this->db->query("SELECT * FROM hospitals ")->result(); 

        return $sql;

    }



    public function Form_Data($id)
    {

        if( (int)$id === 0 ) {

            $data = array(
                'TOKEN' => $this->m_utility->tokenRequest(),
                'ID' => 0,
                'NAME' => '',
                'CODE' => '',
                'PMCC' => '',
                'ADDRESS' => '',
                'ZIPCODE' => ''
            );

            return $data;

        }
        else {

            $data = $this->db->query("SELECT *  FROM hospitals where ID =? LIMIT 1",array($id))->row();
            $data->TOKEN = $this->m_utility->tokenRequest($id);
            
            return $data;
        }
    }




    function Submit_Form()
    {

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data=array();

        $data['err']= '';
        $data['suc']=array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','token', 'trim|required');
        $this->form_validation->set_rules('NAME','Hospital Name','trim|required');
        $this->form_validation->set_rules('CODE','Hospital Acronym','trim|required');
        $this->form_validation->set_rules('PMCC','PMCC','trim');
        $this->form_validation->set_rules('ADDRESS','Address','trim');
        $this->form_validation->set_rules('ZIPCODE','Zip Code','trim');
        

        if ($this->form_validation->run() ){

            if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

                $ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

                if( $ID > 0 ){

                    $this->db->update('hospitals', array(
                        'NAME'      => $this->input->post('NAME'),
                        'CODE'      => $this->input->post('CODE'),
                        'PMCC'      => $this->input->post('PMCC'),
                        'ADDRESS'   => $this->input->post('ADDRESS'),
                        'ZIPCODE'   => $this->input->post('ZIPCODE'),
                        'TOKEN'     => $this->m_utility->passwordHash($ID)
                    ), array('ID' => $ID) );

                }
                else {

                    $this->db->insert('hospitals', array(
                        'NAME'      => $this->input->post('NAME'),
                        'CODE'      => $this->input->post('CODE'),
                        'PMCC'      => $this->input->post('PMCC'),
                        'ADDRESS'   => $this->input->post('ADDRESS'),
                        'ZIPCODE'   => $this->input->post('ZIPCODE')
                    ));

                    $ID = $this->db->insert_id();
                }


            }
            else {

                $data['err'] .='Expired request. Please refresh the page.';
            }

        }

        $data['err'] .=validation_errors(' ',' ');

        return $data;
    }





}

?>