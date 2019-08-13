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


// called by core/templates/login.php

// require config.php for vault_token_base
require("config/config.php");
// important for URL parameter: htaccess.RewriteBase has to be correct. Default is incl. /index.php/

function denyaccess() {
    $URL="/"; 
    echo "<br>Access denied.";
    echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
    echo '<META HTTP-EQUIV="refresh" content="2;URL=' . $URL . '">';
    exit;
}

if (isset($_GET['user']) && !($userid = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_STRING))) {
    denyaccess();
} else {
    $userid = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_STRING);
}

$uri = explode('/', $_SERVER['REQUEST_URI']);
$instance = $uri[1];
$file_name = $CONFIG['vault_token_base'].$userid.".token";
$cookie_name = "vaulttoken_".$instance."_".$userid;

if (!file_exists("$file_name")) {
    echo "Token does not exist.";
    denyaccess();
}

if(!isset($_COOKIE[$cookie_name]) && !($cookie_content = filter_input(INPUT_COOKIE, "$cookie_name", FILTER_SANITIZE_STRING))) {
    echo "Token is not set correctly.";
    denyaccess();
} else {
    $file_content = file_get_contents("$file_name");
    $cookie_content = filter_input(INPUT_COOKIE, "$cookie_name", FILTER_SANITIZE_STRING);

    if (!($file_content === $cookie_content)) {
        echo "Token is not correct.";
        denyaccess();
    } 
}
