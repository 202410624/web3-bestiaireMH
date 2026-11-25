<?php

namespace App\Models;

use App\Libraries\MonsterReference;
use CodeIgniter\Model;

class Test extends Model
{
	protected $table         = 'monsterinfos';
	protected $primaryKey    = 'id';
	protected $allowedFields = ['nameid', 'speciesid'];

	/**
	 * Requête de base : infos du monstre + son nom (jointure sur `monstername`).
	 * L'ordre est explicite pour que la pagination (LIMIT/OFFSET) reste stable.
	 */
	private function monstersWithNames()
	{
		return $this->select('monsterinfos.*, monstername.name, monsterspecies.name AS species_name')
			->join('monstername', 'monstername.id = monsterinfos.nameid')
			->join('monsterspecies', 'monsterspecies.id = monsterinfos.speciesid')
			->orderBy('monsterinfos.id', 'ASC');
	}

	/**
	 * Infos complètes d'un monstre pour sa fiche (nom + espèce) + ses effets & faiblesses.
	 * Les noms du monstre et de l'espèce ont la même colonne `name` : on les référence
	 * toujours par leur table (`monstername.name`, `monsterspecies.name`) dans la vue.
	 *
	 * @return array{monsterinfos: array, monster_name: string, species_name: string,
	 *                speciesid: int, nameid: int, image: string, icon: string,
	 *                display_name: string, effect_reference: ?array} | null
	 */
	public function findMonster(int $id): ?array
	{
		$monster = $this->select('monsterinfos.*, monstername.name AS monster_name, monsterspecies.name AS species_name')
			->join('monstername', 'monstername.id = monsterinfos.nameid')
			->join('monsterspecies', 'monsterspecies.id = monsterinfos.speciesid')
			->where('monsterinfos.id', $id)
			->first();

		if ($monster === null) {
			return null;
		}

		return array_merge((array) $monster, [
			'display_name' => MonsterReference::name($monster['monster_name']),
			'species_name' => MonsterReference::label('species', $monster['species_name']),
			'effect_reference' => MonsterReference::effects($monster['monster_name']),
		]);
	}

	/**
	 * Ramène un chemin de la base ("​/public/img/...") au nom de fichier servi sur le disque.
	 * DB : "/public/img/monsterimg/monstre-661.svg" -> "monsterimg/monstre-661"
	 * (on garde le sous-dossier, on ne supprime que l'extension absente sur le disque).
	 */
	public function normalizeImage(string $path): string
	{
		$path = preg_replace('#^/?public/img/#', '', $path);
		$path = preg_replace('#\.[^./]+$#', '', $path);
		return $path;
	}

	// Toutes les infos avec le nom du monstre (sans pagination)
	public function getMonstersWithNames()
	{
		return $this->monstersWithNames()->findAll();
	}

	/**
	 * Version paginée : le Pager de CodeIgniter calcule le total puis applique
	 * LIMIT/OFFSET en SQL. L'état de la pagination est ensuite disponible dans
	 * `$model->pager` pour la vue.
	 *
	 * @param int      $perPage    Nombre de monstres par page
	 * @param string   $group      Groupe de pagination CI4. Le groupe `default`
	 *                             utilise ?page=N (les autres groupes utiliseraient ?page_<groupe>=N).
	 * @param int|null $speciesId  Filtre optionnel sur l'espèce
	 * @param string   $search     Recherche optionnelle sur le nom du monstre
	 */
	public function paginateMonsters(int $perPage = 36, string $group = 'default', ?int $speciesId = null, string $search = ''): array
	{
		$query = $this->monstersWithNames();

		if ($speciesId !== null) {
			$query = $query->where('monsterinfos.speciesid', $speciesId);
		}

		if ($search !== '') {
			$localizedMatches = MonsterReference::matchingOriginalNames($search);
			$query->groupStart()->like('monstername.name', $search);
			if ($localizedMatches !== []) {
				$query->orWhereIn('monstername.name', $localizedMatches);
			}
			$query->groupEnd();
		}

		return array_map($this->localizeMonster(...), $query->paginate($perPage, $group));
	}

