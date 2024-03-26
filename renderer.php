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

class local_oidcserver_renderer extends plugin_renderer_base {

    public function clients($clients = []) {
        $namestr = get_string('clientname', 'local_oidcserver');
        $identifierstr = get_string('identifier', 'local_oidcserver');
        $secretstr = get_string('secret', 'local_oidcserver');
        $isconfidentialstr = get_string('isconfidential', 'local_oidcserver');
        $table = new html_table();
        $table->head = [$namestr, $identifierstr, $secretstr, $isconfidentialstr];
        $table->width = '100%';
        $table->size = ['30%', '25%', '25%', '10%', '10%'];

        foreach ($clients as $c) {
            $row = [];
            $row[] = $c->name;
            $row[] = $c->identifier;
            $row[] = $c->secret;
            $row[] = $c->isconfidential;

            $editurl = new moodle_url('/local/oidcserver/edit_client.php', ['id' => $c->id]);
            $cmds = '<a href="'.$editurl.'">'.$this->output->pix_icon('t/edit', 'edit').'</a>';
            $deleteurl = new moodle_url('/local/oidcserver/clients.php', ['what' => 'delete', 'id' => $c->id]);
            $cmds .= '&nbsp;<a href="'.$deleteurl.'">'.$this->output->pix_icon('t/delete', 'delete').'</a>';
            $row[] = $cmds;
            $table->data[] = $row;
        }

        return html_writer::table($table, true);
    }

    public function scopes($scopes = []) {
        $identifierstr = get_string('identifier', 'local_oidcserver');
        $descriptionstr = get_string('description', 'local_oidcserver');
        $table = new html_table();
        $table->head = [$identifierstr, $descriptionstr, ''];
        $table->width = '100%';
        $table->size = ['40%', '40%', '20%'];

        foreach ($scopes as $c) {
            $row = [];
            $row[] = $c->identifier;
            $row[] = $c->description;

            $editurl = new moodle_url('/local/oidcserver/edit_scope.php', ['id' => $c->id]);
            $cmds = '<a href="'.$editurl.'">'.$this->output->pix_icon('t/edit', 'edit').'</a>';
            $deleteurl = new moodle_url('/local/oidcserver/scopes.php', ['what' => 'delete', 'id' => $c->id]);
            $cmds .= '&nbsp;<a href="'.$deleteurl.'">'.$this->output->pix_icon('t/delete', 'delete').'</a>';
            $row[] = $cmds;
            $table->data[] = $row;
        }

        return html_writer::table($table, true);
    }

    /*
     *
     *
     */
    public function menu($menus = []) {
        global $OUTPUT;

        $template = new StdClass;

        foreach ($menus as $m) {
            $menutpl = new StdClass;
            $menutpl->url = $m->url;
            $menutpl->label = $m->label;
            $template->menus[] = $menutpl;
        }

        return $OUTPUT->render_from_template('local_oidcserver/menus', $template);
    }

    public function addclientlink() {

        $url = new moodle_url('/local/oidcserver/edit_client.php');
        $link = '<a href="'.$url.'">'.get_string('addclient', 'local_oidcserver').'</a>';

        return $link;
    }

    public function addscopelink() {

        $url = new moodle_url('/local/oidcserver/edit_scope.php');
        $link = '<a href="'.$url.'">'.get_string('addscope', 'local_oidcserver').'</a>';

        return $link;
    }
}