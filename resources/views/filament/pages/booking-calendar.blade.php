<x-filament::page>
    {{-- Inisialisasi komponen Alpine.js untuk mengelola kalender --}}
    <div x-data="calendar(@js($this->getFilterDataForJs()))">

        {{-- Filter Section --}}
        <x-filament::section>
            {{-- Merender form filter yang sudah didefinisikan di file PHP --}}
            {{ $this->form }}
        </x-filament::section>

        {{-- Calendar Section --}}
        <x-filament::section class="mt-6">
            {{-- wire:ignore mencegah Livewire mengganggu render kalender --}}
            <div id="calendar" wire:ignore class="text-sm"></div>
        </x-filament::section>

    </div>
</x-filament::page>

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('calendar', (initialFilters) => ({
                calendar: null,
                filters: initialFilters,

                init() {
                    const calendarEl = document.getElementById('calendar');
                    this.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        timeZone: 'local',
                        locale: 'id',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,listMonth,timeGridDay',
                        },
                        events: (fetchInfo, successCallback, failureCallback) => {
                            const url = new URL('{{ url("/admin/bookings-calendar") }}');
                            if (this.filters.mobil) url.searchParams.append('mobil', this.filters.mobil);
                            if (this.filters.nopol) url.searchParams.append('nopol', this.filters.nopol);

                            fetch(url)
                                .then(response => {
                                    if (!response.ok) throw new Error('Network response was not ok');
                                    return response.json();
                                })
                                .then(data => successCallback(data))
                                .catch(error => failureCallback(error));
                        },
                        eventClick: (info) => {
                            const bookingId = info.event.id;
                            if (bookingId) {
                                // PERBAIKAN DI SINI: Menggunakan path admin yang diketahui
                                window.open(`/admin/bookings/${bookingId}/edit`, '_blank');
                            }
                        }
                    });
                    this.calendar.render();

                    // Alpine akan "mengawasi" perubahan pada properti filters.
                    // Saat Livewire memperbarui filter, Alpine akan mendeteksinya dan memuat ulang event.
                    this.$watch('filters', (newVal) => {
                        this.calendar.refetchEvents();
                    });
                },
            }));
        });
    </script>
@endpush
