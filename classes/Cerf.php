<?php
	class Cerf extends FPDF
	{


		public function __construct(){
			parent::__construct();
		}

		function Footer()
		{
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			// Arial italic 8
			$this->SetFont('Arial','I',8);
			// Page number
			//$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');

		}
		function Header()
		{
			// Logo
			$image1 = "../css/img/logo.jpg";
			$this->Ln(12);

			$this->SetX(40);
			$this->Cell(0, 0, $this->Image($image1, $this->GetX(), $this->GetY(),25), 0, 0, '', false );
			$this->SetFont('Times','B',30);
			$this->Ln(14);

			$this->Cell(280,0,"CERTIFICATE OF COMPLETION",0,0,'C');
			$this->Ln(12);
			$this->setLineWidth(1);
			$this->Line(30, 50, 266, 50);



		}
	}
?>