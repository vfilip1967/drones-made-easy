<?php
// One-off: uploads lambaki_diagram.png into mod_page's file area (mod_page/content/0)
// and inserts an <img> referencing it via pluginfile.php into the worksheet page (cmid=11).
//
// GOTCHA: <svg> tags and data:image;base64 <img> src both get stripped by format_text()'s
// HTML purifier, silently, same class of bug as the mod_page content issue documented
// elsewhere in this README. The only reliable way to embed an image in Moodle text content
// is to store it via the File API and reference it through a real pluginfile.php URL.
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$cmid = 11;
$context = context_module::instance($cmid);
$fs = get_file_storage();

if ($fs->file_exists($context->id, 'mod_page', 'content', 0, '/', 'lambaki_diagram.png')) {
    $fs->get_file($context->id, 'mod_page', 'content', 0, '/', 'lambaki_diagram.png')->delete();
}
$fs->create_file_from_pathname([
    'contextid' => $context->id,
    'component' => 'mod_page',
    'filearea'  => 'content',
    'itemid'    => 0,
    'filepath'  => '/',
    'filename'  => 'lambaki_diagram.png',
], '/tmp/lambaki_diagram.png');

$url = moodle_url::make_pluginfile_url($context->id, 'mod_page', 'content', 0, '/', 'lambaki_diagram.png');

$cm = $DB->get_record('course_modules', ['id' => $cmid]);
$p  = $DB->get_record('page', ['id' => $cm->instance]);

$imgblock = '<div style="max-width:900px;margin:1em 0;border:1px solid #ccc;padding:0.5em;background:#fafafa">'
    . '<img src="' . $url->out(false) . '" alt="Διάγραμμα υλικών και τελικού κυκλώματος" style="max-width:100%;height:auto">'
    . '</div>' . "\n";

$marker = '<h3>Στόχος</h3>';
$pos = strpos($p->content, $marker);
if ($pos !== false && strpos($p->content, 'lambaki_diagram.png') === false) {
    $p->content = substr($p->content, 0, $pos) . $imgblock . substr($p->content, $pos);
    $p->timemodified = time();
    $DB->update_record('page', $p);
}
echo "OK url={$url->out(false)}\n";
