<?php
/**
 * Plugin Name: Samtykke - film og billeder
 * Description: Samtykkeerklæring til brug af film og billeder. Sæt kortkoden [samtykke] ind på en side. PDF'en sendes med e-mail og gemmes ikke på serveren.
 * Version:     1.0.3
 * Author:      Anirudha Talmale
 * Text Domain: samtykke-consent
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SAMTYKKE_VERSION', '1.0.3');
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

    if (isset($saved[$key]) && $saved[$key] !== '') {
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
add_shortcode('samtykke', 'samtykke_shortcode');

// ------------------------------------------------------------- the sending

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
    $headers = array('Content-Type: text/plain; charset=UTF-8');

    $sent = wp_mail($to, $subject, sprintf($s['mailBody'], $name), $headers, array($file));

    $copied = false;

    if (samtykke_get('copy_to_signer') && is_email($email)) {
        $copied = wp_mail($email, $subject, $s['mailBodySigner'], $headers, array($file));
    }

    // Nothing is kept. The promise made on the form is that the website stores
    // no declarations, so the only copies are the two emails.
    @unlink($file);

    if (!$sent) {
        wp_send_json_error(array('message' => 'mail failed'), 500);
    }

    wp_send_json_success(array('sent' => true, 'copied' => (bool) $copied));
}
add_action('wp_ajax_samtykke_send', 'samtykke_handle');
add_action('wp_ajax_nopriv_samtykke_send', 'samtykke_handle');

// ------------------------------------------------------------- settings UI

function samtykke_settings_menu() {
    add_options_page('Samtykke', 'Samtykke', 'manage_options', 'samtykke', 'samtykke_settings_page');
}
add_action('admin_menu', 'samtykke_settings_menu');

function samtykke_settings_register() {
    register_setting('samtykke_group', SAMTYKKE_OPTION, array(
        'sanitize_callback' => 'samtykke_sanitize',
        'default'           => samtykke_defaults(),
    ));
}
add_action('admin_init', 'samtykke_settings_register');

function samtykke_sanitize($input) {
    $out = array();
    $defaults = samtykke_defaults();

    foreach ($defaults as $key => $default) {
        if ($key === 'to_email') {
            $out[$key] = sanitize_email(isset($input[$key]) ? $input[$key] : '');
            continue;
        }

        if ($key === 'copy_to_signer') {
            $out[$key] = empty($input[$key]) ? 0 : 1;
            continue;
        }

        if ($key === 'label_color' || $key === 'text_color') {
            $colour = sanitize_hex_color(isset($input[$key]) ? $input[$key] : '');
            $out[$key] = $colour ? $colour : $default;
            continue;
        }

        $value = isset($input[$key]) ? (string) $input[$key] : '';
        // Multi-line fields keep their line breaks; the rest are single lines.
        $out[$key] = (strpos($key, '_terms') !== false)
            ? sanitize_textarea_field($value)
            : sanitize_text_field($value);
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
      <h1>Samtykke</h1>

      <p>Sæt kortkoden <code>[samtykke]</code> ind på en side for den danske udgave,
         og <code>[samtykke lang="en"]</code> for den engelske.</p>
      <p>Erklæringen sendes med e-mail og <strong>gemmes ikke</strong> på hjemmesiden.</p>

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

        <?php submit_button(); ?>
      </form>
    </div>
    <?php
}
