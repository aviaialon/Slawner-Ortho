<?php 
SHARED_OBJECT::getObjectFromPackage(__APPLICATION_CLASS_PATH__ . "::PAYMENT::PAYPAL_API::PAYPAL_API");
$objPaypalApi = PAYPAL_API::getInstance();
$objPaypalApi->setFirstName('Avi Aialom');
$objPaypalApi->executeTransaction();
new dump($objPaypalApi->getErrors());
print $objPaypalApi->getCurrencyCode();
print 'ok<br />';

echo ('<a href="DoDirectPayment.php">DoDirectPayment.php</a>');