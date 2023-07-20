<template>
  <el-dialog title="Please input email for verification" :visible.sync="showPopup" append-to-body width="90%"
    :close-on-press-escape="false" :show-close="false" center>
    <el-form :model="form" ref="email_form" label-position="top" :rules="rulesEmail" @submit.prevent="send_code">
      <el-form-item prop="email">
        <template #label>
          <span style="color: black:  !important;">Email &nbsp;</span>
          <el-tooltip class="item" effect="dark" placement="top">
            <div slot="content">
              Please enter a valid email address. <br />
              This is required for upcoming steps.
            </div>
            <i class="el-icon-info"></i>
          </el-tooltip>
        </template>
        <el-input v-model="form.email" type="email" autocomplete="nope-1234" suffix-icon="el-icon-message" />
      </el-form-item>
      <el-form-item v-if="verifyClicked">
        <template #label> Cod Verificare </template>
        <el-input v-model="form.codVerificare" type="email" autocomplete="nope-1234" suffix-icon="el-icon-message" />
      </el-form-item>
    </el-form>

    <div class="verify_button">
      <button v-if="!verifyClicked" class="default_button" @click="send_code" v-loading="loading">
        send code
      </button>
      <button v-else class="default_button" @click="submit_email">
        verify email
      </button>
    </div>
  </el-dialog>
</template>

<script>
import LocalSettings from "@/backend/LocalSettings";

export default {
  name: "DialogEmail",
  data() {
    return {
      loading: false,
      showPopup: false,
      form: {
        email: "",
        codVerificare: "",
      },
      codGeneratVerificare: "",
      verifyClicked: false,
      rulesEmail: {
        email: [
          { required: true, message: "Email is required", trigger: "blur" },
          {
            type: "email",
            message: "Please enter a valid email address",
            trigger: ["blur", "change"],
          },
        ],
      },
    };
  },
  methods: {
    show_me() {
      this.showPopup = true;
    },
    async send_code() {
      const valid = await this.$refs.email_form.validate();

      if (!valid) return;

      this.loading = true;

      var response = await this.$http.post(
        LocalSettings.BASE_URL_PHP + "/send_email",
        {
          email: this.form.email,
        }
      );

      if (response.data.status_trimitere) {
        this.$message.success("Verification email sent to " + this.form.email);
        this.codGeneratVerificare = response.data.code;
        // console.log(this.codGeneratVerificare);
      } else {
        this.$message.error("Error sending email");
        return;
      }

      this.verifyClicked = true;
      this.loading = false;
    },
    submit_email() {
      if (this.form.codVerificare == this.codGeneratVerificare) {
        this.$message.success("Email verification successful!");

        this.$emit("email-submitted", this.form.email);

        this.reset_all();
      } else {
        this.$message.error("Incorrect email code");
      }
    },

    reset_all() {
      this.form.email = "";
      this.form.codVerificare = "";
      this.codGeneratVerificare = "";
      this.verifyClicked = false;
      this.showPopup = false;
    },
  },
};
</script>

<style>
.verify_button {
  padding: 20px 0px;
  width: 100%;
  display: flex;
  justify-content: end;
}

.el-form-item__label {
  color: var(---primary-color) !important;
}
</style>