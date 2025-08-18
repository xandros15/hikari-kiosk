<template>
    <div class="attraction" :class="{'attraction-in-progress': isInProgress}">
        <div class="attraction-room">{{ room }}</div>
        <div class="attraction-content">
            <div class="attraction-title">
                {{ normalizedTitle }}
            </div>
            <div class="attraction-speaker">
                {{ speaker }}
            </div>
        </div>
        <div class="attraction-time" v-if="isInProgress" :title="startDatetime">
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
        <div class="attraction-time" v-else-if="isAlmostStart" :title="startDatetime">
            <div class="attraction-status">
                Zaraz się zacznie
            </div>
            <div class="attraction-time-left">
                [Do rozpoczęcia {{ toStart }}]
            </div>
        </div>
        <div class="attraction-time" :title="startDatetime" v-else>
            Start o {{ startDatetime.getHours() }}:{{ (startDatetime.getMinutes() + '').padStart(2, '0') }}
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
            type: String,
        },
        date: {
            required: true,
            type: String,
        },
        startDatetime: {
            required: true,
            type: Date,
        },
        endDatetime: {
            required: true,
            type: Date
        }
    },
    computed: {
        isInProgress() {
            return this.time >= this.startDatetime && this.time <= this.endDatetime
        },
        normalizedTitle() {
            return this.title.length > 70 ? this.title.substring(0, 67) + '...' : this.title
        },
        isAlmostEnd() {
            return ALMOST_END_IN > this.endDatetime - this.time
        },
        isAlmostStart() {
            return ALMOST_START_IN > this.startDatetime - this.time
        },
        toStart() {
            const time = (this.startDatetime - this.time) / 1000;
            const minutes = (Math.floor(time / 60 % 60) + '').padStart(2, '0')
            const seconds = (Math.trunc(time % 60) + '').padStart(2, '0')

            return `${minutes}m ${seconds}s`;
        },
        toFinish() {
            const time = (this.endDatetime - this.time) / 1000;
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