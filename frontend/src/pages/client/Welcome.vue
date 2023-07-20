<template>
  <div class="front_page">
    <div class="homepage_section">
      <div class="text_section">
        <p class="accent_title">Proof of concept</p>
        <p class="main_title">
          SISTEM DESCENTRALIZAT <br />
          DE VOT ELECTRONIC
        </p>
        <p class="secondary_title">
          Aplicație web bazată pe contracte inteligente Ethereum
        </p>

        <button class="crazyBtn" @click="() => this.$router.push('/form')">START</button>
      </div>
      <img src="@/assets/images/User-research.svg" alt="" width="400" />
    </div>

    <FooterComponent></FooterComponent>
  </div>
</template>

<script>

export default {
  name: "WelcomeVotingDapp",
  components: {
    FooterComponent: () => import("@/components/footer.vue"),
  },
};
</script>

<style soped>
.homepage_section {
  min-height: 100vh;
  background-color: var(---background-color);
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-evenly;
  gap: 20px;
  margin-bottom: 100px;
  padding: 0 100px;
}

.text_section {
  margin-left: 200px;
  padding: 0;
}

.accent_title {
  font-style: italic;
  font-size: 20px;
  color: var(---secondary-color);
}

.main_title {
  font-size: 40px;
  font-weight: 700;
  color: var(---primary-color);
  margin-bottom: 30px;
}

.secondary_title {
  font-size: 20px;
  font-weight: 600;
  color: var(---secondary-color);
  margin-bottom: 100px;
}

.footer {
  position: fixed;
  left: 0;
  bottom: 0;
  width: 100%;
  height: 100px;
  background-color: var(---primary-color);
  color: var(---background-color);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  white-space: pre-line;
  z-index: -1;
}
</style>