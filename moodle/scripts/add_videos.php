<?php
// One-off: prepend soldering tutorial video links to the Κεφ.14β book chapter.
// Kept for the record (moodle/CLAUDE.md: GitHub is a log of what scripts did to the
// live site) — not meant to be re-run as-is against a different course/book id.
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');

$ch = $DB->get_record('book_chapters', ['id' => 7], '*', MUST_EXIST);

$videobox = '<div style="border:2px solid #2b6cb0;background:#ebf8ff;padding:1em;margin:1em 0">'
  . '<h4>🎥 Εκπαιδευτικά βίντεο κόλλησης</h4>'
  . '<p>Επειδή αυτό είναι πρακτική δεξιότητα, δείτε πρώτα ένα βίντεο πριν αγγίξετε το iron. '
  . 'Αγγλόφωνα, αλλά η κόλληση φαίνεται καθαρά χωρίς να χρειάζεται ο ήχος.</p>'
  . '<ul>'
  . '<li><strong>Αν κολλάς για πρώτη φορά ποτέ:</strong> '
  . '<a href="https://www.youtube.com/watch?v=MWrJGEcMHQI" target="_blank">How to Solder Electronics, Wires, and Plugs — Complete Beginners Guide</a> '
  . '(γενικά βασικά, ~10 λεπτά)</li>'
  . '<li><strong>Ειδικά για FPV/drone κολλήσεις:</strong> '
  . '<a href="https://www.youtube.com/watch?v=GoPT69y98pY" target="_blank">Most FPV pilots need to watch this soldering tutorial</a> '
  . '(motor wires, XT60, FC pads — ακριβώς ό,τι θα κάνουμε)</li>'
  . '</ul></div>';

$ch->content = $videobox . $ch->content;
$ch->timemodified = time();
$DB->update_record('book_chapters', $ch);
echo "updated chapter {$ch->id}, new_len=" . strlen($ch->content) . "\n";
