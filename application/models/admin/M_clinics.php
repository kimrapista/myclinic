<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class M_clinics extends CI_Model
{

	

    function __construct(){ $this->load->database(); }


    public function Index()
    {



        $sql = $this->db->query("SELECT ID, CLINICNAME, CLINICSUBNAME, CLINICSUBNAME1, CONTACTNO, MOBILENO, ADDRESS, SALES, BLAST, PIRANI, OPHTHALMOLOGIST, OPTICAL, SETUP_CLINIC_ROMERO FROM clinics ")->result(); 



        foreach ($sql as $key => $v) {



            $v->USERS = $this->db->query("SELECT NAME 

            FROM users WHERE CLINICID=? 

            ORDER BY POSITION, NAME",

            array($v->ID))->result();

        }



        return $sql;
    }

 





    public function Form_Data() {

        $_POST += json_decode(file_get_contents('php://input'), true);



        $data = $this->db->query("SELECT * FROM clinics where ID =? LIMIT 1",array($_POST['ID']))->row();

        $data->TOKEN = $this->m_utility->tokenRequest($_POST['ID']);

        return $data;

    }



    function Submit_Form() {



        $_POST += json_decode(file_get_contents('php://input'), true);



        $data=array(

            'err' => '',

            'suc' => []

        );





        $this->load->library('form_validation');

        $this->form_validation->set_rules('TOKEN','token', 'trim|required');

        $this->form_validation->set_rules('HOSPITALID','Hospital ID','trim');

        $this->form_validation->set_rules('CLINICNAME','Clinic Name','trim|required');

        $this->form_validation->set_rules('CLINICSUBNAME','1st Specialist','trim');

        $this->form_validation->set_rules('CLINICSUBNAME1','2nd Specialist','trim');

        $this->form_validation->set_rules('TIN','TIN','trim');

        $this->form_validation->set_rules('ADDRESS','Address','trim');

        $this->form_validation->set_rules('CONTACTNO','Tel. No.','trim');

        $this->form_validation->set_rules('MOBILENO','Mobile No.','trim');



        $this->form_validation->set_rules('DOCTORNAME','Doctor Name','trim');

        $this->form_validation->set_rules('PTR','PTR','trim');

        $this->form_validation->set_rules('LICENSENO','License No,','trim');

        $this->form_validation->set_rules('EMAIL','Email','trim');

        $this->form_validation->set_rules('S2NO','S2 Number','trim');







        if ($this->form_validation->run() ){



            if( $this->m_utility->tokenCheck($this->input->post('TOKEN'),1) ){



                if( $this->input->post('ID') > 0 ){



                    $this->db->update('clinics', array(

                        'HOSPITALID'        => $this->input->post('HOSPITALID'),

                        'CLINICNAME'        => $this->input->post('CLINICNAME'),

                        'CLINICSUBNAME'     => $this->input->post('CLINICSUBNAME'),

                        'CLINICSUBNAME1'    => $this->input->post('CLINICSUBNAME1'),

                        'TIN'               => $this->input->post('TIN'),

                        'ADDRESS'           => $this->input->post('ADDRESS'),

                        'CONTACTNO'         => $this->input->post('CONTACTNO'),

                        'MOBILENO'          => $this->input->post('MOBILENO'),

                        'DOCTORNAME'        => $this->input->post('DOCTORNAME'),

                        'PTR'               => $this->input->post('PTR'),

                        'LICENSENO'         => $this->input->post('LICENSENO'),

                        'EMAIL'             => $this->input->post('EMAIL'),

                        'S2NO'              => $this->input->post('S2NO'),

                        'SALES'             => (Boolean)$this->input->post('SALES') ? 'Y' : 'N',

                        'BLAST'             => (Boolean)$this->input->post('BLAST') ? 'Y' : 'N',

                        'PIRANI'            => (Boolean)$this->input->post('PIRANI') ? 'Y' : 'N',

                        'OPHTHALMOLOGIST'   => (Boolean)$this->input->post('OPHTHALMOLOGIST') ? 'Y' : 'N',

                        'OPTICAL'           => (Boolean)$this->input->post('OPTICAL') ? 'Y' : 'N',

                        'SETUP_CLINIC_ROMERO'     => (Boolean)$this->input->post('SETUP_CLINIC_ROMERO') ? 'Y' : 'N',

                        'UPDATEDBY'         => $this->session->userid,

                        'UPDATEDTIME'       => date('Y-m-d H:i:s',time())

                    ), array('ID' => $this->input->post('ID')) );



                    $ID = $this->input->post('ID');

                }

                else {



                    $this->db->insert('clinics', array(

                        'HOSPITALID'        => $this->input->post('HOSPITALID'),

                        'CLINICNAME'        => $this->input->post('CLINICNAME'),

                        'CLINICSUBNAME'     => $this->input->post('CLINICSUBNAME'),

                        'CLINICSUBNAME1'    => $this->input->post('CLINICSUBNAME1'),

                        'TIN'               => $this->input->post('TIN'),

                        'ADDRESS'           => $this->input->post('ADDRESS'),

                        'CONTACTNO'         => $this->input->post('CONTACTNO'),

                        'MOBILENO'          => $this->input->post('MOBILENO'),

                        'DOCTORNAME'        => $this->input->post('DOCTORNAME'),

                        'PTR'               => $this->input->post('PTR'),

                        'LICENSENO'         => $this->input->post('LICENSENO'),

                        'EMAIL'             => $this->input->post('EMAIL'),

                        'S2NO'              => $this->input->post('S2NO'),

                        'SALES'             => (Boolean)$this->input->post('SALES') ? 'Y' : 'N',

                        'BLAST'             => (Boolean)$this->input->post('BLAST') ? 'Y' : 'N',

                        'PIRANI'            => (Boolean)$this->input->post('PIRANI') ? 'Y' : 'N',

                        'OPHTHALMOLOGIST'   => (Boolean)$this->input->post('OPHTHALMOLOGIST') ? 'Y' : 'N',

                        'OPTICAL'           => (Boolean)$this->input->post('OPTICAL') ? 'Y' : 'N',

                        'SETUP_CLINIC_ROMERO'     => (Boolean)$this->input->post('SETUP_CLINIC_ROMERO') ? 'Y' : 'N',

                        'CREATEDBY'         => $this->session->userid,

                        'CREATEDTIME'       => date('Y-m-d H:i:s',time()),

                        'CANCELLED'         => 'N'

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







    function Report_Forms(){



        $_POST += json_decode(file_get_contents('php://input'), true);



        $sql = $this->db->query("SELECT * FROM reports where CLINICID =? AND TITLE=? LIMIT 1",array($_POST['CLINICID'], $_POST['TITLE']))->row();



        if( $sql ){

            

            $sql->DETAIL = $this->db->query("SELECT * FROM reports_detail where HEADERID=? AND CANCELLED='N' ",array($sql->ID))->result();

        }

        else{



            $this->db->insert('reports', array(

                'CLINICID' => $_POST['CLINICID'],

                'TITLE' => $_POST['TITLE'],

                'WIDTH' => 0,

                'HEIGHT' => 0,

                'ORIENTATION' => 'P',

                'MARGINTOP' => 0,

                'MARGINLEFT' => 0

            ));



            $sql = $this->db->query("SELECT * FROM reports where ID=? LIMIT 1",array($this->db->insert_id()))->row();



            $sql->DETAIL = $this->db->query("SELECT * FROM reports_detail where HEADERID=? AND CANCELLED='N' ",array($sql->ID))->result();

        }





        return $sql;

    }





    function Submit_Report_Forms(){



        $_POST += json_decode(file_get_contents('php://input'), true);



        $this->db->update('reports',array(

            'ORIENTATION' => $this->input->post('ORIENTATION'),

            'WIDTH' => $this->input->post('WIDTH'),

            'HEIGHT' => $this->input->post('HEIGHT'),

            'MARGINTOP' => $this->input->post('MARGINTOP'),

            'MARGINLEFT' => $this->input->post('MARGINLEFT')

        ), array('ID' => $this->input->post('ID')));



        $DETAIL = [];



        if( $this->input->post('DETAIL[]') ){



            foreach ($this->input->post('DETAIL[]') as $key => $v) {

                

                if( $v['ID'] == 0 ){



                    $this->db->insert('reports_detail',array(

                        'HEADERID' => $v['HEADERID'],

                        'COMPONENTTYPE' => $v['COMPONENTTYPE'],

                        'DESCRIPTION' => $v['DESCRIPTION'],

                        'COORDX' => $v['COORDX'],

                        'COORDY' => $v['COORDY'],

                        'WIDTH' => $v['WIDTH'],

                        'HEIGHT' => $v['HEIGHT'],

                        'ALIGN' => $v['ALIGN'],

                        'FONT' => $v['FONT'],

                        'FONTSIZE' => $v['FONTSIZE'],

                        'FONTBOLD' => (Boolean)$v['FONTBOLD'] ? 'Y' : 'N',

                        'FILLCOLOR1' => $v['FILLCOLOR1'],

                        'FILLCOLOR2' => $v['FILLCOLOR2'],

                        'FILLCOLOR3' => $v['FILLCOLOR3'],

                        'TEXTCOLOR1' => $v['TEXTCOLOR1'],

                        'TEXTCOLOR2' => $v['TEXTCOLOR2'],

                        'TEXTCOLOR3' => $v['TEXTCOLOR3'],

                        'BORDERT' => (Boolean)$v['BORDERT'] ? 'Y' : 'N',

                        'BORDERR' => (Boolean)$v['BORDERR'] ? 'Y' : 'N',

                        'BORDERB' => (Boolean)$v['BORDERB'] ? 'Y' : 'N',

                        'BORDERL' => (Boolean)$v['BORDERL'] ? 'Y' : 'N',

                        'BORDERCOLOR1' => $v['BORDERCOLOR1'],

                        'BORDERCOLOR2' => $v['BORDERCOLOR2'],

                        'BORDERCOLOR3' => $v['BORDERCOLOR3'],

                        'CANCELLED' => 'N',

                        'CREATEDTIME' => date('Y-m-d H:i:s',strtotime($v['CREATEDTIME']))

                    ));



                    $DETAIL[] = array(

                        'ID' => $this->db->insert_id(),

                        'TEMPID' => $v['TEMPID']

                    );

                }

                else{ 



                    $this->db->update('reports_detail',array(

                        'COMPONENTTYPE' => $v['COMPONENTTYPE'],

                        'DESCRIPTION' => $v['DESCRIPTION'],

                        'COORDX' => $v['COORDX'],

                        'COORDY' => $v['COORDY'],

                        'WIDTH' => $v['WIDTH'],

                        'HEIGHT' => $v['HEIGHT'],

                        'ALIGN' => $v['ALIGN'],

                        'FONT' => $v['FONT'],

                        'FONTSIZE' => $v['FONTSIZE'],

                        'FONTBOLD' => (Boolean)$v['FONTBOLD'] ? 'Y' : 'N',

                        'FILLCOLOR1' => $v['FILLCOLOR1'],

                        'FILLCOLOR2' => $v['FILLCOLOR2'],

                        'FILLCOLOR3' => $v['FILLCOLOR3'],

                        'TEXTCOLOR1' => $v['TEXTCOLOR1'],

                        'TEXTCOLOR2' => $v['TEXTCOLOR2'],

                        'TEXTCOLOR3' => $v['TEXTCOLOR3'],

                        'BORDERT' => (Boolean)$v['BORDERT'] ? 'Y' : 'N',

                        'BORDERR' => (Boolean)$v['BORDERR'] ? 'Y' : 'N',

                        'BORDERB' => (Boolean)$v['BORDERB'] ? 'Y' : 'N',

                        'BORDERL' => (Boolean)$v['BORDERL'] ? 'Y' : 'N',

                        'BORDERCOLOR1' => $v['BORDERCOLOR1'],

                        'BORDERCOLOR2' => $v['BORDERCOLOR2'],

                        'BORDERCOLOR3' => $v['BORDERCOLOR3'],

                        'CANCELLED' => (Boolean)$v['CANCELLED'] ? 'Y' : 'N',

                    ),array('ID'=> $v['ID']));    

                }

            }

        }  



        return array('DETAIL' => $DETAIL);



    }





}



?>

