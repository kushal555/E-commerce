import axios from 'axios';
import router from './routes.js';
import store from './store'

export default function setup() {
    axios.interceptors.request.use(function(config) {
        const token = localStorage.getItem('token');
        if(token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    }, function(err) {
        return Promise.reject(err);
    });

    // axios.interceptors.response.use(res => response.data, function (error) {
    //     if (error.response.status === 401) {
    //         localStorage.removeItem('token')
    //         store.commit('logoutUser')
    //         router.go({ name: 'login' })
    //     }
    //     return Promise.reject(error);
    //   });
}