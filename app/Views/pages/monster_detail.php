<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$monsterName = esc($item['display_name'] ?? '');
$speciesName = esc($item['species_name'] ?? '');
$effectReference = $item['effect_reference'];
?>
<div class="d-flex justify-content-center align-items-center">
    <form class="d-flex tm-search-form" action="<?= site_url('photos') ?>" method="get">
        <input class="form-control tm-search-input" type="search" name="q" placeholder="<?= esc(lang('Site.search'), 'attr') ?>" aria-label="<?= esc(lang('Site.search'), 'attr') ?>">
        <button class="btn btn-outline-success tm-search-btn" type="submit" aria-label="<?= esc(lang('Site.search'), 'attr') ?>">
            <i class="fas fa-search" aria-hidden="true"></i>
        </button>
    </form>
</div>

<div class="container-fluid tm-container-content tm-mt-60">
    <div class="row mb-4">
        <h2 class="col-12 tm-text-primary"><?= $monsterName ?></h2>
    </div>
    <div class="row tm-mb-90">
        <div class="col-xl-8 col-lg-7 col-md-6 col-sm-12">
            <?php if ($image !== ''): ?>
                <img src="<?= esc(base_url('img/' . $image), 'attr') ?>" alt="<?= esc($item['display_name'], 'attr') ?>" class="img-fluid">
            <?php else: ?>
                <div class="tm-no-monster-image"><i class="fas fa-dragon" aria-hidden="true"></i><span><?= esc(lang('Site.imageMissing')) ?></span></div>
            <?php endif ?>
        </div>
        <div class="col-xl-4 col-lg-5 col-md-6 col-sm-12">
            <div class="tm-bg-gray tm-video-details">
                <dl id="suivi-chasse" class="row mb-3">
                    <dt class="col-7 tm-text-gray-dark"><?= esc(lang('Site.species')) ?></dt>
                    <dd class="col-5 tm-text-primary text-right"><?= $speciesName ?></dd>
                    <?php if ($hunt !== null): ?>
                        <dt class="col-7 tm-text-gray-dark"><?= esc(lang('Site.carts')) ?></dt>
                        <dd class="col-5 tm-text-primary text-right" id="hunt-carts"><?= $hunt['cart'] ?></dd>
                        <dt class="col-7 tm-text-gray-dark"><?= esc(lang('Site.hunted')) ?></dt>
                        <dd class="col-5 tm-text-primary text-right">
                            <strong id="hunt-results" title="<?= esc(lang('Site.huntSummary', [$hunt['kill'], $hunt['capture']]), 'attr') ?>"><?= $hunt['kill'] ?>(<?= $hunt['capture'] ?>)</strong>
                            <small class="d-block"><?= esc(lang('Site.huntFormat')) ?></small>
                        </dd>
                    <?php endif ?>
                </dl>
                <?php if ($hunt !== null): ?>
                    <form action="<?= site_url('monster/' . $id . '/hunt') ?>" method="post" class="d-flex flex-wrap mb-3">
                        <?= csrf_field() ?>
                        <?php if ($combat !== null): ?>
                            <input type="hidden" name="game" value="<?= esc($combat['game'], 'attr') ?>">
                        <?php endif ?>
                        <?php foreach ($huntActions as $action => $label): ?>
                            <button class="btn btn-sm btn-outline-primary mr-2 mb-2" type="submit" name="action" value="<?= esc($action, 'attr') ?>"><?= esc(lang('Site.' . $label)) ?></button>
                        <?php endforeach ?>
                    </form>
                <?php endif ?>
                <?= view('partials/monster_combat', ['combat' => $combat, 'id' => $id]) ?>
                <h3 class="tm-text-gray-dark mb-3"><?= esc(lang('Site.effectsInflicted')) ?></h3>
                <?php if ($effectReference === null): ?>
                    <p class="mb-0"><?= esc(lang('Site.effectsUndocumented')) ?></p>
                <?php elseif ($effectReference['effects'] === []): ?>
                    <p><?= esc(lang('Site.effectsNone')) ?></p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($effectReference['effects'] as $effect): ?>
                            <li>
                                <?= esc($effect['name']) ?>
                                <?php if ($effect['condition'] !== ''): ?>
                                    <small class="d-block"><?= esc($effect['condition']) ?></small>
                                <?php endif ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
                <?php if ($effectReference !== null): ?>
                    <p class="small mb-0"><a href="<?= esc($effectReference['source'], 'attr') ?>"><?= esc(lang('Site.effectsSource')) ?></a></p>
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <h2 class="col-12 tm-text-primary">
            <?= esc(lang('Site.similarMonsters')) ?>
        </h2>
    </div>
    <div class="row mb-3 tm-gallery">
        <?php
        $test = new \App\Models\Test();
        $sameSpecies = $test->getFilteredBySpecies((int) $item['speciesid']);
        ?>
        <?php foreach ($sameSpecies as $index => $monster): ?>
            <?php
            $img = $test->normalizeImage((string) $monster['icon']);
            ?>
            <?= view('partials/card', ['card' => [
                'image' => $img,
                'title' => $monster['display_name'],
                'link'  => site_url('monster/' . $monster['id']),
            ]]) ?>
        <?php endforeach; ?>
    </div> <!-- row -->
</div> <!-- container-fluid, tm-container-content -->
<?= $this->endSection() ?>
