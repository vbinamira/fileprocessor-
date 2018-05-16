<?php 
	/**
	 * 
	 */
	class Accounting
	{
		private $file;
		private $csv;
		private $totalarray = array();
		private $qtyarray 	= array();
		private $costarray 	= array();
		private $profitarray = array();
		private $dollararray = array();
		private $cadarray 	= array();

		function __construct($value)
		{
			$this->file = $value;
		}

		public function parseFile()
		{
			$this->csv = array_map('str_getcsv', file($this->file));
		}

		public function getBody() 
		{
			for ($i=1; $i < count($this->csv); $i++) {
				$headone 	= $this->csv[$i][0];
				$headtwo 	= $this->csv[$i][1];
				$headthree 	= $this->csv[$i][2];
				$headfour 	= $this->csv[$i][3];
				$prodcol 	= $this->insertRow($headone);
				$pricecol 	= $this->insertPrice($headtwo);
				$qtycol 	= $this->checkInteger($headthree);
				$costcol 	= $this->insertPrice($headfour);
				$margincol 	= $this->getProfitMargin($headtwo,$headfour);
				$total 		= $this->getTotal($headtwo,$headthree);
				$totalcol 	= $this->checkMoney($total);
				$cadtotal 	= $this->convert($total);
				$cadtotalcol = $this->checkMoney($cadtotal);
				$this->bodyTable($prodcol,$pricecol,$qtycol,$costcol,$margincol,$totalcol,$cadtotalcol);	
				$this->totalarray[] = $headtwo;
				$this->qtyarray[] 	= $headthree;
				$this->costarray[] 	= $headfour;
			}
			
		}
		public function bodyTable($product,$price,$qty,$cost,$margin,$dollar,$cad) 
		{
			echo "<tr>";
			echo $product;
			echo $price;
			echo $qty;
			echo $cost;
			echo $margin;
			echo $dollar;
			echo $cad;
			echo "</tr>";
		}
		public function footerTable($avgprice,$avgcost,$qty,$margin,$dollar,$cad) {
			echo "<tr>";
			echo "<td></td>";
			echo "<td> Average Price: $ ".$avgprice."</td>";
			echo "<td> Total qty: ".$qty."</td>";
			echo "<td> Average Cost: $".$avgcost."</td>";
			echo "<td> Average Profit Margin: ".$margin." %</td>";
			echo "<td> Total (USD): $ ".$dollar."</td>";
			echo "<td> Total (CAD): $ ".$cad."</td>";
			echo "</tr>";
		}
		public function insertRow($row) {
			return "<td>$row</td>";
		}

		public function insertPrice($money) {
			return "<td>$$money</td>";
		}

		public function checkInteger($int)
		{
			if($int > 0) {
				return "<td class='alert alert-success'>$int</td>";
			} else {
				return "<td class='alert alert-danger'>$int</td>";
			}
		}

		public function checkMoney($int) {
			if($int > 0) {
				return "<td class='alert alert-success'>$$int</td>";
			} else {
				return "<td class='alert alert-danger'>$$int</td>";
			}
		}
		public function getTotal($price,$qty)
		{
			$total = intval($price) * intval($qty);
			$this->dollararray[] = $total;
			return $total;
		}
		public function getAverageprice() 
		{
			$footerarray = $this->totalarray;
			$dividend = intval(count($footerarray));
			for ($i=0; $i < $dividend ; $i++) { 
				$numbers = intval($footerarray[$i]);
				$total += $numbers;
			}
			$averageprice =  $total / $dividend;
			return $averageprice;
		}
		public function getAveragecost() 
		{
			$footerarray 	= $this->costarray;
			$dividend 		= intval(count($footerarray));
			for ($i=0; $i < $dividend ; $i++) { 
				$numbers = intval($footerarray[$i]);
				$total += $numbers;
			}
			$averageprice =  $total / $dividend;
			return $averageprice;
		}
		public function getProfitMargin($revenue,$cost) {
			$grossprofit 	= intval($revenue) - intval($cost);
			$profit 		= intval($grossprofit) / intval($revenue);
			$percentage 	= round($profit * 100);
			$this->profitarray[] = $percentage;
			if($percentage < 0) {
				return "<td class='alert alert-danger'>".$percentage." %</td>";
			} else {
				return "<td class='alert alert-success'>".$percentage." %</td>";
			}
			
		}
		public function getFooter()
		{
			$aveprice 		=  	$this->getAverageprice();
			$avecost 		=	$this->getAveragecost();
			$totalqty 		=  	$this->qtyTotal();
			$averageMargin 	= 	$this->averageMargin();
			$totaldollar 	= 	$this->dollarTotal();
			$totalcad 		= 	$this->convert($totaldollar);
			$this->footerTable($aveprice,$avecost, $totalqty,$averageMargin,$totaldollar,$totalcad);
		}

		public function qtyTotal() 
		{
			$footerarray = $this->qtyarray;
			$dividend = intval(count($footerarray));
			for ($i=0; $i < $dividend ; $i++) { 
				$numbers = intval($footerarray[$i]);
				$total += $numbers;
			}
			return intval($total);
		}

		public function averageMargin()
		{
			$footerarray = $this->profitarray;
			$dividend = intval(count($footerarray));
			for ($i=0; $i < $dividend ; $i++) { 
				$numbers = intval($footerarray[$i]);
				$total += $numbers;
			}
			$averageprice =  $total / $dividend;
			return intval($averageprice);
		}

		public function dollarTotal() {
			$footerarray = $this->dollararray;
			$dividend = intval(count($footerarray));
			for ($i=0; $i < $dividend ; $i++) { 
				$numbers = intval($footerarray[$i]);
				$total += $numbers;
			}
			return intval($total);
		}

		public function convert($value)
		{
			// set API Endpoint, access key, required parameters
			$endpoint = 'convert';
			$access_key = '0ad382c8874cd99afb06466807ad6db2';

			$from = 'USD';
			$to = 'CAD';
			$amount = $value;

			// initialize CURL:
			$ch = curl_init('http://data.fixer.io/api/'.$endpoint.'?access_key='.$access_key.'&from='.$from.'&to='.$to.'&amount='.$amount.'');   
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			// get the JSON data:
			$json = curl_exec($ch);
			curl_close($ch);

			// Decode JSON response:
			$conversionResult = json_decode($json, true);

			// access the conversion result
			return round($conversionResult['result']);
		}

	}
 ?>