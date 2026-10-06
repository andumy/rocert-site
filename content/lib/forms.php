<?php
/**
 * Fluent Forms definitions (free version only). Option values are language-neutral keys so the API
 * receives the same data from the RO and EN forms; labels are per language.
 */

function ff_uid(): string
{
    static $n = 0;
    return 'el_' . (1790000000000 + (++$n));
}

function ff_rules(bool $required, string $msg): array
{
    return ['required' => ['value' => $required, 'message' => $msg, 'global' => false]];
}

/** @param array<int, array{0:string,1:string,2?:string}> $conditions [field, value, operator] */
function ff_cond(array $conditions, string $type = 'any'): array
{
    if (!$conditions) {
        return ['type' => 'any', 'status' => false, 'conditions' => [['field' => '', 'value' => '', 'operator' => '']]];
    }
    return ['type' => $type, 'status' => true, 'conditions' => array_map(static fn ($c) => ['field' => $c[0], 'value' => $c[1], 'operator' => $c[2] ?? '='], $conditions)];
}

function ff_text(string $name, string $label, bool $req, string $msg, array $o = []): array
{
    return [
        'element' => 'input_text',
        'attributes' => ['type' => $o['type'] ?? 'text', 'name' => $name, 'value' => '', 'class' => '', 'placeholder' => $o['placeholder'] ?? ''],
        'settings' => ['container_class' => $o['class'] ?? '', 'label' => $label, 'label_placement' => '', 'admin_field_label' => $o['admin'] ?? $label, 'help_message' => $o['help'] ?? '', 'validation_rules' => ff_rules($req, $msg), 'conditional_logics' => ff_cond($o['cond'] ?? [], $o['cond_type'] ?? 'any')],
        'editor_options' => ['title' => 'Simple Text', 'icon_class' => 'ff-edit-text', 'template' => 'inputText'],
        'uniqElKey' => ff_uid(),
    ];
}

function ff_email(string $name, string $label, bool $req, string $msg, string $invalid): array
{
    return [
        'element' => 'input_email',
        'attributes' => ['type' => 'email', 'name' => $name, 'value' => '', 'id' => '', 'class' => '', 'placeholder' => ''],
        'settings' => ['container_class' => '', 'label' => $label, 'label_placement' => '', 'help_message' => '', 'admin_field_label' => $label, 'validation_rules' => ff_rules($req, $msg) + ['email' => ['value' => true, 'message' => $invalid, 'global' => false]], 'conditional_logics' => []],
        'editor_options' => ['title' => 'Email Address', 'icon_class' => 'ff-edit-email', 'template' => 'inputText'],
        'uniqElKey' => ff_uid(),
    ];
}

function ff_textarea(string $name, string $label, bool $req, string $msg, array $o = []): array
{
    return [
        'element' => 'textarea',
        'attributes' => ['name' => $name, 'value' => '', 'id' => '', 'class' => '', 'placeholder' => $o['placeholder'] ?? '', 'rows' => $o['rows'] ?? 3, 'cols' => 2],
        'settings' => ['container_class' => $o['class'] ?? '', 'label' => $label, 'admin_field_label' => $label, 'label_placement' => '', 'help_message' => $o['help'] ?? '', 'validation_rules' => ff_rules($req, $msg), 'conditional_logics' => ff_cond($o['cond'] ?? [], $o['cond_type'] ?? 'any')],
        'editor_options' => ['title' => 'Text Area', 'icon_class' => 'ff-edit-textarea', 'template' => 'inputTextarea'],
        'uniqElKey' => ff_uid(),
    ];
}

function ff_number(string $name, string $label, array $o = []): array
{
    return [
        'element' => 'input_number',
        'attributes' => ['type' => 'number', 'name' => $name, 'value' => '', 'id' => '', 'class' => '', 'placeholder' => '', 'min' => '0'],
        'settings' => ['container_class' => $o['class'] ?? '', 'label' => $label, 'admin_field_label' => $o['admin'] ?? $label, 'label_placement' => '', 'help_message' => '', 'validation_rules' => ['required' => ['value' => $o['required'] ?? false, 'message' => $o['msg'] ?? '', 'global' => false], 'numeric' => ['value' => true, 'message' => '#', 'global' => false], 'min' => ['value' => '0', 'message' => '≥ 0', 'global' => false], 'max' => ['value' => '', 'message' => '', 'global' => false]], 'conditional_logics' => ff_cond($o['cond'] ?? []), 'calculation_settings' => ['status' => false, 'formula' => '']],
        'editor_options' => ['title' => 'Numeric Field', 'icon_class' => 'icon-slack', 'template' => 'inputText'],
        'uniqElKey' => ff_uid(),
    ];
}

