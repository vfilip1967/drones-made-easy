<?php
// Imports a GIFT file into a course question bank category.
// Usage: php import_gift.php <courseid> <giftfile> <categoryname>
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->dirroot . '/lib/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/format/gift/format.php');
require_once($CFG->dirroot . '/course/modlib.php');

$courseid = (int)$argv[1];
$giftfile = $argv[2];
$catname  = $argv[3];
$qbankcmid = (int)$argv[4];

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$ctx    = context_module::instance($qbankcmid);

$top = question_get_top_category($ctx->id, true);

$cat = $DB->get_record('question_categories', ['contextid' => $ctx->id, 'name' => $catname]);
if (!$cat) {
    $cat = new stdClass();
    $cat->parent = $top->id;
    $cat->contextid = $ctx->id;
    $cat->name = $catname;
    $cat->info = '';
    $cat->infoformat = FORMAT_HTML;
    $cat->stamp = make_unique_id_code();
    $cat->sortorder = 999;
    $cat->idnumber = null;
    $cat->id = $DB->insert_record('question_categories', $cat);
    echo "created category {$cat->id} ({$catname})\n";
} else {
    echo "reusing category {$cat->id} ({$catname})\n";
}

$qformat = new qformat_gift();
$qformat->setCategory($cat);
$qformat->setContexts([$ctx]);
$qformat->setCourse($course);
$qformat->setFilename($giftfile);
$qformat->setRealfilename(basename($giftfile));
$qformat->setMatchgrades('error');
$qformat->setCatfromfile(false);
$qformat->setContextfromfile(false);
$qformat->setStoponerror(true);

if (!$qformat->importpreprocess()) { fwrite(STDERR, "importpreprocess failed\n"); exit(1); }
if (!$qformat->importprocess())    { fwrite(STDERR, "importprocess failed\n");    exit(1); }
if (!$qformat->importpostprocess()){ fwrite(STDERR, "importpostprocess failed\n");exit(1); }

$n = $DB->count_records_sql(
    "SELECT COUNT(1) FROM {question} q
       JOIN {question_versions} qv ON qv.questionid = q.id
       JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
      WHERE qbe.questioncategoryid = ?", [$cat->id]);
echo "OK category={$cat->id} questions_now={$n}\n";
