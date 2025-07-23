<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Helper\EncryptionHelper;

class ShowStudio extends Component
{
    public $studios = [];

    public function mount()
    {
        $this->loadStudios();
    }

    public function loadStudios()
    {
        $response = Http::get(env('PUBLIC_STUDIO_API', 'http://nginx_mppl/api/public-studios'));

        if ($response->successful()) {
            $encrypted = $response->body(); // 🛠 Ambil string terenkripsi

            try {
                $decrypted = EncryptionHelper::decrypt($encrypted);
                $decoded = json_decode($decrypted, true);

                $this->studios = $decoded['data'] ?? [];
            } catch (\Exception $e) {
                $this->studios = [];
            }
        }
    }

    public function render()
    {
        return view('livewire.show-studio');
    }
}
