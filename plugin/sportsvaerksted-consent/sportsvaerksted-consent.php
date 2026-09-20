<?php
/**
 * Plugin Name: Sportsvaerksted Consent form DK EN
 * Description: Samtykkeerklæring til brug af film og billeder, dansk og engelsk. Sæt kortkoden [consent] ind på en side. PDF'en sendes med e-mail og gemmes ikke på serveren.
 * Version:     1.2.2
 * Author:      Anirudha Talmale
 * Text Domain: samtykke-consent
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SAMTYKKE_VERSION', '1.2.2');
define('SAMTYKKE_DIR', plugin_dir_path(__FILE__));
define('SAMTYKKE_URL', plugin_dir_url(__FILE__));
define('SAMTYKKE_OPTION', 'samtykke_settings');

/**
 * The wording, and everything the clinic might want to change.
 *
 * Every one of these is editable under Indstillinger > Samtykke, so nobody has
 * to open a file to change an address or a sentence.
 */
function samtykke_defaults() {
    return array(
        'to_email'       => get_option('admin_email'),
        'copy_to_signer' => 1,
        // His site's gold. Kept as a setting so the next change of mind does
        // not need a new release.
        'label_color'    => '#d1af86',
        // Everything that is not a field label: the checkbox wording, the
        // sign-field text, the small print. Grey on his near-black page was
        // the complaint.
        'text_color'     => '#ffffff',
        'saved_at'       => '',

        // Sending through the host's own SMTP server is what makes the mail
        // pass SPF and DMARC. sportsvaerksted.com publishes
        // "v=spf1 include:spf.simply.com -all" and "p=reject", so anything
        // PHP posts straight from the web server is refused outright by
        // Yahoo and the like.
        'smtp_on'   => 0,
        'smtp_host' => 'websmtp.simply.com',
        'smtp_port' => '587',
        'smtp_secure' => 'tls',
        'smtp_user' => '',
        'smtp_pass' => '',
        'from_name' => 'Sportsvaerksted',

        'da_title'   => 'Samtykke til brug af film og billeder',
        'da_who'     => 'Dataansvarlig: Sportsværkstedet, Domhusgade 13, 1. sal, 6000 Kolding  ·  skriv@sportsvaerkstedet.dk',
        'da_intro'   => 'Udfyld, sæt kryds, og skriv under nederst.',
        'da_terms'   => "Jeg giver Sportsværkstedet tilladelse til at optage video og tage billeder af mig i forbindelse med min behandling.\n"
                      . "Jeg er indforstået med, at optagelserne kan vise min krop, mine skader og selve behandlingen, og at jeg kan være genkendelig.\n"
                      . "Samtykket er frivilligt. Jeg kan til enhver tid trække det tilbage ved at skrive til Sportsværkstedet. Herefter fjerner Sportsværkstedet materialet fra egne kanaler hurtigst muligt. Materiale, som andre allerede har delt eller hentet ned, kan ikke altid fjernes - det er jeg oplyst om.\n"
                      . "Jeg får en kopi af denne erklæring. Sportsværkstedet opbevarer den, så længe materialet er i brug, og i op til to år derefter.\n"
                      . "Jeg kan bede om indsigt i, rettelse af eller sletning af mine oplysninger, og jeg kan klage til Datatilsynet.",
        'da_use'     => 'Sportsværkstedet må bruge materialet på enhver måde, i ethvert medie og i ethvert land, så længe det har direkte forbindelse til Sportsværkstedet.',
        'da_name_ok' => 'Mit fornavn må nævnes i forbindelse med materialet.',

        'en_title'   => 'Consent to the use of film and photographs',
        'en_who'     => 'Data controller: Sportsvaerkstedet, Domhusgade 13, 1st floor, 6000 Kolding, Denmark  ·  skriv@sportsvaerkstedet.dk',
        'en_intro'   => 'Fill this in, tick the boxes, and sign at the bottom.',
        'en_terms'   => "I give Sportsvaerkstedet permission to film and photograph me during my treatment.\n"
                      . "I understand that the material may show my body, my injuries and the treatment itself, and that I may be recognisable.\n"
                      . "This consent is voluntary. I may withdraw it at any time by writing to Sportsvaerkstedet, who will then remove the material from their own channels as soon as possible. Material that others have already shared or downloaded cannot always be removed - I have been told this.\n"
                      . "I receive a copy of this declaration. Sportsvaerkstedet keeps it for as long as the material is in use, and for up to two years afterwards.\n"
                      . "I may ask to see, correct or delete my information, and I may complain to the Danish Data Protection Agency.",
        'en_use'     => 'Sportsvaerkstedet may use the material in any way, in any medium and in any country, as long as it is directly connected to Sportsvaerkstedet.',
        'en_name_ok' => 'My first name may be mentioned in connection with the material.',
    );
}

