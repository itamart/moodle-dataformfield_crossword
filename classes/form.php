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
 * @package dataformfield_crossword
 * @copyright 2015 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_crossword_form extends \mod_dataform\pluginbase\dataformfieldform {

    /**
     * The field default content fieldset. Override parent to display no defaults.
     *
     * @return void
     */
    protected function definition_defaults() {
    }

    /**
     *
     */
    protected function field_definition() {
        $field = &$this->_field;
        $mform = &$this->_form;

        // Words (param1).
        $mform->addElement('textarea', 'param1', get_string('words', 'dataformfield_crossword'));
        $mform->setType('param1', PARAM_TEXT);
        //$mform->addHelpButton('param1', 'filesseparator', 'dataformfield_file');
        
        // Max row size (param2).
        $mform->addElement('text', 'param2', get_string('maxrowsize', 'dataformfield_crossword'));
        $mform->setType('param2', PARAM_INT);
        
        // Min number of words (param3).
        $mform->addElement('text', 'param3', get_string('minnumwords', 'dataformfield_crossword'));
        $mform->setType('param3', PARAM_INT);
        
        // Max number of words (param4).
        $mform->addElement('text', 'param4', get_string('maxnumwords', 'dataformfield_crossword'));
        $mform->setType('param4', PARAM_INT);
        
    }

}
