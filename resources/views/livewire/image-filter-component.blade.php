<div>
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Month Filter -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-love-900 mb-2">
                    Filter Berdasarkan Bulan
                </label>
                <select
                    wire:model="filterMonth"
                    wire:change="applyMonthFilter"
                    class="w-full px-4 py-3 border border-love-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-love-500 focus:border-transparent"
                >
                    <option value="">Semua Bulan</option>
                    @foreach($availableMonths as $month)
                        <option value="{{ $month }}" @if($filterMonth === $month) selected @endif>
                            {{ $this->getMonthName($month) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range Filter -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-love-900 mb-2">
                    Filter Berdasarkan Tanggal
                </label>
                <div class="date-range-group flex space-x-2">
                    <input
                        type="date"
                        wire:model="filterStartDate"
                        wire:change="applyDateFilter"
                        class="date-start flatpickr-input flex-1 px-4 py-3 border border-love-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-love-500 focus:border-transparent"
                    />
                    <span class="self-center text-love-700">-</span>
                    <input
                        type="date"
                        wire:model="filterEndDate"
                        wire:change="applyDateFilter"
                        class="date-end flatpickr-input flex-1 px-4 py-3 border border-love-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-love-500 focus:border-transparent"
                    />
                </div>
            </div>

            <!-- Reset Button -->
            <div class="flex-shrink-0">
                <button
                    wire:click="resetFilters"
                    class="px-6 py-3 border border-love-300 text-love-700 font-semibold rounded-xl hover:bg-love-50 focus:outline-none focus:ring-2 focus:ring-love-500 transition-all"
                >
                    Reset
                </button>
            </div>
        </div>
    </div>
</div>
