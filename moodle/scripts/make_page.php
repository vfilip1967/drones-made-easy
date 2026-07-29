<?php
// Creates a Moodle Page (mod_page) activity with real content.
// Usage: php make_page.php <courseid> <section> <name> <htmlfile>
//
// GOTCHA (cost a live bug on 2026-07-29): page_add_instance() only copies
// $data->page['text'] into $data->content when called with a non-null $mform.
// add_moduleinfo() always passes $mform = null from CLI code, so setting
// $mi->page = ['text'=>...] silently produces an EMPTY page. Set
// $mi->content / $mi->contentformat directly instead — that's what this
// script does. If a Page you create renders blank, this is why.

define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->dirroot . '/course/modlib.php');

if ($argc < 5) {
    fwrite(STDERR, "Usage: php make_page.php <courseid> <section> <name> <htmlfile>\n");
    exit(1);
}

$courseid = (int)$argv[1];
$section  = (int)$argv[2];
$name     = $argv[3];
$htmlfile = $argv[4];

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$module = $DB->get_record('modules', ['name' => 'page'], '*', MUST_EXIST);

$mi = new stdClass();
$mi->modulename    = 'page';
$mi->module        = $module->id;
$mi->course        = $course->id;
$mi->section       = $section;
$mi->visible       = 1;
$mi->name          = $name;
$mi->intro         = '';
$mi->introformat   = FORMAT_HTML;
$mi->content       = file_get_contents($htmlfile);   // NOT $mi->page[...] — see gotcha above
$mi->contentformat = FORMAT_HTML;
$mi->display       = 5;   // RESOURCELIB_DISPLAY_OPEN
$mi->printintro    = 0;
$mi->printlastmodified = 0;

$mi = add_moduleinfo($mi, $course);
echo "OK page cmid={$mi->coursemodule} instance={$mi->instance} content_len=" . strlen($mi->content) . "\n";
