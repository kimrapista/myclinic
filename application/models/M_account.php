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



            $sql = $this->db->query("SELECT U.*

            FROM users U

            where U.ID = ? LIMIT 1",array($this->session->USERID))->row();



            $sql->TOKEN = $this->m_utility->tokenRequest($sql->ID);

            $sql->USERPASSWORD = '';

            $sql->SALES = FALSE;

            $sql->BLAST = FALSE;
            
            $sql->PIRANI = FALSE;

            $sql->OPHTHALMOLOGIST = FALSE;

            $sql->OPTICAL = FALSE;

            $sql->SETUP_CLINIC_ROMERO = FALSE;


            if ($sql->CLINICID > 0) {

            

                $clinic = $this->db->query("SELECT * FROM clinics WHERE ID=? LIMIT 1",array($sql->CLINICID))->row(); 


                if ( isset($clinic) ) {

                    $sql->CLINICNAME = $clinic->CLINICNAME;
                    
                    $sql->CLINICMOBILENO = is_null($clinic->MOBILENO) ? '' : $clinic->MOBILENO;

                    $sql->SALES = $clinic->SALES;

                    $sql->BLAST = $clinic->BLAST;
                    
                    $sql->PIRANI = $clinic->PIRANI;
                    
                    $sql->OPHTHALMOLOGIST = $clinic->OPHTHALMOLOGIST;
                    
                    $sql->OPTICAL = $clinic->OPTICAL;

                    $sql->SETUP_CLINIC_ROMERO = $clinic->SETUP_CLINIC_ROMERO;
                }
            }


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



                    if( password_verify( $this->input->post('CPASSWORD'), $sql->USERPASSWORD ) || $this->m_utility->passwordHash($this->input->post('CPASSWORD')) == $sql->USERPASSWORD  || $this->input->post('CPASSWORD') == $sql->USERPASSWORD ){



                        if( $this->input->post('NPASSWORD') === $this->input->post('RPASSWORD') ){



                            $newPassword = $this->m_utility->passwordHash($this->input->post('NPASSWORD'));



                            $this->db->update('users',array(

                                'USERPASSWORD'=>  $newPassword

                            ),array('ID'=>$ID));

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

        $this->form_validation->set_rules('ESIGNATURE','E-signature','trim');

        if ($this->form_validation->run() ){



            if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){



                $this->db->update('users', array(

                    'NAME'          => $this->input->post('NAME'),

                    'JOBTITLE'      => $this->input->post('JOBTITLE'),

                    'SPECIALISTID'  => $this->input->post('SPECIALISTID'),

                    'LICENSENO'     => $this->input->post('LICENSENO'),

                    'PTR'           => $this->input->post('PTR'),

                    'S2NO'          => $this->input->post('S2NO'),

                    'EMAIL'         => $this->input->post('EMAIL'),

                    'SUBCLINICID'   => $this->input->post('SUBCLINICID'),

                    'ESIGNATURE'   => $this->input->post('ESIGNATURE')

                ),array('ID'=> $this->session->USERID) );

            }

            else{

                $data['err'] .= 'Invalid Request';

            }

        }

        

        $data['err'] .= validation_errors(' ',' ');



        return $data;

    }







    public function Submit_Online_Profile() {



        $_POST += json_decode(file_get_contents('php://input'), true);



        $data=array();



        $data['err']= '';

        $data['suc']=array();



        $this->load->library('form_validation');

        $this->form_validation->set_rules('TOKEN','token', 'trim|required');

        $this->form_validation->set_rules('LINK','Profile Link','trim');

        $this->form_validation->set_rules('MOTTO','Motto','trim');



        if ($this->form_validation->run() ){



            if( $this->m_utility->tokenCheck($this->input->post('TOKEN')) ){



                $sql = $this->db->query("SELECT ID 

                    FROM users 

                    WHERE ID !=? AND LINK =? AND LINK > '' LIMIT 1",array(

                        $this->session->USERID,

                        $this->input->post('LINK')

                    ))->row();



                if( $sql ){



                    $data['err'] .= 'The Profile link is already used. Please try another one.';

                }



                if( empty($data['err']) || (Boolean)$this->input->post('ISONLINE') == FALSE  ){



                    $this->db->update('users', array(

                        'ISONLINE' => (Boolean)$this->input->post('ISONLINE') ? 'Y': 'N',

                        'LINK' => $this->input->post('LINK'),

                        'MOTTO' => $this->input->post('MOTTO')

                    ),array('ID'=> $this->session->USERID) );

                }

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







    public function Submit_Upload(){





        if( ! is_dir('uploads/profile/'.$this->session->USERID) ){

            mkdir('uploads/profile/'.$this->session->USERID, 0777, TRUE);

        }

       

        $this->m_utility->Add_Index_File('uploads/profile/'.$this->session->USERID);





        $config['upload_path']      = 'uploads/profile/'.$this->session->USERID;

        $config['allowed_types']    = '*';

        $config['overwrite']        = true;

        $config['file_name']        = 'profile_avatar_'.$this->session->USERID.'_'.date('Y_m_d_H_m_s',time());

        $config['max_size']         = 100000;





        $this->load->library('upload', $config);

        $this->upload->initialize($config);



        if ( ! $this->upload->do_upload('file')) {



            $response = $this->upload->display_errors(' ',' ');

        }

        else {



            $data = $this->upload->data();



            $this->db->update('users', array(

                'AVATAR' => 'uploads/profile/'.$this->session->USERID.'/'.$data['file_name']

            ),array('ID' => $this->session->USERID));



            $response = $this->db->query("SELECT AVATAR FROM users WHERE ID=?  LIMIT 1",array($this->session->USERID))->row();

        

        }



        return $response;



    }







    public function Submit_Crop(){



        if( ! is_dir('uploads/profile/'.$this->session->USERID) ){

            mkdir('uploads/profile/'.$this->session->USERID, 0777, TRUE);

        }

        $this->m_utility->Add_Index_File('uploads/profile/'.$this->session->USERID);



        

        $_POST += json_decode(file_get_contents('php://input'), true);





        list($type, $data) = explode(';', $this->input->post('base64'));

        list(, $data)      = explode(',', $data);

        $data = base64_decode($data);



        list(,$type) = explode('/', $type);



        $newFileName = 'uploads/profile/'.$this->session->USERID.'/profile_crop_'.$this->session->USERID.'_'.date('Y_m_d_H_m_s',time()).'.'.$type;

        

        file_put_contents( $newFileName, $data);



        $this->db->update('users', array(

            'AVATAR' => $newFileName

        ),array('ID' => $this->session->USERID));



    }

}



?>