<?php
	class PDF extends FPDF
	{
		// Load data
		private  $company = "";
		private  $type = "";
		private  $address = "";
		private  $addtl = "";
		private  $contactt_number = "";
		public function __construct($c='',$type=0,$add='',$addtl='',$contactt_number=''){
			parent::__construct();
			$this->company = $c;
			$this->type = $type;
			$this->address = $add;
			$this->addtl = $addtl;
			$this->contactt_number = $contactt_number;
		}
		function LoadData($file)
		{
			return $file;
		}


		// Better table
		function ImprovedTable($header,$widths, $data)
		{
			$this->SetFillColor(2,0,0);
			$this->SetDrawColor(0,0,0);

			$this->SetFont('','B');
			// Column widths
			//30
			//180
			$w = $widths;


			// Header
			for($i=0;$i<count($header);$i++)
				$this->Cell($w[$i],7,$header[$i],1,0,'C');
			$this->Ln();
			// Data
			$this->SetFillColor(230,230,230);
			$this->SetTextColor(0);
			$this->SetFont('');
			// Data
			$fill = false;

			foreach($data as $row)
			{
				$countWidth = count($widths);
				for($j = 0;$j<$countWidth;$j++){
					$this->Cell($w[$j],4,$row[$j],1,0,'L',false);
				}


				$this->Ln();
				$fill = !$fill;
			}



			// Closing line

		}
		function Footer()
		{
			// Position at 1.5 cm from bottom
			$this->SetY(-15);
			// Arial italic 8
			$this->SetFont('Arial','I',8);
			// Page number
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');

		}
		function Header()
		{
			// Logo
			$image1 = "../css/img/logo.jpg";


			if($this->type == 1){
				$len = 280;
			} else {
				$len = 190;
			}
			$this->Cell(88,0,'',0,0,'C');
			$this->Cell($len, 0, $this->Image($image1, $this->GetX(), $this->GetY()-10,15), 0, 0, 'C', false );
			$this->Ln(10);
			// Arial bold 15
			$this->SetFont('Arial','B',15);

			// Title

			$this->Cell($len,0,$this->company,0,0,'C');
			// Line break
			$this->SetFont('Arial','',10);
			// Move to the right
			$this->Cell(80);
			// Title
			$this->Cell(30,10,'',0,0,'L');
			// Line break
			$this->Ln(5);

			$this->Cell($len,0,$this->address,0,0,'C',false);
			$this->Ln(5);

			$this->Cell($len,0,$this->addtl,0,0,'C',false);
			$this->Ln(5);
			$this->Cell($len,0,"Contact #: ".$this->contactt_number,0,0,'C',false);
			$this->Ln(5);



		}
	}
?>