/** @param array<string,string> $options value => label */
function ff_choice(string $element, string $name, string $label, array $options, bool $req, string $msg, array $o = []): array
{
    $is_select = $element === 'select';
    return [
        'element' => $element,
        'attributes' => $is_select ? ['name' => $name, 'value' => $o['value'] ?? '', 'id' => '', 'class' => ''] : ['type' => $element === 'input_radio' ? 'radio' : 'checkbox', 'name' => $name, 'value' => $element === 'input_checkbox' ? [] : ($o['value'] ?? '')],
        'settings' => ['container_class' => $o['class'] ?? '', 'label' => $label, 'admin_field_label' => $label, 'label_placement' => '', 'display_type' => '', 'help_message' => $o['help'] ?? '', 'placeholder' => $o['placeholder'] ?? '', 'validation_rules' => ff_rules($req, $msg), 'conditional_logics' => ff_cond($o['cond'] ?? [], $o['cond_type'] ?? 'any')],
        'options' => $options,
        'editor_options' => ['title' => $label, 'icon_class' => 'icon-check-square-o', 'template' => $is_select ? 'select' : ($element === 'input_radio' ? 'inputRadio' : 'inputCheckbox')],
        'uniqElKey' => ff_uid(),
    ];
}

function ff_section(string $title, string $description = '', array $cond = []): array
{
    return [
        'element' => 'section_break',
        'attributes' => ['id' => '', 'class' => ''],
        'settings' => ['label' => $title, 'description' => $description, 'align' => 'left', 'conditional_logics' => ff_cond($cond)],
        'editor_options' => ['title' => 'Section Break', 'icon_class' => 'icon-puzzle-piece', 'template' => 'sectionBreak'],
        'uniqElKey' => ff_uid(),
    ];
}

function ff_html(string $html, array $cond = [], string $cond_type = 'any'): array
{
    return [
        'element' => 'custom_html',
        'attributes' => [],
        'settings' => ['html_codes' => $html, 'conditional_logics' => ff_cond($cond, $cond_type), 'container_class' => ''],
        'editor_options' => ['title' => 'Custom HTML', 'icon_class' => 'icon-code', 'template' => 'customHTML'],
        'uniqElKey' => ff_uid(),
    ];
}

/** @param array[] ...$columns each a list of fields */
function ff_cols(array $columns, array $cond = [], string $cond_type = 'any'): array
{
    return [
        'element' => 'container',
        'attributes' => [],
        'settings' => ['container_class' => '', 'conditional_logics' => ff_cond($cond, $cond_type)],
        'columns' => array_map(static fn ($fields) => ['width' => round(100 / count($columns), 2), 'fields' => $fields], $columns),
        'editor_options' => ['title' => count($columns) . ' Column Container', 'icon_class' => 'icon-columns'],
        'uniqElKey' => ff_uid(),
    ];
}

/**
 * A row repeater (Fluent Forms free has none): the rows are built by request.js from the field list below and
 * saved as JSON into the text field $name, which stays a normal Fluent field (validation, entries, e-mails).
 * @param array<int, array{0:string,1:string,2?:string}> $columns [key, label, 'number'|'text'|'wide']
 */
function ff_repeater(string $name, string $label, array $columns, array $texts, bool $start_with_row = false): array
{
    $config = ['columns' => array_map(static fn ($c) => ['key' => $c[0], 'label' => $c[1], 'type' => $c[2] ?? 'text'], $columns), 'startWithRow' => $start_with_row] + $texts;
    return [
        ff_text($name, $label, false, '', ['class' => 'rc-json-field']),
        ff_html(sprintf('<div class="rc-repeater" data-rc-repeater="%s" data-config="%s"></div>', esc_attr($name), esc_attr(wp_json_encode($config, JSON_UNESCAPED_UNICODE)))),
    ];
}

/** Signature pad (request.js) that stores a PNG data URL in the text field $name. */
function ff_signature(string $name, string $label, bool $req, string $msg, string $clear, string $hint): array
{
    return [
        ff_text($name, $label, $req, $msg, ['class' => 'rc-sign-field']),
        ff_html(sprintf('<div class="rc-sign" data-rc-sign="%s" data-clear="%s" data-hint="%s"></div>', esc_attr($name), esc_attr($clear), esc_attr($hint))),
    ];
}

function ff_gdpr(string $html, string $msg): array
{
    return [
        'element' => 'gdpr_agreement',
        'attributes' => ['type' => 'checkbox', 'name' => 'gdpr-agreement', 'value' => false, 'class' => 'ff_gdpr_field'],
        'settings' => ['label' => 'GDPR', 'tnc_html' => $html, 'admin_field_label' => 'GDPR', 'has_checkbox' => true, 'container_class' => '', 'validation_rules' => ff_rules(true, $msg), 'required_field_message' => '', 'conditional_logics' => []],
        'editor_options' => ['title' => 'GDPR Agreement', 'icon_class' => 'icon-check-square-o', 'template' => 'termsCheckbox'],
        'uniqElKey' => ff_uid(),
    ];
}

function ff_turnstile(): array
{
    return ['element' => 'turnstile', 'attributes' => ['name' => 'cf-turnstile-response'], 'settings' => ['label' => '', 'validation_rules' => []], 'editor_options' => ['title' => 'Turnstile', 'icon_class' => 'ff-edit-turnstile', 'template' => 'turnstile'], 'uniqElKey' => ff_uid()];
}

function ff_submit(string $text): array
{
    return ['uniqElKey' => ff_uid(), 'element' => 'button', 'attributes' => ['type' => 'submit', 'class' => ''], 'settings' => ['align' => 'left', 'button_style' => '', 'container_class' => '', 'help_message' => '', 'background_color' => '', 'button_size' => 'lg', 'color' => '', 'button_ui' => ['type' => 'default', 'text' => $text, 'img_url' => '']], 'editor_options' => ['title' => 'Submit Button']];
}

