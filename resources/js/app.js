import './bootstrap';
import {getUserToken, initialization} from './auth';

async function init() {
    await initialization();
    window.axios.defaults.headers.common['pageToken'] = await getUserToken();
  }

  init();

