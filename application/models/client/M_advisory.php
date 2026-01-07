<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_Advisory extends CI_Model
{
	function __construct(){ $this->load->database(); }


	public function Latest_News()
	{
      $sql = $this->db->query("SELECT TITLE, BODY, POSTDATE
         FROM advisory 
         WHERE POST='Y' AND CANCELLED='N' AND (CLINICID=? OR CLINICID = 0 OR CLINICID IS NULL)
         ORDER BY POSTDATE", array($this->session->CLINICID))->result();

		return $sql;
	}
 

}
?>
