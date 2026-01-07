<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_utility extends CI_Model
{

    function __construct(){}

    public function tokenRequest( $ii = 0, $timeExpired = 3000, $LC = FALSE) {

        $token = time().'-'.mt_rand().'-'.mt_rand().'-'.$ii;

        $token = $this->encryptCode($token);

        return $token;
    }


    public function tokenCheck($ii) {

        if( !empty($this->decryptCode($ii) ) ){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }


    public function tokenRetrieve($request)  {

        $request = explode('-', $this->decryptCode($request));

        if( isset($request[3]) ){
            return $request[3];
        }
        else{
            return 0;
        }
    }


    public function initEncryption(){

        $this->load->library('encryption');
        $this->encryption->initialize(array(
            'driver' => 'openssl',
            'cipher' => 'aes-256',
            'mode'   => 'cbc',
            'key'    => 'Alp6523007***CerebroDiagnosticSy'
        ));

    }


    private function encryptCode($txt) {

        $this->initEncryption();
        return $this->encryption->encrypt($txt); 
    }


    private function decryptCode($dtxt) {

        $this->initEncryption();
        return $this->encryption->decrypt($dtxt);
    }


    public function passwordHash($i){ 

        $this->load->helper('security');
        return do_hash($i, 'md5');
    }


    public function ageCompute($v,$current){

        $age =0;
        $year = date('Y',strtotime($v));
        $month = date('m',strtotime($v));
        $day = date('d',strtotime($v));

        $currentYear = date('Y',strtotime($current));
        $currentMonth = date('m',strtotime($current));
        $currentDay = date('d',strtotime($current));

        $age = $currentYear - $year;


        if( $currentMonth == $month ){

            if( $currentDay < $day ) $age -=1;
        }
        else if ( $currentMonth < $month ){
            $age -=1;
        }

        return (int)$age;
    } 



    function codeGenerator($length=6){

        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $shuffled = str_shuffle($str);
        $code = "";

        for($x=0;$x< $length ;$x++){
            $xx = mt_rand(0,strlen($shuffled));
            $code = $code.substr($shuffled, $xx,1);
        }

        return $code;
    }

    function Num_Generator($length=4){

        $str = '0123456789';

        $shuffled = str_shuffle($str);
        $code = "";

        for($x=0;$x< $length ;$x++){
            $xx = mt_rand(0,strlen($shuffled));
            $code = $code.substr($shuffled, $xx,1);
        }

        return $code;
    }


    function Add_Index_File($path){
        
        $path = $path.'/index.php';

        if ( ! file_exists($path) ) {

            if( copy('uploads/index.php', $path) ){

            }
        }
    }



    public function Email_Header_Template(){

        $body = '<html>';
        $body .='<body style="min-height: 50vh; padding: 4rem; background-image:url('.base_url('assets/css/images/background/clinic.jpg').'); background-repeat: no-repeat; background-position: center; background-size: cover;">';

        $body .= '<img src="'.base_url('assets/css/images/cerebro_banner.png').'" style="display: block; margin: 1rem auto 2rem auto; height: 50px;"> ';
        

        return $body;
    }

    public function Email_Footer_Template(){

        $body = '</body></html>';
        return $body;
    }


}

?>