function samtykke_get($key) {
    $saved = get_option(SAMTYKKE_OPTION, array());
    $defaults = samtykke_defaults();

    // A saved EMPTY field is a decision, not a missing value. The old version
    // treated '' as "nothing saved" and handed back the built-in text, so
    // clearing a sentence made my wording reappear - which is exactly what
    // "it shows the pre-programmed version" meant.
    if (array_key_exists($key, $saved)) {
        return $saved[$key];
    }

    return isset($defaults[$key]) ? $defaults[$key] : '';
}

/**
 * Fixed labels. Not in the settings screen on purpose - a clinic wants to edit
 * the legal wording, not the word "Telefon", and twenty more boxes would make
 * the settings page unusable.
 */
function samtykke_strings() {
    return array(
        'da' => array(
            'hUses' => 'Tilladelse',
            'usesHelp' => 'Sæt kryds ved det, du siger ja til.',
            'hYou' => 'Dig',
            'name' => 'Navn', 'birth' => 'Fødselsdato', 'email' => 'E-mail',
            'phone' => 'Telefon (valgfrit)', 'today' => 'Dato for underskrift',
            'hGuardian' => 'Forælder eller værge',
            'guardianWhy' => 'Udfyldes kun, hvis den, der er filmet, er under 18 år. Forælder eller værge skriver under sammen med den unge.',
            'gname' => 'Forælder/værges navn', 'grel' => 'Relation',
            'hSign' => 'Underskrift',
            'signHelp' => 'Skriv din underskrift med fingeren eller musen i feltet herunder.',
            'padHint' => 'Underskriv her',
            'gpadHint' => 'Forælder/værge underskriver her',
            'clear' => 'Slet og prøv igen',
            'agree' => 'Jeg har læst og forstået ovenstående, og jeg giver mit samtykke.',
            'send' => 'Underskriv og send',
            'sending' => 'Sender ...',
            'errName' => 'Skriv venligst dit navn.',
            'errToday' => 'Skriv venligst datoen.',
            'errUse' => 'Sæt kryds i feltet om brug af materialet.',
            'errSign' => 'Der mangler en underskrift.',
            'errAgree' => 'Sæt kryds i feltet om samtykke.',
            'errGuardianSign' => 'Der mangler en underskrift fra forælder eller værge.',
            'errEmailCopy' => 'Skriv din e-mail, så du kan få din egen kopi.',
            'errSend' => 'Erklæringen kunne ikke sendes. Prøv igen, eller kontakt Sportsværkstedet.',
            'done' => 'Tak. Din erklæring er sendt til Sportsværkstedet, og du får en kopi på e-mail.',
            'doneNoCopy' => 'Tak. Din erklæring er sendt til Sportsværkstedet.',
            'yes' => 'JA', 'no' => 'NEJ',
            'pdfName' => 'Samtykke',
            'pdfSigned' => 'Underskrevet digitalt', 'pdfAt' => 'Tidspunkt',
            'pdfUses' => 'Samtykke givet til', 'pdfPerson' => 'Underskriver',
            'pdfGuardian' => 'Forælder/værge',
            'mailSubject' => 'Samtykke: %s',
            'mailBody' => "Ny samtykkeerklæring fra %s.\n\nErklæringen er vedhæftet som PDF.\n\nDen er ikke gemt på hjemmesiden - denne mail er kopien.",
            'mailBodySigner' => "Tak for dit samtykke.\n\nDin erklæring er vedhæftet som PDF. Gem den, så du altid kan se, hvad du har sagt ja til.\n\nVil du trække samtykket tilbage, så skriv til os.",
        ),
        'en' => array(
            'hUses' => 'Permission',
            'usesHelp' => 'Tick what you agree to.',
            'hYou' => 'About you',
            'name' => 'Name', 'birth' => 'Date of birth', 'email' => 'Email',
            'phone' => 'Phone (optional)', 'today' => 'Date signed',
            'hGuardian' => 'Parent or guardian',
            'guardianWhy' => 'Only needed if the person being filmed is under 18. A parent or guardian signs alongside them.',
            'gname' => 'Parent/guardian name', 'grel' => 'Relationship',
            'hSign' => 'Signature',
            'signHelp' => 'Sign with your finger or mouse in the box below.',
            'padHint' => 'Sign here',
            'gpadHint' => 'Parent/guardian signs here',
            'clear' => 'Clear and try again',
            'agree' => 'I have read and understood the above, and I give my consent.',
            'send' => 'Sign and send',
            'sending' => 'Sending ...',
            'errName' => 'Please enter your name.',
            'errToday' => 'Please enter today\'s date.',
            'errUse' => 'Please tick the box about use of the material.',
            'errSign' => 'The signature is missing.',
            'errAgree' => 'Please tick the consent box.',
            'errGuardianSign' => "The parent or guardian's signature is missing.",
            'errEmailCopy' => 'Please enter your email so you can receive your own copy.',
            'errSend' => 'The declaration could not be sent. Please try again, or contact Sportsvaerkstedet.',
            'done' => 'Thank you. Your declaration has been sent to Sportsvaerkstedet, and a copy is on its way to your email.',
            'doneNoCopy' => 'Thank you. Your declaration has been sent to Sportsvaerkstedet.',
            'yes' => 'YES', 'no' => 'NO',
            'pdfName' => 'Consent',
            'pdfSigned' => 'Signed digitally', 'pdfAt' => 'Time',
            'pdfUses' => 'Consent given for', 'pdfPerson' => 'Signed by',
            'pdfGuardian' => 'Parent/guardian',
            'mailSubject' => 'Consent: %s',
            'mailBody' => "New consent declaration from %s.\n\nThe declaration is attached as a PDF.\n\nIt is not stored on the website - this email is the copy.",
            'mailBodySigner' => "Thank you for your consent.\n\nYour declaration is attached as a PDF. Keep it, so you can always see what you agreed to.\n\nIf you want to withdraw your consent, please write to us.",
        ),
    );
}

