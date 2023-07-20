<template>
    <div class="container-notfound">
        <img src="@/assets/images/404.svg" width="600" />

        <br>
        <br>
        <a href="/" style="font-size: 50px; font-weight: bold;">
            go home
        </a>
    </div>
</template>
  
<script>
export default {
    name: 'NotFound'
}

</script>
  
<style scoped>
.container-notfound {
    display: flex;
    flex-direction: column;
    gap: 20px;
    align-items: center;
    justify-content: center;
    overflow: none;
}
</style>