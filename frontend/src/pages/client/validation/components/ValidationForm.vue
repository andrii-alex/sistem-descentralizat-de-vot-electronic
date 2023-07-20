<template>
  <div>
    <el-form :model="form" ref="validation_form" class="validation_form" label-position="top" :rules="rules"
      @submit.prevent="continueForm">
      <el-row :gutter="20">
        <el-col :span="24" :md="12" :sm="12"><el-form-item label="First name" prop="first_name">
            <el-input v-model="form.first_name" placeholder="" autocomplete="nope-1234" /> </el-form-item></el-col>
        <el-col :span="24" :md="12" :sm="12"><el-form-item label="Last name" prop="last_name">
            <el-input v-model="form.last_name" placeholder="" autocomplete="nope-1234" /> </el-form-item></el-col>
        <el-col :span="24" :md="24" :sm="24">
          <el-form-item label="Email" prop="email">
            <el-input @focus="focusEmail" v-model="form.email" type="email" autocomplete="nope-1234"
              suffix-icon="el-icon-message" :disabled="emailDisabled" />
          </el-form-item>
          <DialogEmail ref="dlgEmail" @email-submitted="handleEmailSubmitted" />
        </el-col>
        <el-col :span="24" :md="24" :sm="24"><el-form-item label="Phone" prop="phone">
            <el-input v-model="form.phone" autocomplete="nope-1234" suffix-icon="el-icon-phone">
              <template slot="prepend">+(40)</template>
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="24" :md="12" :sm="12"><el-form-item label="County" prop="county">
            <el-select v-model="form.county" placeholder="" filterable autocomplete="nope-1234" style="width: 100%">
              <el-option v-for="county in info.counties" :label="county.nume" :value="county.nume"
                :key="county.nume"></el-option>
            </el-select> </el-form-item></el-col>
        <el-col :span="24" :md="12" :sm="12"><el-form-item label="CNP" prop="cnp">
            <el-input v-model="form.cnp" placeholder="" autocomplete="nope-1234" maxlength="13" minlength="13"
              show-word-limit /> </el-form-item></el-col>
      </el-row>

      <button @click.prevent="continueForm" class="submitBtn">CONTINUE</button>
    </el-form>
  </div>
</template>

<script>
import LocalSettings from "@/backend/LocalSettings";
import DialogEmail from "./DialogEmail.vue";

