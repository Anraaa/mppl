<div class="space-y-2">
    <div class="font-medium">{{ $nama }}</div>
    
    @if($deskripsi)
    <div class="text-sm text-gray-600">
        <span class="font-medium">Deskripsi:</span> {{ $deskripsi }}
    </div>
    @endif
    
    <div class="grid grid-cols-2 gap-2 text-sm">
        <div>
            <span class="font-medium">Kapasitas:</span> {{ $kapasitas }} orang
        </div>
        <div>
            <span class="font-medium">Jam Operasional:</span> {{ $jam_operasional }}
        </div>
        <div>
            <span class="font-medium">Hari Operasional:</span> {{ $hari_operasional }}
        </div>
    </div>
    
    @if($fasilitas)
    <div class="text-sm">
        <span class="font-medium">Fasilitas:</span> {{ $fasilitas }}
    </div>
    @endif
</div>