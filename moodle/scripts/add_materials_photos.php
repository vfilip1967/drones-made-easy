<?php
// One-off: build the "📸 Από το εργαστήριό μας" gallery on the materials status page
// (cmid 29 / mod_page instance 5) from the teacher's own photos.
//
// v3: adds 2 more photos (precision bit set, flush cutter + tweezers) on top of the v2 three
// (workbench, props, 3D printer), and — new in v3 — RESIZES every image via GD before storing
// (source phone photos were 0.7-1.9MB / full sensor resolution for a 280px-wide display; that's
// wasteful over mobile data, exactly the audience this page is for). Deliberately still leaves
// out: the cold/failed solder-joint photos and the 3D-print troubleshooting screenshots — those
// are debugging material, not "here is the equipment", and one is a shot of a bad joint.
//
// v2 lesson (still applies): Moodle's HTML cleaner strips <figure>/<figcaption> and strips
// width/display from an <img>'s CSS style. Fix used here: plain <div>/<p>, image size via the
// HTML width ATTRIBUTE (not CSS), which survives.
//
// Files are pre-copied into /tmp/photos/ by the caller (docker cp) before running this.
// Kept for the record (moodle/CLAUDE.md: GitHub is a log of what scripts did to the live site).
// Idempotent: safe to re-run; replaces any previous gallery version with this one.
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$CMID       = 29;
$SENTINELS  = ['<!-- workshop-photos:v1 -->', '<!-- workshop-photos:v2 -->'];
$V3         = '<!-- workshop-photos:v3 -->';
$ANCHOR     = '<h4>Το drone</h4>';
$MAX_DIM    = 900;   // px, longest side - plenty for a 280px display width incl. retina
$JPEG_QUAL  = 82;

$cm      = $DB->get_record('course_modules', ['id' => $CMID], '*', MUST_EXIST);
$page    = $DB->get_record('page', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($CMID);

if (strpos($page->content, $V3) !== false) {
    echo "page {$page->id}: v3 gallery already present, nothing to do\n";
    exit(0);
}

function resize_jpeg(string $src, string $dst, int $maxdim, int $quality): void {
    $info = getimagesize($src);
    [$w, $h] = $info;
    $scale = min(1, $maxdim / max($w, $h));
    $nw = max(1, (int)round($w * $scale));
    $nh = max(1, (int)round($h * $scale));

    $srcImg = imagecreatefromjpeg($src);
    // Respect EXIF orientation if present, so photos taken in portrait don't end up rotated.
    if (function_exists('exif_read_data')) {
        $exif = @exif_read_data($src);
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3: $srcImg = imagerotate($srcImg, 180, 0); break;
                case 6: $srcImg = imagerotate($srcImg, -90, 0); [$nw, $nh] = [$nh, $nw]; break;
                case 8: $srcImg = imagerotate($srcImg, 90, 0);  [$nw, $nh] = [$nh, $nw]; break;
            }
        }
    }
    $dstImg = imagecreatetruecolor($nw, $nh);
    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $nw, $nh, imagesx($srcImg), imagesy($srcImg));
    imagejpeg($dstImg, $dst, $quality);
    imagedestroy($srcImg);
    imagedestroy($dstImg);
}

$fs = get_file_storage();

$photos = [
    'workshop-bench.jpg'    => 'Ο πάγκος εργασίας μας',
    'props-hurricane.jpg'   => 'Οι έλικες μας (Gemfan Hurricane 51433-3)',
    'printer-ender3v3.jpg'  => 'Ο 3D printer του σχολείου (Ender-3 V3)',
    'screwdriver-bits.jpg'  => 'Σετ μυτών ακριβείας',
    'cutter-tweezers.jpg'   => 'Κόφτης ακριβείας & τσιμπιδάκια',
];

$blocks = '';
foreach ($photos as $filename => $caption) {
    // Always replace with the resized version (handles both new files and the v2 full-size
    // uploads that need shrinking now).
    if ($fs->file_exists($context->id, 'mod_page', 'content', 0, '/', $filename)) {
        $fs->get_file($context->id, 'mod_page', 'content', 0, '/', $filename)->delete();
    }
    $src = "/tmp/photos/{$filename}";
    $tmp = "/tmp/photos/resized-{$filename}";
    resize_jpeg($src, $tmp, $MAX_DIM, $JPEG_QUAL);

    $filerecord = [
        'contextid' => $context->id,
        'component' => 'mod_page',
        'filearea'  => 'content',
        'itemid'    => 0,
        'filepath'  => '/',
        'filename'  => $filename,
    ];
    $fs->create_file_from_pathname($filerecord, $tmp);
    $newsize = filesize($tmp);

    $url = moodle_url::make_pluginfile_url($context->id, 'mod_page', 'content', 0, '/', $filename);
    $blocks .= '<div style="margin:0 0 1em">'
        . '<img src="' . s($url->out(false)) . '" width="280" alt="' . s($caption) . '" '
        . 'style="border-radius:6px;border:1px solid #ccc">'
        . '<p style="font-size:.85em;color:#555;margin:.35em 0 0">' . s($caption) . '</p>'
        . '</div>';
    echo "  {$filename}: " . round(filesize($src)/1024) . "KB -> " . round($newsize/1024) . "KB\n";
}

$gallery = $V3 . '<h4>📸 Από το εργαστήριό μας</h4>' . $blocks;

// Remove any earlier gallery version (v1 or v2 sentinel through the anchor heading), then
// insert the v3 gallery right before that same anchor.
$content = $page->content;
foreach ($SENTINELS as $sentinel) {
    $start = strpos($content, $sentinel);
    if ($start !== false) {
        $end = strpos($content, $ANCHOR, $start);
        if ($end === false) {
            fwrite(STDERR, "page {$page->id}: found {$sentinel} but not the anchor after it - aborting\n");
            exit(1);
        }
        $content = substr($content, 0, $start) . substr($content, $end);
        break;
    }
}
if (strpos($content, $ANCHOR) === false) {
    fwrite(STDERR, "page {$page->id}: anchor heading not found - aborting, no change made\n");
    exit(1);
}
$new = str_replace($ANCHOR, $gallery . $ANCHOR, $content);

$DB->update_record('page', (object)[
    'id'           => $page->id,
    'content'      => $new,
    'timemodified' => time(),
]);

echo "page {$page->id}: v3 gallery written, old_len=" . strlen($page->content) . " new_len=" . strlen($new) . "\n";
