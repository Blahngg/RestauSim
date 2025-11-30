import dragscroll from 'dragscroll';

document.addEventListener('livewire:navigated', () => dragscroll.reset());
document.addEventListener('livewire:load', () => dragscroll.reset());
document.addEventListener('livewire:update', () => dragscroll.reset());