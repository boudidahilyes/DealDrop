<?php

namespace App\Entity;

use App\Repository\SupportTicketCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupportTicketCategoryRepository::class)]
class SupportTicketCategory extends Category
{
    #[ORM\OneToMany(mappedBy: 'supportTicketCategory', targetEntity: SupportTicket::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, SupportTicket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(SupportTicket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setSupportTicketCategory($this);
        }

        return $this;
    }

    public function removeTicket(SupportTicket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getSupportTicketCategory() === $this) {
                $ticket->setSupportTicketCategory(null);
            }
        }

        return $this;
    }
}
