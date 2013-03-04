<?php
	require_once(__APPLICATION_ROOT__ . '/payment/paypal/paypal_gateway.php');
	$payPalGateway = new PAYPAL_GATEWAY(PAYPAL_HTTPS_SANDBOX_GATEWAY);
	$payPalGateway->setPaymentAmount(159.99); 
	$payPalGateway->setShipping1Amount(0.00);
	$payPalGateway->setShipping2Amount(0.00);
	$payPalGateway->setHandlingAmount(0.00);
	$payPalGateway->setReceiverEmail('danger_1332857874_biz@yahoo.com');
	$payPalGateway->setItemName('Deposit For Quote #185454');
	$payPalGateway->setItemNumber(md5(time() . mt_rand(1,1000000)));
	$payPalGateway->setItemQty(0);
	$payPalGateway->setNoNote(0);
	$payPalGateway->setNoShipping(1);
	$payPalGateway->setCurrencyCode('CAD');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
	<?php $payPalGateway->getPayPalFormData(); ?>
</body>
</html>
