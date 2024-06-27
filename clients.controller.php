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
 * Version details.
 *
 * @package    local_oidcserver
 * @category   local
 * @author     Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright  2010 onwards Valery Fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

namespace local_oidcserver\controllers;

use moodle_url;

require_once($CFG->dirroot.'/local/oidcserver/classes/controller.php');

class client extends controller {

    public function receive($cmd, $data = []) {
        parent::receive($cmd, $data);

        // Receive explicit commands.
        switch ($cmd) {
            case "delete":
            case "unsetconfidential":
            case "setconfidential": {
                $this->data->clientid = required_param('clientid', PARAM_INT);
            }
        }
    }

    public function process($cmd) {
        global $DB;

        $config = get_config('local_oidcserver');
        $clientkeysize = $config->clientkeysize ?? 13;

        parent::process($cmd);
        // Process explicit commands.

        switch ($cmd) {
            case 'add' : {
                if (!empty($this->data->generateidentifier)) {
                    $this->data->identifier = $this->generate($clientkeysize);
                }
                if (!empty($this->data->generatesecret)) {
                    $this->data->secret = $this->generate($clientkeysize);
                }
                unset($this->data->generateidentifier);
                unset($this->data->generatesecret);
                $DB->insert_record('local_oidcserver_client', $this->data);
                return new moodle_url('/local/oidcserver/clients.php');
            }

            case 'update' : {
                if (!empty($this->data->generateidentifier)) {
                    $this->data->identifier = uniqid();
                }
                if (!empty($this->data->generatesecret)) {
                    $this->data->secret = uniqid();
                }
                unset($this->data->generateidentifier);
                unset($this->data->generatesecret);
                $DB->update_record('local_oidcserver_client', $this->data);
                return new moodle_url('/local/oidcserver/clients.php');
            }

            case 'delete' : {
                $DB->delete_records('local_oidcserver_client', ['id' => $this->data->clientid]);
            }

            case 'setconfidential' : {
                $DB->set_field('local_oidcserver_client', 'isconfidential', 1, ['id' => $this->data->clientid]);
            }

            case 'unsetconfidential' : {
                $DB->set_field('local_oidcserver_client', 'isconfidential', 0, ['id' => $this->data->clientid]);
            }
        }
    }

    private function generate($length = 13) {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($length / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $length);
    }
}