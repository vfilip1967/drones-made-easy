<?php
// Third delivery confirmation, same day (2026-09-16): loose 5mm LEDs x200 and the 2
// temperature-controlled soldering stations, photo-identified by the teacher. This clears
// the single blocking item for Week 2 soldering practice.
define('CLI_SCRIPT', true);
require('/var/www/html/public/config.php');

$page = $DB->get_record('page', ['id' => 5]);

$replacements = [
    '<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🔥 2 σταθμοί κόλλησης ρυθμιζόμενης θερμοκρασίας</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 2</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">🚚</td></tr>'
        => '<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🔥 2 σταθμοί κόλλησης ρυθμιζόμενης θερμοκρασίας</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 2</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>',
    '<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">💡 LED 5mm ×200</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">🚚</td></tr>'
        => '<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">💡 LED 5mm ×200</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>',
];

$content = $page->content;
foreach ($replacements as $old => $new) {
    if (strpos($content, $old) === false) {
        echo "NOT FOUND, aborting:\n$old\n";
        exit(1);
    }
    $content = str_replace($old, $new, $content);
}

$DB->set_field('page', 'content', $content, ['id' => 5]);
echo "Updated page id=5: soldering stations + LEDs -> arrived.\n";
