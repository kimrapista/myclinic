<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_sales_summary extends CI_Model
{

	function __construct(){ 

		$this->load->database(); 
      $this->load->library('tcpdf/reports/sales_summary/Pdf');
	} 

	public function PDF(){

		$_POST += json_decode(file_get_contents('php://input'), true);
		$this->load->library('form_validation');

		$DATEFROM = $this->input->post('DATEFROM',TRUE);
		$DATETO = $this->input->post('DATETO',TRUE);
		
 
		$pdf = new Pdf( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$pdf->SetCreator('TCPDF');
		$pdf->SetAuthor('Cerebro Diagnostic System');
		$pdf->SetTitle('Sales Report');
		$pdf->SetSubject('Summary Report');

		$pdf->SetMargins(10, 23, 10, true);
		$pdf->SetAutoPageBreak(TRUE,12);
		
		$pdf->Set_Header_Title('SALES SUMMARY REPORT');
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


		//  detail of subclinics

		$subclinic = $this->db->query("SELECT M.SUBCLINICID, S.NAME, 
				COUNT(M.ID) AS RECORDS, 
				SUM(M.GROSSAMOUNT) AS GROSSAMOUNT,
				SUM(M.DISCOUNTAMOUNT) AS DISCOUNTAMOUNT,
				SUM(M.NETPAYABLES) AS NETPAYABLES
			
			FROM medicalrecords M 
			INNER JOIN subclinic S ON S.ID = M.SUBCLINICID
			WHERE M.CLINICID = ? 
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
			AND M.CANCELLED = 'N'
			GROUP BY S.ID
			ORDER BY NETPAYABLES DESC, S.NAME ", 
			array(
				$this->session->CLINICID,
				date('Y-m-d',strtotime($DATEFROM)),
				date('Y-m-d',strtotime($DATETO))
			))->result();



		$pdf->SetFont('calibri', '', 8);
		$pdf->Cell(125,0, 'CLINIC LOCATION',1,0,'C',TRUE);
		$pdf->Cell(25,0, 'RECORD(S)',1,0,'C',TRUE);
		$pdf->Cell(0,0, 'AMOUNT',1,1,'C',TRUE);
		
		$totalRecord = 0;
		$totalAmount = 0;

      foreach ($subclinic as $key => $value) {
			$record = is_numeric($value->RECORDS) ? (int)$value->RECORDS: 0;
			$amount = is_numeric($value->NETPAYABLES) ? (float)$value->NETPAYABLES: 0;

			$pdf->Cell(125, 5, $value->NAME, 1,0,'L');
			$pdf->Cell(25, 5, number_format($record, 0), 1,0,'C');
			$pdf->Cell(0, 5, number_format($amount, 2), 1,1,'R');

			$totalRecord += $record ;
			$totalAmount += $amount;
		}

		$pdf->SetFont('calibri', '', 8);
		$pdf->Cell(125, 5, 'TOTAL ', 1,0,'R',TRUE);
		$pdf->Cell(25, 5, number_format($totalRecord, 0), 1,0,'C');
		$pdf->Cell(0, 5, number_format($totalAmount, 2) , 1,1,'R');
		$pdf->Ln();



		
		// services
		$services = $this->db->query("SELECT M.SUBCLINICID, S.NAME, SUM(MS.QUANTITY) AS QUANTITY, SUM(MS.AMOUNT) AS AMOUNT
			FROM medicalrecords M 
			INNER JOIN  mr_services MS ON MS.MEDICALRECORDID = M.ID
			INNER JOIN services S ON S.ID = MS.SERVICEID
			INNER JOIN subclinic SC ON SC.ID = M.SUBCLINICID
			WHERE M.CLINICID = ? 
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
			AND M.CANCELLED = 'N'
			AND MS.CANCELLED = 'N'
			GROUP BY M.SUBCLINICID, S.NAME
			ORDER BY SC.NAME, AMOUNT DESC, S.NAME", 
			array(
				$this->session->CLINICID,
				date('Y-m-d',strtotime($DATEFROM)),
				date('Y-m-d',strtotime($DATETO))
			))->result();


		foreach ($subclinic as $key => $value) {

			$pdf->SetFont('calibri', '', 10);
			$pdf->Cell(0,0, $value->NAME, 0, 1);

			$pdf->SetFont('calibri', '', 8);
			$pdf->Cell(125,0, 'SERVICES',1,0,'C',TRUE);
			$pdf->Cell(25, 0, 'Qty', 1,0, 'C',TRUE);
			$pdf->Cell(0,0, 'AMOUNT',1,1,'C',TRUE);

			foreach ($services as $key1 => $value1) {
				if( $value->SUBCLINICID == $value1->SUBCLINICID ){

					$qty = is_numeric($value1->QUANTITY) ? (float)$value1->QUANTITY: 0;
					$amount = is_numeric($value1->AMOUNT) ? (float)$value1->AMOUNT: 0;

					$pdf->Cell(125, 5, $value1->NAME, 1,0,'L');
					$pdf->Cell(25, 5, number_format($qty) , 1,0,'C');
					$pdf->Cell(0, 5, number_format($amount, 2) , 1,1,'R');
				}
			}

			$gross = is_numeric($value->GROSSAMOUNT) ? (float)$value->GROSSAMOUNT: 0;
			$discount = is_numeric($value->DISCOUNTAMOUNT) ? (float)$value->DISCOUNTAMOUNT: 0;
			$total = is_numeric($value->NETPAYABLES) ? (float)$value->NETPAYABLES: 0;

			$pdf->SetFont('calibri', '', 8);
			$pdf->Cell(150, 5, 'Sub Total', 1,0,'R',TRUE);
			$pdf->Cell(0, 5, number_format($gross, 2) , 1,1,'R');
			$pdf->Cell(150, 5, 'Discount', 1,0,'R',TRUE);
			$pdf->Cell(0, 5, number_format($discount, 2) , 1,1,'R');
			$pdf->Cell(150, 5, 'Total', 1,0,'R',TRUE);
			$pdf->Cell(0, 5, number_format($total, 2) , 1,1,'R');
			$pdf->Ln();
		}

      $pdfName = 'temp_files_pdf/Sales_Summary_'.$this->session->CLINICID.'_'.date('Y_m_d',strtotime($DATEFROM)).'_'.date('Y_m_d',strtotime($DATETO)).'.pdf';
      $pdf->Output( getcwd().'/'.$pdfName,'F');
		echo base_url($pdfName);
	}




	

}



?>