<?php
namespace OCA\VaultKeyValidate\AppInfo;

use \OCP\AppFramework\App;

use \OCA\VaultKeyValidate\Hooks\UserHooks;


class Application extends App {

    public function __construct(array $urlParams=array()){
        parent::__construct('vaultkey_validate', $urlParams);

        $container = $this->getContainer();

        /**
         * Controllers
         */
        $container->registerService('UserHooks', function($c) {
            return new UserHooks(
                $c->query('ServerContainer')->getUserManager()
            );
        });
    }
}
