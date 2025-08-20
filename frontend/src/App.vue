<template>
  <div class="app">
    <div class="container">
      <RouterView/>
      <HikariFooter/>
      <EasterEgg v-if="displayEgg" :type="numberEgg"/>
    </div>
  </div>
</template>

<script>
import EasterEgg from "@/components/EasterEgg.vue";
import HikariFooter from "@/components/HikariFooter.vue";

const params = new URLSearchParams(window.location.search)
const EASTER_EGG_TIME = params.has('EASTER_EGG_TIME') ? params.get('EASTER_EGG_TIME') : 1000 * 60 * 10

export default {
  name: 'app',
  components: {HikariFooter, EasterEgg},
  async created() {
    this.showEgg()
  },
  data() {
    return {
      displayEgg: false,
      numberEgg: 1,
    }
  },
  methods: {
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
</style>
