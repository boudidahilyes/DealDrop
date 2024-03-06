<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Chat $chat = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?member $sender = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChat(): ?chat
    {
        return $this->chat;
    }

    public function setChat(?chat $chat): static
    {
        $this->chat = $chat;

        return $this;
    }

    public function getSender(): ?member
    {
        return $this->sender;
    }

    public function setSender(?member $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
