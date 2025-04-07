import './bootstrap';
import { getUserToken, initialization } from './auth';

async function init() {
    await initialization();
    const token = await getUserToken();
    window.pageToken = token;  // Store token globally

    // Set default Axios header
    window.axios.defaults.headers.common['pageToken'] = token;

    // Notify that initialization is done
    window.tokenInitialized = true;
}

init();