function samtykke_texts() {
    $s = samtykke_strings();

    foreach (array('da', 'en') as $lang) {
        $s[$lang]['title'] = samtykke_get($lang . '_title');
        $s[$lang]['who']   = samtykke_get($lang . '_who');
        $s[$lang]['intro'] = samtykke_get($lang . '_intro');

        $terms = preg_split('/\r\n|\r|\n/', samtykke_get($lang . '_terms'));
        $s[$lang]['terms'] = array_values(array_filter(array_map('trim', $terms), 'strlen'));

        $s[$lang]['uses'] = array(
            array('all',  samtykke_get($lang . '_use')),
            array('name', samtykke_get($lang . '_name_ok')),
        );
    }

    return $s;
}

// ---------------------------------------------------------------- the form

function samtykke_shortcode($atts) {
    $atts = shortcode_atts(array('lang' => 'da'), $atts, 'samtykke');

    wp_enqueue_style('samtykke', SAMTYKKE_URL . 'assets/form.css', array(), SAMTYKKE_VERSION);
    wp_enqueue_script('jspdf', SAMTYKKE_URL . 'assets/jspdf.umd.min.js', array(), '2.5.2', true);
    wp_enqueue_script('samtykke', SAMTYKKE_URL . 'assets/form.js', array('jspdf'), SAMTYKKE_VERSION, true);

    wp_localize_script('samtykke', 'SAMTYKKE', array(
        'text'   => samtykke_texts(),
        'lang'   => ($atts['lang'] === 'en') ? 'en' : 'da',
        'ajax'   => admin_url('admin-ajax.php'),
        'nonce'  => wp_create_nonce('samtykke_send'),
        'copy'   => (int) samtykke_get('copy_to_signer'),
    ));

    ob_start();
    include SAMTYKKE_DIR . 'form.php';

    return ob_get_clean();
}
add_shortcode('consent', 'samtykke_shortcode');
// The old name still works. His live page already says [samtykke], and a
// rename that breaks the page it is on is not a rename, it is an outage.
add_shortcode('samtykke', 'samtykke_shortcode');

/**
 * Never let a page carrying this form be cached.
 *
 * The wording lives in the page's own HTML, so a stale copy shows yesterday's
 * text and looks exactly like a save that did not work. His Android browser
 * held one for days. Some responses from the host came back WITHOUT a
 * no-store header, which is all it takes.
 */
