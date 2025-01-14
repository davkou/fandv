<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

class Fruit
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     * @Assert\GreaterThanOrEqual(0)
     * @Groups({"fruit:read", "fruit:write"})
     */
    private int $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Groups({"fruit:read", "fruit:write"})
     */
    private string $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     * @Assert\GreaterThan(0)
     * @Groups({"fruit:read", "fruit:write"})
     */
    private int $grams;

    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGrams(): int
    {
        return $this->grams;
    }

    public function setGrams(int $grams): self
    {
        $this->grams = $grams;

        return $this;
    }
}
