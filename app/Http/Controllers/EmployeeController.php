<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    private const ROLES = [
        'admin' => 'Administrador',
        'sales' => 'Ventas',
        'production' => 'Produccion',
    ];

    public function index()
    {
        $employees = User::query()
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->paginate(10);

        return view('employees.index', [
            'employees' => $employees,
            'roles' => self::ROLES,
        ]);
    }

    public function create()
    {
        return view('employees.create', [
            'roles' => self::ROLES,
        ]);
    }

    public function store(Request $request)
    {
        User::create($this->validateEmployee($request));

        return redirect()
            ->route('employees.index')
            ->with('status', 'Empleado registrado correctamente.');
    }

    public function show(User $employee)
    {
        $this->ensureSameCompany($employee);

        return view('employees.show', [
            'employee' => $employee,
            'roles' => self::ROLES,
        ]);
    }

    public function edit(User $employee)
    {
        $this->ensureSameCompany($employee);

        return view('employees.edit', [
            'employee' => $employee,
            'roles' => self::ROLES,
        ]);
    }

    public function update(Request $request, User $employee)
    {
        $this->ensureSameCompany($employee);

        $employee->update($this->validateEmployee($request, $employee));

        return redirect()
            ->route('employees.index')
            ->with('status', 'Empleado actualizado correctamente.');
    }

    public function destroy(User $employee)
    {
        $this->ensureSameCompany($employee);

        $employee->update(['active' => false]);

        return redirect()
            ->route('employees.index')
            ->with('status', 'Empleado desactivado correctamente.');
    }

    public function toggleStatus(User $employee)
    {
        $this->ensureSameCompany($employee);

        $employee->update(['active' => ! $employee->active]);

        return redirect()
            ->route('employees.index')
            ->with('status', $employee->active ? 'Empleado activado correctamente.' : 'Empleado desactivado correctamente.');
    }

    private function validateEmployee(Request $request, ?User $employee = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($employee),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'in:' . implode(',', array_keys(self::ROLES))],
            'password' => [
                $employee ? 'nullable' : 'required',
                'string',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $data['active'] = $employee?->active ?? true;

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['company_id'] = auth()->user()->company_id;

        return $data;
    }

    private function ensureSameCompany(User $employee): void
    {
        abort_unless($employee->company_id === auth()->user()->company_id, 404);
    }
}
