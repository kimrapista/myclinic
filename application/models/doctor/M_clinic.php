<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// doctor

class M_clinic extends CI_Model
{
	
    function __construct(){ $this->load->database(); }


    public function Index() {

        $sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID))->row(); 

        $sql->SUBCLINICS = $this->db->query("SELECT C.*, H.NAME AS HOSPITALNAME 
            FROM subclinic C 
            LEFT JOIN hospitals H ON H.ID = C.HOSPITALID
            WHERE CLINICID=?",array($this->session->CLINICID))->result();

        return $sql;

    }


    public function Form_Data() {

        $data = $this->db->query("SELECT *  FROM clinics where ID =? LIMIT 1",array($this->session->CLINICID))->row();

        $data->TOKEN = $this->m_utility->tokenRequest($this->session->CLINICID);

        return $data;
    }


    function Submit_Form() {

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data=array();

        $data['err']= '';
        $data['suc']=array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','TOKEN', 'trim|required');
        $this->form_validation->set_rules('CLINICNAME','Clinic Title','trim|required');
        $this->form_validation->set_rules('CLINICSUBNAME','1st Specialist','trim');
        $this->form_validation->set_rules('CLINICSUBNAME1','2nd Specialist','trim');
        $this->form_validation->set_rules('TIN','TIN','trim');
        $this->form_validation->set_rules('ADDRESS','Address','trim');
        $this->form_validation->set_rules('CONTACTNO','Tel. No.','trim');
        $this->form_validation->set_rules('MOBILENO','Mobile No.','trim');
        $this->form_validation->set_rules('EMAIL','Email','trim');
        $this->form_validation->set_rules('REFERRALDEFAULTTEXT','Default Referral letter');
        $this->form_validation->set_rules('CLEARANCEDEFAULTTEXT','Default Clearance letter');


        if ($this->form_validation->run() ){

            if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

                $ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

                if( $ID > 0 ){

                    $this->db->update('clinics', array(
                        'CLINICNAME'            => $this->input->post('CLINICNAME'),
                        'CLINICSUBNAME'         => $this->input->post('CLINICSUBNAME'),
                        'CLINICSUBNAME1'        => $this->input->post('CLINICSUBNAME1'),
                        'TIN'                   => $this->input->post('TIN'),
                        'ADDRESS'               => $this->input->post('ADDRESS'),
                        'CONTACTNO'             => $this->input->post('CONTACTNO'),
                        'MOBILENO'              => $this->input->post('MOBILENO'),
                        'EMAIL'                 => $this->input->post('EMAIL'),
                        'REFERRALDEFAULTTEXT'   => $this->input->post('REFERRALDEFAULTTEXT'),
                        'CLEARANCEDEFAULTTEXT'   => $this->input->post('CLEARANCEDEFAULTTEXT'),
                        'UPDATEDBY'             => $this->session->userid,
                        'UPDATEDTIME'           => date('Y-m-d H:i:s',time())
                    ), array('ID' => $ID) );

                    $data['suc']['redirect'] = base_url('clinics');

                    $this->session->MEDICALHISTORY = $this->input->post('MEDICALHISTORY');
                    $this->session->REFRACTION = $this->input->post('REFRACTION');
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