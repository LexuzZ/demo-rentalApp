<div>
    <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">
        Teks di bawah ini siap untuk disalin ke clipboard Anda.
    </p>

    {{-- Textarea untuk menampilkan teks --}}
    <textarea id="text-to-copy-area" readonly class="w-full h-48 p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">{{ $textToCopy }}</textarea>

    {{-- Tombol Copy dengan logika Alpine.js --}}
    <div class="mt-4 flex justify-end"
         x-data="{
            buttonText: 'Copy ke Clipboard',
            copyToClipboard() {
                const textArea = document.getElementById('text-to-copy-area');
                textArea.select();
                document.execCommand('copy');

                this.buttonText = 'Berhasil Disalin!';
                setTimeout(() => {
                    this.buttonText = 'Copy ke Clipboard';
                }, 2000);
            }
         }">
        <button
            x-on:click="copyToClipboard"
            type="button"
            class="fi-btn fi-btn-size-md fi-btn-color-primary">
            <span x-text="buttonText"></span>
        </button>
    </div>
</div>
