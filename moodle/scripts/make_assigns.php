<?php
define('CLI_SCRIPT', true);
require('/var/www/html/config.php');
require_once($CFG->dirroot . '/course/modlib.php');

$course = $DB->get_record('course', ['id' => 2], '*', MUST_EXIST);
$module = $DB->get_record('modules', ['name' => 'assign'], '*', MUST_EXIST);

// section => [name, intro, type]  type: 'text' (signature) or 'file' (photo/video/doc)
$defs = [
 1  => ['Υπογραφή Κανόνων Ασφαλείας Εργαστηρίου',
        '<p>Πληκτρολόγησε το ονοματεπώνυμό σου και τη σημερινή ημερομηνία, ως αποδοχή των κανόνων ασφαλείας εργαστηρίου κόλλησης.</p>', 'text'],
 2  => ['Φωτογραφία άσκησης κόλλησης (Solder τη λαμπάκι)',
        '<p>Ανέβασε 1 κοντινή φωτογραφία της κόλλησής σου στο κύκλωμα LED.</p>', 'file'],
 3  => ['Φωτογραφία προόδου build — Frame &amp; Motors',
        '<p>Φωτογραφία του drone μετά την τοποθέτηση frame και κινητήρων.</p>', 'file'],
 4  => ['Φωτο κολλήσεων Motor→ESC + self-check',
        '<p>Φωτογραφία των κολλήσεων motor→ESC. Συμπλήρωσε επίσης τη λίστα self-check στο κείμενο της υποβολής.</p>', 'file'],
 5  => ['Υπογραφή Κανόνων Ασφαλείας Μπαταριών LiPo',
        '<p>Πληκτρολόγησε το ονοματεπώνυμό σου και τη σημερινή ημερομηνία, ως αποδοχή των κανόνων ασφαλείας μπαταριών LiPo.</p>', 'text'],
 6  => ['Φύλλο ελέγχου καλωδίωσης',
        '<p>Ανέβασε φωτογραφία/σάρωση του συμπληρωμένου φύλλου ελέγχου καλωδίωσης (receiver, camera/VTX, LEDs, GPS).</p>', 'file'],
 7  => ['Υπογεγραμμένο Checklist 8 σημείων (έλεγχος πολυμέτρου)',
        '<p><strong>Υποχρεωτικό πριν το πρώτο power-on.</strong> Πληκτρολόγησε τα αποτελέσματα και των 8 ελέγχων πολυμέτρου (14β.9) και υπόγραψε.</p>', 'text'],
 8  => ['Βίντεο πρώτης πτήσης',
        '<p>Ανέβασε βίντεο της πρώτης δοκιμαστικής πτήσης (hover, χωρίς payload).</p>', 'file'],
 9  => ['Βίντεο δοκιμής ρίψης payload',
        '<p>Ανέβασε βίντεο της δοκιμής ρίψης σωσιβίου εν πτήσει.</p>', 'file'],
 10 => ['Υλικό παρουσίασης &amp; Checklist συνεδρίου',
        '<p>Ανέβασε τις διαφάνειες/υλικό παρουσίασης και το συμπληρωμένο checklist ετοιμότητας για το συνέδριο.</p>', 'file'],
 11 => ['Φωτογραφίες/βίντεο από το συνέδριο',
        '<p>Ανέβασε φωτογραφίες ή βίντεο από την παρουσίαση, την επίδειξη πτήσης και τη δωρεά.</p>', 'file'],
];

foreach ($defs as $section => [$name, $intro, $type]) {
    $mi = new stdClass();
    $mi->modulename = 'assign';
    $mi->module = $module->id;
    $mi->course = 2;
    $mi->section = $section;
    $mi->visible = 1;
    $mi->name = $name;
    $mi->intro = $intro;
    $mi->introformat = FORMAT_HTML;
    $mi->alwaysshowdescription = 1;
    $mi->duedate = 0;
    $mi->allowsubmissionsfromdate = 0;
    $mi->cutoffdate = 0;
    $mi->gradingduedate = 0;
    $mi->grade = 0;              // ungraded — this is a compliance/progress submission, not a marked assignment
    $mi->maxattempts = -1;
    $mi->markingworkflow = 0;
    $mi->markingallocation = 0;
    $mi->completionsubmit = 1;
    $mi->submissiondrafts = 0;
    $mi->requiresubmissionstatement = 0;
    $mi->sendnotifications = 0;
    $mi->sendlatenotifications = 0;
    $mi->sendstudentnotifications = 1;
    $mi->teamsubmission = 0;
    $mi->requireallteammemberssubmit = 0;
    $mi->blindmarking = 0;
    $mi->attemptreopenmethod = 'untilpass';
    $mi->gradepenalty = 0;
    $mi->markercount = 0;

    if ($type === 'text') {
        $mi->assignsubmission_onlinetext_enabled = 1;
        $mi->assignsubmission_file_enabled = 0;
    } else {
        $mi->assignsubmission_file_enabled = 1;
        $mi->assignsubmission_file_maxfiles = 3;
        $mi->assignsubmission_file_maxsizebytes = 20971520; // 20MB, enough for a short phone video
        $mi->assignsubmission_onlinetext_enabled = 0;
    }

    $mi = add_moduleinfo($mi, $course);
    echo "W{$section} assign cmid={$mi->coursemodule} ({$type}) — {$name}\n";
}
