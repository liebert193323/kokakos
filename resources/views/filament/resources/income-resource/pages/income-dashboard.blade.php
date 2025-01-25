<x-filament::page>
    <x-filament::grid>
        {{-- Total Income Card --}}
        <x-filament::grid.column span="6">
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium tracking-tight">Total Pendapatan</h2>
                        <p class="text-3xl font-bold mt-2">{{ $totalIncome }}</p>
                    </div>
                    <x-heroicon-o-currency-dollar class="w-12 h-12 text-primary-500" />
                </div>
            </x-filament::card>
        </x-filament::grid.column>

        {{-- Last Month Income Card --}}
        <x-filament::grid.column span="6">
            <x-filament::card>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium tracking-tight">Pendapatan Bulan Ini</h2>
                        <p class="text-3xl font-bold mt-2">{{ $lastMonthIncome }}</p>
                    </div>
                    <x-heroicon-o-chart-bar class="w-12 h-12 text-primary-500" />
                </div>
            </x-filament::card>
        </x-filament::grid.column>

        {{-- Income Chart --}}
        <x-filament::grid.column span="12">
            <x-filament::card>
                <h2 class="text-lg font-medium tracking-tight mb-4">Grafik Pendapatan 30 Hari Terakhir</h2>
                <div class="h-80">
                    <div
                        x-data="{
                            chart: null,
                            init() {
                                this.chart = new ApexCharts(this.$refs.chart, {
                                    chart: {
                                        type: 'line',
                                        height: 300
                                    },
                                    series: [{
                                        name: 'Pendapatan',
                                        data: {{ json_encode(collect($chartData)->pluck('amount')) }}
                                    }],
                                    xaxis: {
                                        categories: {{ json_encode(collect($chartData)->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d/m'))) }},
                                        labels: {
                                            rotate: -45,
                                            rotateAlways: true
                                        }
                                    },
                                    yaxis: {
                                        labels: {
                                            formatter: function(value) {
                                                return new Intl.NumberFormat('id-ID', {
                                                    style: 'currency',
                                                    currency: 'IDR',
                                                    minimumFractionDigits: 0
                                                }).format(value);
                                            }
                                        }
                                    }
                                });
                                this.chart.render();
                            }
                        }"
                        x-ref="chart"
                    ></div>
                </div>
            </x-filament::card>
        </x-filament::grid.column>
    </x-filament::grid>
</x-filament::page>