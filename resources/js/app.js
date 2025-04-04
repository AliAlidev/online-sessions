import './bootstrap';
import {getUserToken, initialization} from './auth';

await initialization();

window.axios.defaults.headers.common['pageToken'] = await getUserToken();
