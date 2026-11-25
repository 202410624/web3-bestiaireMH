<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<main class="container py-5">
    <h1 class="tm-text-primary mb-4"><?= esc(lang('Site.deleteMonster')) ?></h1>
    <p><?= esc(lang('Site.confirmDeleteText', [$monster['display_name']])) ?></p>
    <p><?= esc(lang('Site.deleteRelated')) ?></p>
    <form action="<?= site_url('gestion/monstres/' . $monster['id'] . '/supprimer') ?>" method="post">
        <?= csrf_field() ?>
        <button class="btn btn-danger" type="submit"><?= esc(lang('Site.confirmDelete')) ?></button>
        <a class="btn btn-link" href="<?= site_url('gestion/monstres') ?>"><?= esc(lang('Site.cancel')) ?></a>
    </form>
</main>
<?= $this->endSection() ?>
