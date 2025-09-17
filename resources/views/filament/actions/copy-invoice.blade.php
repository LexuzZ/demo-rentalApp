<div>
    <textarea id="invoice-text"
        class="w-full text-sm border rounded p-2 text-black dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
        rows="12" readonly>{{ $textToCopy }}</textarea>

    {{-- <div class="mt-4 flex justify-end">
        <x-filament::button
            color="primary"
            icon="heroicon-o-clipboard"
            x-on:click="
                navigator.clipboard.writeText(document.getElementById('invoice-text').value).then(() => {
                    window.dispatchEvent(new CustomEvent('filament-notify', {
                        detail: { status: 'success', message: 'Detail faktur berhasil disalin 📋' }
                    }))
                })
            "
        >
            Copy ke Clipboard
        </x-filament::button>

    </div> --}}
    <div class="mt-4 flex justify-end" x-data="{
        buttonText: 'Copy ke Clipboard',
        async copyToClipboard() {
            const text = document.getElementById('invoice-text').value;

            try {
                await navigator.clipboard.writeText(text);
                this.buttonText = 'Berhasil Disalin! ✅';

                setTimeout(() => {
                    this.buttonText = 'Copy ke Clipboard';
                }, 2000);
            } catch (err) {
                this.buttonText = 'Gagal Menyalin ❌';
                setTimeout(() => {
                    this.buttonText = 'Copy ke Clipboard';
                }, 2000);
            }
        }
    }">
        <button x-on:click="copyToClipboard" type="button" class="fi-btn fi-btn-size-md fi-btn-color-primary">
            <span x-text="buttonText"></span>
        </button>
    </div>

    <textarea id="text-to-copy-area" class="hidden">
Halo, ini contoh teks faktur yang akan disalin ke clipboard.
</textarea>

</div>
