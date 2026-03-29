<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class LookUpController extends Controller
{
    public function index()
    {
        $results = [];
        $keyWord = request()->get('q', null);

        // if key work starts with PS, search in patients
        if (str_starts_with($keyWord, 'PS')) {
            // If key work length is less than 17
            if (strlen($keyWord) < 17) {
                $results = \App\Models\Patient::where('ps_number', 'LIKE', "{$keyWord}%")
                    ->limit(10)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'type' => 'link',
                            'url' => route('patients-register-ps-number', [
                                'year' => $item->ps_year,
                                'month' => $item->ps_month,
                                'number' => $item->ps_number,
                            ]),
                            'name' => 'View '.$item->name,
                        ];
                    })
                    ->values()
                    ->toArray();
            } elseif (strlen($keyWord) === 17) {
                $patient = \App\Models\Patient::where('ps_number', $keyWord)->first();
                if ($patient) {
                    $results = [
                        [
                            'type' => 'link',
                            'url' => route('patients-register-ps-number', [
                                'year' => $patient->ps_year,
                                'month' => $patient->ps_month,
                                'number' => $patient->ps_number,
                            ]),
                            'name' => 'View '.$patient->name,
                        ],
                    ];
                }

            } elseif (strlen($keyWord) > 17 && strlen($keyWord) <= 27) {
                // Get pattient by first 17 characters and then return service orders related to that patient like this keywork
                $patient = \App\Models\Patient::where('ps_number', substr($keyWord, 0, 17))->first();
                if ($patient) {

                    $results[] = [
                        'type' => 'link',
                        'name' => 'View '.$patient->name,
                    ];

                    $serviceOrders = \App\Models\ServiceOrder::where('patient_id', $patient->id)
                        ->where('so_number', 'LIKE', "{$keyWord}%")
                        ->limit(10)
                        ->get()
                        ->map(function ($item) use ($patient) {
                            return [
                                'type' => 'link',
                                'url' => route('patients-register-ps-number-department-service', [
                                    'year' => $patient->year,
                                    'month' => $patient->month,
                                    'number' => $patient->number,
                                    'departmentKey' => $item->department_key,
                                    'serviceNumber' => $item->serviceNumber,
                                ]),
                                'name' => 'View '.$item->service->name.' for '.$patient->name,
                            ];
                        })
                        ->values()
                        ->toArray();

                    $results = array_merge($results, $serviceOrders);
                }
            } elseif (strlen($keyWord) === 30) {
                // Get pattient by first 17 characters and then return service order by 27 characters
                $patient = \App\Models\Patient::where('ps_number', substr($keyWord, 0, 17))->first();
                if ($patient) {

                    $results[] = [
                        'type' => 'static',
                        'name' => 'View '.$patient->name,
                    ];

                    $serviceOrder = \App\Models\ServiceOrder::where('patient_id', $patient->id)->where('so_number', $keyWord)->first();
                    if ($serviceOrder) {
                        $results[] = [
                            'type' => 'link',
                            'url' => route('patients-register-ps-number-department-service', [
                                'year' => $patient->year,
                                'month' => $patient->month,
                                'number' => $patient->number,
                                'departmentKey' => $serviceOrder->department_key,
                                'serviceNumber' => $serviceOrder->serviceNumber,
                            ]),
                            'name' => 'View '.$serviceOrder->service->name.' for '.$patient->name,
                        ];
                    }
                }
            }

        } elseif (str_starts_with($keyWord, 'TR')) {

            // if string length is less than 14 then search with like if it is 14 then search with exact match
            if (strlen($keyWord) < 18) {
                $results = \App\Models\Transaction::where('tr_number', 'LIKE', "{$keyWord}%")
                    ->limit(10)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'type' => 'link',
                            'url' => route('transaction-view', [
                                'tYear' => $item->year,
                                'tMonth' => $item->month,
                                'tDay' => $item->day,
                                'tNumber' => $item->number,
                            ]),
                            'name' => 'View transaction '.$item->tr_number,
                        ];
                    })
                    ->values()
                    ->toArray();
            } elseif (strlen($keyWord) === 18) {
                $transaction = \App\Models\Transaction::where('tr_number', $keyWord)->first();
                if ($transaction) {
                    $results = [
                        [
                            'type' => 'link',
                            'url' => route('transaction-view', [
                                'tYear' => $transaction->year,
                                'tMonth' => $transaction->month,
                                'tDay' => $transaction->day,
                                'tNumber' => $transaction->number,
                            ]),
                            'name' => 'View transaction '.$transaction->tr_number,
                        ],
                        [
                            'type' => 'link',
                            'url' => route('transaction-edit', [
                                'tYear' => $transaction->year,
                                'tMonth' => $transaction->month,
                                'tDay' => $transaction->day,
                                'tNumber' => $transaction->number,
                            ]),
                            'name' => 'Edit transaction '.$transaction->tr_number,
                        ],
                    ];
                }
            }

        }

        return response()->json([
            'results' => $results,
            'keyWord' => $keyWord,
            'strlen' => strlen($keyWord),
        ]);
    }
}
