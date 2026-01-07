<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_referral_letter_default extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	private function clinicInfo(){

		$sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID));
		return $sql->row();

	}


	// ----------------- Medical Prescription Default Report ---------------
	public function Index($id){

		$this->load->library('pdf');
		$pageSize=array(5.25,8.20);

		$this->pdf->__construct('P','in',$pageSize);
		$pdf = $this->pdf;


		$margL = 0.3;
		$margT = 0.3;
		$pdf->SetMargins($margL,$margT,$margL);
		$pdf->SetAutoPageBreak(false);
		$pdf->AddPage();

		$pageNo = 0;
		$CH = 0.2;
		$CW = 0.8;

		$clinic = $this->clinicInfo();

		if( $id > 0 ){

			$patient = $this->db->query("SELECT p.ID, concat(p.FIRSTNAME,' ',p.MIDDLENAME,' ',p.LASTNAME) as NAME, concat(p.STREETNO) as ADDRESS,concat(p.CITY,' ',p.PROVINCE) as ADDRESS1, p.SEX , 
				mr.REFERRALTO, mr.REFERRALMSG, U.NAME AS DOCTORNAME
				FROM patients p
				INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID
				INNER JOIN clinics c ON c.ID = mr.CLINICID
				LEFT JOIN users U ON U.ID = mr.CREATEDBY
				WHERE mr.ID = ?   
				LIMIT 1",array($id))->row();
		}

		
		$pdf->SetFont('Arial','B',13);
		$pdf->Cell(0,$CH,utf8_decode($clinic->CLINICNAME),0,1,'C');

		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(0,$CH,$clinic->ADDRESS,0,'C');
		$pdf->Ln(0.3);

		$pdf->SetFont('Arial','',11);
		$pdf->Cell(0,$CH,'Referral Letter',0,1,'C');
		$pdf->Ln(0.2);

		$pdf->SetFont('Arial','',10);
		$pdf->Cell(0,$CH,date('F d,Y',time()),0,1,'R');
		$pdf->Ln($CH);

		$CH = 0.3; 

		if( $id > 0 ){

			$pdf->Cell(0,$CH,'To: '.utf8_decode($patient->REFERRALTO),0,1);
			$pdf->MultiCell(0, $CH,utf8_decode($patient->REFERRALMSG),0);
			$pdf->Ln();
			$pdf->Cell(3,$CH,'Respectfully yours,',0,1);
			$pdf->Ln();
			$pdf->Cell(0,$CH,utf8_decode($patient->DOCTORNAME),0,1);

			// $pdf->Output('I','Referral Letter '.utf8_decode($patient->NAME).'.pdf');
			$pdfPath = 'temp_files_pdf/Referral_default_'.$id.'_.pdf';
			$pdf->Output('F', $pdfPath);
			echo base_url($pdfPath);
		}
		else{

			// sample for testing
			$pdf->Cell(0,$CH,'To: Sample Name',0,1);
			$pdf->MultiCell(0, $CH,utf8_decode($clinic->REFERRALDEFAULTTEXT),0);
			$pdf->Ln();
			$pdf->Cell(3,$CH,'Respectfully yours,',0,1);
			$pdf->Ln();
			$pdf->Cell(0,$CH,'Doctor Name',0,1);

			// $pdf->Output('I','Referral Letter.pdf');
			$pdfPath = 'temp_files_pdf/Referral_default_test_'.$clinic->ID.'_.pdf';
			$pdf->Output('F', $pdfPath);
			echo base_url($pdfPath);
		}


	}

}

?>
