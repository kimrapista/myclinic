<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_account extends CI_Model
{
	
    function __construct(){ $this->load->database(); }
   

    public function My_Account(){

        $data = array(); 
        $data['err'] = '';
        $data['suc'] = array();

        if ( isset($this->session->USERID) ){

            $sql = $this->db->query("SELECT * From users where ID = ? LIMIT 1",array($this->session->USERID))->row();

            $sql->TOKEN = $this->m_utility->tokenRequest($sql->ID);
            $sql->USERPASSWORD = '';
            $sql->SALES = FALSE;
            $sql->BLAST = FALSE;
            $sql->PIRANI = FALSE;
            $sql->OPHTHALMOLOGIST = FALSE;
            $sql->OPTICAL = FALSE;
            $sql->ISADDSERVICES = FALSE;
            $sql->ISDISPLAYSERVICESAMOUNT = FALSE;
            

            $data['suc'] = $sql;
        }

        return $data;
    }




    public function Submit_Username() {

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data=array();

        $data['err']= '';
        $data['suc']=array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','token', 'trim|required');
        $this->form_validation->set_rules('USERNAME','Username','trim|required|min_length[4]');
        $this->form_validation->set_rules('NEWUSERNAME','New Username','trim|required|min_length[4]');
        $this->form_validation->set_rules('PASSWORD','Password','trim|required');


        if ($this->form_validation->run() ){

            if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

                $sql=$this->db->query("SELECT USERPASSWORD From users where USERNAME = ? LIMIT 1",array($this->input->post('USERNAME')))->row();
                    
                if( $sql ){

                    if( password_verify($this->input->post('PASSWORD'), $sql->USERPASSWORD) || $this->m_utility->passwordHash($this->input->post('PASSWORD')) == $sql->USERPASSWORD ){
                    
                        $sql1 = $this->db->query("SELECT ID FROM users WHERE ID !=? AND USERNAME=? LIMIT 1",array( $this->sessions->USERID, $this->input->post('NEWUSERNAME')))->row();

                        if( $sql1 ){
                            $data['err'] .= 'Username is already registered. Please try another usename.';  
                        }
                        else{
                        
                            $this->db->update('users', array(
                                'USERNAME' => $this->input->post('NEWUSERNAME')
                            ),
                            array('ID'=> $this->session->USERID));
                        }
                    }
                    else{
                        $data['err'] .= 'Wrong Password';  
                    }
                }
                else{
                    $data['err'] .= 'Wrong Username';
                }
            }
            else{
                $data['err'] .= 'Invalid Request.';    
            }
      
        }

        $data['err'] .=validation_errors(' ',' ');

        return $data;
    }




    public function Submit_Password() {

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data=array();

        $data['err']= '';
        $data['suc']=array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','token', 'trim|required');
        $this->form_validation->set_rules('CPASSWORD','Current Password','trim|required');
        $this->form_validation->set_rules('NPASSWORD','New Password','trim|required|min_length[5]');
        $this->form_validation->set_rules('RPASSWORD','Re-Type Password','trim|required|min_length[5]');


        if ($this->form_validation->run() ){

            if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

                $ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

                $sql=$this->db->query("SELECT USERPASSWORD From users where  ID = ? LIMIT 1",array($ID))->row();

                if( $sql ) {

                    if( password_verify( $this->input->post('CPASSWORD'), $sql->USERPASSWORD ) || $this->m_utility->passwordHash($this->input->post('CPASSWORD')) == $sql->USERPASSWORD ){

                        if( $this->input->post('NPASSWORD') === $this->input->post('RPASSWORD') ){

                            $newPassword = $this->m_utility->passwordHash($this->input->post('NPASSWORD'));
                            $this->db->update('users',array('USERPASSWORD'=>$newPassword),array('ID'=>$ID));
                        }
                        else{
                            $data['err'] .= ' Mismatch new password';  
                        }
                        
                    }
                    else { 
                        $data['err'] .= ' Wrong Current Password.'; 
                    }
                }
                else {

                    $data['err'] .='Invalid Account.';
                }

            }
            else {

                $data['err'] .=' Invalid Request.';
            }

        }

        $data['err'] .=validation_errors(' ',' ');

        return $data;
    }



    public function Submit_Account() {

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data=array();

        $data['err']= '';
        $data['suc']=array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','token', 'trim|required');
        $this->form_validation->set_rules('NAME','Name','trim|required');
        $this->form_validation->set_rules('SPECIALISTID','Specialist','trim');
        $this->form_validation->set_rules('PTR','PTR No.','trim');
        $this->form_validation->set_rules('LICENSENO','License No.','trim');
        $this->form_validation->set_rules('S2NO','S2 No.','trim');
        $this->form_validation->set_rules('EMAIL','Email','trim');
        $this->form_validation->set_rules('SUBCLINICID','Default Clinic','trim');

        if ($this->form_validation->run() ){

            if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

                $ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

                $this->db->update('users', array(
                    'NAME'          => $this->input->post('NAME'),
                    'JOBTITLE'      => $this->input->post('JOBTITLE'),
                    'SPECIALISTID'  => $this->input->post('SPECIALISTID'),
                    'LICENSENO'     => $this->input->post('LICENSENO'),
                    'PTR'           => $this->input->post('PTR'),
                    'S2NO'          => $this->input->post('S2NO'),
                    'EMAIL'         => $this->input->post('EMAIL'),
                    'SUBCLINICID'   => $this->input->post('SUBCLINICID')
                ),array('ID'=> $ID) );
            }
            else{
                $data['err'] .= 'Invalid Request';
            }
        }
        
        $data['err'] .= validation_errors(' ',' ');

        return $data;
    }

 
    public function Submit_Relogin(){

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data=array();

        $data['err']= '';
        $data['suc']=array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','token', 'trim|required');

        if ($this->form_validation->run() ){

            if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){

                $ID = $this->m_utility->tokenRetrieve($this->input->post('TOKEN'));

                $sql=$this->db->query("SELECT * From users where ID = ? LIMIT 1",array($ID))->row();

                $this->session->set_userdata(array(
                    'USERID'            => $sql->ID,
                    'CLINICID'          => $sql->CLINICID,
                    'POSITION'          => $sql->POSITION
                ));

                $data['suc']['ID'] = $ID;
            }
            else{
                $data['err'] .= 'Invalid Request';
            }
        }
        
        $data['err'] .= validation_errors(' ',' ');

        return $data;
    }
}

?>