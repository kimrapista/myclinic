<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medical_prescription_12 extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	private function clinicInfo(){

		$sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID));
		return $sql->row();

	}

	public function Index($id, $return = FALSE){

		$this->load->library('pdf');
		$pageSize=array(3.75,3.75);

		$this->pdf->__construct('P','in',$pageSize);
		$pdf = $this->pdf;

		$margL = 0.1;
		$margT = 0.1;

		$pdf->SetMargins($margL,$margT,$margL);
		$pdf->SetAutoPageBreak(true);
		$pdf->AddPage();

		$pageNo=0;
		$CH=0.15;
		$CW=0.8;

		$clinic = $this->clinicInfo();

		$sql = $this->db->query("SELECT p.ID, concat(p.FIRSTNAME,' ',p.MIDDLENAME,' ',p.LASTNAME) as NAME, concat(p.STREETNO) as ADDRESS,concat(p.CITY,' ',p.PROVINCE) as ADDRESS1, p.SEX , mr.APPOINTMENT, mr.APPOINTMENTDATE, mr.AGE,c.MOBILENO, mr.HRID
			FROM patients p
			INNER JOIN medicalrecords mr ON mr.PATIENTID = p.ID
			INNER JOIN clinics c ON c.ID = mr.CLINICID
			WHERE mr.ID = ?   
			LIMIT 1",array($id));

		$patient = $sql->row();


		$pdf->Image('uploads/reports/clinic'.$clinic->ID.'_1.png', $pdf->getPageWidth() - (($margL*2) + 0.45) , $margT+0.05,0.45);

		$pdf->SetFont('Arial','',7);
		$pdf->Cell(0,$CH,'Republic of the Philippines',0,1,'C');
		$pdf->Cell(0,$CH,'Department of Health',0,1,'C');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(0,$CH,'NORTHERN MINDANAO MEDICAL CENTER',0,1,'C');
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(0,$CH,'Capitol Compound, Cagayan de Oro City',0,1,'C');

		// $CH=0.16;

		$pdf->Ln(0.1);
		$pdf->Cell( 2.8,$CH,'Date: ',0,0,'R');
		$pdf->Cell(0,$CH,date('m/d/Y',time()),'B',1,'C');

		$pdf->Cell(0.8,$CH,"PLEASE PRINT CLEARLY",0,1);

		$pdf->Cell(0.8,$CH,"Name of Patient: ",0);
		$pdf->Cell(1.8,$CH, utf8_decode($patient->NAME),'B',0,'C');
		$pdf->Cell(0.4,$CH,"Hosp #:",0);
		$pdf->Cell(0,$CH,$patient->HRID,'B',1,'C');


		$pdf->Cell(0.25,$CH,"Age:",0);
		$pdf->Cell(0.2,$CH,$patient->AGE,'B',0,'C');
		$pdf->Cell(0.25,$CH,"Sex:",0);
		$pdf->Cell(0.2,$CH,$patient->SEX[0],'B',0,'C');
		$pdf->Cell(1.75,$CH,"(OPD/ER/ADMITTED) Ward/Service",0,0,'C');
		$pdf->Cell(0,$CH,'','B',1);

		$pdf->Cell(0.45,$CH,"Address:",0);
		$pdf->Cell(0,$CH,utf8_decode($patient->ADDRESS),'B',1);

		$pdf->Ln(0.1);


		$pdf->Image('assets/css/images/rx.png',$margL,$pdf->GetY(),0.2);

		
		$pdf->SetFillColor(220,220,220);

		$sql1 = $this->db->query("SELECT m.NAME, mrm.FREQUENCY, mrm.INSTRUCTION , mrm.QUANTITY
			FROM mr_medicines mrm 
			INNER JOIN medicines m ON m.ID = mrm.MEDICINEID
			WHERE mrm.MEDICALRECORDID = ? and mrm.CANCELLED='N' ",array($id));
		$data = $sql1->result();

		if( $data ){
			$pdf->SetFont('Times','',7);	

			foreach ($data as $key => $v) {
				$pdf->SetX(0.4);
				$pdf->MultiCell(0,$CH,utf8_decode( ($key + 1).'. '.$v->NAME.' '.($v->QUANTITY > 0 ? '#'.$v->QUANTITY: '').', '.$v->INSTRUCTION .', '.$v->FREQUENCY ));	
			}
		}

		$currentX = $pdf->getX();
		$currentY = $pdf->getPageHeight()-0.65;

		$pdf->SetFont('Times','B',9);
		$pdf->SetXY($margL+0,$currentY);

		

		$q = $this->db->query("SELECT U.NAME, U.PTR, U.LICENSENO, U.S2NO
			FROM users U
			INNER JOIN medicalrecords MR ON MR.CREATEDBY = U.ID
			WHERE MR.ID = ?   
			LIMIT 1",array($id))->row();

		if( $q ){

			$pdf->SetFont('Arial','U',7);
			$pdf->Cell(0,$CH-0.065, utf8_decode($q->NAME),0,1,'R');

			$pdf->SetFont('Arial','',7);
			$pdf->Cell(2.7,$CH,'License No.:',0,0,'R');
			$pdf->Cell(0,$CH,$q->LICENSENO,'B',1);

			$pdf->Cell(2.7,$CH,'PTR No.:',0,0,'R');
			$pdf->Cell(0,$CH,$q->PTR,'B',1);

			$pdf->Cell(2.7,$CH,'S2 No.:',0,0,'R');
			$pdf->Cell(0,$CH,$q->S2NO,'B',1);
		}

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
