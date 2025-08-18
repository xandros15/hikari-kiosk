const params = new URLSearchParams(window.location.search)
const NOW = params.has('DATE') ? params.get('DATE') : null

export function getNow() {
    return NOW ? new Date(NOW) : new Date()
}

export function getTodayKonwencik() {
    const now = getNow();
    const month = '' + (now.getUTCMonth() + 1);
    const day = '' + now.getDate();

    return `${day.padStart(2, '0')}-${month.padStart(2, '0')}-${now.getFullYear()}`
}

export function getDateTime(date, time) {
    return new Date(`${getNormalizedDate(date)} ${time}`);
}

function getNormalizedDate(date) {
    const [day, month, year] = date.split('-')

    return `${year}-${month}-${day}`
}
