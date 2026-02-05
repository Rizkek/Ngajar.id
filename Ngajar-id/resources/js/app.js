import './bootstrap';

import ApiClient from './services/api-client';

// Expose ApiClient global (opsional, tapi berguna buat Blade scripts)
window.ApiClient = ApiClient;

console.log('App.js loaded, ApiClient ready.');
