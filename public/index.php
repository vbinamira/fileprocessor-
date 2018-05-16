<?php 
	include($_SERVER['DOCUMENT_ROOT'].'/includes/class.accounting.php');
	$file = "products.csv";
	$fileprocessor = new Accounting($file);
	$fileprocessor->parseFile();
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>File Processor</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.min.css">
</head>
<body>

	<table class="accounting-table table">
		<thead>
			<tr>
				<th scope="col">Sku</th>
				<th scope="col">Price</th>
				<th scope="col">Qty</th>
				<th scope="col">Cost</th>
				<th scope="col">Profit Margin</th>
				<th scope="col">Total (USD)</th>
				<th scope="col">Total (CAD)</th>
			</tr>
		</thead>
		<tbody>
			<?php $fileprocessor->getBody(); ?>	
		</tbody>
		<tfoot>
			<?php $fileprocessor->getFooter(); ?>
		</tfoot>
	</table>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/js/bootstrap.min.js"></script>
</html>