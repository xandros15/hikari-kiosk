<template>
  <div class="app">
    <div class="container">
      <div class="attractions">
        <Attraction class="attraction" v-for="attraction in paginatedAttractions" :key="attraction.id"
                    :title="attraction.title"
                    :room="attraction.room"
                    :speaker="attraction.speaker"
                    :startDatetime="attraction.startDatetime"
                    :endDatetime="attraction.endDatetime"
                    :date="attraction.day"
                    :time="time"
        />
      </div>
      <div class="attractions-footer">
        <div style="display: flex; align-items: center; gap: 1rem">
          <img class="image" style="height: 100px" alt="https://konwencik.pl"
               src="../assets/qr-konwencik.jpg">
          <div style="margin-left: .3rem; font-size: 130%">
            Opisy atrakcji i pełen program<br> w aplikacji <b>Konwencik</b>
          </div>
        </div>
        <div
            style="display: flex; align-items: center; gap: 1rem">
          <div style="margin-left: .3rem; font-size: 130%">
            Chcesz kupić jakieś goodsy?<br> Zapraszamy na <b>stoiska wystawców!</b>
          </div>
          <img class="image" style="height: 100px" alt="https://hikari.pl/lista-wystawcow"
               src="../assets/qr-wystawcy.jpg">
        </div>
      </div>
      <EasterEgg v-if="displayEgg" :type="numberEgg"/>
    </div>
  </div>
</template>

<script>
import {getAll} from "./api/attractionTimetable";
import {getNow} from "./api/clock";
import Attraction from "./components/Attraction.vue";
import EasterEgg from "@/components/EasterEgg.vue";


const params = new URLSearchParams(window.location.search)
const PLACES = params.has('PLACES') ? params.get('PLACES').split(',') : null
const WHITE_LIST = params.has('WHITE_LIST') ? params.get('WHITE_LIST').split(',') : null
const PER_PAGE = params.has('PER_PAGE') ? params.get('PER_PAGE') : 10;
const MAX_ATTRACTION = params.has('MAX_ATTRACTION') ? params.get('MAX_ATTRACTION') : 60;
const PAGE_FLIP_TIME = params.has('PAGE_FLIP_TIME') ? params.get('PAGE_FLIP_TIME') : 1000 * 15
const EASTER_EGG_TIME = params.has('EASTER_EGG_TIME') ? params.get('EASTER_EGG_TIME') : 1000 * 60 * 10
const RELOAD_TIMETABLE_TIME = params.has('RELOAD_TIMETABLE_TIME') ? params.get('RELOAD_TIMETABLE_TIME') : 1000 * 60 * 10

export default {
  name: 'app',
  components: {EasterEgg, Attraction},
  data() {
    return {
      page: 0,
      attractions: [],
      time: getNow(),
      interval: null,
      displayEgg: false,
      numberEgg: 1,
    }
  },
  async created() {
    this.interval = setInterval(() => {
      this.time = getNow()
    }, 1000)
    await this.loadAttractions()
    setInterval(() => this.loadAttractions(), RELOAD_TIMETABLE_TIME)
    setInterval(() => this.changePage(), PAGE_FLIP_TIME)
    this.showEgg()
  },
  computed: {
    paginatedAttractions() {
      const attractions = this.attractions.filter(v => v.endDatetime > this.time)
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
      for (const a of await getAll(PLACES, WHITE_LIST)) {
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
      if (this.paginatedAttractions.length === 0) {
        this.page = 0
      }
    },
    showEgg() {
      if (EASTER_EGG_TIME === null) {
        return
      }
      this.displayEgg = true
      setTimeout(() => {
        this.displayEgg = false
        this.numberEgg = this.numberEgg === 3 ? 1 : this.numberEgg + 1
        this.showEgg()
      }, EASTER_EGG_TIME)
    }
  }
}
</script>

<style lang="scss">
.app {
  background-image: url("../assets/bg.jpg");
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
  min-height: 100vh;
}

.container {
  z-index: 1;
  display: flex;
  justify-content: center;
  flex-direction: column;
  padding: 5rem 2rem;
}

.attractions-footer {
  font-size: 15px;
  box-shadow: 1px 1px 5px 1px rgba(0, 0, 0, 0.5);
  background-color: rgba(0, 0, 0, 0.6);
  gap: 1rem;
  margin-bottom: 1rem;
  padding: .7rem;
  display: flex;
  justify-content: space-between;
}

.attractions {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  column-gap: 1rem;
  @media screen and (max-width: 1080px) {
    grid-template-columns:  1fr;
  }
}
</style>
