<?php

namespace App\Entity;

use App\Repository\ReminderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReminderRepository::class)]
class Reminder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeImmutable $reminderTime = null;

    #[ORM\ManyToOne(inversedBy: 'reminders',cascade: ['persist', 'remove'])]
    private ?Member $owner = null;

    #[ORM\ManyToOne(inversedBy: 'reminders',cascade: ['persist', 'remove'])]
    private ?Auction $auction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getReminderTime(): ?\DateTimeImmutable
    {
        return $this->reminderTime;
    }

    public function setReminderTime(\DateTimeImmutable $reminderTime): static
    {
        $this->reminderTime = $reminderTime;

        return $this;
    }

    public function getOwner(): ?Member
    {
        return $this->owner;
    }

    public function setOwner(?Member $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getAuction(): ?Auction
    {
        return $this->auction;
    }

    public function setAuction(?Auction $auction): static
    {
        $this->auction = $auction;

        return $this;
    }
}
