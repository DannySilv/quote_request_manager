<?php

namespace App\Repository;

use App\Model\User;
use PDO;
use InvalidArgumentException;

class UserRepository 
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function save(User $user): int
    {
        $sql = '
            INSERT INTO users (
                email,
                password_hash,
                is_admin
            ) VALUES (
                :email,
                :password_hash,
                :is_admin
            )
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'email' => $user->getEmail(),
            'password_hash' => $user->getPasswordHash(),
            'is_admin' => $user->isAdmin() ? 1 : 0,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function findAll(): array
    {
        $sql = '
            SELECT * FROM users  
            ORDER BY created_at DESC
        ';

        $stmt = $this->pdo->query($sql);

       $rows = $stmt->fetchAll();

       return array_map(
        fn (array $row): User => $this->mapToUser($row), $rows);
    }

    public function findById(int $id): ?User
    {
        $sql = '
            SELECT * FROM users 
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->mapToUser($row);
    }

    public function findByEmail(string $email): ?User
    {
        $sql = '
            SELECT * FROM users 
            WHERE email = :email
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->mapToUser($row);
    }

    public function emailExistsForOtherUser(string $email, int $userId): bool
    {
        $sql = '
            SELECT COUNT(*)
            FROM users
            WHERE email = :email
            AND id != :id
            AND deleted_at IS NULL
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'email' => $email,
            'id' => $userId,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function updateEmail(int $id, string $email): void
    {
        if ($id === null) {
            throw new InvalidArgumentException('ID necessario per aggiornare una richiesta di preventivo.');
        }

        $sql = '
            UPDATE users SET 
                email = :email,
                updated_at = NOW()
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'email' => $email,
            'id' => $id
        ]);
    }

    public function updatePassword(int $id, string $passwordHash): void
    {
        if ($id === null) {
            throw new InvalidArgumentException('ID necessario per aggiornare una richiesta di preventivo.');
        }

        $sql = '
            UPDATE users SET 
                password_hash = :password_hash,
                updated_at = NOW()
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'password_hash' => $passwordHash,
            'id' => $id
        ]);
    }

    public function softDelete(int $id): void
    {
        $sql = '
            UPDATE users 
            SET 
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
            UPDATE users 
            SET 
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
            DELETE FROM users 
            WHERE id = :id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }

    public function emailExists(string $email): bool
{
    $sql = '
        SELECT COUNT(*) 
        FROM users 
        WHERE email = :email
    ';

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['email' => $email]);

    return (int) $stmt->fetchColumn() > 0;
}

    private function mapToUser(array $data): User
    {
        return new User(
            email: $data['email'],
            passwordHash: $data['password_hash'],
            isAdmin: (bool) $data['is_admin'],
            id: (int) $data['id'],
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at'],
            deletedAt: $data['deleted_at']
        );
    }
}