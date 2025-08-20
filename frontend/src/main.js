import {createApp} from 'vue'
import App from './App.vue'
import './../assets/index.css'
import {createRouter, createWebHistory} from "vue-router";
import GenericTimetable from "@/views/GenericTimetable.vue";
import SpeedRoomTimetable from "@/views/SpeedRoomTimetable.vue";

const routes = [
    {path: '/', component: GenericTimetable},
    {path: '/speedroom', component: SpeedRoomTimetable},
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

createApp(App).use(router).mount('#app')
