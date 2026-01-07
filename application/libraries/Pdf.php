<?php
// if ( ! defined('BASEPATH')) exit('No direct script access allowed');
defined('BASEPATH') OR exit('No direct script access allowed');

require('fpdf.php');


class Pdf extends FPDF
{
 
  // Extend FPDF using this class
  // More at fpdf.org -> Tutorials
  	function __construct($orientation='P', $unit='in', $size='A4'){ parent::__construct($orientation,$unit,$size); }

  	var $angle=0;

	function Rotate($angle,$x=-1,$y=-1)
	{
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0)
		{
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}

	function _endpage()
	{
		if($this->angle!=0)
		{
			$this->angle=0;
			$this->_out('Q');
		}
		parent::_endpage();
	}

	function RotatedText($x,$y,$txt,$angle)
	{
		//Text rotated around its origin
		$this->Rotate($angle,$x,$y);
		$this->Text($x,$y,$txt);
		$this->Rotate(0);
	}

	function RotatedImage($file,$x,$y,$w,$h,$angle)
	{
		//Image rotated around its upper-left corner
		$this->Rotate($angle,$x,$y);
		$this->Image($file,$x,$y,$w,$h);
		$this->Rotate(0);
	}


	protected $javascript;
	protected $n_js;


	function AutoPrint($printer='')
	{
		// Open the print dialog
		if($printer)
		{
			$printer = str_replace('\\', '\\\\', $printer);
			$script = "var pp = getPrintParams();";
			$script .= "pp.interactive = pp.constants.interactionLevel.full;";
			$script .= "pp.printerName = '$printer'";
			$script .= "print(pp);";
		}
		else
			$script = 'print(true);';
		$this->IncludeJS($script);
	}

	

	function IncludeJS($script, $isUTF8=false) {
		if(!$isUTF8)
			$script=utf8_encode($script);
		$this->javascript=$script;
	}

	function _putjavascript() {
		$this->_newobj();
		$this->n_js=$this->n;
		$this->_put('<<');
		$this->_put('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
		$this->_put('>>');
		$this->_put('endobj');
		$this->_newobj();
		$this->_put('<<');
		$this->_put('/S /JavaScript');
		$this->_put('/JS '.$this->_textstring($this->javascript));
		$this->_put('>>');
		$this->_put('endobj');
	}

	function _putresources() {
		parent::_putresources();
		if (!empty($this->javascript)) {
			$this->_putjavascript();
		}
	}

	function _putcatalog() {
		parent::_putcatalog();
		if (!empty($this->javascript)) {
			$this->_put('/Names <</JavaScript '.($this->n_js).' 0 R>>');
		}
	}


	function GetMultiCellHeight($w, $h, $txt, $border=null, $align='J') {
		// Calculate MultiCell with automatic or explicit line breaks height
		// $border is un-used, but I kept it in the parameters to keep the call
		//   to this function consistent with MultiCell()
		$cw = &$this->CurrentFont['cw'];
		if($w==0)
			$w = $this->w-$this->rMargin-$this->x;
		$wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
		$s = str_replace("\r",'',$txt);
		$nb = strlen($s);
		if($nb>0 && $s[$nb-1]=="\n")
			$nb--;
		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$ns = 0;
		$height = 0;
		while($i<$nb)
		{
			// Get next character
			$c = $s[$i];
			if($c=="\n")
			{
				// Explicit line break
				if($this->ws>0)
				{
					$this->ws = 0;
					$this->_out('0 Tw');
				}
				//Increase Height
				$height += $h;
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				$ns = 0;
				continue;
			}
			if($c==' ')
			{
				$sep = $i;
				$ls = $l;
				$ns++;
			}
			$l += $cw[$c];
			if($l>$wmax)
			{
				// Automatic line break
				if($sep==-1)
				{
					if($i==$j)
						$i++;
					if($this->ws>0)
					{
						$this->ws = 0;
						$this->_out('0 Tw');
					}
					//Increase Height
					$height += $h;
				}
				else
				{
					if($align=='J')
					{
						$this->ws = ($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
						$this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
					}
					//Increase Height
					$height += $h;
					$i = $sep+1;
				}
				$sep = -1;
				$j = $i;
				$l = 0;
				$ns = 0;
			}
			else
				$i++;
		}
		// Last chunk
		if($this->ws>0)
		{
			$this->ws = 0;
			$this->_out('0 Tw');
		}
		//Increase Height
		$height += $h;
	
		return $height;
	}

}


?>