function ff_l(string $lang): array
{
    return [
        'ro' => [
            'req' => 'Câmp obligatoriu', 'email_bad' => 'Adresă de e-mail invalidă',
            'types' => ['offer' => 'Ofertă', 'certification' => 'Certificare', 'migration' => 'Migrare', 'extension' => 'Extindere', 'reduction' => 'Restrângere', 'recertification' => 'Recertificare'],
            'yes' => 'Da', 'no' => 'Nu',
            'gdpr' => 'Sunt de acord cu prelucrarea datelor transmise, conform <a href="' . rs_url('ro', 'privacy') . '" target="_blank">Politicii de confidențialitate</a>.',
        ],
        'en' => [
            'req' => 'This field is required', 'email_bad' => 'Invalid e-mail address',
            'types' => ['offer' => 'Quote', 'certification' => 'Certification', 'migration' => 'Transfer', 'extension' => 'Extension', 'reduction' => 'Reduction', 'recertification' => 'Recertification'],
            'yes' => 'Yes', 'no' => 'No',
            'gdpr' => 'I agree to the processing of the submitted data in line with the <a href="' . rs_url('en', 'privacy') . '" target="_blank">Privacy Policy</a>.',
        ],
    ][$lang];
}

function ff_request_form(string $lang): array
{
    $l = ff_l($lang);
    $ro = $lang === 'ro';
    $T = static fn ($r, $e) => $ro ? $r : $e;
    $existing = [['request_type', 'migration'], ['request_type', 'extension'], ['request_type', 'reduction'], ['request_type', 'recertification']];

    $standards = [
        'iso-9001' => 'SR EN ISO 9001:2015', 'iso-14001' => 'SR EN ISO 14001:2015', 'iso-45001' => 'SR EN ISO 45001:2023', 'iso-27001' => 'SR EN ISO/IEC 27001:2023',
        'iso-20000-1' => 'SR ISO/IEC 20000-1:2020', 'iso-22000' => 'SR EN ISO 22000:2019', 'iso-ts-22002-1' => 'ISO/TS 22002-1:2009', 'iso-ts-22002-4' => 'ISO/TS 22002-4:2013',
        'iso-13485' => 'SR EN ISO 13485:2016', 'sr-en-15224' => 'SR EN 15224:2017', 'gdp' => 'GDP', 'iso-50001' => 'SR EN ISO 50001:2019', 'iso-37001' => 'SR ISO 37001:2017',
        'iso-37301' => 'SR ISO 37301:2021', 'iso-20400' => 'SR ISO 20400:2021', 'iso-39001' => 'SR ISO 39001:2018', 'sr-13572' => 'SR 13572:2016', 'other' => $T('Alt standard', 'Other standard'),
    ];
    $integration = $ro
        ? ['audit' => 'Audit intern integrat', 'policy' => 'Politică și obiective integrate', 'processes' => 'Procese integrate', 'documents' => 'Documente integrate', 'improvement' => 'Mecanisme de îmbunătățire integrate', 'planning' => 'Planificare integrată', 'responsibilities' => 'Responsabilități și susținere managerială unificate', 'review' => 'Analiză de management unificată']
        : ['audit' => 'Integrated internal audit', 'policy' => 'Integrated policy and objectives', 'processes' => 'Integrated processes', 'documents' => 'Integrated documentation', 'improvement' => 'Integrated improvement mechanisms', 'planning' => 'Integrated planning', 'responsibilities' => 'Unified management responsibilities and support', 'review' => 'Unified management review'];
    $ea_ro = [1 => 'Agricultură, pescuit (01, 02, 03)', 2 => 'Minerit și industrie extractivă (05–09)', 3 => 'Produse alimentare, băuturi și tutun (10, 11, 12)', 4 => 'Textile și produse textile (13, 14)', 5 => 'Piele și produse din piele (15)', 6 => 'Lemn și produse din lemn (16)', 7 => 'Celuloză, hârtie și produse din hârtie (17)', 8 => 'Edituri (581, 592)', 9 => 'Tipografii (18)', 10 => 'Cărbune și produse petroliere rafinate (19)', 12 => 'Substanțe și produse chimice, fibre (20, exc. 2051)', 13 => 'Produse farmaceutice (21)', 14 => 'Cauciuc și mase plastice (22)', 15 => 'Produse minerale nemetalice (23, exc. 235, 236)', 16 => 'Beton, ciment, var, ipsos (235, 236)', 17 => 'Metale de bază și produse din metal (24, 25)', 18 => 'Mașini și echipamente (28, 3312, 332)', 19 => 'Echipamente electrice și optice (26, 27, 2823, 3250, 3313, 951)', 20 => 'Construcția de nave (301, 3315)', 21 => 'Aeronave (303, 3316)', 22 => 'Alte echipamente de transport (29, 302, 304, 309)', 23 => 'Producție n.c.a. (31, 32)', 24 => 'Reciclare (383)', 25 => 'Alimentare cu energie electrică (351)', 26 => 'Alimentare cu gaze (352)', 27 => 'Alimentare cu apă (353, 36)', 28 => 'Construcții (41, 42, 43)', 29 => 'Comerț; repararea autovehiculelor (45, 46, 47, 952)', 30 => 'Hoteluri și restaurante (55, 56)', 31 => 'Transport, depozitare și comunicații (49, 52, 53, 61)', 32 => 'Intermedieri financiare, imobiliare, închirieri (64–68, 77)', 33 => 'Tehnologia informației (582, 62, 631)', 34 => 'Servicii de inginerie (71, 72, 74)', 35 => 'Alte servicii (69, 70, 73, 743, 78, 80–82)', 36 => 'Administrație publică (84)', 37 => 'Învățământ (85)', 38 => 'Sănătate și asistență socială (75, 86, 87, 88)', 39 => 'Alte servicii sociale (37, 381, 382, 39, 591, 60, 639, 79, 90–96)'];
    $ea_en = [1 => 'Agriculture, fishing (01, 02, 03)', 2 => 'Mining and quarrying (05–09)', 3 => 'Food products, beverages and tobacco (10, 11, 12)', 4 => 'Textiles and textile products (13, 14)', 5 => 'Leather and leather products (15)', 6 => 'Wood and wood products (16)', 7 => 'Pulp, paper and paper products (17)', 8 => 'Publishing (581, 592)', 9 => 'Printing (18)', 10 => 'Coke and refined petroleum products (19)', 12 => 'Chemicals, chemical products and fibres (20, exc. 2051)', 13 => 'Pharmaceuticals (21)', 14 => 'Rubber and plastic products (22)', 15 => 'Non-metallic mineral products (23, exc. 235, 236)', 16 => 'Concrete, cement, lime, plaster (235, 236)', 17 => 'Basic metals and fabricated metal products (24, 25)', 18 => 'Machinery and equipment (28, 3312, 332)', 19 => 'Electrical and optical equipment (26, 27, 2823, 3250, 3313, 951)', 20 => 'Shipbuilding (301, 3315)', 21 => 'Aerospace (303, 3316)', 22 => 'Other transport equipment (29, 302, 304, 309)', 23 => 'Manufacturing n.e.c. (31, 32)', 24 => 'Recycling (383)', 25 => 'Electricity supply (351)', 26 => 'Gas supply (352)', 27 => 'Water supply (353, 36)', 28 => 'Construction (41, 42, 43)', 29 => 'Wholesale and retail trade; motor vehicle repair (45, 46, 47, 952)', 30 => 'Hotels and restaurants (55, 56)', 31 => 'Transport, storage and communication (49, 52, 53, 61)', 32 => 'Financial intermediation, real estate, renting (64–68, 77)', 33 => 'Information technology (582, 62, 631)', 34 => 'Engineering services (71, 72, 74)', 35 => 'Other services (69, 70, 73, 743, 78, 80–82)', 36 => 'Public administration (84)', 37 => 'Education (85)', 38 => 'Health and social work (75, 86, 87, 88)', 39 => 'Other social services (37, 381, 382, 39, 591, 60, 639, 79, 90–96)'];
    $ea = [];
    foreach ($ro ? $ea_ro : $ea_en as $n => $label) {
        $ea['ea-' . str_pad((string) $n, 2, '0', STR_PAD_LEFT)] = $n . '. ' . $label;
    }
    $yn = ['yes' => $l['yes'], 'no' => $l['no']];

    $office = $T('Sediu social', 'Registered office') . ' — ';
    $staff = static fn (string $p, bool $req) => [
        ff_cols([
            [ff_number($p . 'staff_p', $T('Personal permanent (P)', 'Permanent staff (P)'), ['required' => $req, 'msg' => $l['req'], 'admin' => $office . $T('Personal permanent (P)', 'Permanent staff (P)')])],
            [ff_number($p . 'staff_t', $T('Personal temporar (T)', 'Temporary staff (T)'), ['required' => $req, 'msg' => $l['req'], 'admin' => $office . $T('Personal temporar (T)', 'Temporary staff (T)')])],
            [ff_number($p . 'staff_r', $T('Part-time (R)', 'Part-time (R)'), ['required' => $req, 'msg' => $l['req'], 'admin' => $office . $T('Part-time (R)', 'Part-time (R)')])],
        ]),
        ff_cols([
            [ff_number($p . 'shift_1', $T('Schimbul 1 (nr. personal)', 'Shift 1 (staff)'), ['required' => $req, 'msg' => $l['req'], 'admin' => $office . $T('Schimbul 1 (nr. personal)', 'Shift 1 (staff)')])],
            [ff_number($p . 'shift_2', $T('Schimbul 2 (nr. personal)', 'Shift 2 (staff)'), ['required' => $req, 'msg' => $l['req'], 'admin' => $office . $T('Schimbul 2 (nr. personal)', 'Shift 2 (staff)')])],
            [ff_number($p . 'shift_3', $T('Schimbul 3 (nr. personal)', 'Shift 3 (staff)'), ['required' => $req, 'msg' => $l['req'], 'admin' => $office . $T('Schimbul 3 (nr. personal)', 'Shift 3 (staff)')])],
        ]),
    ];
    $location_columns = [
        ['address', $T('Adresa', 'Address'), 'wide'], ['activity', $T('Activitatea desfășurată', 'Activity performed'), 'wide'],
        ['staff_p', $T('Permanent (P)', 'Permanent (P)'), 'number'], ['staff_t', $T('Temporar (T)', 'Temporary (T)'), 'number'], ['staff_r', 'Part-time (R)', 'number'],
        ['shift_1', $T('Schimbul 1', 'Shift 1'), 'number'], ['shift_2', $T('Schimbul 2', 'Shift 2'), 'number'], ['shift_3', $T('Schimbul 3', 'Shift 3'), 'number'],
    ];
    $sign_texts = [$T('Șterge semnătura', 'Clear signature'), $T('Semnați cu mouse-ul sau cu degetul în chenarul de mai sus.', 'Sign with your mouse or finger in the box above.')];

    $fields = [
        ff_choice('input_radio', 'request_type', $T('Tipul cererii', 'Request type'), $l['types'], true, $l['req'], ['class' => 'rc-type-tiles', 'value' => 'certification']),

        ff_section($T('1. Date de identificare a organizației', '1. Organisation details'), $T('Introduceți CUI-ul și apăsați „Preia datele de la ANAF” pentru completare automată.', 'Enter the tax ID (CUI) and press “Fetch company data” to fill the details automatically.')),
        ff_text('cui', $T('CUI (cod unic de înregistrare)', 'Tax ID (CUI)'), true, $l['req'], ['class' => 'rc-anaf', 'placeholder' => $T('ex. RO12345678', 'e.g. RO12345678')]),
        ff_text('company_name', $T('Denumirea organizației și forma juridică', 'Organisation name and legal form'), true, $l['req']),
        ff_text('address', $T('Adresa sediului social (stradă, număr, bloc, etaj, apartament)', 'Registered address (street, number, building, floor, apartment)'), true, $l['req']),
        ff_cols([
            [ff_text('city', $T('Localitate / sector', 'City / district'), true, $l['req'])],
            [ff_text('county', $T('Județ', 'County'), true, $l['req'])],
            [ff_text('postal_code', $T('Cod poștal', 'Postal code'), false, $l['req'])],
        ]),
        ff_cols([
            [ff_text('reg_com', $T('Nr. Registrul Comerțului', 'Trade Register no.'), false, $l['req'])],
            [ff_text('iban', 'IBAN', false, $l['req'])],
            [ff_text('bank', $T('Banca', 'Bank'), false, $l['req'])],
        ]),
        ff_cols([
            [ff_text('phone', $T('Telefon', 'Phone'), true, $l['req'], ['type' => 'tel'])],
            [ff_text('mobile', $T('Mobil', 'Mobile'), false, $l['req'], ['type' => 'tel'])],
            [ff_text('fax', 'Fax', false, $l['req'], ['type' => 'tel'])],
        ]),
        ff_cols([
            [ff_email('email', 'E-mail', true, $l['req'], $l['email_bad'])],
            [ff_text('website', 'Website', false, $l['req'], ['type' => 'url'])],
        ]),
        ff_html('<p class="rc-form-sub">' . esc_html($T('Manager de vârf (reprezentant legal)', 'Top manager (legal representative)')) . '</p>'),
        ff_cols([
            [ff_text('manager_name', $T('Nume și prenume', 'Full name'), true, $l['req'], ['admin' => $T('Manager de vârf — Nume', 'Top manager — Name')])],
            [ff_text('manager_role', $T('Funcție', 'Position'), true, $l['req'], ['admin' => $T('Manager de vârf — Funcție', 'Top manager — Position')])],
            [ff_text('manager_phone', $T('Telefon', 'Phone'), false, $l['req'], ['type' => 'tel', 'admin' => $T('Manager de vârf — Telefon', 'Top manager — Phone')])],
        ]),
        ff_html('<p class="rc-form-sub">' . esc_html($T('Persoana de contact responsabilă de sistem', 'Contact person responsible for the system')) . '</p>'),
        ff_cols([
            [ff_text('contact_name', $T('Nume și prenume', 'Full name'), true, $l['req'], ['admin' => $T('Persoană de contact — Nume', 'Contact person — Name')])],
            [ff_text('contact_role', $T('Funcție', 'Position'), false, $l['req'], ['admin' => $T('Persoană de contact — Funcție', 'Contact person — Position')])],
        ]),
        ff_cols([
            [ff_text('contact_phone', $T('Telefon', 'Phone'), true, $l['req'], ['type' => 'tel', 'admin' => $T('Persoană de contact — Telefon', 'Contact person — Phone')])],
            [ff_email('contact_email', 'E-mail', true, $l['req'], $l['email_bad'])],
            [ff_text('contact_fax', 'Fax', false, $l['req'], ['type' => 'tel', 'admin' => $T('Persoană de contact — Fax', 'Contact person — Fax')])],
        ]),

        ff_section($T('2. Modelul de certificare dorit', '2. Requested certification'), $T('Selectați unul sau mai multe standarde.', 'Select one or more standards.')),
        ff_choice('input_checkbox', 'standards', $T('Standarde', 'Standards'), $standards, true, $l['req'], ['class' => 'rc-chip-checks']),
        ff_text('standard_other', $T('Care alt standard?', 'Which other standard?'), false, $l['req'], ['cond' => [['standards', 'other', 'contains']]]),
        ff_choice('input_radio', 'integrated_system', $T('Aveți implementat un sistem de management integrat?', 'Do you run an integrated management system?'), $yn, false, $l['req'], ['class' => 'rc-inline-radio']),
        ff_choice('input_checkbox', 'integration_level', $T('Nivel de integrare', 'Level of integration'), $integration, false, $l['req'], ['cond' => [['integrated_system', 'yes']]]),

        ff_section($T('3. Domeniul de activitate', '3. Scope of activity'), $T('Domeniul pentru care solicitați certificarea, cu codurile CAEN aferente.', 'The scope you want certified, with the related NACE codes.')),
        ff_textarea('scope_description', $T('Descrierea activității de certificat', 'Description of the activity to be certified'), true, $l['req'], ['placeholder' => $T('ex. Proiectarea și execuția de lucrări de construcții civile și industriale', 'e.g. Design and execution of civil and industrial construction works')]),
        ff_choice('input_checkbox', 'ea_codes', $T('Domenii EA (coduri CAEN)', 'EA sectors (NACE codes)'), $ea, true, $l['req'], ['class' => 'rc-ea-grid']),

        ff_section($T('4. Informații pentru stabilirea duratei auditului', '4. Information to determine audit duration'), $T('4.1 Toate locațiile unde se desfășoară activitățile (sediu social, sucursale, puncte de lucru, depozite, laboratoare). P = permanent, T = temporar/sezonier, R = part-time.', '4.1 All sites where the activities take place (registered office, branches, work points, warehouses, laboratories). P = permanent, T = temporary/seasonal, R = part-time.')),
        ff_number('total_employees', $T('Număr total de angajați', 'Total number of employees'), ['required' => true, 'msg' => $l['req']]),
        ff_html('<p class="rc-loc-title">' . esc_html($T('Sediu social', 'Registered office')) . '</p>'),
        ff_cols([
            [ff_text('loc0_address', $T('Adresa', 'Address'), true, $l['req'], ['admin' => $office . $T('Adresa', 'Address')])],
            [ff_text('loc0_activity', $T('Activitatea desfășurată', 'Activity performed'), true, $l['req'], ['admin' => $office . $T('Activitatea desfășurată', 'Activity performed')])],
        ]),
        ...$staff('loc0_', true),
        ...ff_repeater('locations', $T('Locații suplimentare', 'Additional sites'), $location_columns, [
            'add' => $T('Adaugă o locație suplimentară', 'Add another site'), 'remove' => $T('Șterge locația', 'Remove site'),
            'rowTitle' => $T('Locația nr. %d', 'Site no. %d'),
        ]),
        ff_html('<p class="rc-form-sub">' . esc_html($T('4.2 Meserii cu mai mulți angajați care efectuează aceeași activitate', '4.2 Job roles with several employees performing the same activity')) . '</p><p class="rc-form-note">' . esc_html($T('Doar dacă există mai mulți angajați care efectuează aceeași activitate (zidari, strungari, șoferi etc.).', 'Only where several employees perform the same activity (bricklayers, turners, drivers etc.).')) . '</p>'),
        ...ff_repeater('job_roles', $T('Meserii', 'Job roles'), [['role', $T('Denumire meserie', 'Job title'), 'wide'], ['count', $T('Nr. angajați', 'No. of employees'), 'number']], [
            'add' => $T('Adaugă o meserie', 'Add a job role'), 'remove' => $T('Șterge rândul', 'Remove row'), 'rowTitle' => '',
        ], true),
        ff_html('<p class="rc-form-sub">' . esc_html($T('4.3 Complexitatea proceselor', '4.3 Process complexity')) . '</p>'),
        ff_cols([
            [ff_choice('input_radio', 'advanced_technology', $T('Tehnologie avansată', 'Advanced technology'), $yn, false, $l['req'], ['class' => 'rc-inline-radio'])],
            [ff_choice('input_radio', 'regulated_field', $T('Domeniu puternic reglementat (ISCIR, ANRE, ANRSC etc.)', 'Highly regulated field (ISCIR, ANRE, ANRSC etc.)'), $yn, false, $l['req'], ['class' => 'rc-inline-radio'])],
            [ff_choice('input_radio', 'many_processes', $T('Număr mare de procese', 'Large number of processes'), $yn, false, $l['req'], ['class' => 'rc-inline-radio'])],
        ]),
        ff_cols([
            [ff_choice('input_radio', 'design_development', $T('Proiectare-dezvoltare', 'Design and development'), $yn, false, $l['req'], ['class' => 'rc-inline-radio'])],
            [ff_number('design_staff', $T('Nr. personal proiectare', 'Design staff'), ['cond' => [['design_development', 'yes']]])],
            [ff_choice('input_radio', 'shift_differences', $T('Procese diferite de la un schimb la altul', 'Different processes between shifts'), $yn, false, $l['req'], ['class' => 'rc-inline-radio'])],
        ]),
        ff_cols([
            [ff_textarea('repetitive_processes', $T('Procese repetitive', 'Repetitive processes'), false, $l['req'], ['rows' => 2])],
            [ff_textarea('unique_processes', $T('Procese unice', 'One-off processes'), false, $l['req'], ['rows' => 2])],
        ]),
        ff_textarea('shift_details', $T('Ce procese se desfășoară în fiecare schimb?', 'Which processes run in each shift?'), false, $l['req'], ['cond' => [['shift_differences', 'yes']]]),
        ff_textarea('outsourced_processes', $T('Procese externalizate sau activități subcontractate care pot influența conformitatea', 'Outsourced processes or subcontracted activities that may affect conformity'), false, $l['req']),
        ff_html('<p class="rc-form-sub">' . esc_html($T('4.4 Certificări și recunoașteri obținute', '4.4 Certifications and recognitions held')) . '</p><p class="rc-form-note">' . esc_html($T('Obligatoriu pentru migrare, extindere, restrângere și recertificare.', 'Required for transfer, extension, reduction and recertification.')) . '</p>'),
        ff_cols([
            [ff_text('existing_standards', $T('Standard(e) de referință', 'Reference standard(s)'), false, $l['req'])],
            [ff_text('existing_certificate', $T('Nr. certificat / data certificării', 'Certificate no. / date'), false, $l['req'])],
            [ff_text('existing_body', $T('Organism(e) emitent(e)', 'Issuing body(ies)'), false, $l['req'])],
        ]),
        ff_choice('input_checkbox', 'particularities', $T('4.5 Alte particularități care pot influența auditul', '4.5 Other particularities that may affect the audit'), $ro ? ['holding' => 'Apartenență la un holding', 'language' => 'Limba auditului alta decât româna', 'security' => 'Condiții speciale de securitate'] : ['holding' => 'Part of a holding', 'language' => 'Audit language other than Romanian', 'security' => 'Special security conditions'], false, $l['req']),
        ff_textarea('particularities_details', $T('Detalii', 'Details'), false, $l['req'], ['rows' => 2]),
        ff_cols([
            [ff_choice('input_radio', 'iqnet', $T('Emitere certificat IQNet', 'IQNet certificate'), $yn, false, $l['req'], ['class' => 'rc-inline-radio'])],
            [ff_choice('input_radio', 'audit_mode', $T('Auditul se va derula', 'The audit will be'), $ro ? ['integrated' => 'Integrat', 'combined' => 'Combinat', 'joint' => 'Joint-audit'] : ['integrated' => 'Integrated', 'combined' => 'Combined', 'joint' => 'Joint audit'], false, $l['req'], ['class' => 'rc-inline-radio', 'help' => $T('Integrat: un singur sistem pentru mai multe standarde. Combinat: sisteme auditate separat, în același audit. Joint-audit: echipă mixtă cu alt organism.', 'Integrated: one system covering several standards. Combined: systems audited separately within the same audit. Joint audit: mixed team with another body.')])],
        ]),
        ff_choice('input_radio', 'implementation', $T('4.6 Sistemul a fost proiectat, implementat și menținut', '4.6 The system was designed, implemented and maintained'), $ro ? ['in-house' => 'Prin forțe proprii', 'consultancy' => 'Cu o firmă de consultanță', 'external' => 'Cu un colaborator extern'] : ['in-house' => 'In-house', 'consultancy' => 'With a consultancy firm', 'external' => 'With an external collaborator'], false, $l['req'], ['class' => 'rc-inline-radio']),
        ff_text('consultant', $T('Numele firmei de consultanță / colaboratorului și perioada', 'Consultancy / collaborator name and period'), false, $l['req'], ['cond' => [['implementation', 'consultancy'], ['implementation', 'external']]]),

        ff_section($T('5. Alte informații', '5. Other information')),
        ff_text('planned_audit_date', $T('Data preconizată pentru audit', 'Planned audit date'), false, $l['req'], ['placeholder' => $T('ex. martie 2027', 'e.g. March 2027')]),
        ff_choice('input_checkbox', 'source', $T('Cum ați aflat de ROCERT?', 'How did you hear about ROCERT?'), $ro ? ['training' => 'Training-uri', 'events' => 'Conferințe, târguri', 'personal' => 'Contacte individuale', 'internet' => 'Internet', 'ads' => 'Materiale publicitare', 'previous' => 'Colaborări anterioare'] : ['training' => 'Training', 'events' => 'Conferences, fairs', 'personal' => 'Personal contacts', 'internet' => 'Internet', 'ads' => 'Advertising', 'previous' => 'Previous collaboration'], false, $l['req'], ['class' => 'rc-chip-checks']),
        ff_html('<p class="rc-form-note">' . esc_html($T('Documentele suplimentare (lista proceselor tehnologice, copii ale certificatelor existente) ne pot fi trimise după depunerea cererii, la office@rocert.ro.', 'Supporting documents (list of technological processes, copies of existing certificates) can be sent after submitting, to office@rocert.ro.')) . '</p>'),
        ff_html('<p class="rc-form-sub">' . esc_html($T('Persoana care a completat cererea', 'Person who completed the request')) . '</p>'),
        ff_cols([
            [ff_text('filled_by_name', $T('Nume și prenume', 'Full name'), true, $l['req'], ['admin' => $T('Completat de — Nume', 'Completed by — Name')])],
            [ff_text('filled_by_role', $T('Funcție', 'Position'), true, $l['req'], ['admin' => $T('Completat de — Funcție', 'Completed by — Position')])],
        ]),
        ...ff_signature('signature_filler', $T('Semnătura', 'Signature'), true, $l['req'], ...$sign_texts),
        ff_html('<p class="rc-form-sub">' . esc_html($T('Managerul organizației', 'Head of the organisation')) . '</p>'),
        ff_cols([
            [ff_text('signer_manager_name', $T('Nume și prenume', 'Full name'), false, $l['req'], ['admin' => $T('Manager (semnatar) — Nume', 'Head of organisation (signatory) — Name')])],
            [ff_text('signer_manager_role', $T('Funcție', 'Position'), false, $l['req'], ['admin' => $T('Manager (semnatar) — Funcție', 'Head of organisation (signatory) — Position')])],
        ]),
        ...ff_signature('signature_manager', $T('Semnătura managerului', 'Signature of the head of the organisation'), false, $l['req'], ...$sign_texts),
        ff_gdpr($l['gdpr'], $l['req']),
    ];
    return [$fields, $T('Trimite cererea', 'Send request')];
}

