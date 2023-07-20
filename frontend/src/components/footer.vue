<template>
  <footer class="footer">
    <img src="@/assets/images/logo-dice-white-fill.png" width="50px" />
    &nbsp; &nbsp;
    <p>2023 București | Andrii Alexandru-Marian ©</p>
  </footer>
</template>

<script>
export default {
  name: "FooterComponent",
};
</script>

<style scoped>
.footer {
  position: fixed;
  left: 0;
  bottom: 0;
  width: 100%;
  height: 100px;
  background-color: var(---secondary-color);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  white-space: pre-line;
  z-index: -1;
}
</style>