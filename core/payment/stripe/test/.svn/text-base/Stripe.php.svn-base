<?php
// Example useage of stripe php for credit card charging...

require_once("stripe-php/lib/Stripe.php");

Stripe::setApiKey('sk_YdzCPErppHXAO5ZVWWTkAZ6D1vICq '); // <-- API Secret key found at stripe.com / Account Settings / API Keys
$myCard = array('number' => '4242424242424242', 'exp_month' => 5, 'exp_year' => 2015);
$charge = Stripe_Charge::create(array('card' => $myCard, 'amount' => 2000, 'currency' => 'usd'));
echo'<pre>' .  $charge;

