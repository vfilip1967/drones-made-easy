<?php
// Second delivery batch, same day (2026-09-16): backup FC+ESC stack, 24AWG hookup wire,
// AA battery holders x30, loose 220R resistors, heat-shrink kit (w/ gun), digital multimeter
// all confirmed by photo. Also adds a "Εβδ." (week) column to the drone-parts and
// soldering-equipment tables per the teacher's request, so students can see which week each
// item is actually needed for. See chat log 2026-09-16 for the full cross-check.
define('CLI_SCRIPT', true);
require('/var/www/html/public/config.php');

$page = $DB->get_record('page', ['id' => 5]);

$old_drone_table = <<<'HTML'
<h4>Το drone</h4>
<table style="width:100%;border-collapse:collapse;margin-bottom:1.2em">
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🛠️ Πλαίσιο Mark4 5&quot;</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🔌 Κύριο FC+ESC stack (SpeedyBee F405 V5)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🔌 Εφεδρικό FC+ESC stack (για εξάσκηση κόλλησης)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">🚚</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">⚙️ Κινητήρες Velox 2207 KV1750 ×4</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🌀 Έλικες 5&quot; (6 σετ)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">📷 Κάμερα Caddx Ratel 2</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">📡 Πομπός εικόνας VTX (RUSH Tank Solo)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">📶 Δέκτης χειριστηρίου (HappyModel EP1)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🛰️ GPS (Matek M10Q)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🔋 Μπαταρίες πτήσης LiPo 6S ×2</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">⚡ Φορτιστής iMAX B6</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🥽 Γυαλιά FPV (Eachine EV800DM)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em">🪶 Σέρβο μηχανισμού ρίψης</td><td style="padding:.4em .6em;text-align:right;font-size:1.2em">✅</td></tr>
</table>
HTML;

$new_drone_table = <<<'HTML'
<h4>Το drone</h4>
<table style="width:100%;border-collapse:collapse;margin-bottom:1.2em">
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🛠️ Πλαίσιο Mark4 5&quot;</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 3</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🔌 Κύριο FC+ESC stack (SpeedyBee F405 V5)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 4-5</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🔌 Εφεδρικό FC+ESC stack (για εξάσκηση κόλλησης)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 2</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">⚙️ Κινητήρες Velox 2207 KV1750 ×4</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 3</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🌀 Έλικες 5&quot; (6 σετ)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 3</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">📷 Κάμερα Caddx Ratel 2</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 6</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">📡 Πομπός εικόνας VTX (RUSH Tank Solo)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 6</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">📶 Δέκτης χειριστηρίου (HappyModel EP1)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 6</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🛰️ GPS (Matek M10Q)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 6</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🔋 Μπαταρίες πτήσης LiPo 6S ×2</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 7-8</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">⚡ Φορτιστής iMAX B6</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 7</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🥽 Γυαλιά FPV (Eachine EV800DM)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 8</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em">🪶 Σέρβο μηχανισμού ρίψης</td><td style="padding:.4em .6em;text-align:center;font-size:.85em;color:#666">Εβδ. 9</td><td style="padding:.4em .6em;text-align:right;font-size:1.2em">✅</td></tr>
</table>
HTML;

$old_solder_table = <<<'HTML'
<h4>Εξοπλισμός κόλλησης</h4>
<table style="width:100%;border-collapse:collapse;margin-bottom:1.2em">
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🔥 2 σταθμοί κόλλησης ρυθμιζόμενης θερμοκρασίας</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">🚚</td></tr>
<tr><td style="padding:.4em .6em">💾 Κάρτα microSD (καταγραφή πτήσεων στα goggles)</td><td style="padding:.4em .6em;text-align:right;font-size:1.2em">✅</td></tr>
</table>
HTML;

$new_solder_table = <<<'HTML'
<h4>Εξοπλισμός κόλλησης</h4>
<table style="width:100%;border-collapse:collapse;margin-bottom:1.2em">
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🔥 2 σταθμοί κόλλησης ρυθμιζόμενης θερμοκρασίας</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 2</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">🚚</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🌬️ Θερμό πιστολάκι (heat gun, για heat-shrink)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 2</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">📟 Πολύμετρο (2ο, ένα ανά πάγκο)</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:center;font-size:.85em;color:#666">Εβδ. 7</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em">💾 Κάρτα microSD (καταγραφή πτήσεων στα goggles)</td><td style="padding:.4em .6em;text-align:center;font-size:.85em;color:#666">Εβδ. 8</td><td style="padding:.4em .6em;text-align:right;font-size:1.2em">✅</td></tr>
</table>
HTML;

$old_w2_table = <<<'HTML'
<h4>Υλικά άσκησης «Solder τη λαμπάκι» (Εβδ. 2)</h4>
<table style="width:100%;border-collapse:collapse;margin-bottom:.6em">
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">💡 LED 5mm ×200</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">🚚</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🎚️ Αντιστάσεις 220Ω ×200</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">🚚</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🧵 Καλώδιο σύνδεσης 24AWG</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">🚚</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🔲 Θήκες μπαταρίας 2×AA ×30</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">🚚</td></tr>
<tr><td style="padding:.4em .6em">🔋 Μπαταρίες AA ×~100</td><td style="padding:.4em .6em;text-align:right;font-size:1.2em">🚚</td></tr>
</table>
HTML;

$new_w2_table = <<<'HTML'
<h4>Υλικά άσκησης «Solder τη λαμπάκι» (Εβδ. 2)</h4>
<table style="width:100%;border-collapse:collapse;margin-bottom:.6em">
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">💡 LED 5mm ×200</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">🚚</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🎚️ Αντιστάσεις 220Ω ×200</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🧵 Καλώδιο σύνδεσης 24AWG</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em;border-bottom:1px solid #ddd">🔲 Θήκες μπαταρίας 2×AA ×30</td><td style="padding:.4em .6em;border-bottom:1px solid #ddd;text-align:right;font-size:1.2em">✅</td></tr>
<tr><td style="padding:.4em .6em">🔋 Μπαταρίες AA ×~100</td><td style="padding:.4em .6em;text-align:right;font-size:1.2em">🚚</td></tr>
</table>
HTML;

foreach ([[$old_drone_table, $new_drone_table], [$old_solder_table, $new_solder_table], [$old_w2_table, $new_w2_table]] as $pair) {
    [$old, $new] = $pair;
    if (strpos($page->content, $old) === false) {
        echo "BLOCK NOT FOUND, aborting before any partial write:\n---\n$old\n---\n";
        exit(1);
    }
}

$content = $page->content;
foreach ([[$old_drone_table, $new_drone_table], [$old_solder_table, $new_solder_table], [$old_w2_table, $new_w2_table]] as $pair) {
    [$old, $new] = $pair;
    $content = str_replace($old, $new, $content);
}

$DB->set_field('page', 'content', $content, ['id' => 5]);
echo "Updated page id=5: backup stack / hookup wire / AA holders / 220R resistors -> arrived,\n";
echo "heat gun + multimeter rows added, week (Εβδ.) column added to drone + soldering tables.\n";
