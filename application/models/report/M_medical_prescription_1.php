<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medical_prescription_1 extends CI_Model
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

		$margL=0.3;
		$margT=0.3;
		
		$pdf->SetMargins($margL,$margT,$margL);
		$pdf->SetAutoPageBreak(true);
		$pdf->AddPage();


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

		
		$pdf->Image('uploads/reports/clinic'.$clinic->ID.'_1.png',$pdf->GetX(),$pdf->GetY() - 0.1, 0.7);

		$pdf->SetFont('Arial','B',18);
		$pdf->Cell(0,$CH,'LEONARD T. KHU, M.D., FPOA',0,1,'R');
		$pdf->SetFont('Arial','IB',11);
		$pdf->Cell(0,$CH,'Orthopedic Surgery',0,1,'R');
		$pdf->Ln(0.2);

		$pdf->SetFont('Arial','',9);

		$pdf->Cell(0,$CH,'Email: nerdk@yahoo.com','B',1,'R');
		$pdf->Ln(0.01);
		$pdf->Cell(0,0,'','T',1,'R');	

		$pdf->Ln(0.14);

		$CH=0.15;

		$pdf->SetFont('Arial','',10);

		$pdf->Cell(1,$CH,"Patient's Name: ",0);
		$pdf->Cell(2,$CH,utf8_decode($patient->NAME),'B',0,'C');

		$pdf->Cell(0.35,$CH,"Age:",0);
		$pdf->Cell(0.25,$CH,$patient->AGE,'B',0,'C');

		$pdf->Cell(0.6,$CH,"Gender:",0);
		$pdf->Cell(0,$CH,$patient->SEX,'B',1,'C');
		$pdf->Ln(0.05);

		$pdf->Cell(0.6,$CH,"Address:",0);
		$pdf->Cell(2.75,$CH,utf8_decode($patient->ADDRESS),'B',0);

		$pdf->Cell(0.4,$CH,"Date:",0);
		$pdf->Cell(0,$CH,date('m/d/Y',time()),'B',1,'C');
		$pdf->Ln(0.15);



		
		$pdf->Image('assets/css/images/rx.png',$pdf->GetX(),$pdf->GetY(),0.4);

		
		$pdf->SetFillColor(220,220,220);

		$sql1 = $this->db->query("SELECT m.NAME, mrm.FREQUENCY, mrm.INSTRUCTION , mrm.QUANTITY
			FROM mr_medicines mrm 
			INNER JOIN medicines m ON m.ID = mrm.MEDICINEID
			WHERE mrm.MEDICALRECORDID = ? and mrm.CANCELLED='N' ",array($id));
		$data = $sql1->result();


		$CH = 0.18;

		if( $data ){

			$pdf->SetFont('Arial','',12);

			foreach ($data as $key => $v) {
				$pdf->Cell(0.5,$CH,'');
				$pdf->Cell(0,$CH,utf8_decode( ($key + 1).'.  '.$v->NAME.' '.($v->QUANTITY > 0 ? '  #'.$v->QUANTITY: '')),0,1);	
				$pdf->Cell(0.8,$CH,'');
				$pdf->MultiCell(0,$CH,utf8_decode($v->INSTRUCTION .',  '.$v->FREQUENCY));	
				$pdf->Ln(0.1);
			}
		}


		$CH = 0.2;

		$q = $this->db->query("SELECT U.NAME, U.PTR, U.LICENSENO, U.S2NO
			FROM users U
			INNER JOIN medicalrecords MR ON MR.CREATEDBY = U.ID
			WHERE MR.ID = ?   
			LIMIT 1",array($id))->row();

		$pdf->SetFont('Arial','B',10);

		if( $q ){

			$doctor = $this->db->query("SELECT U.NAME, U.PTR, U.LICENSENO, U.S2NO, U.ESIGNATURE, S.ISSIG
				FROM users U
				INNER JOIN medicalrecords MR ON MR.CREATEDBY = U.ID
                LEFT JOIN subclinic S ON S.ID = MR.SUBCLINICID
				WHERE MR.ID = ?   
				LIMIT 1",array($id))->row();


			$pdf->SetXY(3,$pdf->getPageHeight()-1.7);
			$pdf->MultiCell(0,$CH,utf8_decode($q->NAME),'B',1);

			if( $doctor->ISSIG == 'Y' && !is_null($doctor->ESIGNATURE) && !empty($doctor->ESIGNATURE) ){
				$pdf->Image($doctor->ESIGNATURE, $pdf->GetPageWidth() - 2.3 , $pdf->GetY() - 0.8, 0, 1, 'PNG');
			}

			$pdf->SetXY(3, $pdf->GetY() + .02);
			$pdf->SetFont('Arial','B',9);

			$pdf->MultiCell(0.7,$CH,'Lic. No.');
			$pdf->SetXY(3.5, $pdf->GetY() - $CH);
			$pdf->MultiCell(0,$CH,$q->LICENSENO,'B',1);

			$pdf->SetXY(3, $pdf->GetY() + .02);

			$pdf->MultiCell(0.7,$CH,'PTR No.');
			$pdf->SetXY(3.5, $pdf->GetY() - $CH);
			$pdf->MultiCell(0,$CH,$q->PTR,'B',1);
		}


		$pdf->Ln(0.05);

		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(1.9,$CH,"Your next follow-up visit is on:");
		$pdf->Cell(0,$CH, $patient->APPOINTMENT == 'Y' ? date('m/d/Y',strtotime($patient->APPOINTMENTDATE)) : '' ,'B',1);
		$pdf->Ln(0.05);

		$CH = 0.1;

		$pdf->SetFont('Arial','B',8);

		$pdf->Cell($CH*2,$CH*2," ",1);

		$pdf->SetXY( $pdf->GetX() , $pdf->GetY());
		$pdf->MultiCell(1.5,$CH,'Northern Mindanao Medical Center',0,'L');

		$pdf->SetXY( $pdf->GetX() + 1.7 , $pdf->GetY() - ($CH*2));
		$pdf->MultiCell(1.5,$CH,'Mon - Fri: 7:30 - 10am',0);

		$pdf->SetXY( $pdf->GetX() + 3.4 , $pdf->GetY() - $CH);
		$pdf->MultiCell(0,$CH,'721227 loc. 379',0);

		$pdf->Ln(0.15);
		$pdf->Cell($CH*2,$CH*2," ",1);

		$pdf->SetXY( $pdf->GetX() , $pdf->GetY());
		$pdf->MultiCell(1.5,$CH,'Capitol University Medical Center',0,'L');

		$pdf->SetXY( $pdf->GetX() + 1.7 , $pdf->GetY() - ($CH*2));
		$pdf->MultiCell(1.5,$CH,'Mon - Sat: 11am - 2am',0);

		$pdf->SetXY( $pdf->GetX() + 3.4 , $pdf->GetY() - $CH);
		$pdf->MultiCell(0,$CH,'8564706 loc. 120',0);


		$pdf->Ln(0.15);
		$pdf->Cell($CH*2,$CH*2," ",1);

		$pdf->SetXY( $pdf->GetX() , $pdf->GetY());
		$pdf->MultiCell(1.5,$CH,'Cagayan de Oro Medical Center',0,'L');

		$pdf->SetXY( $pdf->GetX() + 1.7 , $pdf->GetY() - ($CH*2));
		$pdf->MultiCell(1.5,$CH,'Mon - Fri: 3 - 5pm',0);

		$pdf->SetXY( $pdf->GetX() + 3.4 , $pdf->GetY() - $CH);
		$pdf->MultiCell(0,$CH,'8571766',0);


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