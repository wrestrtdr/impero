<?php
return;
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that frontpage works');
$I->amOnPage('/login');
$I->see('Password');
$I->fillField('email', 'schtr4jh@schtr4jh.net');
$I->fillField   ('password', 'xkce569f');
$I->submitForm('#formLogin', []);
$I->amOnPage('/');
$I->see('Orders');