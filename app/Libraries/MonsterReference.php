<?php

namespace App\Libraries;

class MonsterReference
{
    private static ?array $data = null;

    public static function name(string $original): string
    {
        return service('language')->getLocale() === 'fr'
            ? self::data()['names'][$original] ?? $original
            : $original;
    }

    public static function matchingOriginalNames(string $query): array
    {
        $search = self::normalize($query);
        if ($search === '') {
            return [];
        }

        $matching = array_filter(
            self::data()['names'],
            static fn (string $name): bool => str_contains(self::normalize($name), $search),
        );

        return array_keys($matching);
    }

    public static function effects(string $original): ?array
    {
        $record = self::data()['monsters'][$original] ?? null;
        if ($record === null) {
            return null;
        }

        // Un numéro identifie une révision du wiki. Les autres sources fournissent leur URL.
        [$source, $recordedEffects] = $record;
        $english = service('language')->getLocale() === 'en';
        $effects = array_map(static fn (array $effect): array => [
            'name' => self::label('effects', $effect[0]),
            'condition' => $english ? self::label('conditions', $effect[1] ?? '') : $effect[1] ?? '',
        ], $recordedEffects);

        return [
            'effects' => $effects,
            'source' => is_int($source) ? 'https://monsterhunterwiki.org/index.php?oldid=' . $source : $source,
        ];
    }

    public static function label(string $group, string $original): string
    {
        $labels = lang('MonsterCatalog.' . $group);

        return is_array($labels) ? ($labels[$original] ?? $original) : $original;
    }

    private static function normalize(string $name): string
    {
        $normalized = \Normalizer::normalize(mb_strtolower(str_replace('’', "'", $name)), \Normalizer::FORM_D);

        return preg_replace('/\p{Mn}+/u', '', $normalized ?: '') ?? '';
    }

    private static function data(): array
    {
        return self::$data ??= json_decode(
            file_get_contents(APPPATH . 'Data/monster-reference.json'),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
    }
}
