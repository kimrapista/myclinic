<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class M_sales_report extends CI_Model
{
	
	function __construct(){ $this->load->database(); }

	private function clinicInfo(){

		$sql = $this->db->query("SELECT * FROM clinics where ID=? LIMIT 1",array($this->session->CLINICID));
		return $sql->row();

	}


	function Sales_Summary_Report($dateFrom,$dateTo)
	{
		$this->load->library('pdf');
		//$pageSize=array();

		$this->pdf->__construct('P','in');
		$pdf = $this->pdf;

		$clinic = $this->clinicInfo();

		$margL=0.5;
		$margT=0.4;
		//$margB = 0.5;
		$pdf->SetMargins($margL,$margT,$margL);
		$pdf->SetAutoPageBreak(true);


		$pageNo=0;
		$CH=0.22;
		$CW=0.8;

		
		$datefr = new Datetime($dateFrom);
		$datet = new Datetime($dateTo);

		$pdf->AddPage();

		$pdf->SetTextColor(100,100,100);
		$pdf->SetFillColor(240,240,240);
		$pdf->SetDrawColor(210,210,210);

		$pdf->Image('assets/css/images/logo.png', $margL, $margT, 0.4);

		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($margL + 0.45, $margT);
		$pdf->MultiCell(0,0.12,"Cerebro Diagnostic System \r\nGate 2, Alwana Business Park \r\nCagayan de Oro 9000, Philippines");
		$pdf->SetTextColor(50,50,50);

		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY($margL,$margT);
		$pdf->Cell(0,$CH,'SALES SUMMARY REPORT',0,1,'R');

		$pdf->Ln(0.5);
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(1,$CH,'Clinic',1,0,'L',true);
		$pdf->Cell(3.5,$CH, utf8_decode($clinic->CLINICNAME) ,1,0,'L');
		$pdf->Cell(1,$CH,'Date Printed',1,0,'L',true);
		$pdf->Cell(0,$CH,date('m/d/Y h:i A',time()),1,1);

		$pdf->Cell(1,$CH,'TIN',1,0,'L',true);
		$pdf->Cell(3.5,$CH, utf8_decode($clinic->TIN) ,1,0);
		$pdf->Cell(1,$CH,'Date Covered',1,0,'L',true);
		$pdf->Cell(0,$CH,$datefr->format("m/d/Y").' - '.$datet->format("m/d/Y"),1,1);

		$pdf->Cell(1,$CH,'Address',1,0,'L',true);
		$pdf->MultiCell(0,$CH, utf8_decode($clinic->ADDRESS),1);
		
		
		$pdf->Ln(0.2);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(4.5,$CH,"CLINIC(S)",1,0,"C",true);
		$pdf->Cell(1,$CH,"RECORD(S)",1,0,"C",true);
		$pdf->Cell(0,$CH,"AMOUNT",1,1,"C",true);


		$SUMMARY = $this->db->query("SELECT COUNT(M.ID) AS MRCOUNT, SUM(M.NETPAYABLES) AS AMOUNT, S.NAME AS FROMCLINIC
			
			FROM patients P 
			INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
			LEFT JOIN subclinic S ON S.ID = M.SUBCLINICID
			WHERE M.CLINICID = ? 
			AND M.CREATEDBY=?
			AND M.CANCELLED = 'N'
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
			GROUP BY S.NAME
			ORDER BY S.NAME", 
			array(
				$this->session->CLINICID,
				$this->session->userid,
				$dateFrom,
				$dateTo
			))->result();
		


		$total = 0;
		$totalPatient = 0;

		$pdf->SetFont('Arial','',9);

		foreach ( $SUMMARY as $key => $v ) 
		{
			$pdf->Cell(4.5,$CH,utf8_decode($v->FROMCLINIC),1,0);
			$pdf->Cell(1,$CH,($v->MRCOUNT),1,0,"C");
			$pdf->Cell(0,$CH, number_format($v->AMOUNT,2),1,1,"R");

			$totalPatient += 1;
			$total += $v->AMOUNT;
		}

		$pdf->Ln(0.1);
		$pdf->SetFont('Arial','B',9);

		$pdf->Cell(5.5, $CH, 'Total Amount:',0,0,'R');
		$pdf->Cell(0, $CH, number_format($total,2), 'B', 1,'R');
		

		$pdf->Output('I','Sales Summary Report','.pdf');
	}





	function Sales_Detail_Report($dateFrom,$dateTo)
	{
		$this->load->library('pdf');
		//$pageSize=array();

		$this->pdf->__construct('P','in');
		$pdf = $this->pdf;

		$clinic = $this->clinicInfo();

		$margL=0.5;
		$margT=0.4;


		$pdf->SetMargins($margL,$margT,$margL);
		$pdf->SetAutoPageBreak(true);

		$pageNo = 0;
		$CH=0.2;
		$CW=0.8;


		$datefr = new Datetime($dateFrom);
		$datet = new Datetime($dateTo);

		$pdf->AddPage();

		$pdf->SetTextColor(100,100,100);
		$pdf->SetFillColor(240,240,240);
		$pdf->SetDrawColor(210,210,210);

		$pdf->Image('assets/css/images/logo.png', $margL, $margT, 0.4);

		$pdf->SetFont('Arial','',8);
		$pdf->SetXY($margL + 0.45, $margT);
		$pdf->MultiCell(0,0.12,"Cerebro Diagnostic System \r\nGate 2, Alwana Business Park \r\nCagayan de Oro 9000, Philippines");
		$pdf->SetTextColor(50,50,50);

		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY($margL,$margT);
		$pdf->Cell(0,$CH,'SALES DETAIL REPORT',0,1,'R');

		$pdf->Ln(0.5);
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(1,$CH,'Clinic',1,0,'L',true);
		$pdf->Cell(3.5,$CH, utf8_decode($clinic->CLINICNAME) ,1,0,'L');
		$pdf->Cell(1,$CH,'Date Printed',1,0,'L',true);
		$pdf->Cell(0,$CH,date('m/d/Y h:i A',time()),1,1);

		$pdf->Cell(1,$CH,'TIN',1,0,'L',true);
		$pdf->Cell(3.5,$CH, utf8_decode($clinic->TIN) ,1,0);
		$pdf->Cell(1,$CH,'Date Covered',1,0,'L',true);
		$pdf->Cell(0,$CH,$datefr->format("m/d/Y").' - '.$datet->format("m/d/Y"),1,1);

		$pdf->Cell(1,$CH,'Address',1,0,'L',true);
		$pdf->MultiCell(0,$CH, utf8_decode($clinic->ADDRESS),1);
		

		$pdf->Ln(0.2);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(1,$CH,"MR NO.",1,0,"C",true);
		$pdf->Cell(1.1,$CH,"CHECKUP",1,0,"C",true);
		$pdf->Cell(2.4,$CH,"PATIENT",1,0,"C",true);
		$pdf->Cell(1.5,$CH,"CLINIC",1,0,"C",true);
		$pdf->Cell(0,$CH,"AMOUNT",1,1,"C",true);


		$DETAIL = $this->db->query("SELECT M.ID, M.CHECKUPDATE, M.NETPAYABLES,			
			CONCAT(P.LASTNAME,', ',P.FIRSTNAME,' ',P.MIDDLENAME) AS NAME, S.NAME AS FROMCLINIC

			FROM patients P 
			INNER JOIN medicalrecords M ON M.PATIENTID = P.ID
			LEFT JOIN subclinic S ON S.ID = M.SUBCLINICID
			WHERE M.CLINICID = ? 
			AND M.CREATEDBY= ?
			AND M.CANCELLED = 'N'
			AND DATE(M.CHECKUPDATE) BETWEEN ? AND ?
			ORDER BY M.CHECKUPDATE, M.ID", 
			array(
				$this->session->CLINICID,
				$this->session->userid,
				$dateFrom,
				$dateTo
			))->result();
		

		$total = 0;
		$totalPatient = 0;

		$pdf->SetFont('Arial','',9);

		foreach ( $DETAIL as $key => $v ) 
		{	
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(1,$CH,($v->ID),1,0,"C");
			$pdf->Cell(1.1,$CH,($v->CHECKUPDATE),1,0,"C");
			$pdf->Cell(2.4,$CH,utf8_decode($v->NAME),1,0);
			$pdf->Cell(1.5,$CH,utf8_decode($v->FROMCLINIC),1,0,"C");
			$pdf->Cell(0,$CH, number_format($v->NETPAYABLES,2),1,1,"R");

			$totalPatient += 1;
			$total += $v->NETPAYABLES;

			if( $pageNo != $pdf->PageNo()  ){

				$pdf->SetFont('Arial','',6);
				$pageNo = $pdf->PageNo();

				$curX = $pdf->GetX();
				$curY = $pdf->GetY();

				$pdf->SetXY($margL, $pdf->GetPageHeight() - 0.5 );
				$pdf->Cell(0,$CH, 'Page No.: '.$pageNo,0,0,'R');

				$pdf->SetXY($curX, $curY);
			}

			if( $pdf->GetY() > ($pdf->GetPageHeight() - 1) ){
				$pdf->AddPage();
			}
		}



		$pdf->Ln(0.1);
		$pdf->SetFont('Arial','B',9);

		$pdf->Cell(4.5, $CH, 'Total Record(s):'. $totalPatient,0,0);
		$pdf->Cell(1.5, $CH, 'Total Amount:',0,0,'R');
		$pdf->Cell(0, $CH, number_format($total,2), 'B', 1,'R');

		$pdf->Output('I','Sales Detail Report','.pdf');
	}


}
?>