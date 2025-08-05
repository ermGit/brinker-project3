<?php

namespace App\Entity;

use App\Repository\LoyaltyRewardRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoyaltyRewardRepository::class)]
class LoyaltyReward
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $reward = [];

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getReward(): array
    {
        return $this->reward;
    }

    /**
     * @param array $reward
     * @return $this
     */
    public function setReward(array $reward): static
    {
        $this->reward = $reward;

        return $this;
    }

    /**
     * @return string
     */
    public function getRewardName(): string
    {
        return $this->reward['name'] ?? 'Unnamed Reward';
    }
}
