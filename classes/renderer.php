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
defined('MOODLE_INTERNAL') or die();

/**
 *
 */
class dataformfield_crossword_renderer extends \mod_dataform\pluginbase\dataformfieldrenderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $fieldname = $field->name;
        $edit = !empty($options['edit']);

        $entrywords = $this->get_entry_words($entry);
        $entrywordssolved = $this->get_entry_words_solved($entry);

        $replacements = array_fill_keys($patterns, '');

        foreach ($patterns as $pattern) {
            if ($pattern == "[[$fieldname]]") {
                if ($edit) {
                    $replacements[$pattern] = array(array($this, 'display_edit'), array($entry));
                }

            } else if ($pattern == "[[$fieldname:wordscount]]") {
                $replacements[$pattern] = count($field->words);

            } else if ($pattern == "[[$fieldname:entrywordscount]]") {
                $replacements[$pattern] = count($entrywords);

            } else if ($pattern == "[[$fieldname:entryletterscount]]") {
                $replacements[$pattern] = array_sum($entrywords);

            } else if ($pattern == "[[$fieldname:entrywordssolved]]") {
                $replacements[$pattern] = count($entrywordssolved);

            } else if ($pattern == "[[$fieldname:crosscompletion]]") {
                $p = 0;
                if ($letterscount = array_sum($entrywords)) {
                    $letterssolvedcount = array_sum($entrywordssolved);
                    $p = round($letterssolvedcount * 100 / $letterscount);
                }
                $replacements[$pattern] = $p. '%';
            }
        }
        return $replacements;
    }

    /**
     *
     */
    public function display_edit(&$mform, $entry, array $options = null) {
        global $PAGE;

        $field = $this->_field;
        $fieldid = $field->id;
        $entryid = $entry->id;
        $fieldname = "field_{$fieldid}_{$entryid}";

        // Puzzle data.
        if (isset($entry->{"c{$fieldid}_content"})) {
            $puzzledata = $entry->{"c{$fieldid}_content"};
        } else {
            $puzzledata = $field->puzzle_data;
        }
        $mform->addElement('hidden', $fieldname, $puzzledata, array('class' => 'puzzle-data'));
        $mform->setType($fieldname, PARAM_TEXT);

        // Solved.
        if (isset($entry->{"c{$fieldid}_content1"})) {
            $solved = $entry->{"c{$fieldid}_content1"};
        } else {
            $solved = null;
        }
        $mform->addElement('hidden', $fieldname. '_solved', $solved, array('class' => 'puzzle-solved'));

        //$mform->addElement('hidden', $fieldname. '_solved', $solved, array('class' => 'puzzle-solved'));
        $mform->setType($fieldname. '_solved', PARAM_TEXT);


        $PAGE->requires->js('/mod/dataform/field/crossword/javascript/jquery.crossword.js');
        $PAGE->requires->js('/mod/dataform/field/crossword/javascript/script.js');

        // The puzzle wrapper.
        $puzzlewrapper = \html_writer::tag('div', null, array('id' => 'puzzle-wrapper'));

        $mform->addElement('html', $puzzlewrapper);
    }

    /**
     * Array of entry word lengths indexed by the respective words.
     */
    protected function get_entry_words($entry) {
        $field = $this->_field;
        $fieldid = $field->id;

        $words = array();
        if (isset($entry->{"c{$fieldid}_content"})) {
            $data = json_decode($entry->{"c{$fieldid}_content"});
            foreach ($data as $item) {
                $words[$item->answer] = strlen($item->answer);
            }
        }
        return $words;
    }

    /**
     * Array of entry word lengths indexed by the respective words.
     */
    protected function get_entry_words_solved($entry) {
        $field = $this->_field;
        $fieldid = $field->id;

        $words = array();
        if (isset($entry->{"c{$fieldid}_content1"})) {
            $data = explode(',', $entry->{"c{$fieldid}_content1"});
            foreach ($data as $item) {
                $words[$item] = strlen($item);
            }
        }
        return $words;
    }

    /**
     * Array of patterns this field supports
     */
    protected function patterns() {
        $fieldname = $this->_field->name;

        $patterns = parent::patterns();
        $patterns["[[$fieldname]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:wordscount]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:entrywordscount]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:entryletterscount]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:entrywordssolved]]"] = array(true, $fieldname);
        $patterns["[[$fieldname:crosscompletion]]"] = array(true, $fieldname);

        return $patterns;
    }
}