function samtykke_page_has_form() {
    if (!is_singular()) {
        return false;
    }

    $post = get_post();

    if (!$post instanceof WP_Post) {
        return false;
    }

    return has_shortcode($post->post_content, 'consent')
        || has_shortcode($post->post_content, 'samtykke');
}

/**
 * Tell caching plugins to leave this page alone.
 *
 * W3 Total Cache, WP Super Cache, LiteSpeed and the rest all look for these
 * constants. It reinstalled itself on his site during an automatic "clean up"
 * and served a stored copy of the page for days, so the form has to defend
 * itself rather than rely on anyone remembering to exclude it.
 *
 * Runs on 'wp', which is early enough that the cache has not decided yet.
 */
function samtykke_do_not_cache() {
    if (!samtykke_page_has_form()) {
        return;
    }

    foreach (array('DONOTCACHEPAGE', 'DONOTCACHEOBJECT', 'DONOTCACHEDB', 'DONOTMINIFY') as $flag) {
        if (!defined($flag)) {
            define($flag, true);
        }
    }
}
add_action('wp', 'samtykke_do_not_cache');

function samtykke_no_cache() {
    if (!samtykke_page_has_form()) {
        return;
    }

    nocache_headers();

    if (!headers_sent()) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0', true);
        header('Pragma: no-cache', true);
    }
}
add_action('template_redirect', 'samtykke_no_cache');

// ------------------------------------------------------------- the sending

/**
 * Route this plugin's mail through the host's SMTP server.
 *
 * Deliberately scoped to our own sending with a flag rather than hooked for
 * the whole site: taking over every email a site sends is not something a
 * consent form should do behind the owner's back.
 */
$GLOBALS['samtykke_sending'] = false;

