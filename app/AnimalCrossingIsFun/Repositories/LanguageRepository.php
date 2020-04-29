<?php

declare(strict_types=1);

namespace Mave\AnimalCrossingIsFun\Repositories;

use Mave\AnimalCrossingIsFun\Dto\Language as LanguageDto;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\BaseRepository;
use Mave\AnimalCrossingIsFun\Repositories\Collectibles\Interfaces\IRepository;

class LanguageRepository extends BaseRepository implements IRepository {

    /**
     * @var string
     */
    protected $dto = LanguageDto::class;

    /**
     * @var string
     */
    protected $indexColumn = 'langCode';

    /**
     * @return static
     */
    public function loadAll() {
        $this->contents = $this->getLanguages();

        return $this;
    }

    /**
     * @return string[][]
     */
    private function getLanguages() {
        return [
            [
                'langCode' => 'en',
                'label'    => 'English',
            ],
            [
                'langCode' => 'fr',
                'label'    => 'French',
            ],
            [
                'langCode' => 'de',
                'label'    => 'German',
            ],
            [
                'langCode' => 'es',
                'label'    => 'Spanish',
            ],
            [
                'langCode' => 'it',
                'label'    => 'Italian',
            ],
            [
                'langCode' => 'nl',
                'label'    => 'Dutch',
            ],
            [
                'langCode' => 'pt',
                'label'    => 'Portuguese',
            ],
            [
                'langCode' => 'ru',
                'label'    => 'Russian',
            ],
            [
                'langCode' => 'jp',
                'label'    => 'Japanese',
            ],
            [
                'langCode' => 'zh-hant',
                'label'    => 'Traditional Chinese',
            ],
            [
                'langCode' => 'zh-hans',
                'label'    => 'Simplified Chinese',
            ],
            [
                'langCode' => 'ko',
                'label'    => 'Korean',
            ],
        ];
    }
}