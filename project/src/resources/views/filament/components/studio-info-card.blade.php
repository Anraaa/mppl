<div class="studio-info-card bg-white rounded-lg overflow-hidden shadow-sm border border-gray-200">
    @if(isset($info['foto']) && $info['foto'])
    <div class="studio-image h-20 bg-gray-50 overflow-hidden flex items-center justify-center p-1 border-b border-gray-200">
        <img src="{{ asset('storage/' . $info['foto']) }}" 
            alt="{{ $info['nama'] }}" 
            class="h-full w-auto object-contain"
            loading="lazy">
    </div>
    @endif
    
    <div class="p-3 space-y-2.5">
        <div class="flex justify-between items-start gap-2">
            <h3 class="text-md-8 font-semibold text-gray-800 line-clamp-1">{{ $info['nama'] }}</h3>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-md font-medium bg-blue-100 text-blue-800">
                {{ $info['kapasitas'] }} orang
            </span>
        </div>
        
        <div class="text-sm text-gray-600 line-clamp-2">
            {{ $info['deskripsi'] }}
        </div>
        
        <div class="grid grid-cols-2 gap-3 text-md">
            <div class="space-y-1">
                <div class="font-medium text-gray-700 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Jam Operasional
                </div>
                <div class="text-gray-600">{{ $info['jam_operasional'] }}</div>
            </div>
            <div class="space-y-1">
                <div class="font-medium text-gray-700 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Hari Operasional
                </div>
                <div class="text-gray-600">{{ $info['hari_operasional'] }}</div>
            </div>
        </div>
        
        @if(!empty($info['fasilitas']))
        <div class="pt-1.5">
            <h4 class="text-md font-medium text-gray-700 mb-1">Fasilitas:</h4>
            <ul class="list-disc list-inside space-y-1 text-md text-gray-600">
                @foreach($info['fasilitas'] as $facility)
                <li class="truncate">{{ trim($facility) }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

<style>
    .studio-info-card {
        transition: all 0.2s ease;
        max-width: 100%;
    }
    .studio-info-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .studio-image {
        height: 24rem; /* 80px */
    }
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>