/** Creates or replaces a Fluent Forms form; returns its id. */
function ff_save(string $title, array $fields, string $submit, string $confirmation, string $subject, int $existing_id = 0): int
{
    global $wpdb;
    if (ROCERT_TURNSTILE_SITE_KEY) {
        $fields[] = ff_turnstile();
    }
    foreach ($fields as $i => &$f) {
        $f['index'] = $i;
    }
    unset($f);
    $now = current_time('mysql');
    $row = [
        'title' => $title,
        'status' => 'published',
        'form_fields' => wp_json_encode(['fields' => $fields, 'submitButton' => ff_submit($submit)], JSON_UNESCAPED_UNICODE),
        'has_payment' => 0,
        'type' => 'form',
        'created_by' => 1,
        'updated_at' => $now,
    ];
    $table = $wpdb->prefix . 'fluentform_forms';
    if ($existing_id && $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $existing_id))) {
        $wpdb->update($table, $row, ['id' => $existing_id]);
        $id = $existing_id;
    } else {
        $wpdb->insert($table, $row + ['created_at' => $now]);
        $id = (int) $wpdb->insert_id;
    }

    $settings = \FluentForm\App\Models\Form::getFormsDefaultSettings();
    $settings['confirmation']['messageToShow'] = '<p class="rc-form-success">' . $confirmation . '</p>';
    $settings['layout']['labelPlacement'] = 'top';
    $settings['layout']['asteriskPlacement'] = 'asterisk-right';
    $notification = [
        'name' => 'ROCERT',
        'sendTo' => ['type' => 'email', 'email' => '{wp.admin_email}', 'field' => 'email', 'routing' => [['email' => null, 'field' => null, 'operator' => '=', 'value' => null]]],
        'fromName' => '', 'fromEmail' => '', 'replyTo' => '{inputs.email}', 'bcc' => '',
        'subject' => $subject,
        'message' => '<p>{all_data}</p><p>{embed_post.permalink}</p>',
        'conditionals' => ['status' => false, 'type' => 'all', 'conditions' => [['field' => null, 'operator' => '=', 'value' => null]]],
        'enabled' => true, 'email_template' => '',
    ];
    $meta = $wpdb->prefix . 'fluentform_form_meta';
    $wpdb->delete($meta, ['form_id' => $id]);
    foreach (['formSettings' => wp_json_encode($settings, JSON_UNESCAPED_UNICODE), 'template_name' => 'blank_form', 'notifications' => wp_json_encode($notification, JSON_UNESCAPED_UNICODE)] as $key => $value) {
        $wpdb->insert($meta, ['form_id' => $id, 'meta_key' => $key, 'value' => $value]);
    }
    return $id;
}

