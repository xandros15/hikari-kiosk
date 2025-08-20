<template>
  <div class="speedroom" v-if="isSetRooms">
    <div class="room-left">
      <div class="has-text-centered is-2 title">
        <div class="arrow">⇦</div>
        {{ roomLeftName }}
      </div>
      <Attraction class="attraction" v-for="attraction in leftRoomAttractions" :key="attraction.id"
                  :title="attraction.title"
                  :room="attraction.room"
                  :speaker="attraction.speaker"
                  :startDatetime="attraction.startDatetime"
                  :endDatetime="attraction.endDatetime"
                  :date="attraction.day"
                  :time="time"
      />
    </div>
    <div class="room-right">
      <div class="has-text-centered is-2 title">{{ roomRightName }}
        <div class="arrow">⇨</div>
      </div>
      <Attraction class="attraction" v-for="attraction in rightRoomAttractions" :key="attraction.id"
                  :title="attraction.title"
                  :room="attraction.room"
                  :speaker="attraction.speaker"
                  :startDatetime="attraction.startDatetime"
                  :endDatetime="attraction.endDatetime"
                  :date="attraction.day"
                  :time="time"
      />
    </div>
  </div>
  <div v-else class="no-rooms">
    <h1 class="is-1 title has-text-centered">You need to set rooms. Add
      <br>
      <code>?LEFT_ROOM={left_room_name}&RIGHT_ROOM={right_room_name}</code>
      <br> to address.
    </h1>
  </div>
</template>

<script>
import Attraction from "@/components/Attraction.vue";
import {getNow} from "@/api/clock";
import {getAll} from "@/api/attractionTimetable";

const params = new URLSearchParams(window.location.search)
const LEFT_ROOM = params.has('LEFT_ROOM') ? params.get('LEFT_ROOM') : null
const RIGHT_ROOM = params.has('RIGHT_ROOM') ? params.get('RIGHT_ROOM') : null
const PER_PAGE = params.has('PER_PAGE') ? params.get('PER_PAGE') : 8;
const MAX_ATTRACTION = params.has('MAX_ATTRACTION') ? params.get('MAX_ATTRACTION') : 60;
const PAGE_FLIP_TIME = params.has('PAGE_FLIP_TIME') ? params.get('PAGE_FLIP_TIME') : 1000 * 10
const RELOAD_TIMETABLE_TIME = params.has('RELOAD_TIMETABLE_TIME') ? params.get('RELOAD_TIMETABLE_TIME') : 1000 * 60 * 10

export default {
  name: 'SpeedRoomTimetable',
  components: {Attraction},
  data() {
    return {
      page: 0,
      isSetRooms: false,
      roomLeftName: null,
      roomRightName: null,
      attractions: [],
      time: getNow(),
      interval: null,
    }
  },
  async created() {
    this.interval = setInterval(() => {
      this.time = getNow()
    }, 1000)
    this.isSetRooms = LEFT_ROOM && RIGHT_ROOM
    this.roomLeftName = LEFT_ROOM
    this.roomRightName = RIGHT_ROOM
    await this.loadAttractions()
    setInterval(() => this.loadAttractions(), RELOAD_TIMETABLE_TIME)
    setInterval(() => this.changePage(), PAGE_FLIP_TIME)
  },
  computed: {
    leftRoomAttractions() {
      const attractions = this.attractions.filter(v => v.endDatetime > this.time && v.room === LEFT_ROOM)
      if (this.page === null) {
        return attractions;
      }

      return attractions.slice(Math.floor(PER_PAGE / 2) * this.page, (this.page + 1) * Math.floor(PER_PAGE / 2))
    },
    rightRoomAttractions() {
      const attractions = this.attractions.filter(v => v.endDatetime > this.time && v.room === RIGHT_ROOM)
      if (this.page === null) {
        return attractions;
      }

      return attractions.slice(Math.floor(PER_PAGE / 2) * this.page, (this.page + 1) * Math.floor(PER_PAGE / 2))
    },
  },
  methods: {
    async loadAttractions() {
      if (!this.isSetRooms) {
        return;
      }

      const onAir = []
      const notReady = []
      for (const a of await getAll([LEFT_ROOM, RIGHT_ROOM])) {
        if (this.time >= a.startDatetime && this.time <= a.endDatetime) {
          onAir.push(a)
        } else {
          notReady.push(a)
        }
      }
      onAir.sort((a, b) => a.endDatetime - b.endDatetime)
      notReady.sort((a, b) => a.startDatetime - b.startDatetime)

      this.attractions = [...onAir, ...notReady].slice(0, MAX_ATTRACTION);
    },
    changePage() {
      ++this.page
      if (
          this.leftRoomAttractions.length === 0
          || this.rightRoomAttractions.length === 0
      ) {
        this.page = 0
      }
    },
  },
}
</script>

<style scoped lang="scss">
.speedroom {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem
}

.title {
  color: var(--bulma-body-color);
  position: relative;
}

.arrow {
  //transform: scale(2);
  display: inline-block;
  position: relative;
  //top: 30px;
  //line-height: 1;
  //animation: 1s infinite alternate arrow-spin;
  //animation-delay: 0s;
  //animation-timing-function: linear;
  //overflow: hidden;
  //left: 15px
}

@keyframes arrow-spin {
  //from {
  //  transform: rotateX(0deg) scale(2)
  //}
  //to {
  //  transform: rotateX(180deg)  scale(2)
  //}
}
</style>
