<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories\Collectibles;

use DateTime;
use Exception;
use Mave\AnimalCrossingIsFun\Dto\Villager as VillagerDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

class VillagerRepository extends BaseRepository implements IRepository {

    /**
     * @param string $name
     *
     * @return bool|mixed
     */
    public function get(string $name) {
        $search = array_search($name, array_map(function($name) {
            return str_replace(" ", '-', strtolower($name));
        }, array_column($this->contents, 'name')));

        if(false === $search) {
            return false;
        }

        return new VillagerDto($this->contents[$search]);
    }

    /**
     * @param DateTime $dt
     *
     * @return \Mave\AnimalCrossingIsFun\Dto\Dto[]
     */
    public function getByNearbyBirthdates(DateTime $dt) {
        $this->contents = array_filter($this->contents, function($villager) use ($dt) {
            $diff = DateTime::createFromFormat('m-d', $villager['date'])
                ->diff($dt);
            if($diff->invert) {
                return $diff->days < 7;
            }

            return false;
        });
        usort($this->contents, function($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        return parent::map(new VillagerDto());
    }

    public function getAll(): array {
        if(empty($this->contents)) {
            throw new Exception('Must first load data into repository');
        }
        return parent::map(new VillagerDto());
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $this->contents = $this->databaseService->loadFromDatabase('villagers');

        return $this;
    }

}