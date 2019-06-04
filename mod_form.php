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
 * The main congrea configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_congrea
 * @copyright  2014 Pinky Sharma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/congrea/locallib.php');

/**
 * Module instance settings form
 *
 * @copyright  2014 Pinky Sharma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_congrea_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;
        $mform = $this->_form;
        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));
        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('congreaname', 'congrea'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'congreaname', 'congrea');
        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->version > 2014111008) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor(); // Moodle.2.8.9 or earlier.
        }
        // Adding the rest of congrea settings
        // Adding list of teachers.
        $teacheroptions = congrea_course_teacher_list();
        if (empty($teacheroptions)) {
            $teacheroptions = "";
        } else {
            $teacheroptions[0] = 'Select';
        }
        $mform->addElement('select', 'moderatorid', get_string('selectteacher', 'congrea'), $teacheroptions);
        $mform->addHelpButton('moderatorid', 'selectteacher', 'congrea');
        if (get_config('mod_congrea', 'allowoverride')) { // If admin allowoverride is enabled then all settings is show.
            // congrea General Settings.
            $mform->addElement('header', 'general', get_string('studentm', 'congrea'));
            // Disable attendee Audio.
            $mform->addElement('advcheckbox', 'studentaudio', get_string('studentaudio', 'congrea'), ' ', null);
            $mform->addHelpButton('studentaudio', 'studentaudio', 'congrea');
            if (get_config('mod_congrea', 'studentaudio')) {
                $mform->setDefault('studentaudio', 1);
            } else {
                $mform->setDefault('studentaudio', 0);
            }
            // Disable attendee video.
            $mform->addElement('advcheckbox', 'studentvideo', get_string('studentvideo', 'congrea'), ' ', null);
            $mform->addHelpButton('studentvideo', 'studentvideo', 'congrea');
            if (get_config('mod_congrea', 'studentvideo')) {
                $mform->setDefault('studentvideo', 1);
            } else {
                $mform->setDefault('studentvideo', 0);
            }
            // Disable private chat by attendee.
            $mform->addElement('advcheckbox', 'studentpc', get_string('studentpc', 'congrea'), ' ', null);
            $mform->addHelpButton('studentpc', 'studentpc', 'congrea');
            if (get_config('mod_congrea', 'studentpc')) {
                $mform->setDefault('studentpc', 1);
            } else {
                $mform->setDefault('studentpc', 0);
            }
            // Disable group chat by attendee.
            $mform->addElement('advcheckbox', 'studentgc', get_string('studentgc', 'congrea'), ' ', null);
            $mform->addHelpButton('studentgc', 'studentgc', 'congrea');
            if (get_config('mod_congrea', 'studentgc')) {
                $mform->setDefault('studentgc', 1);
            } else {
                $mform->setDefault('studentgc', 0);
            }
            // Disable Raise Hand.
            $mform->addElement('advcheckbox', 'raisehand', get_string('raisehand', 'congrea'), ' ', null);
            $mform->addHelpButton('raisehand', 'raisehand', 'congrea');
            if (get_config('mod_congrea', 'raisehand')) {
                $mform->setDefault('raisehand', 1);
            } else {
                $mform->setDefault('raisehand', 0);
            }
            // Disable user list for attendees.
            $mform->addElement('advcheckbox', 'userlist', get_string('userlist', 'congrea'), ' ', null);
            $mform->addHelpButton('userlist', 'userlist', 'congrea');
            if (get_config('mod_congrea', 'userlist')) {
                $mform->setDefault('userlist', 1);
            } else {
                $mform->setDefault('userlist', 0);
            }
            // Congrea recording settings.
            if (get_config('mod_congrea', 'enablerecording')) { // Enable recording is on then all setting of rec are show.
                $mform->addElement('header', 'general', get_string('recordingsection', 'congrea'));
                $mform->addElement('advcheckbox', 'enablerecording', get_string('cgrecording', 'congrea'), ' ', null); // Enablerecording.
                $mform->addHelpButton('enablerecording', 'cgrecording', 'congrea');
                if (get_config('mod_congrea', 'enablerecording')) {
                    $mform->setDefault('enablerecording', 1);
                } else {
                    $mform->setDefault('enablerecording', 0);
                }
                // Allow presentor to control A/V recording (button in live session.
                $mform->addElement('advcheckbox', 'recallowpresentoravcontrol',
                                get_string('recAllowpresentorAVcontrol', 'congrea'), ' ', null);
                $mform->addHelpButton('recallowpresentoravcontrol', 'recAllowpresentorAVcontrol', 'congrea');
                if (get_config('mod_congrea', 'recAllowpresentorAVcontrol')) {
                    $mform->setDefault('recallowpresentoravcontrol', 1);
                } else {
                    $mform->setDefault('recallowpresentoravcontrol', 0);
                }
                $mform->disabledIf('recallowpresentoravcontrol', 'enablerecording', 'notchecked');
                // Show recording status to presentor.
                $mform->addElement('advcheckbox', 'showpresentorrecordingstatus',
                        get_string('recShowPresentorRecordingStatus', 'congrea'), ' ', null);
                $mform->addHelpButton('showpresentorrecordingstatus', 'recShowPresentorRecordingStatus', 'congrea');
                if (get_config('mod_congrea', 'recShowPresentorRecordingStatus')) {
                    $mform->setDefault('showpresentorrecordingstatus', 1);
                } else {
                    $mform->setDefault('showpresentorrecordingstatus', 0);
                }
                $mform->disabledIf('showpresentorrecordingstatus', 'enablerecording', 'notchecked');
                $mform->disabledIf('showpresentorrecordingstatus', 'recallowpresentoravcontrol', 'checked');
                // Disable attendee A/V in recording.
                $mform->addElement('advcheckbox', 'recattendeeav',
                        get_string('recattendeeav', 'congrea'), ' ', null);
                $mform->addHelpButton('recattendeeav', 'recattendeeav', 'congrea');
                if (get_config('mod_congrea', 'recattendeeav')) {
                    $mform->setDefault('recattendeeav', 1);
                } else {
                    $mform->setDefault('recattendeeav', 0);
                }
                $mform->disabledIf('recdisableattendeeav', 'enablerecording', 'notchecked');
                // Allow attendees to control A/V settings.
                $mform->addElement('advcheckbox', 'recallowattendeeavcontrol',
                        get_string('recAllowattendeeAVcontrol', 'congrea'), ' ', null);
                $mform->addHelpButton('recallowattendeeavcontrol', 'recAllowattendeeAVcontrol', 'congrea');
                if (get_config('mod_congrea', 'recAllowattendeeAVcontrol')) {
                    $mform->setDefault('recallowattendeeavcontrol', 1);
                } else {
                    $mform->setDefault('recallowattendeeavcontrol', 0);
                }
                $mform->disabledIf('recallowattendeeavcontrol', 'enablerecording', 'notchecked');
                // Show recording status to attendees.
                $mform->addElement('advcheckbox', 'showattendeerecordingstatus',
                        get_string('showAttendeeRecordingStatus', 'congrea'), ' ', null);
                $mform->addHelpButton('showattendeerecordingstatus', 'showAttendeeRecordingStatus', 'congrea');
                if (get_config('mod_congrea', 'showAttendeeRecordingStatus')) {
                    $mform->setDefault('showattendeerecordingstatus', 1);
                } else {
                    $mform->setDefault('showattendeerecordingstatus', 0);
                }
                $mform->disabledIf('showattendeerecordingstatus', 'enablerecording', 'notchecked');
                $mform->disabledIf('showattendeerecordingstatus', 'recallowattendeeavcontrol', 'checked');
                // Trim recordings where A/V is marked off.
                $mform->addElement('advcheckbox', 'trimrecordings', get_string('trimRecordings', 'congrea'), ' ', null);
                $mform->addHelpButton('trimrecordings', 'trimRecordings', 'congrea');
                if (get_config('mod_congrea', 'trimRecordings')) {
                    $mform->setDefault('trimrecordings', 1);
                } else {
                    $mform->setDefault('trimrecordings', 0);
                }
                $mform->disabledIf('trimrecordings', 'enablerecording', 'notchecked');
            }
        }
        // Schedule fo session.
        $mform->addElement('header', 'general', get_string('sessionsschedule', 'congrea'));
        $mform->addElement('date_time_selector', 'opentime', get_string('opentime', 'congrea'));
        $mform->addRule('opentime', null, 'required', null, 'client');
        $mform->addElement('date_time_selector', 'closetime', get_string('closetime', 'congrea'));
        $mform->addRule('closetime', null, 'required', null, 'client');
        $this->standard_coursemodule_elements();
        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    /**
     * Validate this form.
     *
     * @param array $data submitted data
     * @param array $files not used
     * @return array errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        // Check open and close times are consistent.
        if ($data['opentime'] != 0 && $data['closetime'] != 0 &&
                $data['closetime'] < $data['opentime']) {
            $errors['closetime'] = get_string('closebeforeopen', 'congrea');
        }
        if ($data['opentime'] != 0 && $data['closetime'] == 0) {
            $errors['closetime'] = get_string('closenotset', 'congrea');
        }
        if ($data['opentime'] != 0 && $data['closetime'] != 0 &&
                $data['closetime'] == $data['opentime']) {
            $errors['closetime'] = get_string('closesameopen', 'congrea');
        }
        return $errors;
    }

}
