<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class BusinessSettingController extends Controller
{
    public function edit()
    {
        return view('settings.edit', [
            'settings' => BusinessSetting::query()->first() ?? new BusinessSetting([
                'business_name' => 'Negocio de personalizados',
                'currency' => 'MXN',
                'work_start_time' => '09:00',
                'work_end_time' => '18:00',
                'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'overtime_hours' => 0,
                'overtime_end_time' => '18:00',
                'marketing_unit_cost_percentage' => 0,
                'taxes_percentage' => 0,
                'contingency_fund_percentage' => 0,
                'platform_commission_percentage' => 0,
                'payment_gateway_percentage' => 0,
                'profit_percentage' => 0,
            ]),
            'expenseConcepts' => $this->expenseConcepts(),
            'weekDays' => $this->weekDays(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'currency' => ['required', 'string', 'size:3'],
            'work_start_time' => ['required', 'date_format:H:i'],
            'work_end_time' => ['required', 'date_format:H:i'],
            'working_days' => ['required', 'array', 'min:1'],
            'working_days.*' => ['required', 'in:' . implode(',', array_keys($this->weekDays()))],
            'overtime_end_time' => ['required', 'date_format:H:i'],
            'marketing_unit_cost_percentage' => ['required', 'numeric', 'min:0'],
            'taxes_percentage' => ['required', 'numeric', 'min:0'],
            'contingency_fund_percentage' => ['required', 'numeric', 'min:0'],
            'platform_commission_percentage' => ['required', 'numeric', 'min:0'],
            'payment_gateway_percentage' => ['required', 'numeric', 'min:0'],
            'profit_percentage' => ['required', 'numeric', 'min:0'],
        ]);
        $data['overtime_hours'] = $this->hoursBetween($data['work_end_time'], $data['overtime_end_time']);

        $settings = BusinessSetting::query()->first() ?? new BusinessSetting([
            'company_id' => auth()->user()->company_id,
        ]);

        $settings->fill($data);
        $settings->save();

        return redirect()
            ->route('settings.edit')
            ->with('status', 'Configuracion actualizada correctamente.');
    }

    private function expenseConcepts(): array
    {
        return [
            'marketing_unit_cost_percentage' => 'Costo unitario de marketing',
            'taxes_percentage' => 'Impuestos',
            'contingency_fund_percentage' => 'Fondo imprevisto',
            'platform_commission_percentage' => 'Comision de plataforma',
            'payment_gateway_percentage' => 'Pasarelas de pago',
            'profit_percentage' => 'Utilidad',
        ];
    }

    private function weekDays(): array
    {
        return [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miercoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sabado',
            'sunday' => 'Domingo',
        ];
    }

    private function hoursBetween(string $start, string $end): float
    {
        [$startHours, $startMinutes] = array_map('intval', explode(':', $start));
        [$endHours, $endMinutes] = array_map('intval', explode(':', $end));

        $minutes = ($endHours * 60 + $endMinutes) - ($startHours * 60 + $startMinutes);

        if ($minutes < 0) {
            $minutes += 24 * 60;
        }

        return round($minutes / 60, 2);
    }

}
