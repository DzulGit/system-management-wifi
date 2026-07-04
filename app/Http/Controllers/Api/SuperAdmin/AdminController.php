<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Filters\AdminFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\SimpanAdminRequest;
use App\Http\Requests\SuperAdmin\UbahAdminRequest;
use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private readonly AdminRepositoryInterface $adminRepository,
    ) {}

    public function index(AdminFilter $filter)
    {
        return response()->json([
            'data' => $this->adminRepository->paginate($filter),
        ]);
    }

    public function show(Admin $admin)
    {
        return response()->json(['data' => $admin]);
    }

    public function store(SimpanAdminRequest $request, Request $httpRequest)
    {
        $data = $request->validated();
        $data['dibuat_oleh'] = $httpRequest->user()->id;

        $admin = $this->adminRepository->create($data);

        return response()->json(['data' => $admin], 201);
    }

    public function update(UbahAdminRequest $request, Admin $admin)
    {
        $admin = $this->adminRepository->update($admin, $request->validated());

        return response()->json(['data' => $admin]);
    }

    public function nonaktifkan(Admin $admin)
    {
        $admin = $this->adminRepository->update($admin, ['status_aktif' => false]);

        return response()->json(['data' => $admin, 'message' => 'Admin berhasil dinonaktifkan.']);
    }
}