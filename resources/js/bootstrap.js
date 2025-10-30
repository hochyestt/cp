// import axios from 'axios';
// window.axios = axios;

// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Важно для CSRF токена
window.axios.defaults.withCredentials = true;

// Получение CSRF токена при загрузке
await window.axios.get('/sanctum/csrf-cookie');