function samtykke_phpmailer($phpmailer) {
    if (empty($GLOBALS['samtykke_sending']) || !samtykke_get('smtp_on')) {
        return;
    }

    $user = samtykke_get('smtp_user');
    $host = samtykke_get('smtp_host');

    if (!$user || !$host) {
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host = $host;
    $phpmailer->Port = (int) samtykke_get('smtp_port');
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = $user;
    $phpmailer->Password = samtykke_get('smtp_pass');

    $secure = samtykke_get('smtp_secure');

    if ($secure === 'ssl' || $secure === 'tls') {
        $phpmailer->SMTPSecure = $secure;
    } else {
        $phpmailer->SMTPSecure = '';
        $phpmailer->SMTPAutoTLS = false;
    }

    // DMARC aligns on the From domain, so the From address has to be the
    // mailbox we authenticated as - not WordPress's invented wordpress@domain.
    $phpmailer->setFrom($user, samtykke_get('from_name'), false);
}
add_action('phpmailer_init', 'samtykke_phpmailer');


/** Send as this plugin: turns the SMTP routing on for one call. */
function samtykke_send_mail($to, $subject, $body, $attachments = array()) {
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    $user = samtykke_get('smtp_user');

    if (samtykke_get('smtp_on') && $user) {
        $headers[] = sprintf('From: %s <%s>', samtykke_get('from_name'), $user);
    }

    $failure = '';
    $catch = function ($error) use (&$failure) {
        $failure = $error->get_error_message();
    };
    add_action('wp_mail_failed', $catch);

    $GLOBALS['samtykke_sending'] = true;
    $sent = wp_mail($to, $subject, $body, $headers, $attachments);
    $GLOBALS['samtykke_sending'] = false;

    remove_action('wp_mail_failed', $catch);

    return array($sent, $failure);
}


function samtykke_handle() {
    check_ajax_referer('samtykke_send', 'nonce');

    // A honeypot: a field no human ever sees, so anything that fills it in is
    // a robot. Answer politely and do nothing.
    if (!empty($_POST['website'])) {
        wp_send_json_success(array('sent' => true));
    }

    $lang = (isset($_POST['lang']) && $_POST['lang'] === 'en') ? 'en' : 'da';
    $strings = samtykke_strings();
    $s = $strings[$lang];

    $name  = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $pdf   = isset($_POST['pdf']) ? (string) wp_unslash($_POST['pdf']) : '';

    if ($name === '' || $pdf === '') {
        wp_send_json_error(array('message' => 'missing'), 400);
    }

    // Base64 of a one-page PDF is well under this. The cap is here so a bad
    // request cannot fill the server's memory or its mail queue.
    if (strlen($pdf) > 6 * 1024 * 1024) {
        wp_send_json_error(array('message' => 'too big'), 413);
    }

    // jsPDF returns "data:application/pdf;filename=generated.pdf;base64,...".
    // The filename part is easy to miss, and a prefix left in front of the
    // base64 makes strict decoding fail - so cut at the first comma and take
    // whatever follows, whatever the prefix happens to say.
    $comma = strpos($pdf, ',');

    if ($comma !== false) {
        $pdf = substr($pdf, $comma + 1);
    }

    $binary = base64_decode($pdf, true);

    if ($binary === false || substr($binary, 0, 5) !== '%PDF-') {
        wp_send_json_error(array('message' => 'not a pdf'), 400);
    }

    $safe = preg_replace('/[^\p{L}\p{N} _-]/u', '', $name);
    $safe = trim(preg_replace('/\s+/', '-', $safe));

    if ($safe === '') {
        $safe = 'x';
    }

    $file = trailingslashit(get_temp_dir()) . $s['pdfName'] . '-' . $safe . '-' . wp_generate_password(6, false) . '.pdf';

    if (file_put_contents($file, $binary) === false) {
        wp_send_json_error(array('message' => 'write failed'), 500);
    }

    $to = samtykke_get('to_email');
    $subject = sprintf($s['mailSubject'], $name);

    list($sent, $why) = samtykke_send_mail($to, $subject, sprintf($s['mailBody'], $name), array($file));

    $copied = false;

    if (samtykke_get('copy_to_signer') && is_email($email)) {
        list($copied, ) = samtykke_send_mail($email, $subject, $s['mailBodySigner'], array($file));
    }

    // Nothing is kept. The promise made on the form is that the website stores
    // no declarations, so the only copies are the two emails.
    @unlink($file);

    if (!$sent) {
        wp_send_json_error(array('message' => $why ? $why : 'mail failed'), 500);
    }

    wp_send_json_success(array('sent' => true, 'copied' => (bool) $copied));
}
add_action('wp_ajax_samtykke_send', 'samtykke_handle');
add_action('wp_ajax_nopriv_samtykke_send', 'samtykke_handle');

/** "Send a test" - the only way to know the mail really arrives. */
function samtykke_test_mail() {
    if (!current_user_can('manage_options')) {
        wp_die('nope');
    }

    check_admin_referer('samtykke_test');

    // Aimable on purpose. Sending a test to a mailbox on the same host never
    // leaves the building and arrives however broken the authentication is -
    // which is exactly how a real client's copy went missing while the
    // owner's arrived. The address that matters is one at Gmail.
    $to = isset($_POST['samtykke_test_to']) ? sanitize_email(wp_unslash($_POST['samtykke_test_to'])) : '';

    if (!is_email($to)) {
        $to = samtykke_get('to_email');
    }

    list($sent, $why) = samtykke_send_mail(
        $to,
        'Test fra samtykkeformularen',
        "Det her er en testmail fra samtykkeformularen på " . home_url() . ".\n\n"
        . "Kommer den frem, virker udsendelsen. Kommer den i spam, så marker den som 'ikke spam'."
    );

    $args = array(
        'page' => 'samtykke',
        'samtykke_test' => $sent ? 'ok' : 'fail',
        'samtykke_to' => rawurlencode($to),
    );

    if (!$sent && $why) {
        $args['samtykke_why'] = rawurlencode(substr($why, 0, 200));
    }

    wp_safe_redirect(add_query_arg($args, admin_url('options-general.php')));
    exit;
}
add_action('admin_post_samtykke_test', 'samtykke_test_mail');

/**
 * Is sending actually set up?
 *
 * Two sites, and only one of them got configured - the other kept posting
 * mail straight from the web server until Gmail refused it. Silence is no
 * good here: the form says "sent" to the person signing either way.
 */
function samtykke_mail_trouble() {
    if (!samtykke_get('smtp_on')) {
        return 'off';
    }

    if (!samtykke_get('smtp_user') || !samtykke_get('smtp_pass') || !samtykke_get('smtp_host')) {
        return 'half';
    }

    return '';
}


/** Say so on every admin screen, not only on ours - it is easy to miss. */
function samtykke_admin_warning() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $trouble = samtykke_mail_trouble();

    if (!$trouble) {
        return;
    }

    $where = admin_url('options-general.php?page=samtykke');

    $message = ($trouble === 'off')
        ? 'Samtykkeformularen sender mail direkte fra webserveren. Gmail, Yahoo og Outlook afviser eller spamfiltrerer den slags. Sæt SMTP op, så kommer erklæringerne frem.'
        : 'Samtykkeformularen er sat til at sende via SMTP, men der mangler oplysninger. Mailen sendes derfor stadig direkte fra webserveren.';

    printf(
        '<div class="notice notice-warning"><p><strong>Samtykke:</strong> %s <a href="%s">Ret det her</a>.</p></div>',
        esc_html($message),
        esc_url($where)
    );
}
add_action('admin_notices', 'samtykke_admin_warning');

