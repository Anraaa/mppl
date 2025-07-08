document.addEventListener('livewire:init', () => {
    Livewire.on('booking-conflict', (message) => {
        alert(message);
    });
});