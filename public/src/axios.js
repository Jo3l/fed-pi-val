import axios from 'axios'
import { cacheAdapterEnhancer, throttleAdapterEnhancer } from 'axios-extensions';

export default axios.create({
	withCredentials : true,
	baseURL: '/api',
	headers: { 'Cache-Control': 'no-cache', 'Content-Type': 'application/json' },
	adapter: throttleAdapterEnhancer(cacheAdapterEnhancer(axios.defaults.adapter, true))
});