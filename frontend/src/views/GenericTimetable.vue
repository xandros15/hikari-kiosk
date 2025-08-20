<template>
    <div class="attractions">
      <Attraction class="attraction" v-for="attraction in paginatedAttractions" :key="attraction.id"
                  :title="attraction.title"
                  :room="attraction.room"
                  :speaker="attraction.speaker"
                  :startDateTime="attraction.startDateTime"
                  :endDateTime="attraction.endDateTime"
                  :date="attraction.day"
                  :time="time"
      />
    </div>
</template>

<script>
import {getAll} from "@/api/attractionTimetable";
import {getNow} from "@/api/clock";
import Attraction from "@/components/Attraction.vue";
import EasterEgg from "@/components/EasterEgg.vue";

const params = new URLSearchParams(window.location.search)
const ROOMS = params.has('ROOMS') ? params.get('ROOMS').split(',') : null
const PER_PAGE = params.has('PER_PAGE') ? params.get('PER_PAGE') : 10;
const MAX_ATTRACTION = params.has('MAX_ATTRACTION') ? params.get('MAX_ATTRACTION') : 60;
const PAGE_FLIP_TIME = params.has('PAGE_FLIP_TIME') ? params.get('PAGE_FLIP_TIME') : 1000 * 15
const RELOAD_TIMETABLE_TIME = params.has('RELOAD_TIMETABLE_TIME') ? params.get('RELOAD_TIMETABLE_TIME') : 1000 * 60 * 10

export default {
  name: 'GenericTimetable',
  components: {EasterEgg, Attraction},
  data() {
    return {
      page: 0,
      attractions: [],
      time: getNow(),
      interval: null,
    }
  },
  async created() {
    this.interval = setInterval(() => {
      this.time = getNow()
    }, 1000)
    await this.loadAttractions()
    setInterval(() => this.loadAttractions(), RELOAD_TIMETABLE_TIME)
    setInterval(() => this.changePage(), PAGE_FLIP_TIME)
  },
  computed: {
    paginatedAttractions() {
      const attractions = this.attractions.filter(v => v.endDateTime > this.time)
      if (this.page === null) {
        return attractions;
      }

      return attractions.slice(PER_PAGE * this.page, (this.page + 1) * PER_PAGE)
    }
  },
  methods: {
    async loadAttractions() {
      const onAir = []
      const notReady = []
      for (const a of await getAll(ROOMS)) {
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
      if (this.paginatedAttractions.length === 0) {
        this.page = 0
      }
    },
  }
}
</script>

<style scoped lang="scss">
.attractions {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  column-gap: 1rem;
  @media screen and (max-width: 1080px) {
    grid-template-columns:  1fr;
  }
}
</style>
