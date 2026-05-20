<?php
global $Wcms;

function next_root_path(string $path = ''): string
{
	return dirname(__DIR__, 2) . ($path !== '' ? '/' . ltrim($path, '/') : '');
}

function next_html(string $value): string
{
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function next_team_path(): string
{
	return next_root_path('data/team.json');
}

function next_home_path(): string
{
	return next_root_path('data/home.json');
}

function next_default_home(): array
{
	return [
		'hero_kicker' => 'welkom bij NEXT',
		'hero_title' => 'Groeien op jouw tempo',
		'intro_title' => "De volgende stap,\nop jouw tempo",
		'intro_text' => 'NEXT is een praktische dagbesteding voor jongeren die tijdelijk uitvallen of vastlopen. In een warme en veilige omgeving krijgen jongeren de ruimte om tot rust te komen, opnieuw vertrouwen op te bouwen en stap voor stap te werken aan hun toekomst.',
		'intro_button_text' => 'Ontdek Wat we doen',
		'intro_button_link' => 'werking',
		'quote' => 'Hier mag je op jouw tempo opnieuw je weg vinden.',
		'about' => [
			[
				'letter' => 'N',
				'title' => 'Nieuw begin',
				'text' => 'Jongeren krijgen de kans om opnieuw te starten, zonder oordeel. We bieden een warme, neutrale en veilige plek waar jongeren zichzelf kunnen zijn.'
			],
			[
				'letter' => 'E',
				'title' => 'Ervaring',
				'text' => 'We bieden een praktische, zinvolle dagbesteding aan. We leren door te doen en werken zeer laagdrempelig en op maat van de jongere.'
			],
			[
				'letter' => 'X',
				'title' => 'X-factor',
				'text' => 'Iedereen is uniek, heeft talenten en krachten. We gaan die samen ontdekken. We nemen even de time-out en staan stil bij onszelf.'
			],
			[
				'letter' => 'T',
				'title' => 'Toekomstgericht',
				'text' => 'Blik vooruit. We focussen op de toekomst en gaan samen doelen bepalen. Wat komt, telt meer dan wat achter ons ligt.'
			]
		],
		'cta_title' => 'Contact',
		'cta_text' => 'Hulp nodig? Neem contact met ons op. Heb je vragen of wil je graag inschrijven? Neem dan met ons contact op.',
		'cta_button_text' => 'Contacteer ons',
		'cta_button_link' => 'contact'
	];
}

function next_load_home(): array
{
	$path = next_home_path();
	if (!is_file($path)) {
		return next_default_home();
	}
	$home = json_decode((string) file_get_contents($path), true);
	if (!is_array($home)) {
		return next_default_home();
	}
	return array_replace_recursive(next_default_home(), $home);
}

function next_save_home(array $home): bool
{
	$json = json_encode($home, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	return $json !== false && file_put_contents(next_home_path(), $json, LOCK_EX) !== false;
}

function next_plain_field(string $value): string
{
	return trim(strip_tags($value));
}

function next_link_field(string $value, string $fallback): string
{
	$value = trim(strip_tags($value));
	return $value !== '' ? $value : $fallback;
}

function next_default_team(): array
{
	return [
		[
			'name' => 'Debbie',
			'image' => 'data/files/Debbie.png',
			'text' => "Ik ben Debbie en al meer dan 15 jaar actief in het secundair onderwijs: eerst als leerkracht en de laatste jaren als leerlingenbegeleidster. Samen met mijn man Raf en zoontje Jax, vormen we een hecht team. Ik ben heel empathisch en werk graag samen met jongeren. Ik ben geduldig en heb een luisterend oor. Respect en dankbaarheid zijn belangrijke waarden in mijn leven. Soms lukt het gewoon even niet meer, om eender welke reden dan ook. Ik zou heel graag naar je verhaal luisteren, of gewoon even naast je zitten. Ik hoop dat NEXT een plek kan worden waar je je als jongere gezien en gehoord voelt en dat er ergens terug een klein 'vuurtje' wordt aangewakkerd."
		],
		[
			'name' => 'Raf',
			'image' => 'data/files/Raf.png',
			'text' => 'Ik ben Raf en de voorbije 15 jaar werkte ik als zelfstandige in de fitness-sector. Daarnaast was ik eveneens stagebegeleider in de richting Beweging en Sport bij Atheneum Louis Zimmer. Tijdens mijn jaren als leraar LO ontdekte ik de kracht van beweging en meer bepaald het effect ervan op het algemeen welzijn. Bij NEXT zal je deze kracht zelf ervaren en begeleid ik jou graag mee tijdens jouw persoonlijk traject.'
		],
		[
			'name' => 'Floor',
			'image' => 'data/files/floor.png',
			'text' => 'Floor hier! Een enthousiaste en empathische spring-in-\'t-veld. Naast NEXT ben ik een maatschappelijk werker op een CLB. Ik heb nog niet zoveel werkervaring op mijn teller staan zoals mijn twee toppers van collega\'s, maar ik volg graag het motto van Pipi Langkous: "Ik heb het nog nooit gedaan, dus ik denk dat ik het wel kan." Met een open blik kijk ik graag mee naar jouw verhaal en probeer ik samen met jou handvaten te vinden, waar het even onstabiel voelt. Ik hoop dat NEXT een plek mag zijn waar je je welkom voelt, waar er tijd en ruimte is voor jou, waar we stap voor stap een weg vooruit kunnen zoeken of net even een moment pauze inlassen.'
		]
	];
}

function next_load_team(): array
{
	$path = next_team_path();
	if (!is_file($path)) {
		return next_default_team();
	}
	$team = json_decode((string) file_get_contents($path), true);
	return is_array($team) ? $team : next_default_team();
}

function next_save_team(array $team): bool
{
	$json = json_encode(array_values($team), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	return $json !== false && file_put_contents(next_team_path(), $json, LOCK_EX) !== false;
}

function next_upload_team_image(int $index): ?string
{
	$file = $_FILES['team_upload'] ?? null;
	if (
		!isset($file['error'][$index])
		|| $file['error'][$index] === UPLOAD_ERR_NO_FILE
	) {
		return null;
	}
	if ($file['error'][$index] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'][$index])) {
		return null;
	}

	$imageInfo = @getimagesize($file['tmp_name'][$index]);
	if ($imageInfo === false) {
		return null;
	}

	$extension = strtolower(pathinfo((string) $file['name'][$index], PATHINFO_EXTENSION));
	$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
	if (!in_array($extension, $allowedExtensions, true)) {
		return null;
	}

	$baseName = preg_replace('/[^a-z0-9]+/i', '-', pathinfo((string) $file['name'][$index], PATHINFO_FILENAME));
	$baseName = trim((string) $baseName, '-') ?: 'teamfoto';
	$fileName = sprintf('team-%s-%s.%s', strtolower($baseName), time(), $extension);
	$target = next_root_path('data/files/' . $fileName);

	if (!move_uploaded_file($file['tmp_name'][$index], $target)) {
		return null;
	}
	@chmod($target, 0777);
	return 'data/files/' . $fileName;
}

function next_clean_team_member(array $member): array
{
	$name = trim(strip_tags((string) ($member['name'] ?? '')));
	$image = trim(strip_tags((string) ($member['image'] ?? '')));
	$text = trim(strip_tags((string) ($member['text'] ?? '')));
	return [
		'name' => $name !== '' ? $name : 'Nieuw teamlid',
		'image' => $image !== '' ? $image : 'data/files/logo next.png',
		'text' => $text
	];
}

function next_team_image_options(string $selected): string
{
	$files = glob(next_root_path('data/files') . '/*.{png,jpg,jpeg,webp,gif}', GLOB_BRACE) ?: [];
	$options = '';
	foreach ($files as $file) {
		$value = 'data/files/' . basename($file);
		$isSelected = $value === $selected ? ' selected' : '';
		$options .= '<option value="' . next_html($value) . '"' . $isSelected . '>' . next_html(basename($file)) . '</option>';
	}
	return $options;
}

function next_clean_home(array $input): array
{
	$defaults = next_default_home();
	$home = [
		'hero_kicker' => next_plain_field((string) ($input['hero_kicker'] ?? $defaults['hero_kicker'])),
		'hero_title' => next_plain_field((string) ($input['hero_title'] ?? $defaults['hero_title'])),
		'intro_title' => next_plain_field((string) ($input['intro_title'] ?? $defaults['intro_title'])),
		'intro_text' => next_plain_field((string) ($input['intro_text'] ?? $defaults['intro_text'])),
		'intro_button_text' => next_plain_field((string) ($input['intro_button_text'] ?? $defaults['intro_button_text'])),
		'intro_button_link' => next_link_field((string) ($input['intro_button_link'] ?? $defaults['intro_button_link']), $defaults['intro_button_link']),
		'quote' => next_plain_field((string) ($input['quote'] ?? $defaults['quote'])),
		'about' => [],
		'cta_title' => next_plain_field((string) ($input['cta_title'] ?? $defaults['cta_title'])),
		'cta_text' => next_plain_field((string) ($input['cta_text'] ?? $defaults['cta_text'])),
		'cta_button_text' => next_plain_field((string) ($input['cta_button_text'] ?? $defaults['cta_button_text'])),
		'cta_button_link' => next_link_field((string) ($input['cta_button_link'] ?? $defaults['cta_button_link']), $defaults['cta_button_link'])
	];

	foreach ($defaults['about'] as $index => $defaultItem) {
		$item = $input['about'][$index] ?? [];
		$home['about'][] = [
			'letter' => $defaultItem['letter'],
			'title' => next_plain_field((string) ($item['title'] ?? $defaultItem['title'])),
			'text' => next_plain_field((string) ($item['text'] ?? $defaultItem['text']))
		];
	}

	return $home;
}

function next_handle_home_post(Wcms $Wcms): void
{
	if (!$Wcms->loggedIn || $Wcms->currentPage !== 'home' || !isset($_POST['next_home_action'], $_POST['token'])) {
		return;
	}
	if (!$Wcms->hashVerify((string) $_POST['token'])) {
		$Wcms->alert('danger', 'Home kon niet opgeslagen worden. Probeer opnieuw in te loggen.');
		return;
	}

	$home = next_clean_home(is_array($_POST['home'] ?? null) ? $_POST['home'] : []);
	if (next_save_home($home)) {
		$Wcms->alert('success', 'Home is opgeslagen.');
	} else {
		$Wcms->alert('danger', 'Home kon niet opgeslagen worden. Controleer of data/home.json schrijfbaar is.');
	}
	$Wcms->redirect(Wcms::url('home'));
}

function next_render_home_admin(array $home, string $token): string
{
	$output = '<section class="team-admin-panel home-admin-panel"><div class="section__inner"><h2>Home beheren</h2><form method="post">';
	$output .= '<input type="hidden" name="token" value="' . next_html($token) . '">';
	$output .= '<article class="team-admin-card"><h3>Intro bovenaan</h3>';
	$output .= '<label>Kleine tekst<input type="text" name="home[hero_kicker]" value="' . next_html((string) $home['hero_kicker']) . '"></label>';
	$output .= '<label>Grote titel<input type="text" name="home[hero_title]" value="' . next_html((string) $home['hero_title']) . '"></label>';
	$output .= '</article>';
	$output .= '<article class="team-admin-card"><h3>Intro sectie</h3>';
	$output .= '<label>Titel<textarea name="home[intro_title]" rows="2">' . next_html((string) $home['intro_title']) . '</textarea></label>';
	$output .= '<label>Tekst<textarea name="home[intro_text]" rows="5">' . next_html((string) $home['intro_text']) . '</textarea></label>';
	$output .= '<label>Knop tekst<input type="text" name="home[intro_button_text]" value="' . next_html((string) $home['intro_button_text']) . '"></label>';
	$output .= '<label>Knop link<input type="text" name="home[intro_button_link]" value="' . next_html((string) $home['intro_button_link']) . '"></label>';
	$output .= '</article>';
	$output .= '<article class="team-admin-card"><h3>Quote</h3>';
	$output .= '<label>Quote<textarea name="home[quote]" rows="2">' . next_html((string) $home['quote']) . '</textarea></label>';
	$output .= '</article>';
	$output .= '<article class="team-admin-card"><h3>NEXT blokken</h3>';
	foreach ($home['about'] as $index => $item) {
		$output .= '<label>' . next_html((string) $item['letter']) . ' titel<input type="text" name="home[about][' . $index . '][title]" value="' . next_html((string) $item['title']) . '"></label>';
		$output .= '<label>' . next_html((string) $item['letter']) . ' tekst<textarea name="home[about][' . $index . '][text]" rows="3">' . next_html((string) $item['text']) . '</textarea></label>';
	}
	$output .= '</article>';
	$output .= '<article class="team-admin-card"><h3>Contactblok</h3>';
	$output .= '<label>Titel<input type="text" name="home[cta_title]" value="' . next_html((string) $home['cta_title']) . '"></label>';
	$output .= '<label>Tekst<textarea name="home[cta_text]" rows="3">' . next_html((string) $home['cta_text']) . '</textarea></label>';
	$output .= '<label>Knop tekst<input type="text" name="home[cta_button_text]" value="' . next_html((string) $home['cta_button_text']) . '"></label>';
	$output .= '<label>Knop link<input type="text" name="home[cta_button_link]" value="' . next_html((string) $home['cta_button_link']) . '"></label>';
	$output .= '</article>';
	$output .= '<div class="team-admin-actions"><button class="button" type="submit" name="next_home_action" value="save">Home opslaan</button></div>';
	$output .= '</form></div></section>';
	return $output;
}

function next_render_multiline(string $value): string
{
	return nl2br(next_html($value), false);
}

function next_render_home_page(array $home, bool $loggedIn, string $token): string
{
	$output = $loggedIn ? next_render_home_admin($home, $token) : '';
	$output .= '<section class="section hero">';
	$output .= '<div class="section__inner">';
	$output .= '<p class="hero__kicker">' . next_html((string) $home['hero_kicker']) . '</p>';
	$output .= '<h1>' . next_html((string) $home['hero_title']) . '</h1>';
	$output .= '</div></section>';
	$output .= '<section class="section home-intro-section"><div class="section__inner split"><div class="intro-copy">';
	$output .= '<h2>' . next_render_multiline((string) $home['intro_title']) . '</h2>';
	$output .= '<p style="text-align: justify;">' . next_render_multiline((string) $home['intro_text']) . '</p>';
	$output .= '<div style="text-align: justify;"><a class="button" href="' . next_html((string) $home['intro_button_link']) . '">' . next_html((string) $home['intro_button_text']) . '</a></div>';
	$output .= '</div><img class="poster poster--plain" src="data/files/FotoHome.png" alt="De volgende stap op jouw tempo"></div></section>';
	$output .= '<section class="quote-band"><blockquote>“' . next_html((string) $home['quote']) . '”</blockquote></section>';
	$output .= '<section class="section home-next-section">';
	$output .= '<img class="decor decor--left" src="data/files/backgroundvisuals.png" alt="" aria-hidden="true">';
	$output .= '<img class="decor decor--right" src="data/files/backgroundvisuals.png" alt="" aria-hidden="true">';
	$output .= '<div class="section__inner home-next-inner"><div class="about-list">';
	foreach ($home['about'] as $item) {
		$output .= '<article class="about-item"><span class="about-letter">' . next_html((string) $item['letter']) . '</span><div><h2>' . next_html((string) $item['title']) . '</h2><p>' . next_render_multiline((string) $item['text']) . '</p></div></article>';
	}
	$output .= '</div></div></section>';
	$output .= '<section class="section section--blue section--tight cta cta--split"><div class="section__inner"><div>';
	$output .= '<h2>' . next_html((string) $home['cta_title']) . '</h2>';
	$output .= '<p>' . next_render_multiline((string) $home['cta_text']) . '</p>';
	$output .= '</div><a class="button" href="' . next_html((string) $home['cta_button_link']) . '">' . next_html((string) $home['cta_button_text']) . '</a></div></section>';
	$output .= '<section class="pattern-band" aria-hidden="true"></section>';
	return $output;
}

function next_handle_team_post(Wcms $Wcms): void
{
	if (!$Wcms->loggedIn || $Wcms->currentPage !== 'over-ons' || !isset($_POST['next_team_action'], $_POST['token'])) {
		return;
	}
	if (!$Wcms->hashVerify((string) $_POST['token'])) {
		$Wcms->alert('danger', 'Team kon niet opgeslagen worden. Probeer opnieuw in te loggen.');
		return;
	}

	$team = [];
	foreach ($_POST['team'] ?? [] as $member) {
		if (is_array($member)) {
			$index = count($team);
			$cleanMember = next_clean_team_member($member);
			$uploadedImage = next_upload_team_image($index);
			if ($uploadedImage !== null) {
				$cleanMember['image'] = $uploadedImage;
			}
			$team[] = $cleanMember;
		}
	}

	$action = (string) $_POST['next_team_action'];
	if ($action === 'add') {
		$team[] = ['name' => 'Nieuw teamlid', 'image' => 'data/files/logo next.png', 'text' => 'Schrijf hier de tekst voor dit teamlid.'];
	}
	if (str_starts_with($action, 'delete:')) {
		$deleteIndex = (int) substr($action, 7);
		unset($team[$deleteIndex]);
	}

	if (next_save_team($team)) {
		$Wcms->alert('success', 'Team is opgeslagen.');
	} else {
		$Wcms->alert('danger', 'Team kon niet opgeslagen worden. Controleer of data/team.json schrijfbaar is.');
	}
	$Wcms->redirect(Wcms::url('over-ons'));
}

function next_render_team_admin(array $team, string $token): string
{
	$output = '<section class="team-admin-panel"><div class="section__inner"><h2>Team beheren</h2><form method="post" enctype="multipart/form-data">';
	$output .= '<input type="hidden" name="token" value="' . next_html($token) . '">';
	foreach (array_values($team) as $index => $member) {
		$name = (string) ($member['name'] ?? '');
		$image = (string) ($member['image'] ?? '');
		$text = (string) ($member['text'] ?? '');
		$output .= '<article class="team-admin-card">';
		$output .= '<div class="team-admin-card__top"><h3>' . next_html($name !== '' ? $name : 'Nieuw teamlid') . '</h3><button class="team-admin-delete" type="submit" name="next_team_action" value="delete:' . $index . '" onclick="return confirm(\'Teamlid verwijderen?\')">Verwijderen</button></div>';
		$output .= '<label>Naam<input type="text" name="team[' . $index . '][name]" value="' . next_html($name) . '"></label>';
		$output .= '<div class="team-admin-photo">';
		$output .= '<img src="' . next_html($image !== '' ? $image : 'data/files/logo next.png') . '" alt="">';
		$output .= '<div><label>Huidige foto<select name="team[' . $index . '][image]">' . next_team_image_options($image) . '</select></label>';
		$output .= '<label>Nieuwe foto kiezen<input type="file" name="team_upload[' . $index . ']" accept="image/png,image/jpeg,image/webp,image/gif"></label></div>';
		$output .= '</div>';
		$output .= '<label>Tekst<textarea name="team[' . $index . '][text]" rows="6">' . next_html($text) . '</textarea></label>';
		$output .= '</article>';
	}
	$output .= '<div class="team-admin-actions"><button class="button" type="submit" name="next_team_action" value="save">Team opslaan</button><button class="button button--secondary" type="submit" name="next_team_action" value="add">Teamlid toevoegen</button></div>';
	$output .= '</form></div></section>';
	return $output;
}

function next_render_team_section(array $team, bool $loggedIn, string $token): string
{
	$output = $loggedIn ? next_render_team_admin($team, $token) : '';
	$output .= '<section class="section center team-section">';
	$output .= '<img class="decor decor--right" src="data/files/backgroundvisuals.png" alt="" aria-hidden="true">';
	$output .= '<div class="section__inner"><h2 class="section-title">Ons Team</h2><div class="team-list">';
	foreach ($team as $member) {
		$name = next_html((string) ($member['name'] ?? ''));
		$image = next_html((string) ($member['image'] ?? 'data/files/logo next.png'));
		$text = nl2br(next_html((string) ($member['text'] ?? '')));
		$output .= '<article class="team-member"><img src="' . $image . '" alt="' . $name . '"><div><h3>' . $name . '</h3><p>' . $text . '</p></div></article>';
	}
	$output .= '</div></div></section>';
	return $output;
}

function next_replace_team_section(string $content, array $team, bool $loggedIn, string $token): string
{
	$section = next_render_team_section($team, $loggedIn, $token);
	$updated = preg_replace('~<section class="section center team-section">.*?</section>~s', $section, $content, 1);
	return $updated ?? $content . $section;
}

next_handle_home_post($Wcms);
next_handle_team_post($Wcms);
$home = next_load_home();
$team = next_load_team();

$footerContactDefault = '
	<p class="footer-contact__item">
		<span class="footer-contact__icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" focusable="false"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
		</span>
		<span>raf@nextlier.be<br>debbie@nextlier.be<br>floor@nextlier.be</span>
	</p>
	<p class="footer-contact__item">
		<span class="footer-contact__icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" focusable="false"><path d="M12 21s7-5.2 7-12a7 7 0 0 0-14 0c0 6.8 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/></svg>
		</span>
		<span>Beukenlaan 16 - Nijlen</span>
	</p>
	<p class="footer-contact__item">
		<span class="footer-contact__icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" focusable="false"><path d="M6.6 3.8 4.5 5.9c-.8.8-.6 3.8 3.8 8.2s7.4 4.6 8.2 3.8l2.1-2.1-4-3-1.5 1.5c-1.5-.7-2.8-2-3.5-3.5L11.1 9l-4.5-5.2z"/></svg>
		</span>
		<span>0479/66.45.24</span>
	</p>';
$blocks = $Wcms->get('blocks');
$footerContactContent = isset($blocks->footerContact) ? $blocks->footerContact->content : $footerContactDefault;
$footerContact = $Wcms->loggedIn
	? $Wcms->editable('footerContact', $footerContactContent, 'blocks')
	: $footerContactContent;
$isLoginPage = $Wcms->currentPage === $Wcms->get('config', 'login') && !$Wcms->loggedIn;
?>

<!DOCTYPE html>
<html lang="<?= $Wcms->getSiteLanguage() ?>">
	<head>
		<!-- Encoding, browser compatibility, viewport -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- Search Engine Optimization (SEO) -->
		<meta name="title" content="<?= $Wcms->get('config', 'siteTitle') ?> - <?= $Wcms->page('title') ?>" />
		<meta name="description" content="<?= $Wcms->page('description') ?>">
		<meta name="keywords" content="<?= $Wcms->page('keywords') ?>">
		<meta property="og:url" content="<?= $this->url() ?>" />
		<meta property="og:type" content="website" />
		<meta property="og:site_name" content="<?= $Wcms->get('config', 'siteTitle') ?>" />
		<meta property="og:title" content="<?= $Wcms->page('title') ?>" />
		<meta name="twitter:site" content="<?= $this->url() ?>" />
		<meta name="twitter:title" content="<?= $Wcms->get('config', 'siteTitle') ?> - <?= $Wcms->page('title') ?>" />
		<meta name="twitter:description" content="<?= $Wcms->page('description') ?>" />

		<!-- Website and page title -->
		<title>
			<?= $Wcms->get('config', 'siteTitle') ?> - <?= $Wcms->page('title') ?>

		</title>

		<!-- Admin CSS -->
		<?= $Wcms->css() ?>
		
		<!-- Theme CSS -->
		<link rel="stylesheet" href="https://use.typekit.net/bdd6rtl.css">
		<link rel="stylesheet" href="<?= $Wcms->asset('css/style.css') ?>?v=5">
	</head>

	<body class="<?= $isLoginPage ? 'is-admin-login' : '' ?>">
		<!-- Admin settings panel and alerts -->
		<?= $Wcms->settings() ?>

		<?= $Wcms->alerts() ?>

		<?php if (!$isLoginPage): ?>
		<header class="site-header">
			<div class="site-header__inner">
				<a class="site-logo" href="<?= Wcms::url() ?>" aria-label="NEXT home">
					<img src="<?= Wcms::url('data/files/logo next.png') ?>" alt="NEXT">
				</a>

				<button class="menu-toggle" type="button" aria-label="Menu openen" aria-expanded="false" aria-controls="site-navigation">
					<span></span>
					<span></span>
					<span></span>
				</button>

				<nav id="site-navigation" class="site-nav" aria-label="Hoofdnavigatie">
					<ul class="menu">
						<?= $Wcms->menu() ?>
						<li class="nav-item nav-item--signup">
							<a class="nav-link" href="https://forms.office.com/pages/responsepage.aspx?id=rjKKHSBshUOmlb_S_QwCS2QvhAZCDiZJgMqH-8yv_l1UOE1XRFhOUVBISVc0QjlPMUpPWEYyS0hVUS4u&route=shorturl" target="_blank" rel="noopener">Aanmelden</a>
						</li>
					</ul>
				</nav>
			</div>
		</header>
		<?php endif; ?>

		<main id="wrapper" class="site-main">
			<?php
				$pageContent = $Wcms->page('content');
				if ($Wcms->currentPage === 'home') {
					$pageContent = next_render_home_page($home, $Wcms->loggedIn, $Wcms->getToken());
				} elseif ($Wcms->currentPage === 'over-ons') {
					$pageContent = next_replace_team_section($pageContent, $team, $Wcms->loggedIn, $Wcms->getToken());
				}
				echo $pageContent;
			?>
		</main>

		<?php if (!$isLoginPage): ?>
		<footer class="site-footer">
			<div class="site-footer__inner">
				<div class="footer-mark">
					<img src="<?= Wcms::url('data/XFooter.png') ?>" alt="" aria-hidden="true">
				</div>
				<div class="footer-col">
					<h2>Navigatie</h2>
					<ul>
						<li><a href="<?= Wcms::url() ?>">Home</a></li>
						<li><a href="<?= Wcms::url('werking') ?>">Werking</a></li>
						<li><a href="<?= Wcms::url('visie') ?>">Visie</a></li>
						<li><a href="<?= Wcms::url('voor-wie') ?>">Voor wie</a></li>
						<li><a href="<?= Wcms::url('over-ons') ?>">Over ons</a></li>
					</ul>
				</div>
				<div class="footer-col footer-contact">
					<h2>Contact</h2>
					<?= $footerContact ?>
				</div>
			</div>
			<div class="site-footer__bottom">&copy; 2026 NEXT</div>
		</footer>
		<?php endif; ?>

		<!-- Admin JavaScript. More JS libraries can be added below -->
		<?= $Wcms->js() ?>
		<script src="<?= $Wcms->asset('js/animations.js') ?>?v=3"></script>

	</body>
</html>
