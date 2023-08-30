<?php

namespace Twofingers\Location\Internal;

use Bitrix\Main\Web\Json;

trait HasCollectionTrait
{
    /** @var Collection  */
    protected $collection;


    /**
     * @return Collection
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->getCollection()->getCollection();
    }

    /**
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     */
    public function toJson(): string
    {
        return Json::encode($this->toArray());
    }
}