// ------------------------------------------------------------- settings UI

function samtykke_settings_menu() {
    add_options_page('Consent form DK EN', 'Consent form', 'manage_options', 'samtykke', 'samtykke_settings_page');
}
add_action('admin_menu', 'samtykke_settings_menu');

function samtykke_settings_register() {
    register_setting('samtykke_group', SAMTYKKE_OPTION, array(
        'sanitize_callback' => 'samtykke_sanitize',
        'default'           => samtykke_defaults(),
    ));
}
add_action('admin_init', 'samtykke_settings_register');

/** The fields rendered as textareas - they must keep their line breaks. */
function samtykke_multiline_keys() {
    return array('da_terms', 'en_terms', 'da_use', 'en_use', 'da_name_ok', 'en_name_ok');
}


function samtykke_sanitize($input) {
    $out = array();
    $defaults = samtykke_defaults();

    // Since an empty field now stays empty, there has to be a road back.
    // The tick box promises "texts and colours", so it must NOT throw away his
    // email address with them - that would be a nasty surprise from a button
    // labelled as cosmetic.
    if (!empty($input['restore_defaults'])) {
        $saved = get_option(SAMTYKKE_OPTION, array());
        $keep = array('to_email', 'copy_to_signer', 'smtp_on', 'smtp_host', 'smtp_port',
                      'smtp_secure', 'smtp_user', 'smtp_pass', 'from_name');

        foreach ($keep as $key) {
            if (array_key_exists($key, $saved)) {
                $defaults[$key] = $saved[$key];
            }
        }

        $defaults['saved_at'] = current_time('d-m-Y H:i');

        return $defaults;
    }

    foreach ($defaults as $key => $default) {
        if ($key === 'to_email') {
            $out[$key] = sanitize_email(isset($input[$key]) ? $input[$key] : '');
            continue;
        }

        if ($key === 'copy_to_signer') {
            $out[$key] = empty($input[$key]) ? 0 : 1;
            continue;
        }

        if ($key === 'smtp_on') {
            $out[$key] = empty($input[$key]) ? 0 : 1;
            continue;
        }

        if ($key === 'smtp_pass') {
            // Kept as typed - a password may contain anything, and sanitising
            // it would silently break the login.
            $out[$key] = isset($input[$key]) ? (string) $input[$key] : '';
            continue;
        }

        if ($key === 'smtp_user') {
            $out[$key] = sanitize_email(isset($input[$key]) ? $input[$key] : '');
            continue;
        }

        if ($key === 'label_color' || $key === 'text_color') {
            $colour = sanitize_hex_color(isset($input[$key]) ? $input[$key] : '');
            $out[$key] = $colour ? $colour : $default;
            continue;
        }

        $value = isset($input[$key]) ? (string) $input[$key] : '';
        // Every field shown as a textarea keeps its line breaks. The tick
        // labels are textareas too, and sanitize_text_field was flattening a
        // deliberate line break into a space.
        if (in_array($key, samtykke_multiline_keys(), true)) {
            // Browsers post CRLF. A stray \r reaches jsPDF and can draw as a
            // box glyph in the finished PDF, so normalise it here, once.
            $out[$key] = str_replace("\r\n", "\n", sanitize_textarea_field($value));
            $out[$key] = str_replace("\r", "\n", $out[$key]);
            continue;
        }

        $out[$key] = sanitize_text_field($value);
    }

    return $out;
}

