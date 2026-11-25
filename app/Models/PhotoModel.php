<?php

namespace App\Models;

/**
 * Modèle (MVC) gérant les données des photos du Bestiaire.
 * Source de données statique (pas de base de données requise).
 */
class PhotoModel
{
    private const ENGLISH_TITLES = [
        'Horloges' => 'Clocks',
        'Plantes' => 'Plants',
        'Matin' => 'Morning',
        'Abstrait' => 'Abstract',
        'Fleurs' => 'Flowers',
        'Rosée' => 'Dew',
        'Roche' => 'Rock',
        'Violet' => 'Purple',
        'Mer' => 'Sea',
        'Tortue' => 'Turtle',
        'Paix' => 'Peace',
    ];

    protected array $items = [
        ['id' => 1, 'image' => 'img-01.jpg', 'title' => 'Gore-magala', 'date' => '24 Oct 2020', 'views' => '10 460'],
        ['id' => 2, 'image' => 'img-02.jpg', 'title' => 'Zorah-Magdaros', 'date' => '22 Oct 2020', 'views' => '14 502'],
        ['id' => 3, 'image' => 'img-03.jpg', 'title' => 'Horloges', 'date' => '18 Oct 2020', 'views' => '11 906'],
        ['id' => 4, 'image' => 'img-04.jpg', 'title' => 'Plantes', 'date' => '12 Oct 2020', 'views' => '16 100'],
        ['id' => 5, 'image' => 'img-05.jpg', 'title' => 'Matin', 'date' => '24 Sep 2020', 'views' => '16 008'],
        ['id' => 6, 'image' => 'img-06.jpg', 'title' => 'Rose', 'date' => '21 Sep 2020', 'views' => '12 860'],
        ['id' => 7, 'image' => 'img-07.jpg', 'title' => 'Bus', 'date' => '18 Sep 2020', 'views' => '10 900'],
        ['id' => 8, 'image' => 'img-08.jpg', 'title' => 'New York', 'date' => '14 Sep 2020', 'views' => '10 820'],
        ['id' => 9, 'image' => 'img-09.jpg', 'title' => 'Abstrait', 'date' => '12 Sep 2020', 'views' => '42 700'],
        ['id' => 10, 'image' => 'img-10.jpg', 'title' => 'Fleurs', 'date' => '9 Sep 2020', 'views' => '11 402'],
        ['id' => 11, 'image' => 'img-11.jpg', 'title' => 'Rosée', 'date' => '8 Sep 2020', 'views' => '32 906'],
        ['id' => 12, 'image' => 'img-12.jpg', 'title' => 'Roche', 'date' => '6 Sep 2020', 'views' => '50 700'],
        ['id' => 13, 'image' => 'img-13.jpg', 'title' => 'Violet', 'date' => '28 Aug 2020', 'views' => '107 510'],
        ['id' => 14, 'image' => 'img-14.jpg', 'title' => 'Mer', 'date' => '24 Aug 2020', 'views' => '118 006'],
        ['id' => 15, 'image' => 'img-15.jpg', 'title' => 'Tortue', 'date' => '20 Aug 2020', 'views' => '121 300'],
        ['id' => 16, 'image' => 'img-16.jpg', 'title' => 'Paix', 'date' => '14 Aug 2020', 'views' => '21 204'],
    ];

    public function all(): array
    {
        return array_map($this->localize(...), $this->items);
    }

    public function find(int $id): ?array
    {
        foreach ($this->items as $item) {
            if ($item['id'] === $id) {
                return $this->localize($item);
            }
        }
        return null;
    }

    public function related(int $excludeId = 0, int $limit = 8): array
    {
        $result = [];
        foreach ($this->items as $item) {
            if ($item['id'] === $excludeId) {
                continue;
            }
            $result[] = $this->localize($item);
            if (count($result) >= $limit) {
                break;
            }
        }
        return $result;
    }

    public function search(string $query): array
    {
        $query = mb_strtolower(trim($query));
        if ($query === '') {
            return $this->all();
        }
        $matches = array_filter($this->items, function (array $item) use ($query): bool {
            return str_contains(mb_strtolower($item['title']), $query)
                || str_contains(mb_strtolower($this->localize($item)['title']), $query);
        });
        return array_values(array_map($this->localize(...), $matches));
    }

    private function localize(array $item): array
    {
        if (service('language')->getLocale() === 'en') {
            $item['title'] = self::ENGLISH_TITLES[$item['title']] ?? $item['title'];
        }
        return $item;
    }
}
