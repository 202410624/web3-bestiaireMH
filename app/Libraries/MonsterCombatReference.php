<?php

namespace App\Libraries;

class MonsterCombatReference
{
    private const GAMES = [
        'MHWilds' => 'Monster Hunter Wilds',
        'MHRS' => 'Monster Hunter Rise: Sunbreak',
        'MHWI' => 'Monster Hunter World: Iceborne',
        'MHRise' => 'Monster Hunter Rise',
        'MHWorld' => 'Monster Hunter: World',
        'MHGU' => 'Monster Hunter Generations Ultimate',
        'MH4U' => 'Monster Hunter 4 Ultimate',
        'MH3U' => 'Monster Hunter 3 Ultimate',
        'MHFZ' => 'Monster Hunter Frontier Z',
        'MHP3' => 'Monster Hunter Portable 3rd',
        'MHFU' => 'Monster Hunter Freedom Unite',
        'MHF' => 'Monster Hunter Freedom',
        'MHST3' => 'Monster Hunter Stories 3: Twisted Reflection',
        'MHST' => 'Monster Hunter Stories',
        'MHO' => 'Monster Hunter Online',
        'MHXR' => 'Monster Hunter Explore',
        'MHOutlanders' => 'Monster Hunter Outlanders',
    ];

    private const ELEMENTS = ['fire', 'water', 'thunder', 'ice', 'dragon'];

    private static ?array $data = null;

    public static function forMonster(string $name, ?string $requestedGame): ?array
    {
        self::$data ??= json_decode(
            file_get_contents(APPPATH . 'Data/monster-combat.json'),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $profiles = self::$data[$name] ?? [];
        if ($profiles === []) {
            return null;
        }

        $games = array_intersect_key(self::GAMES, $profiles);
        $elementalGames = array_filter(
            $games,
            static fn (string $game): bool => isset($profiles[$game]['zones'])
                || isset($profiles[$game]['elementRatings'])
                || isset($profiles[$game]['elementResistances']),
            ARRAY_FILTER_USE_KEY,
        );
        $game = $requestedGame !== null && isset($games[$requestedGame])
            ? $requestedGame
            : array_key_first($elementalGames ?: $games);
        $profile = $profiles[$game];
        $zones = $profile['zones'] ?? [];
        $rank = isset($profile['rank'])
            ? implode(' · ', array_map(
                static fn (string $label): string => MonsterReference::label('ranks', $label),
                explode(' · ', $profile['rank']),
            ))
            : null;

        return [
            'games' => $games,
            'game' => $game,
            'rank' => $rank,
            'zoneCount' => count($zones),
            'elements' => self::elementAverages($zones),
            'zonesSource' => self::sourceUrl($profile['zonesSource'] ?? null),
            'elementRatings' => $profile['elementRatings'] ?? [],
            'elementRatingsSource' => self::sourceUrl($profile['elementRatingsSource'] ?? null),
            'elementRatingsByPart' => $profile['elementRatingsByPart'] ?? false,
            'elementResistances' => $profile['elementResistances'] ?? [],
            'elementResistancesSource' => self::sourceUrl($profile['elementResistancesSource'] ?? null),
            'ailments' => $profile['ailments'] ?? [],
            'ailmentsSource' => self::sourceUrl($profile['ailmentsSource'] ?? null),
            'ailmentResistances' => $profile['ailmentResistances'] ?? [],
            'ailmentResistancesSource' => self::sourceUrl($profile['ailmentResistancesSource'] ?? null),
            'ailmentRatings' => $profile['ailmentRatings'] ?? [],
            'ailmentRatingsSource' => self::sourceUrl($profile['ailmentRatingsSource'] ?? null),
        ];
    }

    public static function supportsGame(string $game): bool
    {
        return isset(self::GAMES[$game]);
    }

    private static function elementAverages(array $zones): array
    {
        if ($zones === []) {
            return [];
        }

        $averages = [];
        foreach (self::ELEMENTS as $column => $element) {
            $values = array_filter(array_column($zones, $column), 'is_int');
            if ($values !== []) {
                $sum = array_sum($values);
                $count = count($values);
                $averages[$element] = ['mean' => $sum / $count, 'integer' => intdiv($sum, $count)];
            }
        }
        uasort($averages, static fn (array $a, array $b): int => $b['mean'] <=> $a['mean']);

        return array_map(static fn (array $value): int => $value['integer'], $averages);
    }

    private static function sourceUrl(int|string|null $source): ?string
    {
        return is_int($source) ? 'https://monsterhunterwiki.org/index.php?oldid=' . $source : $source;
    }
}
