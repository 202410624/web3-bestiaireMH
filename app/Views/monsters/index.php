<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<main class="container py-5">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <h1 class="tm-text-primary mb-2"><?= esc(lang('Site.management')) ?></h1>
        <a class="btn btn-primary mb-2" href="<?= site_url('gestion/monstres/ajouter') ?>"><?= esc(lang('Site.addMonster')) ?></a>
    </div>
    <form class="mb-4" action="<?= site_url('gestion/monstres') ?>" method="get">
        <label for="monster-search"><?= esc(lang('Site.searchByName')) ?></label>
        <div class="input-group">
            <input class="form-control" id="monster-search" name="q" type="search" value="<?= esc($search, 'attr') ?>">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit"><?= esc(lang('Site.search')) ?></button>
            </div>
        </div>
    </form>

    <?php if ($monsters === []): ?>
        <p><?= esc(lang('Site.noMonsters')) ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?= esc(lang('Site.name')) ?></th>
                        <th scope="col"><?= esc(lang('Site.species')) ?></th>
                        <th scope="col"><?= esc(lang('Site.actions')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monsters as $monster): ?>
                        <tr>
                            <th scope="row">
                                <a href="<?= site_url('monster/' . $monster['id']) ?>"><?= esc($monster['display_name']) ?></a>
                            </th>
                            <td><?= esc($monster['species_name']) ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary mr-2" href="<?= site_url('gestion/monstres/' . $monster['id'] . '/modifier') ?>"><?= esc(lang('Site.edit')) ?></a>
                                <a class="btn btn-sm btn-outline-danger" href="<?= site_url('gestion/monstres/' . $monster['id'] . '/supprimer') ?>"><?= esc(lang('Site.delete')) ?></a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <?= $pager->links('default', 'bestiaire') ?>
    <?php endif ?>
</main>
<?= $this->endSection() ?>
