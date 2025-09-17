<x-filament-panels::page>
    <div x-data="signaturePad({ state: @entangle('signature').defer })" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="booking_id">
                Nama Lengkap
            </label>
            <input wire:model.defer="booking_id" id="booking_id" type="text"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500"
                placeholder="Masukkan nama" />
            @error('booking_id')
                <span class="text-sm text-red-600">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <p class="font-medium mb-2">Tanda Tangan:</p>
            <canvas x-ref="canvas" class="border border-gray-300 rounded-lg shadow-sm w-full"
                style="height: 250px;"></canvas>

            <div class="flex gap-2 mt-2">
                <x-filament::button color="danger" size="sm" @click="clear">
                    Hapus
                </x-filament::button>
                <x-filament::button color="success" size="sm" wire:click="save">
                    Simpan
                </x-filament::button>
            </div>

            @error('signature')
                <span class="text-sm text-red-600">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('signaturePad', ({
                state
            }) => ({
                signaturePadInstance: null,
                state: state,

                init() {
                    this.$nextTick(() => {
                        setTimeout(() => {
                            const canvas = this.$refs.canvas;
                            const ratio = Math.max(window.devicePixelRatio || 1, 1);
                            canvas.width = canvas.offsetWidth * ratio;
                            canvas.height = 250 * ratio;
                            canvas.getContext("2d").scale(ratio, ratio);

                            this.signaturePadInstance = new SignaturePad(canvas);

                            if (this.state) {
                                this.signaturePadInstance.fromDataURL(this.state);
                            }

                            this.signaturePadInstance.addEventListener("endStroke",
                            () => {
                                    this.state = this.signaturePadInstance
                                        .toDataURL();
                                });
                        }, 500);
                    });
                },

                clear() {
                    if (this.signaturePadInstance) {
                        this.signaturePadInstance.clear();
                    }
                    this.state = null;
                }
            }))
        })
    </script>
</x-filament-panels::page>
