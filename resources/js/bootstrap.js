import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withXSRFToken = true;
window.axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';

