<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid tm-mt-60">
    <div class="row justify-content-center tm-mb-50">
        <div class="col-lg-4 col-12 mb-5">
            <h2 class="tm-text-primary mb-5"><?= esc(lang('Site.contact')) ?></h2>

            <form id="contact-form" action="<?= site_url('contact') ?>" method="POST" class="tm-contact-form mx-auto">
                <?= csrf_field() ?>
                <div class="form-group">
                    <input type="text" name="name" class="form-control rounded-0" placeholder="<?= esc(lang('Site.name'), 'attr') ?>" required />
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control rounded-0" placeholder="<?= esc(lang('Site.email'), 'attr') ?>" required />
                </div>
                <div class="form-group">
                    <textarea rows="8" name="message" class="form-control rounded-0" placeholder="<?= esc(lang('Site.message'), 'attr') ?>" required></textarea>
                </div>

                <div class="form-group tm-text-right">
                    <button type="submit" class="btn btn-primary"><?= esc(lang('Site.send')) ?></button>
                </div>
            </form>
        </div>
    </div>
</div> <!-- container-fluid, tm-container-content -->
<?= $this->endSection() ?>
