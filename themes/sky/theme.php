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

next_handle_team_post($Wcms);
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
		<link rel="stylesheet" href="<?= $Wcms->asset('css/style.css') ?>">
	</head>

	<body>
		<!-- Admin settings panel and alerts -->
		<?= $Wcms->settings() ?>

		<?= $Wcms->alerts() ?>

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

		<main id="wrapper" class="site-main">
			<?php
				$pageContent = $Wcms->page('content');
				if ($Wcms->currentPage === 'over-ons') {
					$pageContent = next_replace_team_section($pageContent, $team, $Wcms->loggedIn, $Wcms->getToken());
				}
				echo $pageContent;
			?>
		</main>

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

		<!-- Admin JavaScript. More JS libraries can be added below -->
		<?= $Wcms->js() ?>
		<script src="<?= $Wcms->asset('js/animations.js') ?>?v=3"></script>

	</body>
</html>
