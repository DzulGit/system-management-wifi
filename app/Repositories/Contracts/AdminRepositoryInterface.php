<?php

namespace App\Repositories\Contracts;

use App\Filters\AdminFilter;
use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminRepositoryInterface
{
    public function create(array $data): Admin;

    public function update(Admin $admin, array $data): Admin;

    public function find(int $id): ?Admin;

    public function paginate(AdminFilter $filter, int $perPage = 20): LengthAwarePaginator;
}