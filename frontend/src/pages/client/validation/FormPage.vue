<template>
  <div v-if="!loading">
    <div class="container">
      <div class="form-title">
        <h2>VALIDATION</h2>
        <p>Validate your identity with real data</p>
        <ValidationForm @form-submitted="onFormSubmitted" />
      </div>
      <div class="aside form-display-none">
        <img src="@/assets/images/Agreement.svg" alt="" width="400" />
      </div>
    </div>

    <DialogUploadIdCard ref="dlgUploadIdCard" :cnp="this.formData.get('cnp') ? this.formData.get('cnp') : ''"
      @upload-id-card="onUploadIdCard" />

    <DialogCamera ref="dlgSelfieCamera" @upload-selfie="onUploadSelfie" />

    <FooterComponent />
  </div>
  <loader v-else />
</template>

<script>
import LocalSettings from '@/backend/LocalSettings';

export default {
  name: "Form",
  components: {
    ValidationForm: () => import("./components/ValidationForm.vue"),
    FooterComponent: () => import("@/components/footer.vue"),
    DialogUploadIdCard: () => import("./components/DialogUploadIdCard.vue"),
    DialogCamera: () => import("./components/DialogCamera.vue"),
    loader: () => import("@/components/loader.vue"),
  },
  data() {
    return {
      baseUrl: LocalSettings.BASE_URL_PHP,
      formData: new FormData(),
      loading: false,
    };
  },
  methods: {
    onFormSubmitted(formData) {
      this.formData = formData;

      console.log(this.formData.get("cnp"));
      this.$refs.dlgUploadIdCard.show_me();
    },
    onUploadIdCard(fileIdCard) {
      this.formData.append("id_card", fileIdCard.raw, 'id_card');

      console.log("onUploadIdCard", this.formData.get("id_card"));

      this.$refs.dlgSelfieCamera.show_me(fileIdCard);
    },
    async onUploadSelfie(fileSelfie) {
      this.loading = true;

      this.$message({
        message: "Uploading data... please wait",
        type: "info",
      });

      this.formData.append("selfie", fileSelfie, 'selfie');

      var response = await this.$http.post(
        this.baseUrl + "/validation_form/submit_form",
        this.formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      this.loading = false;

      if (response.data) {
        this.$router.push({ name: "registered-form" });
      } else {
        this.$message({
          message: "Failed to upload data. Please try again.",
          type: "error",
        });
      }


    },
  },
};
</script>

<style scoped>
.container {
  display: flex;
  flex-direction: row;
  min-height: 100vh;
  margin-bottom: 100px;
}

.aside {
  display: flex;
  justify-content: center;
  flex: 1;
  align-items: center;
  padding: 50px;
  background: var(---primary-color);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
}

.form-title {
  background-color: var(---background-color);
  display: flex;
  flex-direction: column;
  flex: 2;
  align-items: center;
  padding-top: 50px;
  padding-bottom: 40px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
}

.form-title h2 {
  color: var(---primary-color);
}

.form-title p {
  color: var(---secondary-color);
}

@media only screen and (max-width: 481px) {
  .steps-bar {
    display: none;
  }
}

@media only screen and (max-width: 1200px) {
  .form-display-none {
    display: none;
  }
}
</style>