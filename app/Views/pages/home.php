<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$tableMonsterinfo = $tableMonsterinfo ?? [];
$pager            = $pager ?? null;
$searchQuery      = $searchQuery ?? '';
$currentPage      = $pager ? $pager->getCurrentPage('default') : 1;
$pageCount        = $pager ? max(1, $pager->getPageCount('default')) : 1;
?>
<div class="tm-hero d-flex justify-content-center align-items-center">
    <form class="d-flex tm-search-form" action="<?= site_url('photos') ?>" method="get">
        <input class="form-control tm-search-input" type="search" name="q" placeholder="<?= esc(lang('Site.monsterName'), 'attr') ?>"
                aria-label="<?= esc(lang('Site.monsterName'), 'attr') ?>" value="<?= esc($searchQuery, 'attr') ?>">
        <button class="btn btn-outline-success tm-search-btn" type="submit" aria-label="<?= esc(lang('Site.search'), 'attr') ?>">
            <i class="fas fa-search" aria-hidden="true"></i>
        </button>
    </form>
</div>
<div class="container-fluid tm-container-content tm-mt-60">
    <div class="mb-4">
        <h1 class="tm-text-primary"><?= esc(lang('Site.homeHeading')) ?></h1>
        <p><?= esc(lang('Site.homeIntro')) ?></p>
        <?php if ($pageCount > 1): ?>
            <p class="tm-text-gray"><?= esc(lang('Site.pageOf', [$currentPage, $pageCount])) ?></p>
        <?php endif ?>
    </div>
    <div class="row tm-mb-90 tm-gallery">
        <?php foreach ($tableMonsterinfo as $monsterInfo): ?>
            <?php
            // "/public/img/monsterimg/monstre-1.svg" : on le ramène sous "monsterimg/monstre-1".
            // On garde le sous-dossier monsterimg/ (présent sur le disque), card.php va préfixer "img/"
            // pour former "/public/img/monsterimg/monstre-1". On ne supprime que l'extension (.svg).
            $monsterIcons = preg_replace('#^/?public/img/#', '', (string) ($monsterInfo['icon'] ?? $monsterInfo['image'] ?? ''));
            $monsterIcons = preg_replace('#\.[^./]+$#', '', $monsterIcons);
            ?>
            <?= view('partials/card', ['card' => [
                'image' => $monsterIcons,
                'title' => $monsterInfo['display_name'],
                'link'  => site_url('monster/' . $monsterInfo['id']),
            ]]) ?>
        <?php endforeach; ?>
    </div> <!-- row -->
    <?php if ($tableMonsterinfo === []): ?>
        <p class="tm-text-gray mb-5"><?= esc(lang('Site.noResults')) ?></p>
    <?php endif; ?>
    <?= $pager ? $pager->links('default', 'bestiaire') : '' ?>
</div> <!-- container-fluid, tm-container-content -->
<?= $this->endSection() ?>
