<?php

namespace App\Repositories\Eloquent;

use App\Filters\AdminFilter;
use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminRepository implements AdminRepositoryInterface
{
    public function create(array $data): Admin
    {
        return Admin::create($data);
    }

    public function update(Admin $admin, array $data): Admin
    {
        $admin->update($data);

        return $admin->fresh();
    }

    public function find(int $id): ?Admin
    {
        return Admin::find($id);
    }

    public function paginate(AdminFilter $filter, int $perPage = 20): LengthAwarePaginator
    {
        $query = Admin::query()->latest();

        return $filter->apply($query)->paginate($perPage);
    }
}