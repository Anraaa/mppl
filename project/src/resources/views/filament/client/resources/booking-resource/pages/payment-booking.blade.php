<x-filament::page>
    <x-filament::card>
        <div class="space-y-6">

            <!-- Header Section -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Pembayaran Booking Studio
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lengkapi pembayaran sebelum 
                    <span class="font-medium text-primary-600">
                        {{ \Carbon\Carbon::parse($booking->created_at)->addHours(2)->format('H:i') }}
                    </span>
                </p>
            </div>

            <!-- Booking Summary -->
            <div class="mt-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium text-lg">{{ $booking->studio->nama_studio }}</p>
                        <p class="text-gray-600 dark:text-gray-300 mt-1">
                            <x-heroicon-s-calendar class="w-4 h-4 inline mr-1" />
                            {{ \Carbon\Carbon::parse($booking->tanggal_booking)->translatedFormat('l, d F Y') }}
                        </p>
                        <p class="text-gray-600 dark:text-gray-300">
                            <x-heroicon-s-clock class="w-4 h-4 inline mr-1" />
                            {{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }}
                           
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Pembayaran</p>
                        <p class="text-2xl font-bold text-primary-600">
                            Rp {{ number_format($booking->total_bayar, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <form wire:submit.prevent="submitPayment" enctype="multipart/form-data" class="space-y-6 mt-6">
                <!-- Payment Method Selection -->
                <div>
                    <label for="metode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Pilih Metode Pembayaran
                    </label>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div>
                            <input type="radio" id="gopay" wire:model="metode" value="gopay" class="hidden peer">
                            <label for="gopay" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg cursor-pointer peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:border-gray-700 dark:peer-checked:border-primary-500 dark:peer-checked:bg-gray-800">
                                <img src="{{ asset('images/payment/gopay.png') }}" alt="Gopay" class="h-8 mb-2">
                                <span class="text-sm">Gopay</span>
                            </label>
                        </div>
                        <div>
                            <input type="radio" id="va_bca" wire:model="metode" value="va_bca" class="hidden peer">
                            <label for="va_bca" class="flex flex-col items-center p-3 border border-gray-200 rounded-lg cursor-pointer peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:border-gray-700 dark:peer-checked:border-primary-500 dark:peer-checked:bg-gray-800">
                                <img src="{{ asset('images/payment/bca.png') }}" alt="BCA" class="h-8 mb-2">
                                <span class="text-sm">VA BCA</span>
                            </label>
                        </div>
                    </div>
                    @error('metode') <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Dynamic Payment Instructions -->
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800/50 transition-all duration-300" 
                     x-data="{ expanded: true }"
                     wire:ignore>
                    <div class="flex justify-between items-center cursor-pointer" @click="expanded = !expanded">
                        <h3 class="font-medium text-blue-800 dark:text-blue-200 flex items-center">
                            <x-heroicon-s-information-circle class="w-5 h-5 mr-2" />
                            Instruksi Pembayaran
                        </h3>
                        <x-heroicon-s-chevron-down x-show="!expanded" class="w-5 h-5 text-blue-500" />
                        <x-heroicon-s-chevron-up x-show="expanded" class="w-5 h-5 text-blue-500" />
                    </div>

                    <div x-show="expanded" x-collapse class="mt-3 space-y-3">
                        <!-- Gopay Instructions -->
                        <div x-show="$wire.metode === 'gopay'" class="space-y-2">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/50 rounded-full p-1 mr-3">
                                    <span class="text-blue-800 dark:text-blue-200 text-sm font-bold">1</span>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Buka aplikasi Gopay di smartphone Anda</p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/50 rounded-full p-1 mr-3">
                                    <span class="text-blue-800 dark:text-blue-200 text-sm font-bold">2</span>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Pilih <span class="font-medium">"Bayar"</span> atau <span class="font-medium">"Transfer"</span></p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/50 rounded-full p-1 mr-3">
                                    <span class="text-blue-800 dark:text-blue-200 text-sm font-bold">3</span>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Masukkan nomor tujuan: <span class="font-bold bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded">0855-9141-3027</span></p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/50 rounded-full p-1 mr-3">
                                    <span class="text-blue-800 dark:text-blue-200 text-sm font-bold">4</span>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Masukkan jumlah: <span class="font-bold">Rp {{ number_format($booking->total_bayar, 0, ',', '.') }}</span></p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/50 rounded-full p-1 mr-3">
                                    <span class="text-blue-800 dark:text-blue-200 text-sm font-bold">5</span>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Ikuti petunjuk selanjutnya untuk menyelesaikan pembayaran</p>
                            </div>
                        </div>

                        <!-- VA BCA Instructions -->
                        <div x-show="$wire.metode === 'va_bca'" class="space-y-2" style="display: none;">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/50 rounded-full p-1 mr-3">
                                    <span class="text-blue-800 dark:text-blue-200 text-sm font-bold">1</span>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Buka aplikasi BCA Mobile atau ATM BCA</p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/50 rounded-full p-1 mr-3">
                                    <span class="text-blue-800 dark:text-blue-200 text-sm font-bold">2</span>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Pilih menu <span class="font-medium">"Transfer"</span> → <span class="font-medium">"Virtual Account"</span></p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/50 rounded-full p-1 mr-3">
                                    <span class="text-blue-800 dark:text-blue-200 text-sm font-bold">3</span>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Masukkan nomor Virtual Account: <span class="font-bold bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded">39012 34567 890</span></p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/50 rounded-full p-1 mr-3">
                                    <span class="text-blue-800 dark:text-blue-200 text-sm font-bold">4</span>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Jumlah pembayaran akan terisi otomatis</p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900/50 rounded-full p-1 mr-3">
                                    <span class="text-blue-800 dark:text-blue-200 text-sm font-bold">5</span>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Konfirmasi dan selesaikan pembayaran</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Proof Upload -->
                <div x-data="{
                    filePreview: null,
                    previewFile(event) {
                        const file = event.target.files[0];
                        if (!file) {
                            this.filePreview = null;
                            return;
                        }
                        if (!file.type.startsWith('image/')) {
                            alert('Hanya file gambar (PNG, JPG, JPEG) yang diperbolehkan');
                            event.target.value = '';
                            this.filePreview = null;
                            return;
                        }
                        if (file.size > 5 * 1024 * 1024) {
                            alert('Ukuran file maksimal 5MB');
                            event.target.value = '';
                            this.filePreview = null;
                            return;
                        }
                        this.filePreview = URL.createObjectURL(file);
                    }
                }">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Upload Bukti Pembayaran
                        <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="mt-1 flex justify-center px-6 pt-8 pb-6 border-2 border-gray-300 border-dashed rounded-xl dark:border-gray-600 hover:border-primary-500 transition-colors"
                         x-data="{ isDragging: false }"
                         x-on:dragover.prevent="isDragging = true"
                         x-on:dragleave.prevent="isDragging = false"
                         x-on:drop.prevent="isDragging = false; $refs.fileInput.files = event.dataTransfer.files; previewFile($event)">
                        <div class="space-y-2 text-center w-full"
                             x-data="{ isUploading: false, progress: 0 }"
                             x-on:livewire-upload-start="isUploading = true"
                             x-on:livewire-upload-finish="isUploading = false"
                             x-on:livewire-upload-error="isUploading = false"
                             x-on:livewire-upload-progress="progress = $event.detail.progress">
                            
                            <div x-show="!filePreview" class="flex flex-col items-center">
                                <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-2 flex text-sm text-gray-600 dark:text-gray-400">
                                    <label class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none px-4 py-2 border border-primary-600 hover:border-primary-500 transition">
                                        <span>Pilih file</span>
                                        <input id="bukti_transfer" 
                                               name="bukti_transfer" 
                                               wire:model="bukti_transfer" 
                                               type="file" 
                                               class="sr-only" 
                                               accept="image/*"
                                               x-ref="fileInput"
                                               x-on:change="previewFile($event)">
                                    </label>
                                    <p class="pl-3 self-center">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    PNG, JPG, JPEG (maks. 5MB)
                                </p>
                            </div>

                            <!-- Upload Progress -->
                            <div x-show="isUploading" class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mb-2 overflow-hidden">
                                <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-300" 
                                     x-bind:style="`width: ${progress}%`"></div>
                            </div>

                            <!-- Preview Section -->
                            <template x-if="filePreview">
                                <div class="mt-2 flex flex-col items-center">
                                    <div class="relative group">
                                        <img :src="filePreview" 
                                             alt="Preview Bukti Transfer"
                                             class="max-h-64 rounded-lg border border-gray-300 dark:border-gray-600 shadow-md object-contain" />
                                        <button type="button" 
                                                @click="filePreview = null; $refs.fileInput.value = ''"
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity shadow-md">
                                            <x-heroicon-s-x-mark class="w-4 h-4" />
                                        </button>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        Klik gambar untuk mengubah
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>
                    @error('bukti_transfer') 
                        <span class="text-red-600 text-sm mt-2 block flex items-center">
                            <x-heroicon-s-exclamation-circle class="w-4 h-4 mr-1" />
                            {{ $message }}
                        </span> 
                    @enderror
                </div>

                <!-- Payment Notes -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-100 dark:border-yellow-800/50">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Penting!</h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>Pastikan nominal transfer sesuai dengan total pembayaran</li>
                                    <li>Proses verifikasi membutuhkan waktu 1x3 jam</li>
                                    <li>Simpan bukti transfer sampai status booking berubah menjadi "Dibayar"</li>
                                    <li>Hubungi WhatsApp 0855-9141-3027 jika mengalami kesulitan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-between items-center pt-4">
                    
                    <button type="submit" 
                            class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition flex items-center justify-center"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submitPayment">
                            Konfirmasi Pembayaran
                        </span>
                        <span wire:loading wire:target="submitPayment" class="flex items-center">
                            <x-heroicon-s-arrow-path class="animate-spin w-4 h-4 mr-2" />
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>

        </div>
    </x-filament::card>
</x-filament::page>