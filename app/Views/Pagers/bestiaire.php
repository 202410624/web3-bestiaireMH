<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * Vue de pagination du bestiaire.
 * Reprend la maquette du template : .tm-btn-prev / .tm-paging / .tm-btn-next.
 *
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);

$pageCount  = $pager->getPageCount();
$current    = $pager->getCurrentPageNumber();
$firstShown = $pager->getFirstPageNumber();
$lastShown  = $pager->getLastPageNumber();
?>
<?php if ($pageCount > 1): ?>
<div class="row tm-mb-90">
    <div class="col-12 d-flex justify-content-between align-items-center tm-paging-col">
        <?php if ($pager->hasPreviousPage()): ?>
            <a href="<?= $pager->getPreviousPage() ?>" class="btn btn-primary tm-btn-prev mb-2" rel="prev"><?= esc(lang('Site.previous')) ?></a>
        <?php else: ?>
            <span class="btn btn-primary tm-btn-prev mb-2 disabled" aria-disabled="true"><?= esc(lang('Site.previous')) ?></span>
        <?php endif; ?>

        <div class="tm-paging d-flex">
            <?php if ($firstShown > 1): ?>
                <a href="<?= $pager->getFirst() ?>" class="tm-paging-link">1</a>
                <?php if ($firstShown > 2): ?>
                    <span class="tm-paging-link" aria-hidden="true">…</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php foreach ($pager->links() as $link): ?>
                <a href="<?= $link['uri'] ?>"
                   class="tm-paging-link<?= $link['active'] ? ' active' : '' ?>"
                   <?= $link['active'] ? 'aria-current="page"' : '' ?>><?= $link['title'] ?></a>
            <?php endforeach; ?>

            <?php if ($lastShown < $pageCount): ?>
                <?php if ($lastShown < $pageCount - 1): ?>
                    <span class="tm-paging-link" aria-hidden="true">…</span>
                <?php endif; ?>
                <a href="<?= $pager->getLast() ?>" class="tm-paging-link"><?= $pageCount ?></a>
            <?php endif; ?>
        </div>

        <?php if ($pager->hasNextPage()): ?>
            <a href="<?= $pager->getNextPage() ?>" class="btn btn-primary tm-btn-next" rel="next"><?= esc(lang('Site.next')) ?></a>
        <?php else: ?>
            <span class="btn btn-primary tm-btn-next disabled" aria-disabled="true"><?= esc(lang('Site.next')) ?></span>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
