import {getDateTime, getNow, getTodayKonwencik} from "./clock";

// const API_URL = 'http://localhost:8080/api';
const API_URL = '/api';

function filterRooms({attr, rooms}) {
    return Array.isArray(rooms) ? rooms.indexOf(attr.room_id) !== -1 : true
}

export async function getAll(rooms) {
    return await fetch(API_URL)
            .then(r => r.ok ? r.json() : [])
            .then(r => r.filter(i => i.day === getTodayKonwencik()))
            .then(r => r.filter(attr => rooms ? filterRooms({attr, rooms}) : true))
            .then(r => r.map(i => {
                i.startDatetime = getDateTime(i.day, i.start)
                i.endDatetime = getDateTime(i.day, i.end)
                return i;
            }))
            .then(r => r.filter(i => i.endDatetime > getNow()))
}
