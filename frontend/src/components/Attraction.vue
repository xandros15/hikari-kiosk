<template>
    <div class="attraction" :class="{'attraction-in-progress': isInProgress}">
        <div class="attraction-room">{{ room }}</div>
        <div class="attraction-content">
            <div class="attraction-title">
                {{ normalizedTitle }}
            </div>
            <div class="attraction-speaker">
                {{ speaker.join(', ') }}
            </div>
        </div>
        <div class="attraction-time" v-if="isInProgress" :title="startDateTime">
            <div v-if="isAlmostEnd" class="attraction-status">
                Kończy się
            </div>
            <div v-else class="attraction-status">
                W trakcie
            </div>
            <div class="attraction-time-left">
                [Zostało {{ toFinish }}]
            </div>
        </div>
        <div class="attraction-time" v-else-if="isAlmostStart" :title="startDateTime">
            <div class="attraction-status">
                Zaraz się zacznie
            </div>
            <div class="attraction-time-left">
                [Do rozpoczęcia {{ toStart }}]
            </div>
        </div>
        <div class="attraction-time" :title="startDateTime" v-else>
            Start o {{ startDateTime.getHours() }}:{{ (startDateTime.getMinutes() + '').padStart(2, '0') }}
        </div>
    </div>
</template>

<script>
const ALMOST_START_IN = 1000 * 60 * 10;
const ALMOST_END_IN = 1000 * 60 * 10;

export default {
    name: "Attraction",
    components: {},
    props: {
        time: {
            required: true,
        },
        room: {
            required: true,
            type: String,
        },
        title: {
            required: true,
            type: String,
        },
        speaker: {
            required: true,
            type: Array,
        },
        startDateTime: {
            required: true,
            type: Date,
        },
        endDateTime: {
            required: true,
            type: Date
        }
    },
    computed: {
        isInProgress() {
            return this.time >= this.startDateTime && this.time <= this.endDateTime
        },
        normalizedTitle() {
            const title = decodeURI(this.title)

            return title.length > 70 ? title.substring(0, 67) + '...' : title
        },
        isAlmostEnd() {
            return ALMOST_END_IN > this.endDateTime - this.time
        },
        isAlmostStart() {
            return ALMOST_START_IN > this.startDateTime - this.time
        },
        toStart() {
            const time = (this.startDateTime - this.time) / 1000;
            const minutes = (Math.floor(time / 60 % 60) + '').padStart(2, '0')
            const seconds = (Math.trunc(time % 60) + '').padStart(2, '0')

            return `${minutes}m ${seconds}s`;
        },
        toFinish() {
            const time = (this.endDateTime - this.time) / 1000;
            const minutes = (Math.floor(time / 60 % 60) + '').padStart(2, '0')
            const hours = (Math.floor(time / 3600) + '');
            const seconds = (Math.trunc(time % 60) + '').padStart(2, '0')
            if (time > 3600) {
                return `${hours}h ${minutes}m`;
            }

            return `${minutes}m ${seconds}s`;
        }
    }
}
</script>


<style scoped lang="scss">
.attraction {
    font-size: 19px;
    display: grid;
    grid-template-columns: 1fr 2fr 1fr;
    box-shadow: 1px 1px 5px 1px rgba(0, 0, 0, 0.5);
    background-color: rgba(0, 0, 0, 0.6);
    gap: .3rem;
    margin-bottom: 1rem;
    padding: .3rem;

    &-title {
        font-weight: 700;
    }

    &-room {
        font-size: 15px;
    }

    &-content {
        padding: 0 1rem;
    }

    &-time {
        font-size: 15px;
        text-align: right;
    }

    &-speaker {
        font-size: small;
        font-style: italic;
        opacity: .8;
    }

    &-time-left {
        font-size: small;
        opacity: .8;
    }
}
</style>
