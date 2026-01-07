<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_clearance_letter_1 extends CI_Model
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
				mr.CLEARANCETO, mr.CLEARANCEMSG, U.NAME AS DOCTORNAME
				FROM patients p
				INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID
				INNER JOIN clinics c ON c.ID = mr.CLINICID
				LEFT JOIN users U ON U.ID = mr.CREATEDBY
				WHERE mr.ID = ?   
				LIMIT 1",array($id))->row();
		}


		$pdf->Image('uploads/reports/clinic'.$clinic->ID.'_1.png',$pdf->GetX(),$pdf->GetY(),0.6);
		
		$pdf->SetFont('Arial','B',18);
		$pdf->Cell(0,$CH,'LEONARD T. KHU, M.D., FPOA',0,1,'R');
		$pdf->SetFont('Arial','IB',11);
		$pdf->Cell(0,$CH,'Orthopedic Surgery',0,1,'R');

		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,$CH,'Email: nerdk@yahoo.com',0,1,'R');


		$pdf->Ln(0.3);
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(0,$CH,'Clearance Letter',0,1,'C');
		$pdf->Ln(0.2);

		$pdf->SetFont('Arial','',10);
		$pdf->Cell(0,$CH,date('F d,Y',time()),0,1,'R');
		$pdf->Ln($CH);

		$CH = 0.23; 

		if( $id > 0 ){
			
			$pdf->Cell(0,$CH,'To: '.utf8_decode($patient->CLEARANCETO),0,1);
			$pdf->MultiCell(0, $CH,utf8_decode($patient->CLEARANCEMSG),0);

			//$pdf->Output('I','Clearance Letter '.utf8_decode($patient->NAME).'.pdf');
			$pdfPath = 'temp_files_pdf/Clearance_'.$id.'_.pdf';
			$pdf->Output('F', $pdfPath);
			echo base_url($pdfPath);

		}
		else{
			// sample for testing
			$pdf->Cell(0,$CH,'To: Sample Name',0,1);
			$pdf->MultiCell(0, $CH,utf8_decode($clinic->CLEARANCEDEFAULTTEXT),0);
			
			// $pdf->Output('I','Clearance Letter Sample.pdf');
			$pdfPath = 'temp_files_pdf/Clearance_test'.$clinic->ID.'_.pdf';
			$pdf->Output('F', $pdfPath);
			echo base_url($pdfPath);
		}

	}

}

?>
