<?php
// One-off: attach 3 real workshop photos (teacher's own, uploaded via chat 2026-09-04) to the
// "Υλικά Build — Κατάσταση" page (cmid 29 / mod_page instance 5) as a small gallery, chosen
// deliberately over the teacher's raw request to "use the photos I already sent you" — picked
// only the ones that are clean, on-topic, and not troubleshooting/failure shots (no broken
// print, no cold solder joint, no error screens): workbench, the actual prop stock, and the
// school's 3D printer in use.
//
// v2: v1 used <figure>/<figcaption> and CSS width/display on <img> - Moodle's HTML cleaner
// strips figure/figcaption entirely and strips width/display from style (confirmed via
// format_text() test, chat 2026-09-04). Fixed: plain <div>/<p>, image size set via the HTML
// width ATTRIBUTE (not CSS) which survives, images stack vertically (no CSS layout needed).
//
// Files are pre-copied into /tmp/photos/ by the caller (docker cp) only on first run; the file
// API storage step is idempotent regardless.
//
// Kept for the record (moodle/CLAUDE.md: GitHub is a log of what scripts did to the live site).
// Idempotent: safe to re-run; upgrades a v1 gallery to v2, or exits if v2 already present.
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$CMID   = 29;
$V1     = '<!-- workshop-photos:v1 -->';
$V2     = '<!-- workshop-photos:v2 -->';
$ANCHOR = '<h4>Το drone</h4>';

$cm      = $DB->get_record('course_modules', ['id' => $CMID], '*', MUST_EXIST);
$page    = $DB->get_record('page', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($CMID);

if (strpos($page->content, $V2) !== false) {
    echo "page {$page->id}: v2 gallery already present, nothing to do\n";
    exit(0);
}

$fs = get_file_storage();

$photos = [
    'workshop-bench.jpg'   => 'Ο πάγκος εργασίας μας',
    'props-hurricane.jpg'  => 'Οι έλικες μας (Gemfan Hurricane 51433-3)',
    'printer-ender3v3.jpg' => 'Ο 3D printer του σχολείου (Ender-3 V3)',
];

$blocks = '';
foreach ($photos as $filename => $caption) {
    if (!$fs->file_exists($context->id, 'mod_page', 'content', 0, '/', $filename)) {
        $filerecord = [
            'contextid' => $context->id,
            'component' => 'mod_page',
            'filearea'  => 'content',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $filename,
        ];
        $fs->create_file_from_pathname($filerecord, "/tmp/photos/{$filename}");
    }
    $url = moodle_url::make_pluginfile_url($context->id, 'mod_page', 'content', 0, '/', $filename);
    $blocks .= '<div style="margin:0 0 1em">'
        . '<img src="' . s($url->out(false)) . '" width="280" alt="' . s($caption) . '" '
        . 'style="border-radius:6px;border:1px solid #ccc">'
        . '<p style="font-size:.85em;color:#555;margin:.35em 0 0">' . s($caption) . '</p>'
        . '</div>';
}

$gallery = $V2 . '<h4>📸 Από το εργαστήριό μας</h4>' . $blocks;

if (strpos($page->content, $V1) !== false) {
    // Replace the whole old (broken) v1 block, from its sentinel up to the anchor heading.
    $start = strpos($page->content, $V1);
    $end   = strpos($page->content, $ANCHOR, $start);
    if ($end === false) {
        fwrite(STDERR, "page {$page->id}: found v1 sentinel but not the anchor after it - aborting\n");
        exit(1);
    }
    $new = substr($page->content, 0, $start) . $gallery . substr($page->content, $end);
} else {
    if (strpos($page->content, $ANCHOR) === false) {
        fwrite(STDERR, "page {$page->id}: anchor heading not found - aborting, no change made\n");
        exit(1);
    }
    $new = str_replace($ANCHOR, $gallery . $ANCHOR, $page->content);
}

$DB->update_record('page', (object)[
    'id'           => $page->id,
    'content'      => $new,
    'timemodified' => time(),
]);

echo "page {$page->id}: v2 gallery written, old_len=" . strlen($page->content) . " new_len=" . strlen($new) . "\n";
