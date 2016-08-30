<?php

$I = new AcceptanceTester($scenario);
$I->wantTo('Check that ordering works');
$I->amOnPage('/dev.php/order?offer_id=14&packets%5B32%5D=2&packets%5B16%5D=2');

// $I->fillField();
/**
 * Visit /orderform with parameters.
 */
