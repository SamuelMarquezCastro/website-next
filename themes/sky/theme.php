<?php
global $Wcms;

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

				<nav class="site-nav" aria-label="Hoofdnavigatie">
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
			<?= $Wcms->page('content') ?>
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
		<script src="<?= $Wcms->asset('js/animations.js') ?>?v=2"></script>

	</body>
</html>
