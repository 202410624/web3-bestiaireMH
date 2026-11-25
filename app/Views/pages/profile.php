<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
use App\Libraries\MonsterReference;

$rankings = [
    'hunts' => [
        'title' => lang('Site.mostHunted'),
        'totalLabel' => lang('Site.totalHunted'),
        'empty' => lang('Site.noHunts'),
        'rows' => $mostHunted,
    ],
    'carts' => [
        'title' => lang('Site.mostCarts'),
        'totalLabel' => lang('Site.carts'),
        'empty' => lang('Site.noCarts'),
        'rows' => $mostCarts,
    ],
];
?>
<main class="container py-5">
    <h1 class="tm-text-primary mb-2"><?= esc(lang('Site.profile')) ?></h1>
    <p class="mb-4"><?= esc($hunterName) ?></p>
    <div class="row">
        <?php foreach ($rankings as $key => $ranking): ?>
            <section class="col-12 col-lg-6 mb-4" aria-labelledby="ranking-<?= $key ?>">
                <h2 class="h4 tm-text-primary mb-3" id="ranking-<?= $key ?>"><?= esc($ranking['title']) ?></h2>
                <?php if ($ranking['rows'] === []): ?>
                    <p><?= esc($ranking['empty']) ?></p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm tm-table">
                            <thead>
                                <tr>
                                    <th scope="col"><?= esc(lang('Site.rank')) ?></th>
                                    <th scope="col"><?= esc(lang('Site.monster')) ?></th>
                                    <th scope="col" class="text-right"><?= esc($ranking['totalLabel']) ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ranking['rows'] as $index => $monster): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <th scope="row">
                                            <a href="<?= site_url('monster/' . $monster['monster_id']) ?>"><?= esc(MonsterReference::name($monster['name'])) ?></a>
                                            <?php if ($key === 'hunts'): ?>
                                                <small class="d-block font-weight-normal"><?= esc(lang('Site.huntBreakdown', [(int) $monster['kills'], (int) $monster['captures']])) ?></small>
                                            <?php endif ?>
                                        </th>
                                        <td class="text-right"><?= (int) $monster['total'] ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif ?>
            </section>
        <?php endforeach ?>
    </div>
</main>
<?= $this->endSection() ?>
