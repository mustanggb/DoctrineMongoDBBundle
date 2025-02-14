<?php

declare(strict_types=1);

namespace Doctrine\Bundle\MongoDBBundle\Tests\Fixtures\DataCollector;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MongoDB\BSON\ObjectId;

/** @ODM\Document */
class Category
{
    /** @ODM\Id */
    protected ?ObjectId $id = null;

    /** @ODM\Field(type="string") */
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
