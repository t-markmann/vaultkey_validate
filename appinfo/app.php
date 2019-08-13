<?php

$app = new \OCA\VaultKeyValidate\AppInfo\Application();
// $app->register();
$app->getContainer()->query('UserHooks')->register();
