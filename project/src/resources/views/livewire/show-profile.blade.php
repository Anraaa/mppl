@php
                    // Ambil semua data studio
                    $studios = App\Models\Studio::all();
                @endphp

<main>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h3 class="text-center my-4">Layanan Studio Foto</h3>

            <div class="row">
                @foreach ($studios as $studio)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="{{ asset('storage/' . $studio->foto) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $studio->nama_studio }}">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $studio->nama_studio }}</h5>
                                <p class="card-text text-muted">{{ $studio->deskripsi }}</p>
                                <p class="text-success fw-bold">Rp{{ number_format($studio->harga_per_jam) }} / jam</p>
                                <ul class="list-unstyled small text-muted">
                                    @foreach (explode(',', $studio->fasilitas) as $fasilitas)
                                        <li>• {{ trim($fasilitas) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</main>
