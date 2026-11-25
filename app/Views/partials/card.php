<?php
/**
 * Carte de galerie (photo, vidéo ou monstre).
 *
 * `date` et `views` sont optionnels (non fournis pour les monstres).
 *
 * @var array{image: string, title: string, link: string, date?: string, views?: string} $card
 */
$cardDate  = $card['date'] ?? '';
$cardViews = $card['views'] ?? '';
?>
<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 mb-5">
    <figure class="effect-ming tm-video-item">
        <?php if ($card['image'] !== ''): ?>
            <img src="<?= esc(base_url('img/' . $card['image']), 'attr') ?>" alt="<?= esc($card['title'], 'attr') ?>" class="img-fluid">
        <?php else: ?>
            <div class="tm-no-monster-image" aria-hidden="true"><i class="fas fa-dragon"></i></div>
        <?php endif ?>
        <figcaption class="d-flex align-items-center justify-content-center">
            <h2><?= esc($card['title']) ?></h2>
            <a href="<?= esc($card['link'], 'attr') ?>" aria-label="<?= esc(lang('Site.viewMonster', [$card['title']]), 'attr') ?>"><?= esc(lang('Site.viewMore')) ?></a>
        </figcaption>
    </figure>
    <?php if ($cardDate !== '' || $cardViews !== ''): ?>
    <div class="d-flex justify-content-between tm-text-gray">
        <span class="tm-text-gray-light"><?= esc($cardDate) ?></span>
        <span><?= esc(lang('Site.viewCount', [$cardViews])) ?></span>
    </div>
    <?php endif; ?>
</div>
