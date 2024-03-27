<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     local_oidcserver
 * @category    local
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright   Valery Fremaux <valery.fremaux@gmail.com> (MyLearningFactory.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');

// this page is public and requires no login.

$config = get_config('local_oidcserver');

if (empty($config->enabled)) {
    throw new moodle_exception("Oidc server not enabled in this moodle instance");
}

$json = new StdClass();
if ($config->encryptionalgorithm == 'RSA') {
    $json->kty = 'RSA';
    $json->alg = 'RS256';
} else {
    $json->kty = 'HMAC';
    $json->alg = 'HS256';
}
$json->use = 'sig';
$json->kid = '1';
$json->n = $config->encryptionkey;
$json->e = '';

header("Content-type:application/json");
echo json_encode($json);
die;