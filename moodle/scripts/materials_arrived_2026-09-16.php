<?php
// Flip the microSD row from "on the way" to "have it" — confirmed arrived 2026-09-16
// (photo showed a SanDisk Ultra 32GB microSD among today's delivery). Other 🚚 rows on
// this page (backup FC+ESC stack, 2 soldering stations, LED/resistor/wire/AA-holder Wk2
// kit) were NOT in today's photos, so they stay 🚚 — see chat log 2026-09-16 for the full
// item-by-item cross-check against ORDER-LIST.md §G.
define('CLI_SCRIPT', true);
require('/var/www/html/public/config.php');

$page = $DB->get_record('page', ['id' => 5]);
$old = '<td style="padding:.4em .6em">💾 Κάρτα microSD (καταγραφή πτήσεων στα goggles)</td><td style="padding:.4em .6em;text-align:right;font-size:1.2em">🚚</td>';
$new = '<td style="padding:.4em .6em">💾 Κάρτα microSD (καταγραφή πτήσεων στα goggles)</td><td style="padding:.4em .6em;text-align:right;font-size:1.2em">✅</td>';

if (strpos($page->content, $old) === false) {
    echo "MICROSD ROW NOT FOUND AS EXPECTED - aborting, no change made.\n";
    exit(1);
}
$newcontent = str_replace($old, $new, $page->content);

// Also bump the "last updated" note.
$newcontent = str_replace(
    'Ενημέρωση: 4 Σεπ 2026.',
    'Ενημέρωση: 16 Σεπ 2026.',
    $newcontent
);

$DB->set_field('page', 'content', $newcontent, ['id' => 5]);
echo "Updated page id=5: microSD -> arrived, date note bumped.\n";
