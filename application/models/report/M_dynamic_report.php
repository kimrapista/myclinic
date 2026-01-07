<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class M_dynamic_report extends CI_Model
{
	
	function __construct(){ 
      
      $this->load->database();
      
      $this->load->library('tcpdf/reports/dynamic/Pdf');
   }


 
	private function Clinic(){

		$sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID));
		return $sql->row();

   }
   

   public function Report(){

      $_POST += json_decode(file_get_contents('php://input'), true);
      // header report always be call
      // post required
      // clinic id
      // title
      // ECHO  FOR ECHO URL

      $header = $this->db->query("SELECT * FROM reports WHERE CLINICID=? AND TITLE='HEADER' ", array($this->input->post('CLINICID')))->row();

      if( $header ){
         $header->DETAIL = $this->db->query("SELECT * FROM reports_detail WHERE HEADERID=? AND CANCELLED='N'",array($header->ID))->result();
      }

      $TITLE = $this->input->post('TITLE') == 'HEADER' ? 'PRESCRIPTION' : $this->input->post('TITLE');

      $body = $this->db->query("SELECT * FROM reports WHERE CLINICID=? AND TITLE=? ", array($this->input->post('CLINICID'),$TITLE))->row();

      if( $body ){

         $pdf = new Pdf( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

         $pdf->Set_Header_Title($header);
         $pdf->setPrintHeader(true);
         $pdf->setPrintFooter(false);

         $pdf->SetMargins( $body->MARGINLEFT, $body->MARGINTOP, $body->MARGINLEFT, true);
         // $pdf->SetAutoPageBreak(TRUE,12);

         $pdf->AddPage( $body->ORIENTATION, array( $body->WIDTH, $body->HEIGHT));
         
         //A4 width 210.00014444444; half 105
         //A4 height 297.00008333333; half 148.5
         // $pdf->AddPage('P', 'A4');
         

         $pdf->SetFont('calibri', '', 10);
         $pdf->SetTextColor(40,40,40);
         $pdf->Cell(0, 5, 'A4: W:'.$pdf->getPageWidth().', H:'. $pdf->getPageHeight(), 1,0,'L');
         
         $pdf->Output( getcwd().'/temp_files_pdf/sample/sample_'.$this->input->post('CLINICID').'.pdf','F');
      }
   }
}

?>