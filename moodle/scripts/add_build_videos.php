<?php
// One-off: add a "drone assembly videos" box to the Κεφ.14β book chapter (id 7), just after
// the soldering-video box added earlier (add_videos.php / fix_videos.php).
//
// STOPGAP: these are YouTube *search* links, not curated single videos - the teacher had not
// picked specific videos yet and we won't put unverified video IDs in front of students. Swap
// in real watch?v= links later (same box). Plain <a> links only; the media_youtube player is
// disabled site-wide (see moodle/CLAUDE.md) so nothing auto-embeds and the app opens them
// externally.
//
// Kept for the record (moodle/CLAUDE.md: GitHub is a log of what scripts did to the live site).
// Idempotent: exits if the box is already present.
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$CHAPTER_ID = 7;
$HEADING    = '<h4>Στόχοι μάθησης</h4>';
$SENTINEL   = '<!-- build-videos:v1 -->';

$content = $DB->get_field('book_chapters', 'content', ['id' => $CHAPTER_ID], MUST_EXIST);

if (strpos($content, $HEADING) === false) {
    fwrite(STDERR, "chapter {$CHAPTER_ID}: heading anchor not found - aborting, no change\n");
    exit(1);
}
if (strpos($content, $SENTINEL) !== false) {
    echo "chapter {$CHAPTER_ID}: build-videos box already present, nothing to do\n";
    exit(0);
}

function yt_search_li(string $label, string $query, string $note): string {
    $url = 'https://www.youtube.com/results?search_query=' . rawurlencode($query);
    return '<li style="margin:.5em 0"><strong>' . $label . '</strong><br>'
        . '<a href="' . s($url) . '" target="_blank" rel="noopener">🔎 ' . s($query) . '</a> ' . $note . '</li>';
}

$box = $SENTINEL
  . '<div style="border:2px solid #2f855a;background:#e6fffa;padding:1em;margin:1em 0">'
  . '<h4>🔧 Βίντεο συναρμολόγησης drone</h4>'
  . '<p>Δείτε ένα πλήρες build πριν ξεκινήσουμε. <em>Προσωρινά</em> αυτοί είναι σύνδεσμοι '
  . '<strong>αναζήτησης</strong> στο YouTube — σύντομα θα μπουν συγκεκριμένα βίντεο. Ανοίγουν '
  . 'στο YouTube (εφαρμογή ή browser).</p>'
  . '<ul>'
  . yt_search_li('Το ακριβές μας stack (FC + ESC):', 'SpeedyBee F405 V5 stack installation',
                 '(καλωδίωση μοτέρ, XT60, capacitor)')
  . yt_search_li('Το πλαίσιό μας:', 'GEPRC Mark4 5 inch assembly',
                 '(σειρά βιδώματος, standoffs, μοτέρ)')
  . yt_search_li('Πλήρης οδηγός 5":', 'Joshua Bardwell how to build a 5 inch FPV drone',
                 '(το standard reference — κόλληση, Betaflight, first flight)')
  . yt_search_li('Ρύθμιση Betaflight:', 'SpeedyBee F405 V5 Betaflight setup ELRS',
                 '(ports, modes, failsafe, bind)')
  . '</ul></div>';

$new = str_replace($HEADING, $box . $HEADING, $content);

$DB->update_record('book_chapters', (object)[
    'id'           => $CHAPTER_ID,
    'content'      => $new,
    'timemodified' => time(),
]);

echo "chapter {$CHAPTER_ID}: build-videos box added, old_len=" . strlen($content)
   . " new_len=" . strlen($new) . "\n";
