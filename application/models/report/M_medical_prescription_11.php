<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medical_prescription_11 extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	private function clinicInfo(){

		$sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID));
		return $sql->row();

	}


	public function Index($id, $return = FALSE){

		$this->load->library('pdf');
		$pageSize=array(5.25,8.20);

		$this->pdf->__construct('P','in',$pageSize);
		$pdf = $this->pdf;

		$margL=0.4;
		$margT=0.4;
		
		$pdf->SetMargins($margL,$margT,$margL);
		$pdf->SetAutoPageBreak(true);
		$pdf->AddPage();

		$pdf->SetFillColor(40,40,40);

		$pageNo=0;
		$CH = 0.2;
		$CW = 0.8;

		$clinic = $this->clinicInfo();


		$sql = $this->db->query("SELECT p.ID, concat(p.FIRSTNAME,' ',p.MIDDLENAME,' ',p.LASTNAME) as NAME, concat(p.STREETNO) as ADDRESS,concat(p.CITY,' ',p.PROVINCE) as ADDRESS1, p.SEX , mr.APPOINTMENT, mr.APPOINTMENTDATE, mr.AGE,c.MOBILENO
			FROM patients p
			INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID
			INNER JOIN clinics c ON c.ID = mr.CLINICID
			WHERE mr.ID = ?   
			LIMIT 1",array($id));

		$patient = $sql->row();


		$pdf->Image('uploads/reports/clinic'.$clinic->ID.'_1.png', 0.15,$pdf->GetY(),0.6);
		$pdf->Image('uploads/reports/clinic'.$clinic->ID.'_2.png', $pdf->getPageWidth() - 1.05,$pdf->GetY(), 0.9);


		$pdf->SetFont('Times','B',11);
		$pdf->Cell(0,$CH,'MARY JANE ERAN-DEL MUNDO, MD, FPAFP',0,1,'C');

		$pdf->SetFont('Times','',10);
		$pdf->Cell(0,$CH,'Adult and Childrens Diseases',0,1,'C');
		$pdf->Cell(0,$CH,'Family and Community Medicine Specialist',0,1,'C');
		$pdf->Cell(0,$CH,'Ambulatory Diabetes Care Specialist',0,1,'C');

		$pdf->Ln(0.3);

		$CH=0.12;
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(0.5,$CH,"Name: ",0);
		$pdf->Cell(2,$CH,utf8_decode($patient->NAME),'B',0,'C');
		$pdf->Cell(0.3,$CH,"Age:",0);
		$pdf->Cell(0.5,$CH,$patient->AGE,'B',0,'C');

		$pdf->Cell(0.4,$CH," Gender:",0);
		$pdf->Cell(0,$CH,$patient->SEX,'B',1,'C');
		$pdf->Ln(0.05);

		$pdf->Cell(0.5,$CH,"Address:",0);
		$pdf->Cell(2,$CH,utf8_decode($patient->ADDRESS),'B',0);

		$pdf->Cell(0.3,$CH,"Date:",0);
		$pdf->Cell(0,$CH,date('m/d/Y',time()),'B',1,'C');
		$pdf->Ln(0.15);

		
		$pdf->Image('assets/css/images/rx.png',$pdf->GetX(),$pdf->GetY(),0.4);

		
		$pdf->SetFillColor(220,220,220);

		$sql1 = $this->db->query("SELECT m.NAME, mrm.FREQUENCY, mrm.INSTRUCTION , mrm.QUANTITY
			FROM mr_medicines mrm 
			INNER JOIN medicines m ON m.ID = mrm.MEDICINEID
			WHERE mrm.MEDICALRECORDID = ? and mrm.CANCELLED='N' ",array($id));
		$data = $sql1->result();

		if( $data ){

			$pdf->SetFont('Arial','',9);

			foreach ($data as $key => $v) {
				$pdf->Cell(0.5,$CH,'');
				$pdf->Cell(0,$CH,utf8_decode( ($key + 1).'. '.$v->NAME.' '.($v->QUANTITY > 0 ? '#'.$v->QUANTITY: '')),0,1);	
				$pdf->Ln(0.03);
				$pdf->Cell(0.8,$CH,'');
				$pdf->MultiCell(0,$CH,utf8_decode($v->INSTRUCTION .' '.$v->FREQUENCY));	
				$pdf->Ln(0.1);
			}
		}



		$CH = 0.15;

		$q = $this->db->query("SELECT U.NAME, U.PTR, U.LICENSENO, U.S2NO
			FROM users U
			INNER JOIN medicalrecords MR ON MR.CREATEDBY = U.ID
			WHERE MR.ID = ?   
			LIMIT 1",array($id))->row();


		if( $q ){

			$pdf->SetFont('Times','',9);

			$pdf->SetXY(2.5,$pdf->getPageHeight()-1.5);
			$pdf->MultiCell(0,$CH, utf8_encode($q->NAME),'B','L');

			$pdf->SetX(2.5);
			$pdf->MultiCell(0,$CH,"License No: ".$q->LICENSENO."\r\nTIN: ".$clinic->TIN."\r\nPTR No: ".$q->PTR."\r\nS2 License: ".$q->S2NO,0,'L');

		}

		$pdf->SetXY($margL,$pdf->getPageHeight()-1.5);

		$pdf->SetFont('Times','',9);
		$pdf->MultiCell(1.8,$CH,"Please come and see me again on  \r\n\r\n".($patient->APPOINTMENT == 'Y' ? date('m/d/Y',strtotime($patient->APPOINTMENTDATE)) : "")."\r\n ",1,1);
		$pdf->Ln();

		$pdf->SetFont('Times','',8);
		$pdf->SetTextColor(150,150,150);
		$pdf->MultiCell(0, $CH, "BETHEL BAPTIST HOSPITAL\r\nMonday to Friday\r\n10:00 AM - 2:00 PM",0,'C');


		// $pdf->Output('I','Medical Prescription '.utf8_decode($patient->NAME).'.pdf');
		$pdfPath = 'temp_files_pdf/Prescription_'.$id.'_.pdf';
		$pdf->Output('F', $pdfPath);
		// echo base_url($pdfPath);

		if( $return ){
			return base_url($pdfPath);
		}
		else{
			echo base_url($pdfPath);
		}

	}

}

?>