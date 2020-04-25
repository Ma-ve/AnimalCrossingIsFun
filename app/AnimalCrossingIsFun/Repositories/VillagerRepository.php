<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories;

use DateTime;
use Exception;
use Mave\AnimalCrossingIsFun\Dto\Villager as VillagerDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\BaseRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

/**
 * @method getAll()
 */
class VillagerRepository extends BaseRepository implements IRepository {

    /**
     * @var string
     */
    protected $dto = VillagerDto::class;

    /**
     * @param DateTime $dt
     *
     * @return VillagerDto[]
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

    /**
     * @return $this
     * @throws Exception
     */
    public function loadAll() {
        $this->contents = $this->databaseService->loadFromDatabase('villagers');

        return $this;
    }

}