function samtykke_field($key, $label, $rows = 0, $help = '') {
    $value = samtykke_get($key);
    $name = SAMTYKKE_OPTION . '[' . $key . ']';

    echo '<tr><th scope="row"><label for="' . esc_attr($key) . '">' . esc_html($label) . '</label></th><td>';

    if ($rows) {
        echo '<textarea id="' . esc_attr($key) . '" name="' . esc_attr($name) . '" rows="' . (int) $rows
            . '" class="large-text">' . esc_textarea($value) . '</textarea>';
    } else {
        echo '<input id="' . esc_attr($key) . '" name="' . esc_attr($name) . '" type="text" class="large-text" value="'
            . esc_attr($value) . '">';
    }

    if ($help) {
        echo '<p class="description">' . esc_html($help) . '</p>';
    }

    echo '</td></tr>';
}

function samtykke_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
      <h1>Sportsvaerksted Consent form DK EN</h1>

      <?php if (isset($_GET['samtykke_test'])) : ?>
        <?php if ($_GET['samtykke_test'] === 'ok') : ?>
          <div class="notice notice-success"><p>
            Testmailen er sendt til
            <strong><?php echo esc_html(rawurldecode(wp_unslash($_GET['samtykke_to'] ?? ''))); ?></strong>.
            Kig i indbakken - og i spamfilteret. Kommer den ikke frem inden for et par minutter,
            er afsendelsen stadig ikke i orden.
          </p></div>
        <?php else : ?>
          <div class="notice notice-error"><p>
            Testmailen kunne ikke sendes.
            <?php if (!empty($_GET['samtykke_why'])) : ?>
              <br><code><?php echo esc_html(rawurldecode(wp_unslash($_GET['samtykke_why']))); ?></code>
            <?php endif; ?>
          </p></div>
        <?php endif; ?>
      <?php endif; ?>

      <p>Sæt kortkoden <code>[consent]</code> ind på en side for den danske udgave,
         og <code>[consent lang="en"]</code> for den engelske.
         <br>Den gamle kortkode <code>[samtykke]</code> virker stadig.</p>
      <p>Erklæringen sendes med e-mail og <strong>gemmes ikke</strong> på hjemmesiden.</p>

      <p><strong>Version <?php echo esc_html(SAMTYKKE_VERSION); ?></strong><?php
        $when = samtykke_get('saved_at');
        echo $when ? ' &middot; dine indstillinger blev gemt ' . esc_html($when)
                   : ' &middot; du har ikke gemt noget endnu, så teksten herunder er standardteksten.';
      ?></p>

      <form method="post" action="options.php">
        <?php settings_fields('samtykke_group'); ?>

        <h2>Modtager</h2>
        <table class="form-table" role="presentation">
          <?php
            samtykke_field('to_email', 'Send erklæringen til', 0, 'Din egen e-mailadresse.');
            $copy = samtykke_get('copy_to_signer');
          ?>
          <tr>
            <th scope="row">Kopi til underskriveren</th>
            <td>
              <label>
                <input type="checkbox" name="<?php echo esc_attr(SAMTYKKE_OPTION); ?>[copy_to_signer]"
                       value="1" <?php checked($copy, 1); ?>>
                Send også en kopi til den, der skriver under
              </label>
            </td>
          </tr>
        </table>

        <h2>Afsendelse (SMTP)</h2>
        <p class="description" style="max-width:40em">
          Uden det her sender WordPress mailen direkte fra webserveren. Domæner med
          en streng DMARC-regel - som sportsvaerksted.com - får den afvist af bl.a.
          Yahoo, Gmail og Outlook. Log ind på husets mailserver, så kommer den igennem.
        </p>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row">Send via SMTP</th>
            <td>
              <label>
                <input type="checkbox" name="<?php echo esc_attr(SAMTYKKE_OPTION); ?>[smtp_on]"
                       value="1" <?php checked(samtykke_get('smtp_on'), 1); ?>>
                Ja tak
              </label>
            </td>
          </tr>
          <?php
            samtykke_field('smtp_host', 'Server', 0, 'Hos Simply.com: websmtp.simply.com');
            samtykke_field('smtp_port', 'Port', 0, '587 med TLS, eller 465 med SSL.');
          ?>
          <tr>
            <th scope="row"><label for="smtp_secure">Kryptering</label></th>
            <td>
              <select id="smtp_secure" name="<?php echo esc_attr(SAMTYKKE_OPTION); ?>[smtp_secure]">
                <?php foreach (array('tls' => 'TLS (port 587)', 'ssl' => 'SSL (port 465)', '' => 'Ingen') as $k => $label) : ?>
                  <option value="<?php echo esc_attr($k); ?>" <?php selected(samtykke_get('smtp_secure'), $k); ?>>
                    <?php echo esc_html($label); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
          <?php
            samtykke_field('smtp_user', 'Brugernavn', 0,
              'Den fulde mailadresse, fx skriv@sportsvaerksted.com. Mailen bliver også sendt FRA den adresse - det er dét, der gør, at den ikke bliver afvist.');
          ?>
          <tr>
            <th scope="row"><label for="smtp_pass">Adgangskode</label></th>
            <td>
              <input id="smtp_pass" type="password" class="large-text" autocomplete="new-password"
                     name="<?php echo esc_attr(SAMTYKKE_OPTION); ?>[smtp_pass]"
                     value="<?php echo esc_attr(samtykke_get('smtp_pass')); ?>">
              <p class="description">Adgangskoden til den mailkonto. Den gemmes i databasen, ligesom i alle andre SMTP-plugins.</p>
            </td>
          </tr>
          <?php samtykke_field('from_name', 'Afsendernavn'); ?>
        </table>

        <h2>Udseende</h2>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row"><label for="label_color">Farve på feltnavne</label></th>
            <td>
              <input id="label_color" type="color"
                     name="<?php echo esc_attr(SAMTYKKE_OPTION); ?>[label_color]"
                     value="<?php echo esc_attr(samtykke_get('label_color')); ?>">
              <p class="description">Farven på feltnavnene: "Navn", "E-mail", "Dato for underskrift".</p>
            </td>
          </tr>
          <tr>
            <th scope="row"><label for="text_color">Farve på den øvrige tekst</label></th>
            <td>
              <input id="text_color" type="color"
                     name="<?php echo esc_attr(SAMTYKKE_OPTION); ?>[text_color]"
                     value="<?php echo esc_attr(samtykke_get('text_color')); ?>">
              <p class="description">Teksten ved afkrydsningsfelterne, ved underskriftsfelterne og den lille vejledningstekst.</p>
            </td>
          </tr>
        </table>

        <h2>Dansk tekst</h2>
        <table class="form-table" role="presentation">
          <?php
            samtykke_field('da_title', 'Overskrift');
            samtykke_field('da_who', 'Dataansvarlig', 0, 'Navn, adresse og e-mail.');
            samtykke_field('da_intro', 'Kort vejledning');
            samtykke_field('da_terms', 'Vilkår', 10, 'Ét afsnit pr. linje.');
            samtykke_field('da_use', 'Afkrydsning: tilladelse', 3);
            samtykke_field('da_name_ok', 'Afkrydsning: fornavn', 2);
          ?>
        </table>

        <h2>English text</h2>
        <table class="form-table" role="presentation">
          <?php
            samtykke_field('en_title', 'Heading');
            samtykke_field('en_who', 'Data controller');
            samtykke_field('en_intro', 'Short instruction');
            samtykke_field('en_terms', 'Terms', 10, 'One paragraph per line.');
            samtykke_field('en_use', 'Tick box: permission', 3);
            samtykke_field('en_name_ok', 'Tick box: first name', 2);
          ?>
        </table>

        <h2>Nulstil</h2>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row">Standardtekst</th>
            <td>
              <label>
                <input type="checkbox" name="<?php echo esc_attr(SAMTYKKE_OPTION); ?>[restore_defaults]" value="1">
                Sæt alle tekster og farver tilbage til standard, når jeg gemmer
              </label>
              <p class="description">Sletter dine egne rettelser. Kan ikke fortrydes.</p>
            </td>
          </tr>
        </table>

        <?php submit_button(); ?>
      </form>

      <h2>Test</h2>
      <p><strong>Test til en Gmail-adresse</strong> - ikke til en af dine egne adresser hos Simply.
         Mail til dit eget hus kommer altid frem, også når afsendelsen er forkert opsat. Det var
         præcis sådan, en klients kopi forsvandt uden at nogen opdagede det.</p>
      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="samtykke_test">
        <?php wp_nonce_field('samtykke_test'); ?>
        <input type="email" name="samtykke_test_to" class="regular-text"
               placeholder="din-adresse@gmail.com"
               value="<?php echo esc_attr(samtykke_get('to_email')); ?>">
        <?php submit_button('Send testmail', 'secondary', 'submit', false); ?>
        <p class="description">Gem dine indstillinger først.</p>
      </form>
    </div>
    <?php
}
