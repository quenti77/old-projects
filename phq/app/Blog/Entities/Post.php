<?php

namespace App\Blog\Entities;

use DateTime;
use PHQ\Database\Entity;

/**
 * Interface IPostRepository
 * @package App\Blog\Repositories
 */
class Post extends Entity
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var string $content
     */
    private $content;

    /**
     * @var DateTime $createdAt
     */
    private $createdAt;

    /**
     * @var DateTime $updatedAt
     */
    private $updatedAt;
}
