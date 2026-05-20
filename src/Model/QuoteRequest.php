<?php

namespace App\Model;

class QuoteRequest 
{
    public function __construct(
        private int $userId,
        private string $company,
        private string $email,
        private string $sector,
        private int $quantity,
        private string $status,
        private string $message,
        private ?int $id = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null,
        private ?string $deletedAt = null
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getEmail(): string{
        return $this->email;
    }

    public function getSector(): string
    {
        return $this->sector;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deletedAt;
    }

    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setSector(string $sector): void
    {
        $this->sector = $sector;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setDeletedAt(string $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function getSizeLabel(): string 
    {
        if ($this->quantity < 100) {
            return 'Piccola';
        } elseif ($this->quantity <= 150) {
            return 'Media';
        } else {
            return 'Grande';
        }
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'new' => 'Nuova',
            'in_progress' => 'In lavorazione',
            'completed' => 'Evasa',
            'archived' => 'Archiviata',
            'to_restore' => 'Ripristina',
            'cancelled' => 'Eliminata',
            default => 'Sconosciuto',
        };
    }
}