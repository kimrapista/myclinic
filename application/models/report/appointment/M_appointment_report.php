<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_appointment_report extends CI_Model
{

	function __construct(){ 

		$this->load->database(); 
      	$this->load->library('tcpdf/reports/sales_summary/Pdf');
	}

	public function PDF(){

		$_POST += json_decode(file_get_contents('php://input'), true);
		$this->load->library('form_validation');

      $SEARCH = $this->input->post('SEARCH',TRUE);
		$DATEFROM = $this->input->post('DATEFROM',TRUE);
		$DATETO = $this->input->post('DATETO',TRUE);
		

		$pdf = new Pdf( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$pdf->SetCreator('TCPDF');
		$pdf->SetAuthor('Cerebro Diagnostic System');
		$pdf->SetTitle('Appointment Report');
		$pdf->SetSubject('Appointment Report');

		$pdf->SetMargins(10, 23, 10, true);
		$pdf->SetAutoPageBreak(TRUE,12);
		
		$pdf->Set_Header_Title('APPOINTMENT REPORT');
		$pdf->setPrintHeader(TRUE);
		$pdf->setPrintFooter(TRUE);
		
		$pdf->SetTextColor(40,40,40);
		$pdf->SetFillColor(220,220,220);
		$pdf->SetDrawColor(210,210,210);
		
		$pdf->AddPage('P', 'A4');
		
		$clinic = $this->db->query("SELECT * FROM clinics WHERE ID=? LIMIT 1",array($this->session->CLINICID))->row();

		
		$pdf->SetFont('calibri', '', 9);

		$pdf->Cell(25,0, 'CLINIC',1,0,'L',TRUE);
		$pdf->Cell(100,0, $clinic->CLINICNAME, 1, 0);
		$pdf->Cell(25,0, 'DATE COVERED',1,0,'L',TRUE);
		$pdf->Cell(0, 0,  date('m/d/Y',strtotime($DATEFROM)).' - '.date('m/d/Y',strtotime($DATETO)) , 1, 1,'L');

		$pdf->Cell(25,0, 'DOCTOR',1,0,'L',TRUE);
		$pdf->Cell(100,0, $clinic->DOCTORNAME, 1, 0);
		$pdf->Cell(25,0, 'DATE PRINTED',1,0,'L',TRUE);
		$pdf->Cell(0, 0,  date('m/d/Y H:i A', time()) , 1, 1,'L');

		$pdf->Cell(25,0, 'ADDRESS',1,0,'L',TRUE);
		$pdf->Cell(0,0, $clinic->ADDRESS, 1, 1);
		$pdf->Ln();


      // patients
      $pat = $this->db->query("SELECT MR.ID, MR.CHECKUPDATE,	MR.APPOINTMENTDATE,	MR.DIAGNOSIS, MR.APPOINTMENTDESCRIPTION,
		 	   CONCAT(P.LASTNAME,', ',P.FIRSTNAME,' ',P.MIDDLENAME) AS PATIENTNAME, P.MOBILENO,
		 	   S.NAME AS FROMCLINIC,
            S1.NAME AS APPOINTCLINIC

            FROM patients P 
            INNER JOIN medicalrecords MR  ON MR.PATIENTID = P.ID
            INNER JOIN subclinic S ON S.ID = MR.SUBCLINICID
            LEFT JOIN users U ON U.ID = MR.CREATEDBY
            LEFT JOIN subclinic S1 ON S1.ID = MR.APPOINTMENTSUBCLINICID
            WHERE MR.CLINICID = ?
            AND ( concat(P.FIRSTNAME,' ',P.LASTNAME) like ? OR concat(P.LASTNAME,' ',P.FIRSTNAME) like ?)
            AND APPOINTMENT = 'Y'
            AND DATE(MR.APPOINTMENTDATE) BETWEEN ? AND ?
            AND P.CANCELLED = 'N' 
            AND MR.CANCELLED = 'N'
            ORDER BY P.LASTNAME, P.FIRSTNAME",
            array(
                $this->session->CLINICID, 
                '%'.$SEARCH.'%',
                '%'.$SEARCH.'%',
                date('Y-m-d',strtotime($DATEFROM)),
               date('Y-m-d',strtotime($DATETO))
            ))->result();

      $pdf->SetFont('calibri', '', 8);
      $pdf->Cell(10,0, '#',1,0,'C',TRUE);
      $pdf->Cell(20,0, 'APPOINTMENT',1,0,'C',TRUE);
      $pdf->Cell(40,0, 'PATIENT NAME',1,0,'C',TRUE);
      $pdf->Cell(20,0, 'LAST CHECKUP',1,0,'C',TRUE);
      $pdf->Cell(30,0, 'CONTACT NO',1,0,'C',TRUE);
      $pdf->Cell(0,0, 'BRING',1,1,'C',TRUE);
      
  

      foreach ($pat as $key => $value) {

         $pdf->Cell(10,0, ($key+1),1,0,'C');
         $pdf->Cell(20,0, date('m/d/Y',strtotime($value->APPOINTMENTDATE)),1,0,'C');
         $pdf->Cell(40,0,  $value->PATIENTNAME,1,0,'L');
         $pdf->Cell(20,0, date('m/d/Y',strtotime($value->CHECKUPDATE)),1,0,'C');
         $pdf->Cell(30,0, $value->MOBILENO,1,0,'C');    
         $pdf->Cell(0,0, $value->APPOINTMENTDESCRIPTION,1,1,'C');  

         $pdf->Cell(0,0, 'DIAGNOSIS',1,1,'C',TRUE);
         $pdf->MultiCell(0, 0 , $value->DIAGNOSIS, 1,'L');
       	$pdf->Ln(5);
 
      }
  

     
      $pdfName = 'temp_files_pdf/Appointment_Detail_'.$this->session->CLINICID.'_'.date('Y_m_d',strtotime($DATEFROM)).'_'.date('Y_m_d',strtotime($DATETO)).'.pdf';
      $pdf->Output( getcwd().'/'.$pdfName,'F');
		echo base_url($pdfName);
	}


	

}
?>