<?php
// Sets weekly section summaries linking theory + Moodle task + build progress.
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->dirroot . '/course/lib.php');

$courseid = 2;
$bookcmid = (int)$argv[1];   // cmid of the theory Book

$b = "$CFG->wwwroot/mod/book/view.php?id=$bookcmid";

$weeks = [
 1 => ['Εισαγωγή, ασφάλεια & απογραφή υλικού',
   "Κεφ.1 — Ο κόσμος των drones· γιατί χτίζουμε drone για τους ναυαγοσώστες",
   "Ανάγνωση Κεφ.1 · <strong>Υποχρεωτική ψηφιακή υπογραφή</strong> κανόνων εργαστηρίου κόλλησης",
   "Καμία κόλληση ακόμη — απογραφή υλικού (Mark4 frame, SpeedyBee F405 V5, T-Motor Velox, RUSH Tank+Ratel2, ELRS EP1, GPS M10Q)",
   "Όλες οι 9 ομάδες έχουν υπογράψει τους κανόνες ασφαλείας"],
 2 => ['Θεωρία & πρακτική κόλλησης',
   "Κεφ.14β.1, 14β.3-4 — τι είναι το soldering, θερμοκρασίες, καλή vs κακή κόλληση",
   "Quiz «Αναγνώριση καλής/κακής κόλλησης» (8 ερωτήσεις) · Υποβολή φωτογραφίας δικής σου κόλλησης",
   "🔧 <strong>ΟΛΟΙ</strong>: ατομική άσκηση «Solder τη λαμπάκι» (LED+αντίσταση) — <em>όχι</em> στο drone ακόμη",
   "Όλες οι ομάδες έχουν κάνει τουλάχιστον 1 πρακτική κόλληση"],
 3 => ['Frame & τοποθέτηση κινητήρων',
   "Κεφ.4 — Ανατομία drone (FC, ESC, κινητήρες, δέκτης)",
   "Quiz Κεφ.4 · Φωτογραφία προόδου build",
   "🔧 <strong>HANDS-ON: Α3-Ομάδα 2</strong> — frame assembly + τοποθέτηση 4 motors (14β στάδια 1-3)",
   "Frame συναρμολογημένο, 4 motors τοποθετημένα"],
 4 => ['Κόλληση κινητήρων στο ESC',
   "Κεφ.14β.5 — βήμα-βήμα κόλληση καλωδίου σε pad· πίνακας wire gauge",
   "Φωτο κολλήσεων + self-check λίστα (στιλπνή; δεν κουνιέται;)",
   "🔧 <strong>HANDS-ON: Α5-Ομάδα 2</strong> — κόλληση motor wires στο ESC (14β στάδιο 4)",
   "4 motors συνδεδεμένα στο ESC"],
 5 => ['FC, καλωδίωση & battery pad',
   "Κεφ.5 — Μπαταρίες LiPo: χημεία, C-rating, κίνδυνοι",
   "Quiz LiPo · <strong>Υποχρεωτική υπογραφή</strong> κανόνων ασφαλείας μπαταριών",
   "🔧 <strong>HANDS-ON: Α4-Ομάδα 2</strong> — FC mounting + FC↔ESC wiring + battery pad/capacitor (14β στάδια 5-7)",
   "FC τοποθετημένος, battery pad + capacitor έτοιμα"],
 6 => ['Δέκτης, κάμερα/VTX & GPS',
   "Κεφ.8 — Ραδιοεπικοινωνία, ELRS, διαδικασία bind· τι κάνει το GPS Rescue",
   "Φύλλο ελέγχου καλωδίωσης · Quiz Κεφ.8",
   "🔧 <strong>HANDS-ON: Α3-Ομάδα 1</strong> — receiver + camera/VTX + LEDs + GPS M10Q (14β στάδια 8-10)<br><span style='color:#b00'>⚠ 28/10 αργία — Α2-Ομ2 &amp; Α5-Ομ1 χάνουν το μάθημα, αναπλήρωση μέσω Moodle</span>",
   "Όλα τα ηλεκτρονικά συνδεδεμένα"],
 7 => ['⭐⭐ Έλεγχοι πολυμέτρου & πρώτο power-on',
   "Κεφ.14β.9 — οι 8 έλεγχοι με πολύμετρο· 14β.10 — smoke stopper",
   "<strong>Υπογεγραμμένο checklist 8 σημείων</strong> ανά ομάδα (υποχρεωτικό πριν το power-on)",
   "🔧 <strong>HANDS-ON: Α3-Ομ1 (tidy/bind) &amp; Α2-Ομ1 (έλεγχοι + πρώτο power-on με smoke stopper)</strong>",
   "⭐⭐ ΚΡΙΣΙΜΟ: πρώτο ασφαλές power-on επιτυχές"],
 8 => ['Betaflight, GPS Rescue & πρώτη πτήση',
   "Κεφ.14β.12-14 — Betaflight setup, ρύθμιση GPS Rescue, τοποθέτηση/ζυγοστάθμιση ελίκων",
   "Ανάρτηση βίντεο πρώτης πτήσης (hover, χωρίς payload)",
   "🔧 <strong>HANDS-ON: Α4-Ομάδα 1</strong> — πλήρες Betaflight config + GPS Rescue + props + πρώτη δοκιμαστική πτήση",
   "Πρώτη επιτυχής πτήση"],
 9 => ['Μηχανισμός ρίψης & πτήση με σωσίβιο',
   "Πώς λειτουργεί ο servo-release· επίδραση του βάρους payload στην ώθηση",
   "Βίντεο δοκιμής ρίψης · Αναστοχασμός: «τι θα αλλάζαμε στο build μας;»",
   "🔧 <strong>HANDS-ON: Α2-Ομ2 (τοποθέτηση servo/μηχανισμού) &amp; Α1-Ομ1 (πτήση με payload + ρίψη)</strong>",
   "Επιτυχημένη ρίψη payload εν πτήσει"],
 10 => ['Τελική πρόβα & προετοιμασία συνεδρίου',
   "Κεφ.16 — Το μέλλον: AI, precision agriculture, <strong>έρευνα &amp; διάσωση (SAR)</strong>",
   "Υλικό παρουσίασης · Checklist συνεδρίου · Τελικό quiz",
   "🔧 <strong>ΟΛΟΙ</strong>: πρόβα παρουσίασης/πτήσης, επιλογή αντιπροσωπείας, τελικός έλεγχος αξιοπιστίας",
   "Έτοιμοι για το συνέδριο"],
 11 => ['🎤 ΣΥΝΕΔΡΙΟ ΝΑΥΑΓΟΣΩΣΤΩΝ — Παρουσίαση & Δωρεά',
   "—",
   "Ανάρτηση φωτογραφιών/βίντεο από την εκδήλωση",
   "🎤 Παρουσίαση, επίδειξη πτήσης &amp; ρίψης σωσιβίου, <strong>δωρεά του drone</strong>",
   "🎉 Η ΔΩΡΕΑ ΟΛΟΚΛΗΡΩΘΗΚΕ"],
];

foreach ($weeks as $num => [$title, $theory, $moodle, $build, $milestone]) {
    $sec = $DB->get_record('course_sections', ['course' => $courseid, 'section' => $num], '*', MUST_EXIST);
    $sec->name = "Εβδομάδα $num — $title";
    $sec->summary =
        "<table border='1' cellpadding='6' style='border-collapse:collapse;width:100%'>"
      . "<tr><td style='width:22%'><strong>📖 Θεωρία</strong></td><td>$theory"
      . ($theory !== '—' ? " <a href='$b'>(άνοιγμα βιβλίου)</a>" : "") . "</td></tr>"
      . "<tr><td><strong>💻 Moodle</strong></td><td>$moodle</td></tr>"
      . "<tr><td><strong>🔧 Συναρμολόγηση</strong></td><td>$build</td></tr>"
      . "<tr><td><strong>🎯 Milestone</strong></td><td><em>$milestone</em></td></tr>"
      . "</table>";
    $sec->summaryformat = FORMAT_HTML;
    $DB->update_record('course_sections', $sec);
    echo "week $num set\n";
}
rebuild_course_cache($courseid, true);
echo "done\n";
