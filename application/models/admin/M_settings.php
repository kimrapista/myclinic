<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_settings extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	function submit_services()
	{

		$_POST += json_decode(file_get_contents('php://input'), true);

        $data=array('err'=>'','suc'=>array());

        $this->load->library('form_validation');
        $this->form_validation->set_rules('token','token', 'trim|required');
        $this->form_validation->set_rules('list[]','Services List','trim|required');
                               
        if ($this->form_validation->run() == TRUE){

            if( $this->my_utilities->token($this->input->post('token'),1) ){

                $data['suc']['newID'] = FALSE;

            	foreach ($this->input->post('list[]') as $key => $v) 
                {
                    $sql = $this->db->query("SELECT ID FROM services WHERE ID!=? AND CLINICID=? AND NAME=? LIMIT 1",array($v['ID'],$this->session->CLINICID,$v['NAME']));

                    if( !$sql->row() )
                    {
                        if( (int)$v['ID'] > 0)
                        {
                            $this->db->update('services',array('NAME'=>$v['NAME'],'PRICE'=>$v['PRICE']),array('ID'=>$v['ID']));
                        }
                        else
                        {
                            $this->db->insert('services',array('CLINICID'=>$this->session->CLINICID,'NAME'=>$v['NAME'],'PRICE'=>$v['PRICE']));
                            $data['suc']['newID'] = TRUE;
                        }    
                    }
                    else
                    {
                        $data['err'] .='<div><strong>'.$v['NAME'].'</strong> is already registered.</div>';
                    } 
                }

            }
            else
            {
                $data['err'] .='<div>Expired request. Please refresh the page.</div>';
            }

        }
        
        $data['err'] .=validation_errors('<div>','</div>');

        echo json_encode($data);
    }


    function submit_discounts()
    {

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data=array('err'=>'','suc'=>array());

        $this->load->library('form_validation');
        $this->form_validation->set_rules('token','token', 'trim|required');
        $this->form_validation->set_rules('list[]','Services List','trim|required');
                               
        if ($this->form_validation->run() == TRUE){

            if( $this->my_utilities->token($this->input->post('token'),1) ){

                $data['suc']['newID'] = FALSE;

                foreach ($this->input->post('list[]') as $key => $v) 
                {
                    $sql = $this->db->query("SELECT ID FROM discounts WHERE ID!=? AND CLINICID=? AND NAME=? LIMIT 1",array($v['ID'],$this->session->CLINICID,$v['NAME']));
                    if( !$sql->row() )
                    {    
                        if( (int)$v['ID'] > 0)
                        {
                            $this->db->update('discounts',array('NAME'=>$v['NAME'],'PERCENTAGE'=>$v['PERCENTAGE'],'AMOUNT'=>$v['AMOUNT']),array('ID'=>$v['ID']));
                        }
                        else
                        {
                            $this->db->insert('discounts',array('CLINICID'=>$this->session->CLINICID,'NAME'=>$v['NAME'],'PERCENTAGE'=>$v['PERCENTAGE'],'AMOUNT'=>$v['AMOUNT']));
                            $data['suc']['newID'] = TRUE;
                        }    
                    } 
                    else
                    {
                        $data['err'] .='<div><strong>'.$v['NAME'].'</strong> is already registered.</div>';
                    }   
                }

            }
            else
            {
                $data['err'] .='<div>Expired request. Please refresh the page.</div>';
            }

        }
        
        $data['err'] .=validation_errors('<div>','</div>');

        echo json_encode($data);
    }


    function submit_hmo()
    {

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data=array('err'=>'','suc'=>array());

        $this->load->library('form_validation');
        $this->form_validation->set_rules('token','token', 'trim|required');
        $this->form_validation->set_rules('list[]','Services List','trim|required');
                               
        if ($this->form_validation->run() == TRUE){

            if( $this->my_utilities->token($this->input->post('token'),1) )
            {

                $data['suc']['newID'] = FALSE;

                foreach ($this->input->post('list[]') as $key => $v) 
                {
                    
                    $sql = $this->db->query("SELECT ID FROM hmo WHERE ID!=? AND CLINICID=? AND NAME=? LIMIT 1",array($v['ID'],$this->session->CLINICID,$v['NAME']));
                    if( !$sql->row() )
                    {
                        if( (int)$v['ID'] > 0)
                        {
                            $this->db->update('hmo',array('NAME'=>$v['NAME']),array('ID'=>$v['ID']));
                        }
                        else
                        {
                            $this->db->insert('hmo',array('CLINICID'=>$this->session->CLINICID,'NAME'=>$v['NAME']));
                            $data['suc']['newID'] = TRUE;
                        }    
                    }
                    else
                    {
                        $data['err'] .='<div><strong>'.$v['NAME'].'</strong> is already registered.</div>';
                    }
                }
            }
            else{ $data['err'] .='<div>Expired request. Please refresh the page.</div>'; }

        }
        
        $data['err'] .=validation_errors('<div>','</div>');

        echo json_encode($data);
    }



    function submit_medicines()
    {

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data=array('err'=>'','suc'=>array());

        $this->load->library('form_validation');
        $this->form_validation->set_rules('token','token', 'trim|required');
        $this->form_validation->set_rules('list[]','Services List','trim|required');
                               
        if ($this->form_validation->run() == TRUE){

            if( $this->my_utilities->token($this->input->post('token'),1) )
            {

                $data['suc']['newID'] = FALSE;

                foreach ($this->input->post('list[]') as $key => $v) 
                {
                    
                    $sql = $this->db->query("SELECT ID FROM medicines WHERE ID!=? AND CLINICID=? AND NAME=? LIMIT 1",array($v['ID'],$this->session->CLINICID,$v['NAME']));
                    if( !$sql->row() )
                    {
                        if( (int)$v['ID'] > 0)
                        {
                            $this->db->update('medicines',array('NAME'=>$v['NAME']),array('ID'=>$v['ID']));
                        }
                        else
                        {
                            $this->db->insert('medicines',array('CLINICID'=>$this->session->CLINICID,'NAME'=>$v['NAME']));
                            $data['suc']['newID'] = TRUE;
                        }    
                    }
                    else
                    {
                        $data['err'] .='<div><strong>'.$v['NAME'].'</strong> is already registered.</div>';
                    }
                }
            }
            else{ $data['err'] .='<div>Expired request. Please refresh the page.</div>'; }

        }
        
        $data['err'] .=validation_errors('<div>','</div>');

        echo json_encode($data);
    }



    function submit_user()
    {

        $_POST += json_decode(file_get_contents('php://input'), true);

        $data=array();

        $data['err']= '';
        $data['suc']=array();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('TOKEN','token', 'trim|required');
        $this->form_validation->set_rules('NAME','Name','trim|required');
        $this->form_validation->set_rules('JOBTITLE','Job Title','trim|required');
        $this->form_validation->set_rules('POSITION','User Position','trim|required');
        $this->form_validation->set_rules('USERNAME','Username','trim|required|min_length[5]');
        $this->form_validation->set_rules('CANCELLED','Active User','trim');
       
                        
        if ($this->form_validation->run() == TRUE){

            if( $this->my_utilities->token($this->input->post('TOKEN'),1) ){

                $ID = $this->my_utilities->token($this->input->post('TOKEN'),2)[0];
                
                
                $sql=$this->db->query("SELECT USERNAME 
                    From users 
                    where ID != ? AND  USERNAME = ? 
                    LIMIT 1",array($ID,$this->input->post('USERNAME')));
                
                if( $sql->row() )
                {
                    $data['err'] .= '<div>USERNAME is already registered. Please try another username.</div>';
                }
                else
                {

                    if( (int)$ID  === 0 ){

                        $this->db->insert('users',array(
                            'CLINICID' => $this->session->CLINICID,
                            'NAME' => $this->input->post('NAME'),
                            'JOBTITLE' => $this->input->post('JOBTITLE'),
                            'POSITION' => $this->input->post('POSITION'),
                            'USERNAME' => $this->input->post('USERNAME'),
                            'USERPASSWORD' => password_hash( $this->input->post('USERNAME') , PASSWORD_BCRYPT ),
                            'CREATEDBY' => $this->session->userid,
                            'CREATEDTIME' => date('Y-m-d H:i:s',time()),
                            'CANCELLED' => $this->input->post('CANCELLED')
                        ));
                    }else{

                        $this->db->update('users',
                            array(
                                'NAME' => $this->input->post('NAME'),
                                'JOBTITLE' => $this->input->post('JOBTITLE'),
                                'POSITION' => $this->input->post('POSITION'),
                                'USERNAME' => $this->input->post('USERNAME'),
                                'UPDATEDBY' => $this->session->userid,
                                'UPDATEDTIME' => date('Y-m-d H:i:s',time()),
                                'CANCELLED' => $this->input->post('CANCELLED')
                            ),
                            array('ID'=>$ID)
                        );
                    }

                    $data['suc']['redirect'] = base_url('settings');
                }

            }
            else
            {
                $data['err'] .='<div>Expired request. Please refresh the page.</div>';
            }

        }
        
        $data['err'] .=validation_errors('<div>','</div>');

        echo json_encode($data);
    }



}



?>