<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Check that basket order works');
$I->amOnPage('/order');
$I->see('Name');
$I->see('Email');
$I->see('Name');
$I->see('Email');
$I->seeInSource('promo_code');
$I->submitForm('#orderform', []);
$I->see('Installments');
$I->selectOption('.installments', 2);
$I->submitForm('#estimateform', []);
$I->see('Select payment method');
/**
 * Visit /orderform with parameters.
 */
