const params = new URLSearchParams(window.location.search)
const NOW = params.has('DATE') ? params.get('DATE') : null

export function getNow() {
    return NOW ? new Date(NOW) : new Date()
}
