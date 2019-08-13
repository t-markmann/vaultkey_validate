<?php

# Copyright (C) 2019 Torsten Markmann
# Mail: info@uplinked.net 
# WWW: edudocs.org uplinked.net

# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <https://www.gnu.org/licenses/>.

namespace OCA\VaultKeyValidate\Hooks;
use OCP\IUserManager;

class UserHooks {

    private $userManager;

    public function __construct(IUserManager $userManager){
        $this->userManager = $userManager;
    }

    public function register() {
        $callback = function($user) {
            // this Hook is called right after login. It makes sure that the correct user
            // was entered in the login-form and that the token from file matches the cookie.

            // require config.php for vault_token_base and vault_url
            require("config/config.php");

            $userid = \OC::$server->getUserSession()->getUser()->getUID();
            $uri = explode('/', $_SERVER['REQUEST_URI']);
            $instance = $uri[1];
            $file_name = $CONFIG['vault_token_base'].$userid.".token";
            $cookie_name = "vaulttoken_".$instance."_".$userid;
            
            if (!file_exists("$file_name")) {
                \OC::$server->getUserSession()->logout();
                echo "Token does not exist.";
            } 

            if(!isset($_COOKIE[$cookie_name]) && !($cookie_content = filter_input(INPUT_COOKIE, "$cookie_name", FILTER_SANITIZE_STRING))) {
                echo "Token is not set correctly.";
                \OC::$server->getUserSession()->logout();
            } else {
                $file_content = file_get_contents("$file_name");
                $cookie_content = filter_input(INPUT_COOKIE, "$cookie_name", FILTER_SANITIZE_STRING);

                if (!($file_content === $cookie_content)) {
                    echo "Token is not correct.";
                    \OC::$server->getUserSession()->logout();
                }
            }
            // delete token file, so login-page is not accessible
            unlink("$file_name");
        };
        $this->userManager->listen('\OC\User', 'postLogin', $callback);
    }
}
