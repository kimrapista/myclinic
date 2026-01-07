<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_medical_record_12 extends CI_Model
{
	
	function __construct(){ $this->load->database(); }


	private function clinicInfo(){

		$sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID));
		return $sql->row();

	}


	// ----------------- Dr khu ---------------
	public function Index($id){
		

		$this->load->library('pdf');
		$pageSize=array(5,8.27);

		$this->pdf->__construct('P','in','A4');
		$pdf = $this->pdf;

		$margL=0.5;
		$margT=0.4;
		$pdf->SetMargins($margL,$margT,$margL);
		$pdf->SetAutoPageBreak(false);
		
		$pageNo=0;
		$CH=0.15;
		$CW=0.8;

		$clinic = $this->clinicInfo();

		$q = $this->db->query("SELECT P.ID, concat(P.FIRSTNAME,' ',P.MIDDLENAME,' ',P.LASTNAME) as NAME, P.MOBILENO,P.CIVILSTATUS,
			MR.CHEIFCOMPLAINT,MR.PRESENTILLNESS,MR.BP_SYSTOLIC,MR.BP_DIASTOLIC,MR.HEART_RATE,MR.RESPIRATORY,MR.TEMPERATURE,MR.WEIGHT,MR.HEIGHT,
			MR.FINDINGS,MR.DIAGNOSIS,MR.PROCEDURE_DONE, MR.INSTRUCTION, MR.APPOINTMENT, MR.APPOINTMENTDATE, MR.APPOINTMENTDESCRIPTION, S.NAME AS SUBCLINIC
 
			FROM patients P
			INNER JOIN medicalrecords MR ON MR.PATIENTID = P.ID
			LEFT JOIN subclinic S ON S.ID = MR.SUBCLINICID
			WHERE MR.ID = ?  AND MR.CLINICID=?
			LIMIT 1",array($id, $this->session->CLINICID))->row();

		$pdf->AddPage();


		$NAME = '';
		$showBorder = 0;

		if( isset($q) ){
			$NAME = $q->NAME;

			$pdf->SetFont('Arial','',9);

			$pdf->SetXY(1.5, 3.07);
			$pdf->MultiCell(1.5,$CH,$q->MOBILENO,$showBorder,'L');	

			$pdf->SetXY(6, 3.07);
			$pdf->MultiCell(1,$CH,$q->CIVILSTATUS,$showBorder,'L');	


			$pdf->SetXY(0.6, 3.86);
			$size = $this->Get_Text_Size($pdf, (4.62 - 3.96), 0, $CH, utf8_decode($q->CHEIFCOMPLAINT));
			$pdf->SetFont('Arial','',$size['fs']);
			$pdf->MultiCell(0, $size['ch'],utf8_decode($q->CHEIFCOMPLAINT), $showBorder,'L');

			$pdf->SetXY(0.6, 4.62);
			$size = $this->Get_Text_Size($pdf, (5.65 - 4.72), 0, $CH, utf8_decode($q->PRESENTILLNESS));
			$pdf->SetFont('Arial','',$size['fs']);
			$pdf->MultiCell(0, $size['ch'], utf8_decode($q->PRESENTILLNESS), $showBorder, 'L');

	
			$pdf->SetXY(0.6, 5.65);
			$size = $this->Get_Text_Size($pdf, (6.65 - 5.75), 3.75, $CH, utf8_decode($q->FINDINGS));
			$pdf->SetFont('Arial','',$size['fs']);
			$pdf->MultiCell(3.75, $size['ch'], utf8_decode($q->FINDINGS),$showBorder,'L');

			// vital signs
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY(4.9,5.60);
			$pdf->MultiCell(0.5,$CH,utf8_decode($q->HEIGHT),$showBorder,'L');

			$pdf->SetXY(6.2,5.60);
			$pdf->MultiCell(0.5,$CH,utf8_decode($q->WEIGHT),$showBorder,'L');

			$pdf->SetXY(7.5,5.60);
			$pdf->MultiCell(0.5,$CH,utf8_decode($q->TEMPERATURE),$showBorder,'L');


			$pdf->SetXY(4.9,5.75);
			$pdf->MultiCell(0.5,$CH,utf8_decode($q->HEART_RATE),$showBorder,'L');

			$pdf->SetXY(6.2,5.75);
			$pdf->MultiCell(0.5,$CH,utf8_decode($q->BP_SYSTOLIC.'/'.$q->BP_DIASTOLIC),$showBorder,'L');

			$pdf->SetXY(7.5,5.75);
			$pdf->MultiCell(0.5,$CH,utf8_decode($q->RESPIRATORY),$showBorder,'L');


			$pdf->SetXY(0.6, 6.65);
			$size = $this->Get_Text_Size($pdf, (7.75 - 6.75), 0, $CH, utf8_decode($q->DIAGNOSIS));
			$pdf->SetFont('Arial','',$size['fs']);
			$pdf->MultiCell(0, $size['ch'],utf8_decode($q->DIAGNOSIS),$showBorder,'L');


			$pdf->SetXY(0.6, 7.75);
			$size = $this->Get_Text_Size($pdf, 1, 0, $CH, utf8_decode($q->DIAGNOSIS));
			$pdf->SetFont('Arial','',$size['fs']);
			$pdf->MultiCell(3, $size['ch'],utf8_decode($q->INSTRUCTION),$showBorder,'L');

			$meds1 = '';
			$meds2 = '';

			$pres = $this->db->query("SELECT m.NAME, mrm.FREQUENCY, mrm.INSTRUCTION , mrm.QUANTITY
				FROM mr_medicines mrm 
				INNER JOIN medicines m ON m.ID = mrm.MEDICINEID
				WHERE mrm.MEDICALRECORDID = ? and mrm.CANCELLED='N' ",array($id))->result();

			foreach ($pres as $key => $v) {
				if( $key < 4 ){
					$meds1 .= $v->NAME.($v->QUANTITY == 0 ? '' : ' '.$v->QUANTITY).' '.$v->INSTRUCTION.( empty($v->FREQUENCY) ? '' : $v->FREQUENCY )."\r\n";
				}
				else{
					$meds2 .= $v->NAME.($v->QUANTITY == 0 ? '' : ' '.$v->QUANTITY).' '.$v->INSTRUCTION.( empty($v->FREQUENCY) ? '' : $v->FREQUENCY )."\r\n";
				}
			}
		
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetXY(3.5,7.75);
			$pdf->MultiCell(2,$CH,utf8_decode($meds1),$showBorder,'L');

			$pdf->SetXY(5.6,7.75);
			$pdf->MultiCell(0,$CH,utf8_decode($meds2),$showBorder,'L');

			if( $q->APPOINTMENT == 'Y' ){
				$pdf->SetFont('Arial','',8);
				$pdf->SetXY(0.5,9.4);
				$pdf->Cell(2,$CH, 'Appointment Date: '.date('m/d/Y',strtotime($q->APPOINTMENTDATE)),$showBorder,1);
				$pdf->Cell(0,$CH, 'Bring: '.utf8_decode($q->APPOINTMENTDESCRIPTION),$showBorder,1);
			}
		}


		// $pdf->Output('I','Medical Record '.utf8_decode($NAME).'.pdf');
		$pdfPath = 'temp_files_pdf/Medical_Record_'.$id.'_.pdf';
		$pdf->Output('F', $pdfPath);
		echo base_url($pdfPath);

	}

	private function Get_Text_Size($pdf, $maxHeight, $cw, $ch, $text ){
		$isDone = FALSE;
		$fontSize = 9;

		$pdf->SetFont('Arial','', $fontSize);
		$tempHeight = $pdf->GetMultiCellHeight($cw, $ch, $text, 0, 'L');

		if( $tempHeight <= $maxHeight ){
			return array('fs'=> $fontSize, 'ch' => $ch);
		}
		else{

			$chDeduct = 0;

			while ($isDone == FALSE) {

				if( $chDeduct == 3){
					$fontSize = $fontSize - 1;
					$chDeduct = 0;
				}
				else{
					$ch = $ch - 0.01;
				}

				$tempHeight = $pdf->GetMultiCellHeight($cw, $ch, $text, 0, 'L');
				if( $tempHeight <= $maxHeight ){
					$isDone = TRUE;
				}

				$chDeduct = $chDeduct + 1;
			}

			return array('fs'=> $fontSize, 'ch' => $ch);
		}
	}

}

?>