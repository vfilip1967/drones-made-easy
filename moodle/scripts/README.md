# Moodle content scripts

Scripts για την αυτόματη δημιουργία περιεχομένου στο `sch.filipakis.com`.
Τρέχουν **μέσα στο container** ως `www-data`.

## Σημαντικά ευρήματα (Moodle 5.x)

- Οι τράπεζες ερωτήσεων ΔΕΝ ζουν πλέον σε course context. Είναι ξεχωριστό module
  (`mod_qbank`) με δικό του **module context** — γι' αυτό το `import_gift.php` παίρνει
  το cmid του qbank, όχι το courseid μόνο.
- Το `add_moduleinfo()` απαιτεί να έχει γίνει ήδη `require_once` το `lib/questionlib.php`,
  αλλιώς πετάει «Call to undefined function question_get_default_category()» και αφήνει
  ανοιχτό transaction.
- Το `mod_quiz` θέλει ρητά `password`, `subnet`, `overduehandling` κ.λπ. — τα defaults
  του schema δεν αρκούν.
- Το `mod_assign` θέλει ακόμα περισσότερα ρητά πεδία: `submissiondrafts`,
  `requiresubmissionstatement`, `teamsubmission` κ.λπ. — δες `make_assigns.php`.
- **`mod_page` bug που προκάλεσε πραγματικό κενό περιεχόμενο (29/7/2026):**
  `page_add_instance()` αντιγράφει το `$data->page['text']` στο `$data->content` ΜΟΝΟ όταν
  καλείται με μη-null `$mform`. Το `add_moduleinfo()` περνάει πάντα `$mform = null` από CLI
  κώδικα, οπότε αν στήσεις `$mi->page = ['text'=>...]` το Page βγαίνει **εντελώς άδειο,
  χωρίς κανένα error**. Σωστός τρόπος: όρισε απευθείας `$mi->content` / `$mi->contentformat`
  (βλέπε `make_page.php`). Αν κάποιο Page που έφτιαξες φαίνεται κενό, αυτό είναι το πρώτο
  πράγμα να ελέγξεις.

## Χρήση

```bash
# 1. Παραγωγή JSON spec από το βιβλίο (τρέχει στον host)
python3 build_spec.py '{"name":"...","intro":"...","chapters":["1","4","5","8","14β","16"]}' out.json

# 2. Δημιουργία Book activity με τα κεφάλαια
docker cp make_book.php moodle-app:/tmp/ && docker cp out.json moodle-app:/tmp/
docker exec -u www-data moodle-app php /tmp/make_book.php <courseid> <section> /tmp/out.json

# 3. Import GIFT ερωτήσεων (χρειάζεται cmid ενός mod_qbank)
docker exec -u www-data moodle-app php /tmp/import_gift.php <courseid> /tmp/x.gift "<κατηγορία>" <qbank_cmid>

# 4. Εβδομαδιαίες ενότητες με θεωρία/Moodle/build/milestone
docker exec -u www-data moodle-app php /tmp/weeks.php <book_cmid>
```

Μετά από κάθε αλλαγή: `docker exec -u www-data moodle-app php /var/www/html/admin/cli/purge_caches.php`