	// Filtre par espèce, sans pagination
	public function getFilteredBySpecies($speciesId)
	{
		$monsters = $this->monstersWithNames()
			->where('monsterinfos.speciesid', $speciesId)
			->findAll();
		return array_map($this->localizeMonster(...), $monsters);
	}

	public function findForManagement(int $id): ?array
	{
		$monster = $this->monstersWithNames()->where('monsterinfos.id', $id)->first();

		return $monster === null ? null : $this->localizeMonster($monster);
	}

	public function speciesOptions(): array
	{
		$species = $this->db->table('monsterspecies')->orderBy('name')->get()->getResultArray();
		return array_map(static function (array $row): array {
			$row['name'] = MonsterReference::label('species', $row['name']);
			return $row;
		}, $species);
	}

	private function localizeMonster(array $monster): array
	{
		$monster['display_name'] = MonsterReference::name($monster['name']);
		$monster['species_name'] = MonsterReference::label('species', $monster['species_name']);

		return $monster;
	}

	public function saveMonster(string $name, int $speciesId, ?int $id = null): bool
	{
		if (! $this->db->transStart()) {
			return false;
		}

		$names = $this->db->table('monstername');
		if ($id === null) {
			$names->insert(['name' => $name]);
			$saved = $this->db->transStatus() && $this->insert([
				'nameid' => $this->db->insertID(),
				'speciesid' => $speciesId,
			], false);
			return $this->db->transComplete() && $saved;
		}

		$nameId = $this->lockMonsterName($id);
		if ($nameId === null) {
			$this->db->transComplete();
			return false;
		}

		$currentName = $names->where('id', $nameId)->get();
		if ($currentName !== false && $currentName->getRowArray()['name'] !== $name) {
			if ($this->builder()->where('nameid', $nameId)->where('id !=', $id)->countAllResults() > 0) {
				$nameId = $this->copyMonsterName($nameId, $name);
			} else {
				$names->where('id', $nameId)->update(['name' => $name]);
			}
		}

		$saved = $this->db->transStatus() && $this->update($id, ['nameid' => $nameId, 'speciesid' => $speciesId]);
		return $this->db->transComplete() && $saved;
	}

	public function deleteMonster(int $id): bool
	{
		if (! $this->db->transStart()) {
			return false;
		}

		$nameId = $this->lockMonsterName($id);
		$deleted = $nameId !== null && $this->delete($id);
		if ($deleted && $this->builder()->where('nameid', $nameId)->countAllResults() === 0) {
			$this->db->table('monstereffect')->where('monsterid', $nameId)->delete();
			$this->db->table('monsterweakness')->where('monsterid', $nameId)->delete();
			$this->db->table('monstername')->where('id', $nameId)->delete();
		}

		return $this->db->transComplete() && $deleted;
	}

	private function lockMonsterName(int $id): ?int
	{
		$monsters = $this->db->protectIdentifiers($this->table, true);
		$names = $this->db->protectIdentifiers('monstername', true);
		$row = $this->db->query(
			"SELECT m.nameid FROM {$monsters} m JOIN {$names} n ON n.id = m.nameid WHERE m.id = ? FOR UPDATE",
			[$id],
		);
		$monster = $row ? $row->getRowArray() : null;

		return $monster === null ? null : (int) $monster['nameid'];
	}

	private function copyMonsterName(int $sourceId, string $name): int
	{
		if (! $this->db->table('monstername')->insert(['name' => $name])) {
			return $sourceId;
		}

		$nameId = (int) $this->db->insertID();
		// Les effets et les faiblesses suivent la clé de monstername.
		foreach (['monstereffect', 'monsterweakness'] as $table) {
			$builder = $this->db->table($table);
			$rows = $builder->where('monsterid', $sourceId)->get();
			if ($rows === false) {
				break;
			}
			$rows = $rows->getResultArray();
			foreach ($rows as &$row) {
				$row['monsterid'] = $nameId;
			}
			unset($row);
			if ($rows !== [] && $builder->insertBatch($rows) === false) {
				break;
			}
		}

		return $nameId;
	}
}
