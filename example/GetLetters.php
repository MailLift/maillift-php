<?php

// This file holds API keys for the demos
require_once('Constants.php'); 				

// Include the MailLift API 
require_once('../lib/MailLift.php'); 		


// Initialize the MailLift API with username and API key
MailLift::init(MAILLIFT_USERNAME, MAILLIFT_APIKEY); 

$letters = MailLift::GetLetters(); 

?>
<html>
	<body>
		<h1>Get all letters for an account</h1>
		<table border="1">
			<tr>
				<th>SenderName1</th>
				<th>SenderName2</th>
				<th>SenderAddress1</th>
				<th>SenderAddress2</th>
				<th>SenderCity</th>
				<th>SenderStateCode</th>
				<th>SenderPostCode</th>
				<th>RecipientName1</th>
				<th>RecipientName2</th>
				<th>RecipientAddress1</th>
				<th>RecipientAddress2</th>
				<th>RecipientCity</th>
				<th>RecipientStateCode</th>
				<th>RecipientPostCode</th>
				<th>MessageBody</th>
			</tr>
			<?foreach($letters as $letter): ?>
				<tr>
					<td><?=$letter->SenderName1?></td>
					<td><?=$letter->SenderName2?></td>
					<td><?=$letter->SenderAddress1?></td>
					<td><?=$letter->SenderAddress2?></td>
					<td><?=$letter->SenderCity?></td>
					<td><?=$letter->SenderStateCode?></td>
					<td><?=$letter->SenderPostCode?></td>
					<td><?=$letter->RecipientName1?></td>
					<td><?=$letter->RecipientName2?></td>
					<td><?=$letter->RecipientAddress1?></td>
					<td><?=$letter->RecipientAddress2?></td>
					<td><?=$letter->RecipientCity?></td>
					<td><?=$letter->RecipientStateCode?></td>
					<td><?=$letter->RecipientPostCode?></td>
					<td><?=$letter->MessageBody?></td>
				</tr>
			<?endforeach?>
		</table>
	</body>
</html>