import './bootstrap';
import 'flowbite';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Make Chart.js available globally
window.Chart = Chart;

// Make Flatpickr available globally
window.flatpickr = flatpickr;
