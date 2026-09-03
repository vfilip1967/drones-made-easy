<?php
// One-off: fix the soldering tutorial videos in the Κεφ.14β book chapter (id 7) so they
// are reachable from the Moodle mobile app.
//
// History (chat 2026-09-02/03):
//  - add_videos.php added them as <a href="youtube.com/watch?v=..." target="_blank"> links.
//  - Moodle's mediaplugin filter auto-embedded them (first as a videojs-YouTube player, then,
//    after media_videojs:youtube=0, as a classic youtube.com/embed iframe).
//  - BOTH embed forms fail in the Moodle app: the app rewrites the embed's src to route
//    through admin/tool/mobile/autologin.php, and Moodle refuses to render that page inside
//    an iframe (X-Frame-Options) -> net::ERR_BLOCKED_BY_RESPONSE shown inline in the chapter.
//  - scriptallowlist + youtube-nocookie did not help.
//
// Final approach: NO embedding. The media_youtube player is disabled site-wide
// (\core\plugininfo\media::enable_plugin('youtube', 0)), so YouTube URLs stay plain links.
// In the app a plain external link opens in the system browser / YouTube app (no iframe, no
// autologin wrap, no frame-busting). On desktop they are ordinary links that open in a new tab.
//
// Kept for the record (moodle/CLAUDE.md: GitHub is a log of what scripts did to the live
// site). Idempotent: safe to re-run.
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$CHAPTER_ID = 7;
$HEADING    = '<h4>Στόχοι μάθησης</h4>';
$SENTINEL   = '<!-- videos:v3 -->';

$content = $DB->get_field('book_chapters', 'content', ['id' => $CHAPTER_ID], MUST_EXIST);

if (strpos($content, '🎥 Εκπαιδευτικά βίντεο κόλλησης') === false || strpos($content, $HEADING) === false) {
    fwrite(STDERR, "chapter {$CHAPTER_ID}: expected markers not found — aborting, no change made\n");
    exit(1);
}
if (strpos($content, $SENTINEL) !== false) {
    echo "chapter {$CHAPTER_ID}: already at v3, nothing to do\n";
    exit(0);
}

function yt_li(string $label, string $id, string $title, string $note): string {
    $url = 'https://www.youtube.com/watch?v=' . $id;
    return '<li style="margin:.5em 0"><strong>' . $label . '</strong><br>'
        . '<a href="' . $url . '" target="_blank" rel="noopener">▶ ' . s($title) . '</a> ' . $note . '</li>';
}

$videobox = $SENTINEL
  . '<div style="border:2px solid #2b6cb0;background:#ebf8ff;padding:1em;margin:1em 0">'
  . '<h4>🎥 Εκπαιδευτικά βίντεο κόλλησης</h4>'
  . '<p>Επειδή αυτό είναι πρακτική δεξιότητα, δείτε πρώτα ένα βίντεο πριν αγγίξετε το iron. '
  . 'Αγγλόφωνα, αλλά η κόλληση φαίνεται καθαρά χωρίς να χρειάζεται ο ήχος. '
  . 'Οι σύνδεσμοι ανοίγουν στο YouTube (στην εφαρμογή YouTube ή στον browser).</p>'
  . '<ul>'
  . yt_li('Αν κολλάς για πρώτη φορά ποτέ:', 'MWrJGEcMHQI',
          'How to Solder Electronics, Wires, and Plugs — Complete Beginners Guide',
          '(γενικά βασικά, ~10 λεπτά)')
  . yt_li('Ειδικά για FPV/drone κολλήσεις:', 'GoPT69y98pY',
          'Most FPV pilots need to watch this soldering tutorial',
          '(motor wires, XT60, FC pads — ακριβώς ό,τι θα κάνουμε)')
  . '</ul>'
  . '<p style="font-size:.9em;color:#555">Αν ο σύνδεσμος δεν ανοίγει, αναζητήστε τον τίτλο '
  . 'απευθείας στην εφαρμογή YouTube.</p>'
  . '</div>';

$new = $videobox . substr($content, strpos($content, $HEADING));

$DB->update_record('book_chapters', (object)[
    'id'           => $CHAPTER_ID,
    'content'      => $new,
    'timemodified' => time(),
]);

echo "chapter {$CHAPTER_ID}: updated to v3, old_len=" . strlen($content) . " new_len=" . strlen($new) . "\n";
