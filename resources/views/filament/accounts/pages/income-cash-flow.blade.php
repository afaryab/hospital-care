<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left Side: Filters and Options --}}
        <div class="space-y-6">
            <form wire:submit.prevent="$refresh" class="space-y-6">
                {{-- Date Filters --}}
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-calendar class="w-5 h-5 text-primary-500" />
                            <span>Date Range</span>
                        </div>
                    </x-slot>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                From Date
                            </label>
                            <input 
                                type="date" 
                                wire:model.live="date_from"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                To Date
                            </label>
                            <input 
                                type="date" 
                                wire:model.live="date_to"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                        </div>
                    </div>
                </x-filament::section>

                {{-- Other Filters --}}
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-funnel class="w-5 h-5 text-primary-500" />
                            <span>Filters</span>
                        </div>
                    </x-slot>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Counter Statement
                            </label>
                            <select 
                                wire:model.live="closing_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">All Counters</option>
                                @foreach(\App\Models\Closing::orderBy('created_at', 'desc')->get() as $closing)
                                    <option value="{{ $closing->id }}">{{ $closing->ct_number }} - {{ $closing->created_at->format('M d, Y') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Service
                            </label>
                            <select 
                                wire:model.live="service_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">All Services</option>
                                @foreach(\App\Models\Service::orderBy('name')->get() as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Service Provider (Doctor)
                            </label>
                            <select 
                                wire:model.live="doctor_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option value="">All Providers</option>
                                @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Patient
                            </label>
                            <input 
                                type="text" 
                                wire:model.live="patient_id"
                                placeholder="Enter Patient ID or PS Number"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                        </div>
                    </div>
                </x-filament::section>

                {{-- Group By --}}
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-squares-2x2 class="w-5 h-5 text-primary-500" />
                            <span>Group By</span>
                        </div>
                    </x-slot>
                    
                    <div class="space-y-2">
                        <label class="flex items-center gap-2">
                            <input 
                                type="radio" 
                                wire:model.live="group_by" 
                                value="none"
                                class="rounded-full border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">No Grouping</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="radio" 
                                wire:model.live="group_by" 
                                value="counter"
                                class="rounded-full border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Group by Counter</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="radio" 
                                wire:model.live="group_by" 
                                value="service"
                                class="rounded-full border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Group by Service</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="radio" 
                                wire:model.live="group_by" 
                                value="doctor"
                                class="rounded-full border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Group by Provider</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="radio" 
                                wire:model.live="group_by" 
                                value="date"
                                class="rounded-full border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Group by Date</span>
                        </label>
                    </div>
                </x-filament::section>

                {{-- Column Visibility --}}
                <x-filament::section>
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-view-columns class="w-5 h-5 text-primary-500" />
                            <span>Show Columns</span>
                        </div>
                    </x-slot>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                wire:model.live="show_date"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Date</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                wire:model.live="show_transaction_number"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Transaction #</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                wire:model.live="show_patient_name"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Patient Name</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                wire:model.live="show_service_name"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Service Name</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                wire:model.live="show_service_order"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Service Order #</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                wire:model.live="show_provider_name"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Provider Name</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                wire:model.live="show_original_amount"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Original Amount</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                wire:model.live="show_edited_amount"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Edited Amount</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                wire:model.live="show_customer_payed"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Customer Paid</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                wire:model.live="show_change"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Change</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input 
                                type="checkbox" 
                                wire:model.live="show_balance"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Balance</span>
                        </label>
                    </div>
                </x-filament::section>

                {{-- Action Buttons --}}
                <div class="flex gap-3">
                    <button 
                        type="button"
                        wire:click="$refresh"
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium shadow-sm transition-colors"
                    >
                        <div class="flex items-center justify-center gap-2">
                            <x-heroicon-o-arrow-path class="w-5 h-5" />
                            <span>Refresh Report</span>
                        </div>
                    </button>
                    <a 
                        href="{{ $this->getReportUrl() }}&download=1"
                        target="_blank"
                        class="flex-1 px-4 py-2 bg-success-600 hover:bg-success-700 text-white rounded-lg font-medium shadow-sm transition-colors text-center"
                    >
                        <div class="flex items-center justify-center gap-2">
                            <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                            <span>Download PDF</span>
                        </div>
                    </a>
                </div>
            </form>
        </div>

        {{-- Right Side: PDF Preview --}}
        <div class="lg:sticky lg:top-6 lg:self-start">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-document-text class="w-5 h-5 text-primary-500" />
                            <span>Report Preview</span>
                        </div>
                        <span class="text-xs text-gray-500">Live Preview</span>
                    </div>
                </x-slot>
                
                <div class="bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden" style="height: calc(100vh - 200px);">
                    <iframe 
                        src="{{ $this->getReportUrl() }}"
                        class="w-full h-full border-0"
                        wire:key="report-iframe-{{ md5(json_encode([
                            $date_from, $date_to, $closing_id, $service_id, 
                            $doctor_id, $patient_id, $group_by,
                            $show_patient_name, $show_service_name, $show_service_order,
                            $show_provider_name, $show_original_amount, $show_edited_amount,
                            $show_customer_payed, $show_change, $show_balance,
                            $show_transaction_number, $show_date
                        ])) }}"
                    ></iframe>
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
