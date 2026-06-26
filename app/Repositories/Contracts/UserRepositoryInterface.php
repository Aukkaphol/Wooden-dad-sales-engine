<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function create(array $attributes): User;

    public function find(string $id): ?User;

    public function findByEmail(string $email): ?User;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function update(User $user, array $attributes): User;

    public function markLastLogin(User $user): User;

    public function delete(User $user): bool;
}
