import {getNow} from "./clock";

// const API_URL = 'http://localhost:8080/api';
const API_URL = '/api';

function filterRooms({attr, rooms}) {
    return Array.isArray(rooms) ? rooms.indexOf(attr.room_id) !== -1 : true
}

export async function getAll(rooms) {
    const now = getNow()

    return await fetch(API_URL)
        .then(r => r.ok ? r.json() : [])
        .then(r => r.map(i => {
            i.startDateTime = new Date(i.start_date_time)
            i.endDateTime = new Date(i.end_date_time)
            return i;
        }))
        .then(r => r.filter(i => i.startDateTime.toDateString() === now.toDateString() || i.endDateTime.toDateString() === now.toDateString()))
        .then(r => r.filter(attr => rooms ? filterRooms({attr, rooms}) : true))
        .then(r => r.filter(i => i.endDateTime > now))
}
