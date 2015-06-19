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

require_once(__DIR__. '/../lib.php');

/**
 *
 */
class dataformfield_crossword_crossword extends \mod_dataform\pluginbase\dataformfield {

    /**
     *
     */
    public function content_names() {
        return array('', 'solved');
    }

    /**
     *
     */
    public function get_words() {
        if (!$this->param1) {
            return null;
        }

        $words = array();
        foreach (explode("\n", $this->param1) as $pair) {
            list($word, $clue) = explode(' ', $pair, 2);
            $words[$word] = $clue;
        }

        return $words;
    }

    /**
     *
     */
    public function get_puzzle_data() {
        $reps = array();

        if (!$words = $this->words) {
            return null;
        }

        if (!$wordcount = count($words) or $wordcount < 2) {
            return null;
        }

        // Max row size.
        $maxrowsize = $this->param2 ? $this->param2 : 60;

        // Minimum number of words for crossword.
        $minwords = $this->param3 ? $this->param3 : 2;
        $minwords = ($minwords > $wordcount ?  $wordcount : $minwords);

        // Maximum number of words for crossword.
        $maxwords = $this->param4 ? $this->param4 : 5;
        $maxwords = ($maxwords > $wordcount ?  $wordcount : $maxwords);

        $cross = new Cross;
        $cross->setwords($words, $maxrowsize, $reps);

        if(!$cross->computedata($crossm, $crossd, $letters, $minwords, $maxwords)) {
            return 'Could not generate crossword. Please refresh the page to try again.';
        }

        $positions = $crossd;
        ksort($positions);
        $currentkey = null;
        $count = 1;
        foreach ($positions as $key => $unused) {
            $newkey = substr($key, 0, 6);
            if (!$currentkey) {
                $currentkey = $newkey;
            }

            if ($newkey != $currentkey) {
                $count++;
                $currentkey = $newkey;
            }
            $positions[$key] = $count;
        }

        $puzzledata = array();
        foreach ($positions as $key => $position) {
            $crossentry = $crossd[$key];
            $puzzledata[] = (object) array(
                'clue' => $crossentry->questiontext,
                'answer' => $crossentry->answertext,
                'position' => $position,
                'orientation' => ($crossentry->horizontal ? 'across' : 'down'),
                'startx' => $crossentry->col,
                'starty' => $crossentry->row
            );
        }

        return json_encode($puzzledata);
    }

    /**
     *
     */
    protected function format_content($entry, array $values = null) {
        $fieldid = $this->id;
        $oldcontents = array();
        $contents = array();

        $varcontent = "c$fieldid". '_content';
        $varcontent1 = "c$fieldid". '_content1';

        // Puzzle data.
        $oldcontents[0] = isset($entry->$varcontent) ? $entry->$varcontent : null;
        $contents[0] = !empty($values['']) ? $values[''] :  null;

        // Puzzle solution.
        $oldcontents[1] = isset($entry->$varcontent1) ? $entry->$varcontent1 : null;
        $contents[1] = !empty($values['solved']) ? $values['solved'] :  null;

        return array($contents, $oldcontents);
    }

    /**
     *
     */
    public function get_content_parts() {
        return array('content', 'content1');
    }

    /**
     * Overriding parent to return no sort/search options.
     *
     * @return array
     */
    public function get_sort_options_menu() {
        return array();
    }
}
