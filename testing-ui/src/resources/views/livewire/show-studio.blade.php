<div>
    @php
        $studios = App\Models\Studio::where('is_active', true)
            ->orderBy('harga_per_jam', 'asc')
            ->get();
    @endphp

    <main class="py-5 bg-light">
        <div class="container" style="max-width: 1200px;">
            <!-- Header -->
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark mb-3">Layanan Studio Foto</h2>
                <p class="lead text-muted mx-auto" style="max-width: 600px;">
                    Temukan studio foto terbaik dengan fasilitas lengkap untuk kebutuhan kreatif Anda.
                </p>
                <div class="bg-primary rounded-pill mx-auto" style="width: 80px; height: 4px;"></div>
            </div>

            <!-- Studio List -->
            @if ($studios->isEmpty())
                <div class="alert alert-info text-center mx-auto" style="max-width: 800px;">
                    <i class="fas fa-info-circle me-2"></i>
                    Maaf, tidak ada studio yang tersedia saat ini. Silakan coba lagi nanti.
                </div>
            @else
                <div class="row g-4">
                    @foreach ($studios as $studio)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm d-flex flex-column">

                                <!-- Gambar Studio -->
                                <div class="position-relative">
                                    <div class="ratio ratio-4x3 bg-light">
                                        @if ($studio->foto)
                                            <img src="{{ asset('storage/' . $studio->foto) }}"
                                                 class="w-100 h-100 object-fit-cover rounded-top"
                                                 alt="{{ $studio->nama_studio }}"
                                                 loading="lazy">
                                        @else
                                            <div class="d-flex justify-content-center align-items-center h-100 text-muted">
                                                <i class="fas fa-camera fa-3x opacity-50"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Badge Harga -->
                                    <div class="position-absolute bottom-0 start-0 m-2 bg-white px-3 py-1 rounded-pill shadow-sm">
                                        <span class="fw-bold text-primary">
                                            Rp{{ number_format($studio->harga_per_jam, 0, ',', '.') }}/jam
                                        </span>
                                    </div>

                                    <!-- Badge Diskon -->
                                    @if ($studio->is_discount)
                                        <div class="position-absolute top-0 end-0 m-2 bg-danger text-white px-2 py-1 rounded-pill small fw-bold">
                                            {{ $studio->discount_percent }}% OFF
                                        </div>
                                    @endif
                                </div>

                                <!-- Konten Studio -->
                                <div class="card-body d-flex flex-column">
                                    <h5 class="fw-bold mb-2">{{ $studio->nama_studio }}</h5>
                                    <p class="text-muted mb-3">
                                        {{ \Illuminate\Support\Str::limit($studio->deskripsi, 100) }}
                                    </p>

                                    <!-- Fasilitas -->
                                    @if (!empty($studio->fasilitas))
                                        <div class="mb-3">
                                            <h6 class="text-muted fw-semibold text-uppercase small mb-2">Fasilitas</h6>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach (array_filter(explode(',', $studio->fasilitas)) as $fasilitas)
                                                <span class="d-inline-flex align-items-center px-3 py-1 rounded-pill bg-success text-white shadow-sm small" style="font-weight: 500;">
                                                    <i class="fas fa-check me-2"></i>
                                                    {{ trim($fasilitas) }}
                                                </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Tombol Pesan -->
                                    <div class="mt-auto d-flex justify-content-end">
                                        <a href="{{ route('filament.client.auth.login') }}" class="btn btn-sm btn-outline-primary">
                                            Pesan Sekarang <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
</div>
