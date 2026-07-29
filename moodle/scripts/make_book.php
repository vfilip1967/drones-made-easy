<?php
// Creates a Moodle Book activity and populates its chapters from a JSON spec.
// Usage: php make_book.php <courseid> <section> <spec.json>
// spec.json: {"name":"...","intro":"...","chapters":[{"title":"...","content":"<p>html</p>","subchapter":0}]}

define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/lib/datalib.php');

if ($argc < 4) {
    fwrite(STDERR, "Usage: php make_book.php <courseid> <section> <spec.json>\n");
    exit(1);
}

$courseid = (int)$argv[1];
$section  = (int)$argv[2];
$specfile = $argv[3];

$spec = json_decode(file_get_contents($specfile), true, 512, JSON_THROW_ON_ERROR);
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$module = $DB->get_record('modules', ['name' => 'book'], '*', MUST_EXIST);

$mi = new stdClass();
$mi->modulename    = 'book';
$mi->module        = $module->id;
$mi->course        = $course->id;
$mi->section       = $section;
$mi->visible       = 1;
$mi->name          = $spec['name'];
$mi->intro         = $spec['intro'] ?? '';
$mi->introformat   = FORMAT_HTML;
$mi->numbering     = 1;   // numeric
$mi->navstyle      = 1;
$mi->customtitles  = 0;

$mi = add_moduleinfo($mi, $course);
$bookid = $mi->instance;

$pagenum = 0;
foreach ($spec['chapters'] as $ch) {
    $pagenum++;
    $rec = new stdClass();
    $rec->bookid        = $bookid;
    $rec->pagenum       = $pagenum;
    $rec->subchapter    = (int)($ch['subchapter'] ?? 0);
    $rec->title         = $ch['title'];
    $rec->content       = $ch['content'];
    $rec->contentformat = FORMAT_HTML;
    $rec->hidden        = 0;
    $rec->timecreated   = time();
    $rec->timemodified  = time();
    $rec->importsrc     = '';
    $DB->insert_record('book_chapters', $rec);
}

echo "OK book cmid={$mi->coursemodule} instance={$bookid} chapters={$pagenum}\n";
