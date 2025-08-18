import {getDateTime, getNow, getTodayKonwencik} from "./clock";

// const API_URL = 'http://localhost:8080/api';
const API_URL = '/api';

function filterPlaces({attr, places, whiteList}) {
    console.log(attr.room)
    if (Array.isArray(whiteList) && whiteList.indexOf('' + attr.id) !== -1) {
        return true
    }

    return Array.isArray(places) ? places.indexOf(attr.room) !== -1 : true
}

export async function getAll(places, whiteList) {
    return await fetch(API_URL)
            .then(r => r.ok ? r.json() : [])
            .then(r => r.map(i => {
                console.log(i.day, getTodayKonwencik())
                return i
            }))
            .then(r => r.filter(i => i.day === getTodayKonwencik()))
            .then(r => r.filter(attr => places ? filterPlaces({attr, places, whiteList}) : true))
            .then(r => r.map(i => {
                i.startDatetime = getDateTime(i.day, i.start)
                i.endDatetime = getDateTime(i.day, i.end)
                return i;
            }))
            .then(r => r.filter(i => i.endDatetime > getNow()))
}
