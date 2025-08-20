<script setup lang="ts">

</script>

<template>
  <div class="attractions-footer">
    <div style="display: flex; align-items: center; gap: 1rem">
      <img class="image" style="height: 100px" alt="https://konwencik.pl"
           src="../../assets/qr-konwencik.jpg">
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
           src="../../assets/qr-wystawcy.jpg">
    </div>
  </div>
</template>

<style scoped lang="scss">
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
</style>
