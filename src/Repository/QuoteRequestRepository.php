<?php

namespace App\Repository;

use App\Model\QuoteRequest;
use PDO;
use InvalidArgumentException;

class QuoteRequestRepository 
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function save(QuoteRequest $quoteRequest): int
    {
        $sql = '
            INSERT INTO quote_requests (
                user_id,
                company,
                email,
                sector,
                quantity,
                status,
                message
            ) VALUES (
                :user_id,
                :company,
                :email,
                :sector,
                :quantity,
                :status,
                :message
            )
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'user_id' => $quoteRequest->getUserId(),
            'company' => $quoteRequest->getCompany(),
            'email' => $quoteRequest->getEmail(),
            'sector' => $quoteRequest->getSector(),
            'quantity' => $quoteRequest->getQuantity(),
            'status' => 'new',
            'message' => $quoteRequest->getMessage(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findAll(): array
    {
        $sql = '
            SELECT * FROM quote_requests  
            ORDER BY created_at DESC
        ';

        $stmt = $this->pdo->query($sql);

       $rows = $stmt->fetchAll();

       return array_map(
        fn (array $row): QuoteRequest => $this->mapToQuoteRequest($row), $rows);
    }

    public function findByUserId(int $user_id): array
    {
        $sql = '
            SELECT * FROM quote_requests 
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);

        $rows = $stmt->fetchAll();

        return array_map(
            fn (array $rows): QuoteRequest => $this->mapToQuoteRequest($rows), 
            $rows
        );
    }

    public function findByIdAndUserId(int $id, int $userId): ?QuoteRequest
    {
        $sql = '
            SELECT *
            FROM quote_requests
            WHERE id = :id
            AND user_id = :user_id
            LIMIT 1
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->mapToQuoteRequest($row);
    }

    public function update(QuoteRequest $quoteRequest): void
    {
        if ($quoteRequest->getId() === null) {
            throw new InvalidArgumentException('ID necessario per aggiornare una richiesta di preventivo.');
        }

        $sql = '
            UPDATE quote_requests 
            SET 
                company = :company,
                email = :email,
                sector = :sector,
                quantity = :quantity,
                message = :message,
                updated_at = NOW()
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'company' => $quoteRequest->getCompany(),
            'email' => $quoteRequest->getEmail(),
            'sector' => $quoteRequest->getSector(),
            'quantity' => $quoteRequest->getQuantity(),
            'message' => $quoteRequest->getMessage(),
            'id' => $quoteRequest->getId(),
        ]);
    }

    public function updateStatus(int $id, string $status): void
    {
        $sql = '
            UPDATE quote_requests 
            SET 
                status = :status,
                updated_at = NOW()
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'status' => $status,
            'id' => $id
        ]);
    }

    public function updateFromUser(int $id, int $userId, int $quantity, string $message): void 
    {
        $sql = '
            UPDATE quote_requests
            SET
                quantity = :quantity,
                message = :message,
                updated_at = NOW()
            WHERE id = :id
            AND user_id = :user_id
            AND status IN ("new", "in_progress")
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
            'quantity' => $quantity,
            'message' => $message,
        ]);
    }

    public function cancelFromUser(int $id, int $userId): void
    {
        $sql = '
            UPDATE quote_requests
            SET
                status = "cancelled",
                updated_at = NOW()
            WHERE id = :id
            AND user_id = :user_id
            AND status IN ("new", "in_progress")
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);
    }

    public function softDelete(int $id): void
    {
        $sql = '
            UPDATE quote_requests 
            SET 
                status = "archived",
                deleted_at = NOW(),
                updated_at = NOW() 
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public function restore(int $id): void
    {
        $sql = '
            UPDATE quote_requests 
            SET 
                status = "new",
                deleted_at = NULL,
                updated_at = NOW() 
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public function delete(int $id): void
    {
        $sql = '
            DELETE FROM quote_requests 
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    private function mapToQuoteRequest(array $data): QuoteRequest
    {
        return new QuoteRequest(
            userId: (int) $data['user_id'],
            company: $data['company'],
            email: $data['email'],
            sector: $data['sector'],
            quantity: (int) $data['quantity'],
            status: $data['status'],
            message: $data['message'],
            id: (int) $data['id'],
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at'],
            deletedAt: $data['deleted_at']
        );
    }
}