<template>
  <div class="double-room" v-if="isSetRooms">
    <div class="room-left">
      <div v-if="roomLeftName" class="has-text-centered is-2 title">
        <div class="arrow">⇦</div>
        {{ roomLeftName }}
      </div>
      <Attraction class="attraction" v-for="attraction in leftRoomAttractions" :key="attraction.id"
                  :title="attraction.title"
                  :room="attraction.room"
                  :speaker="attraction.speaker"
                  :startDateTime="attraction.startDateTime"
                  :endDateTime="attraction.endDateTime"
                  :time="time"
      />
    </div>
    <div class="room-right">
      <div v-if="roomRightName" class="has-text-centered is-2 title">
        {{ roomRightName }}
        <div class="arrow">⇨</div>
      </div>
      <Attraction class="attraction" v-for="attraction in rightRoomAttractions" :key="attraction.id"
                  :title="attraction.title"
                  :room="attraction.room"
                  :speaker="attraction.speaker"
                  :startDateTime="attraction.startDateTime"
                  :endDateTime="attraction.endDateTime"
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
  name: 'DoubleRoomTimetable',
  components: {Attraction},
  data() {
    return {
      page: 0,
      isSetRooms: false,
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
    await this.loadAttractions()
    setInterval(() => this.loadAttractions(), RELOAD_TIMETABLE_TIME)
    setInterval(() => this.changePage(), PAGE_FLIP_TIME)
  },
  computed: {
    roomLeftName(){
      return this.leftRoomAttractions[0]?.room;
    },
    roomRightName(){
      return this.rightRoomAttractions[0]?.room;
    },
    leftRoomAttractions() {
      const attractions = this.attractions.filter(v => v.endDateTime > this.time && v.room_id === LEFT_ROOM)
      if (this.page === null) {
        return attractions;
      }

      return attractions.slice(Math.floor(PER_PAGE / 2) * this.page, (this.page + 1) * Math.floor(PER_PAGE / 2))
    },
    rightRoomAttractions() {
      const attractions = this.attractions.filter(v => v.endDateTime > this.time && v.room_id === RIGHT_ROOM)
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
        if (this.time >= a.startDateTime && this.time <= a.endDateTime) {
          onAir.push(a)
        } else {
          notReady.push(a)
        }
      }
      onAir.sort((a, b) => a.endDateTime - b.endDateTime)
      notReady.sort((a, b) => a.startDateTime - b.startDateTime)

      this.attractions = [...onAir, ...notReady].slice(0, MAX_ATTRACTION);
    },
    changePage() {
      ++this.page
      if (
          this.leftRoomAttractions.length === 0
          && this.rightRoomAttractions.length === 0
      ) {
        this.page = 0
      }
    },
  },
}
</script>

<style scoped lang="scss">
.double-room {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem
}

.title {
  color: var(--bulma-body-color);
  position: relative;
}

.arrow {
  display: inline-block;
  position: relative;
}
</style>
