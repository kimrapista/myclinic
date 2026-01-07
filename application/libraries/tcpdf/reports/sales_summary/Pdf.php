<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '../../../tcpdf.php';

class Pdf extends TCPDF 
{	
	private $HeaderTitle;

	function __construct() {
		parent::__construct();
	}

	public function Set_Header_Title($TITLE){
		$this->HeaderTitle = $TITLE;
	}

	public function Header() {

		$this->setJPEGQuality(100);
		$this->Image( dirname(__FILE__).'/images/logo.jpg', 10, 6, 10, 10, 'JPG', 'https://cerebrodiagnostics.com');

		$this->SetFont('calibri', '', 9);
		$this->SetTextColor(80, 80, 80);
		$this->SetXY(23,5);
		$this->MultiCell(50,0, "Cerebro Diagnostic System\nGate 2, Alwana Business Park\nCagayan de Oro 9000, Philippines", 0, 'L');

		$this->SetFont('calibri', '', 15);
		$this->SetTextColor(50, 50, 50);
		$this->SetXY( $this->getPageWidth() - 80, 7);
		$this->MultiCell(70,0, $this->HeaderTitle, 0, 'R');
	}


	public function Footer() {

		$this->SetY( $this->getPageHeight() - 10 );
		$this->SetFont('calibri','', 8);
		$this->SetTextColor(100,100,100);

		$this->Cell( 0, 0, 'Cerebro Diagnostic System', 0, FALSE, 'L');
		$this->Cell( 0, 0, 'Page '.$this->getPage(), 0, FALSE, 'R');
	}


	public function _destroy($destroyall = false, $preserve_objcopy = false)
	{
		if ($destroyall) {
				unset($this->imagekeys);
		}
		parent::_destroy($destroyall, $preserve_objcopy);
	}


}