export default {
  name: "ValidationForm",
  components: {
    DialogEmail,
  },
  data() {
    return {
      base_url: LocalSettings.BASE_URL_PHP,
      loading: false,
      form_submitted: false,
      selfie_tag_status: "",
      emailDisabled: false,
      form: {
        first_name: "",
        last_name: "",
        county: "",
        cnp: "",
        phone: "",
        email: "",
        img_selfie: "",
        img_id: "",
        selfie: null,
        id_card: null,
      },
      camera_dialog_visible: false,
      dialogVisible: false,
      info: {
        counties: [],
      },
      rules: {
        first_name: [
          {
            required: true,
            message: "First name is required",
            trigger: "blur",
          },
          {
            pattern: /^[a-zA-Z\s]*$/,
            message:
              "First name should only contain alphabetic characters and whitespace",
            trigger: "blur",
          },
          {
            max: 30,
            message: "First name cannot exceed 30 characters",
            trigger: "blur",
          },
        ],
        last_name: [
          { required: true, message: "Last name is required", trigger: "blur" },
          {
            pattern: /^[a-zA-Z\s]*$/,
            message:
              "Last name should only contain alphabetic characters and whitespace",
            trigger: "blur",
          },
          {
            max: 30,
            message: "Last name cannot exceed 30 characters",
            trigger: "blur",
          },
        ],
        email: [
          { required: true, message: "Email is required", trigger: "blur" },
        ],
        county: [
          { required: true, message: "County is required", trigger: "blur" },
        ],
        cnp: [
          { required: true, message: "CNP is required", trigger: "blur" },
          {
            pattern: /^\d{13}$/,
            message: "CNP should be 13 digits",
            trigger: "blur",
          },
        ],
        phone: [
          { required: true, message: "Phone is required", trigger: "blur" },
          {
            pattern: /^\d{8,15}$/,
            message: "Phone should be between 8-15 digits",
            trigger: "blur",
          },
        ],
        img_id: [
          {
            required: true,
            message: "Upload of ID card is required",
            trigger: "blur",
          },
        ],
        img_selfie: [
          { required: true, message: "Selfie is required", trigger: "blur" },
        ],
      },
    };
  },
  methods: {
    change_upload_idcard(file, fileList) {
      this.form.id_card = file;
    },
    change_upload_selfie(file, fileList) {
      this.form.selfie = file;
    },
    succes_upload_idcard(response, file, fileList) {
      this.form.img_id = response.img_id;
    },
    handleSelfie(object) {
      this.form.img_selfie = object.selfie_path;
      this.status_success_upload = object.status_success_upload;
      this.selfie_tag_status = object.success;
    },
    openCameraDialog() {
      this.$refs.cameraDialog.show_me();
    },
    continueForm() {
      this.$refs["validation_form"].validate(async (valid) => {
        if (valid) {
          var formData = this.loadFormData();

          var response = await this.$http.post(
            this.base_url + "/validation_form/check_cnp",
            formData
          );

          if (!response.data) {
            this.$message({
              message: "No voter registered with this CNP!",
              type: "error",
            });
            return;
          }

          this.$emit("form-submitted", formData);
        }
      });
    },
    loadFormData() {
      const formData = new FormData();

      formData.append("first_name", this.form.first_name);
      formData.append("last_name", this.form.last_name);
      formData.append("county", this.form.county);
      formData.append("cnp", this.form.cnp);
      formData.append("phone", this.form.phone);
      formData.append("email", this.form.email);
      formData.append("selfie", this.form.selfie);
      formData.append("id_card", this.form.id_card);

      return formData;
    },
    resetForm() {
      this.selfie_tag_status = "";
      this.$refs["validation_form"].resetFields();
      this.$refs.refUploadIdCard.clearFiles();
      this.$refs.cameraDialog.resetCamera();
    },
    check_if_form_submitted() {
      const token = localStorage.getItem("form_submitted_id");
      if (!token) {
        return;
      }
      this.form_submitted = true;
    },
    async fetchcountiesRomania() {
      var response = await this.$http.get("https://roloca.coldfuse.io/judete")
        .then((response) => {
          this.info.counties = response.data;
        })
        .catch((error) => {
          console.error(error);
        });
    },
    formSubmitted(response) {
      this.$message.success("Form submitted!");
      this.form_submitted = true;
      localStorage.setItem("form_submitted_id", response.data.insert_id);
    },
    focusEmail() {
      this.$refs.dlgEmail.show_me();
    },
    handleEmailSubmitted(email) {
      this.emailDisabled = true;
      this.form.email = email;
      console.log(this.form.email);
    },
  },
  computed: {},
  mounted() {
    this.loading = true;
    this.check_if_form_submitted();
    this.fetchcountiesRomania();
    this.loading = false;
  },
};
</script>

<style scoped>
.validation_form {
  height: auto;
  color: white !important;
  padding: 50px 100px;
}

.buttons {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  flex-wrap: nowrap !important;
  gap: 10px;
}

@media only screen and (max-width: 1000px) {
  .mobile_display_none {
    display: none;
  }
}
</style>

<style scoped>
.submitBtn {
  width: 100%;
  position: relative;
  height: 3.5em;
  border: 3px ridge var(---primary-color);
  outline: none;
  background-color: transparent;
  color: var(---primary-color);
  -webkit-transition: 1s;
  transition: 1s;
  border-radius: 0.3em;
  font-size: 16px;
  font-weight: bold;
  margin-top: 100px;
}

.submitBtn::after {
  content: "";
  position: absolute;
  top: -10px;
  left: 3%;
  width: 95%;
  height: 40%;
  background-color: var(---background-color);
  -webkit-transition: 0.5s;
  transition: 0.5s;
  -webkit-transform-origin: center;
  -ms-transform-origin: center;
  transform-origin: center;
}

.submitBtn::before {
  content: "";
  -webkit-transform-origin: center;
  -ms-transform-origin: center;
  transform-origin: center;
  position: absolute;
  top: 80%;
  left: 3%;
  width: 95%;
  height: 40%;
  background-color: var(---background-color);
  -webkit-transition: 0.5s;
  transition: 0.5s;
}

.submitBtn:hover::before,
.submitBtn:hover::after {
  -webkit-transform: scale(0);
  -ms-transform: scale(0);
  transform: scale(0);
}

.submitBtn:hover {
  -webkit-box-shadow: inset 0px 0px 25px var(---primary-color);
  box-shadow: inset 0px 0px 25px var(---primary-color);
}
</style>