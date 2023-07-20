<template>
    <div class="front_page">
        <el-result icon="success" title="Registration successful!"
            subTitle="Congratulations on successfully completing the validation form for the voting system! Keep an eye on your email for the next step, where you will receive a link to generate your unique EVUID (Electronic Voter Unique Identifier). This step is crucial for ensuring the integrity and security of the voting process. Stay tuned for further instructions and get ready to participate in the upcoming election with confidence.">
        </el-result>

        <FooterComponent></FooterComponent>
    </div>
</template>
  
<script>
export default {
    name: "WelcomeVotingDapp",
    components: {
        FooterComponent: () => import("@/components/footer.vue"),
    },
    data() {
        return {
        };
    },
    methods: {
    },
    mounted() { },
};
</script>
  
<style scoped>
.front_page {
    min-height: 100vh;
    background-color: var(---primary-color);
    display: flex;
    justify-content: center;
    align-content: center;
    margin-bottom: 100px;
    padding: 0px 150px;
}

.footer {
    background-color: var(---background-color) !important;
    color: var(---primary-color) !important;
}
</style>
  