/** Creates or updates the RO and EN request forms (same ids), drops obsolete ones; returns the id map. */
function ff_save_request_forms(): array
{
    $forms = (array) get_option('rocert_forms', []);
    foreach (['contact_ro', 'contact_en'] as $obsolete) {
        if (!empty($forms[$obsolete])) {
            $GLOBALS['wpdb']->delete($GLOBALS['wpdb']->prefix . 'fluentform_forms', ['id' => (int) $forms[$obsolete]]);
            $GLOBALS['wpdb']->delete($GLOBALS['wpdb']->prefix . 'fluentform_form_meta', ['form_id' => (int) $forms[$obsolete]]);
            unset($forms[$obsolete]);
        }
    }
    foreach (RS_LANGS as $lang) {
        $ro = $lang === 'ro';
        [$fields, $submit] = ff_request_form($lang);
        $forms['request_' . $lang] = ff_save(
            $ro ? 'Cerere de certificare C02/ROC (RO)' : 'Certification request C02/ROC (EN)', $fields, $submit,
            $ro ? 'Mulțumim! Am primit cererea de certificare. Vă trimitem oferta în maximum 3 zile lucrătoare.' : 'Thank you! We received your certification request. You will receive our quote within 3 working days.',
            ($ro ? '[ROCERT] Cerere de certificare — ' : '[ROCERT] Certification request — ') . '{inputs.company_name} ({inputs.request_type})', (int) ($forms['request_' . $lang] ?? 0)
        );
    }
    update_option('rocert_forms', $forms, false);
    return $forms;
}
