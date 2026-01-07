<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medical_prescription_5 extends CI_Model
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
		$CH=0.2;
		$CW=0.8;

		$clinic = $this->clinicInfo();


		$sql = $this->db->query("SELECT p.ID, concat(p.FIRSTNAME,' ',p.MIDDLENAME,' ',p.LASTNAME) as NAME, concat(p.STREETNO) as ADDRESS,concat(p.CITY,' ',p.PROVINCE) as ADDRESS1, p.SEX , mr.APPOINTMENT, mr.APPOINTMENTDATE, mr.AGE,c.MOBILENO
			FROM patients p
			INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID
			INNER JOIN clinics c ON c.ID = mr.CLINICID
			WHERE mr.ID = ?   
			LIMIT 1",array($id));

		$patient = $sql->row();

		$pdf->Image('uploads/reports/clinic'.$clinic->ID.'_0.png', $pdf->GetX() + 0.75,$pdf->GetY(), 3);
		
		
		$pdf->Ln(0.4);

		$pdf->SetFont('Arial','',13);
		$pdf->Cell(0,$CH,utf8_decode($clinic->CLINICSUBNAME),0,1,'C');
		
		if( ! empty($clinic->CLINICSUBNAME1) ){
			$pdf->SetFont('Arial','',12);
			$pdf->Cell(0,$CH,utf8_decode($clinic->CLINICSUBNAME1),0,1,'C');
		}
		
		$pdf->Ln(0.2);

		$pdf->SetFont('Arial','',8);
		$pdf->Cell(0,$CH,'Malaybalay Polymedic General Hospital, M. Fortich St., Malaybalay City 8700','',1);

		$pdf->Cell(2,$CH,'Tel.(088)813 - 3209, 0917-8614344','',0);
		$pdf->Cell(0,$CH,'Hospital Affiliations: MMH, SJMC, BPMC, VPGH','',1,'R');
		$pdf->Ln(0.1);
		
		$pdf->MultiCell(0,0.08,'',0,0,true);	

		$pdf->Ln(0.1);

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


		
		$CH = 0.2;

		$q = $this->db->query("SELECT U.NAME, U.PTR, U.LICENSENO, U.S2NO
			FROM users U
			INNER JOIN medicalrecords MR ON MR.CREATEDBY = U.ID
			WHERE MR.ID = ?   
			LIMIT 1",array($id))->row();

		
		if( $q ){

			$pdf->SetFont('Arial','',9);

			$pdf->SetXY(2.3,$pdf->getPageHeight()-1.4);
			$pdf->MultiCell(0,$CH, utf8_encode($q->NAME),'','C');
			
			$pdf->SetX(2.3);
			$pdf->MultiCell(0,$CH,'Signature','T','C');
			$pdf->Ln();

			$pdf->SetFont('Arial','',7);

			$pdf->SetX(2);
			$pdf->MultiCell(0,$CH,'PRC LIC. '.$q->LICENSENO.'; S2 '.$q->S2NO.'; PTR '.$q->PTR,0,'R');

		}

		$pdf->SetXY($margL,$pdf->getPageHeight()-1.2);


		$pdf->SetFont('Arial','B',8);
		$pdf->MultiCell(1.5,$CH,"Next follow up: \r\n".($patient->APPOINTMENT == 'Y' ? date('m/d/Y',strtotime($patient->APPOINTMENTDATE)) : "")."\r\n ",1,1);
		
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