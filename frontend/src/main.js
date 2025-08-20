import {createApp} from 'vue'
import App from './App.vue'
import './../assets/index.css'
import {createRouter, createWebHistory} from "vue-router";
import GenericTimetable from "@/views/GenericTimetable.vue";
import DoubleRoomTimetable from "@/views/DoubleRoomTimetable.vue";

const routes = [
    {path: '/', component: GenericTimetable},
    {path: '/double-room', component: DoubleRoomTimetable},
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

createApp(App).use(router).mount('#app')
