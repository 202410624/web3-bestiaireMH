<?php
use App\Libraries\MonsterReference;

$hasHitzones = ! empty($combat['elements']);
$hasThresholds = ! empty($combat['ailments']);
$hasAilmentRatings = ! empty($combat['ailmentRatings']);
$ailmentHeading = $hasThresholds ? 'Site.initialThreshold' : 'Site.publishedResistance';
$ailmentValues = $combat['ailmentResistances'] ?? [];
$ailmentSource = $combat['ailmentsSource'] ?? $combat['ailmentResistancesSource'] ?? null;
$ailmentSourceLabel = $hasThresholds ? 'Site.ailmentsSource' : 'Site.ailmentResistancesSource';
if (! $hasThresholds && $hasAilmentRatings) {
    $ailmentHeading = 'Site.elementEffectiveness';
    $ailmentValues = array_map(
        static fn (string $rating): string => MonsterReference::label('effectiveness', $rating),
        $combat['ailmentRatings'],
    );
    $ailmentSource = $combat['ailmentRatingsSource'];
    $ailmentSourceLabel = 'Site.ailmentRatingsSource';
}
$elementValues = $combat['elements'] ?? [];
$elementHeading = 'Site.averageHitzone';
$elementSource = $combat['zonesSource'] ?? null;
$elementSourceLabel = 'Site.elementalSource';
if (! $hasHitzones && ! empty($combat['elementResistances'])) {
    $elementValues = $combat['elementResistances'];
    $elementHeading = 'Site.elementResistance';
    $elementSource = $combat['elementResistancesSource'];
    $elementSourceLabel = 'Site.elementResistancesSource';
} elseif (! $hasHitzones && ! empty($combat['elementRatings'])) {
    $elementValues = array_map(
        static fn (string $rating): string => MonsterReference::label('effectiveness', $rating),
        $combat['elementRatings'],
    );
    $elementHeading = $combat['elementRatingsByPart'] ? 'Site.bestPartEffectiveness' : 'Site.elementEffectiveness';
    $elementSource = $combat['elementRatingsSource'];
    $elementSourceLabel = 'Site.elementRatingsSource';
}
?>

<?php if ($combat !== null && count($combat['games']) > 1): ?>
    <form action="<?= site_url('monster/' . $id) ?>" method="get" class="mb-4">
        <label for="combat-game"><?= esc(lang('Site.combatGame')) ?></label>
        <div class="input-group">
            <select class="form-control" id="combat-game" name="game">
                <?php foreach ($combat['games'] as $code => $name): ?>
                    <option value="<?= esc($code, 'attr') ?>"<?= $code === $combat['game'] ? ' selected' : '' ?>><?= esc($name) ?></option>
                <?php endforeach ?>
            </select>
            <div class="input-group-append">
                <button type="submit" class="btn btn-outline-secondary"><?= esc(lang('Site.showCombatGame')) ?></button>
            </div>
        </div>
    </form>
<?php elseif ($combat !== null): ?>
    <p class="mb-3"><?= esc($combat['games'][$combat['game']]) ?></p>
<?php endif ?>

<?php if (isset($combat['rank'])): ?>
    <p class="small mb-3"><?= esc(lang('Site.combatRank', [$combat['rank']])) ?></p>
<?php endif ?>

<section class="mb-4" aria-labelledby="elemental-weaknesses">
    <h3 class="tm-text-gray-dark mb-3" id="elemental-weaknesses"><?= esc(lang('Site.elementalWeaknesses')) ?></h3>
    <?php if ($elementValues === []): ?>
        <p><?= esc(lang('Site.elementalUnavailable')) ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm tm-table">
                <thead>
                    <tr>
                        <th scope="col"><?= esc(lang('Site.element')) ?></th>
                        <th scope="col" class="text-right"><?= esc(lang($elementHeading)) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($elementValues as $element => $value): ?>
                        <tr>
                            <th scope="row"><?= esc(MonsterReference::label('elements', $element)) ?></th>
                            <td class="text-right"><?= esc((string) $value) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <?php if ($hasHitzones): ?>
            <p class="small mb-2"><?= esc(lang('Site.elementalAverageMethod', [$combat['zoneCount']])) ?></p>
        <?php elseif (! empty($combat['elementResistances'])): ?>
            <p class="small mb-2"><?= esc(lang('Site.elementResistanceHelp')) ?></p>
        <?php endif ?>
        <p class="small mb-0"><a href="<?= esc($elementSource, 'attr') ?>"><?= esc(lang($elementSourceLabel)) ?></a></p>
    <?php endif ?>
</section>

<section class="mb-4" aria-labelledby="ailment-susceptibility">
    <h3 class="tm-text-gray-dark mb-3" id="ailment-susceptibility"><?= esc(lang('Site.ailmentSusceptibility')) ?></h3>
    <?php if (! $hasThresholds && $ailmentValues === []): ?>
        <p><?= esc(lang('Site.ailmentsUnavailable')) ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm tm-table">
                <thead>
                    <tr>
                        <th scope="col"><?= esc(lang('Site.ailment')) ?></th>
                        <th scope="col" class="text-right"><?= esc(lang($ailmentHeading)) ?></th>
                        <?php if ($hasThresholds): ?>
                            <th scope="col" class="text-right"><?= esc(lang('Site.increase')) ?></th>
                        <?php endif ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($hasThresholds): ?>
                        <?php foreach ($combat['ailments'] as [$ailment, $threshold, $increase]): ?>
                            <tr>
                                <th scope="row"><?= esc(MonsterReference::label('effects', $ailment)) ?></th>
                                <td class="text-right"><?= $threshold === 'immune' ? esc(lang('Site.ailmentImmune')) : $threshold ?></td>
                                <td class="text-right"><?= $increase === null ? '—' : $increase ?></td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <?php foreach ($ailmentValues as $ailment => $value): ?>
                            <tr>
                                <th scope="row"><?= esc(MonsterReference::label('effects', $ailment)) ?></th>
                                <td class="text-right"><?= esc((string) $value) ?></td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
        <?php if ($hasThresholds): ?>
            <p class="small mb-2"><?= esc(lang('Site.ailmentThresholdHelp')) ?></p>
        <?php endif ?>
        <p class="small mb-0"><a href="<?= esc($ailmentSource, 'attr') ?>"><?= esc(lang($ailmentSourceLabel)) ?></a></p>
    <?php endif ?>
</section>
