<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Menampilkan daftar pengguna
    public function index(Request $request)
    {
        $query = User::with('warehouse')->latest();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    // Form tambah pengguna
    public function create()
    {
        $warehouses = Warehouse::orderBy('warehouse_name')->get();

        return view('users.create', compact('warehouses'));
    }

    // Menyimpan pengguna baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => [
                'required',
                Rule::in([
                    'super_admin',
                    'admin_gudang',
                    'supervisor',
                    'petugas_fg',
                ]),
            ],
            'warehouse_id' => [
                'nullable',
                'exists:warehouses,id',
            ],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    // Form edit pengguna
    public function edit(User $user)
    {
        $warehouses = Warehouse::orderBy('warehouse_name')->get();

        return view('users.edit', compact('user', 'warehouses'));
    }

    // Memperbarui pengguna
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'role' => [
                'required',
                Rule::in([
                    'super_admin',
                    'admin_gudang',
                    'supervisor',
                    'petugas_fg',
                ]),
            ],
            'warehouse_id' => [
                'nullable',
                'exists:warehouses,id',
            ],
        ]);

        // Mencegah Super Admin mengubah role akunnya sendiri
        if ($user->id === auth()->id()) {
            $validated['role'] = $user->role;
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    // Menghapus pengguna
    public function destroy(User $user)
    {
        // Mencegah penghapusan akun sendiri
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Mencegah penghapusan Super Admin aktif terakhir
        if (
            $user->role === 'super_admin' &&
            $user->is_active &&
            User::where('role', 'super_admin')
                ->where('is_active', true)
                ->count() <= 1
        ) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Tidak dapat menghapus Super Admin aktif terakhir.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    // Mengaktifkan atau menonaktifkan pengguna
    public function toggleStatus(User $user)
    {
        // Mencegah Super Admin menonaktifkan akunnya sendiri
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        // Mencegah penonaktifan Super Admin aktif terakhir
        if (
            $user->role === 'super_admin' &&
            $user->is_active &&
            User::where('role', 'super_admin')
                ->where('is_active', true)
                ->count() <= 1
        ) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Tidak dapat menonaktifkan Super Admin aktif terakhir.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active
            ? 'diaktifkan'
            : 'dinonaktifkan';

        return redirect()
            ->route('users.index')
            ->with('success', "Akun pengguna berhasil {$status}.");
    }
}
