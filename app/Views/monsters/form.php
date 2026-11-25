<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$action = $monster === null ? 'ajouter' : $monster['id'] . '/modifier';
$values = session()->getFlashdata('monsterInput') ?? [];
?>
<main class="container py-5">
    <h1 class="tm-text-primary mb-4"><?= esc($title) ?></h1>
    <form action="<?= site_url('gestion/monstres/' . $action) ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="monster-name"><?= esc(lang('Site.name')) ?></label>
            <input class="form-control" id="monster-name" name="name" maxlength="100" value="<?= esc($values['name'] ?? $monster['display_name'] ?? '', 'attr') ?>" required>
        </div>
        <div class="form-group">
            <label for="monster-species"><?= esc(lang('Site.species')) ?></label>
            <select class="form-control" id="monster-species" name="speciesid" required>
                <option value=""><?= esc(lang('Site.chooseSpecies')) ?></option>
                <?php foreach ($species as $option): ?>
                    <option value="<?= (int) $option['id'] ?>"<?= (int) ($values['speciesid'] ?? $monster['speciesid'] ?? 0) === (int) $option['id'] ? ' selected' : '' ?>>
                        <?= esc($option['name']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <button class="btn btn-primary" type="submit"><?= esc(lang('Site.save')) ?></button>
        <a class="btn btn-link" href="<?= site_url('gestion/monstres') ?>"><?= esc(lang('Site.cancel')) ?></a>
    </form>
</main>
<?= $this->endSection() ?>
