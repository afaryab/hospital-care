<?php

namespace App\Services\Search;

use App\Helpers\PiiHasher;
use App\Models\Appointment;
use App\Models\Closing;
use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Backs the command palette. Interprets the query by its shape (record
 * number prefix, CNIC, phone, or free text) and returns only records the
 * user is allowed to view.
 */
class GlobalSearchService
{
    private const PER_GROUP = 6;

    /**
     * @return array<int, array{group: string, title: string, subtitle: string|null, url: string}>
     */
    public function search(User $user, string $query): array
    {
        $query = trim($query);
        $upper = strtoupper($query);

        if (mb_strlen($query) < 2) {
            return [];
        }

        $results = match (true) {
            str_starts_with($upper, 'PS/') => $this->byPsPrefix($user, $upper),
            str_starts_with($upper, 'TR/') => $this->transactions($user, Transaction::query()->where('tr_number', 'like', "{$upper}%")),
            str_starts_with($upper, 'CT/') => $this->closings($user, Closing::query()->where('ct_number', 'like', "{$upper}%")),
            str_starts_with($upper, 'VC/') => $this->vouchers($user, ExpenseVoucher::query()->where('vc_number', 'like', "{$upper}%")),
            str_starts_with($upper, 'APT/') => $this->appointments($user, Appointment::query()->where('appointment_number', 'like', "{$upper}%")),
            (bool) preg_match('/^[A-Z]{2,5}\/\d+$/', $upper) => $this->serviceOrders($user, ServiceOrder::query()->where('so_short', 'like', "{$upper}%")),
            (bool) preg_match('/^\d{5}-?\d{7}-?\d$/', $query) => $this->patients($user, Patient::query()->where('cnic_hash', PiiHasher::cnic($this->formatCnic($query)))),
            (bool) preg_match('/^[\d\s+()-]{10,}$/', $query) => $this->patients($user, Patient::query()->where('contact_hash', PiiHasher::contact($query))),
            default => $this->freeText($user, $query),
        };

        return $results->values()->all();
    }

    private function byPsPrefix(User $user, string $upper): Collection
    {
        if (mb_strlen($upper) > 17) {
            return $this->serviceOrders($user, ServiceOrder::query()->where('so_number', 'like', "{$upper}%"));
        }

        return $this->patients($user, Patient::query()->where('ps_number', 'like', "{$upper}%"));
    }

    private function freeText(User $user, string $query): Collection
    {
        $patients = $this->patients($user, Patient::query()
            ->where(fn (Builder $q) => $q->where('name', 'like', "%{$query}%")->orWhere('ps_number', 'like', "%{$query}%"))
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', ["{$query}%"]));

        $serviceOrders = $this->serviceOrders($user, ServiceOrder::query()
            ->where(fn (Builder $q) => $q->where('so_number', 'like', "%{$query}%")->orWhere('so_short', 'like', "%{$query}%")));

        return $patients->concat($serviceOrders);
    }

    private function patients(User $user, Builder $query): Collection
    {
        return $this->visible($user, $query->latest('id'))
            ->map(fn (Patient $patient): array => [
                'group' => 'Patients',
                'title' => $patient->name,
                'subtitle' => $patient->ps_number,
                'url' => route('patients-register-ps-number', [
                    'year' => $patient->year,
                    'month' => $patient->month,
                    'number' => $patient->number,
                ], false),
            ]);
    }

    private function serviceOrders(User $user, Builder $query): Collection
    {
        return $this->visible($user, $query->with(['patient:id,name,ps_number', 'service:id,name'])->latest('id'))
            ->filter(fn (ServiceOrder $order): bool => $order->patient !== null && $order->year !== null)
            ->map(fn (ServiceOrder $order): array => [
                'group' => 'Service orders',
                'title' => trim(($order->service?->name ?? $order->type).' — '.$order->patient->name),
                'subtitle' => $order->so_number,
                'url' => route('patients-register-ps-number-department-service', [
                    'year' => $order->year,
                    'month' => $order->month,
                    'number' => $order->number,
                    'departmentKey' => $order->department_key,
                    'serviceNumber' => $order->serviceNumber,
                ], false),
            ]);
    }

    private function transactions(User $user, Builder $query): Collection
    {
        return $this->visible($user, $query->with('patient:id,name')->latest('id'))
            ->flatMap(function (Transaction $transaction) use ($user): array {
                $params = [
                    'tYear' => $transaction->year,
                    'tMonth' => $transaction->month,
                    'tDay' => $transaction->day,
                    'tNumber' => $transaction->number,
                ];

                $items = [[
                    'group' => 'Transactions',
                    'title' => $transaction->tr_number,
                    'subtitle' => trim($transaction->income_or_expense.' · '.number_format((float) $transaction->amount).($transaction->patient ? ' · '.$transaction->patient->name : '')),
                    'url' => route('transaction-view', $params, false),
                ]];

                if ($transaction->closing_id && $user->can('update', $transaction)) {
                    $items[] = [
                        'group' => 'Transactions',
                        'title' => 'Edit '.$transaction->tr_number,
                        'subtitle' => null,
                        'url' => route('transaction-edit', $params, false),
                    ];
                }

                return $items;
            });
    }

    private function closings(User $user, Builder $query): Collection
    {
        return $this->visible($user, $query->with('reception:id,name')->latest('id'))
            ->map(fn (Closing $closing): array => [
                'group' => 'Closings',
                'title' => $closing->ct_number,
                'subtitle' => trim(($closing->reception?->name ?? '').' · '.(is_object($closing->status) ? $closing->status->name : $closing->status), ' ·'),
                'url' => route('counter-view', [
                    'ctYear' => $closing->year,
                    'ctMonth' => $closing->month,
                    'ctNumber' => $closing->number,
                ], false),
            ]);
    }

    private function vouchers(User $user, Builder $query): Collection
    {
        return $this->visible($user, $query->latest('id'))
            ->map(function (ExpenseVoucher $voucher): array {
                [, $year, $month] = array_pad(explode('/', (string) $voucher->vc_number), 3, null);

                return [
                    'group' => 'Expense vouchers',
                    'title' => $voucher->vc_number,
                    'subtitle' => number_format((float) $voucher->amount).' · '.$voucher->status,
                    'url' => route('counter-expense-vouchers-list', ['year' => $year, 'month' => $month], false),
                ];
            });
    }

    private function appointments(User $user, Builder $query): Collection
    {
        if (! $user->isAdmin() && ! $user->isReceptionist()) {
            return collect();
        }

        return $query->with(['patient:id,name', 'service:id,name'])->latest('id')->limit(self::PER_GROUP)->get()
            ->map(fn (Appointment $appointment): array => [
                'group' => 'Appointments',
                'title' => $appointment->appointment_number.' — '.($appointment->patient?->name ?? ''),
                'subtitle' => trim(($appointment->service?->name ?? '').' · '.$appointment->scheduled_at?->format('d M Y H:i'), ' ·'),
                'url' => route('appointments-calendar', ['month' => $appointment->scheduled_at?->format('Y-m')], false),
            ]);
    }

    /**
     * Over-fetch, then keep only what the user's policies allow them to view.
     */
    private function visible(User $user, Builder $query): Collection
    {
        return $query->limit(self::PER_GROUP * 3)->get()
            ->filter(fn ($model): bool => $user->can('view', $model))
            ->take(self::PER_GROUP);
    }

    private function formatCnic(string $cnic): string
    {
        $digits = preg_replace('/\D+/', '', $cnic);

        return substr($digits, 0, 5).'-'.substr($digits, 5, 7).'-'.substr($digits, 12, 1);
    }
}
