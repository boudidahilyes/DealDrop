<?php

namespace App\Entity;

use App\Repository\ReminderRepository;
use DateInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $reminderDate = null;

    #[ORM\OneToOne(inversedBy: 'reminder')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Auction $auction = null;

    #[ORM\ManyToMany(targetEntity: Member::class, inversedBy: 'reminders')]
    private Collection $members;

    public function __construct(Auction $auction)
    {
        $this->members = new ArrayCollection();
        $this->auction = $auction;
        $this->reminderDate = date_sub($auction->getStartDate(), new DateInterval('PT5M'));
        $this->status = "waiting";
    }

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

    public function getReminderDate(): ?\DateTimeInterface
    {
        return $this->reminderDate;
    }

    public function setReminderDate(\DateTimeInterface $reminderDate): static
    {
        $this->reminderDate = $reminderDate;

        return $this;
    }

    public function getAuction(): ?Auction
    {
        return $this->auction;
    }

    public function setAuction(Auction $auction): static
    {
        $this->auction = $auction;

        return $this;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(Member $member): static
    {
        $this->members->removeElement($member);

        return $this;
    }
}
