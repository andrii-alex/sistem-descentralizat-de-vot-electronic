<template>
  <div class="login-bg">
    <el-card class="box-card login-box" v-loading="loading">
      <template #header>
        <div class="card-header">
          <span><b>LOGIN ADMIN</b></span>
          <!-- <el-button class="button" text>Operation button</el-button> -->
        </div>
      </template>
      <el-form ref="login" :model="loginForm" @submit.prevent :rules="rules" label-position="top">
        <el-form-item label="Username" prop="username" label-width="50px">
          <el-input type="text" v-model="loginForm.username" autocomplete="nope-1234"></el-input>
        </el-form-item>
        <el-form-item label="Password" prop="password" label-width="50px">
          <el-input type="password" v-model="loginForm.password" show-password autocomplete="nope-1234"></el-input>
        </el-form-item>
        <br />
        <el-form-item>
          <div class="login-button">
            <el-button native-type="submit" type="success" @click="login()" icon="el-icon-user"
              :loading="loading">Login</el-button>
          </div>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import LocalSettings from "@/backend/LocalSettings";
import axios from "axios";

export default {
  name: "LoginAdmin",
  data() {
    return {
      loading: false,
      loginForm: {
        username: "",
        password: "",
      },
      rules: {
        username: [
          {
            required: true,
            message: "Please enter a username",
            trigger: "blur",
          },
        ],
        password: [
          {
            required: true,
            message: "Please enter a password",
            trigger: "blur",
          },
        ],
      },
    };
  },
  methods: {
    login() {
      this.loading = true;
      this.$refs.login.validate(async (valid) => {
        if (valid) {
          try {
            axios
              .post(LocalSettings.BASE_URL_PHP + "/admin/login", {
                username: this.loginForm.username,
                password: this.loginForm.password,
              })
              .then((response) => {
                const token = response.data.token;
                localStorage.setItem(LocalSettings.TOKEN_KEY, token);
                this.$message.success("LOGARE CU SUCCES!");

                setTimeout(() => {
                  this.$router.push("/admin/dashboard");
                }, 1000);
                // return jwtDecode(token);
              })
              .catch((error) => {
                this.loading = false;
                this.$message.error(error.response.data.message);
                throw error;
              });
          } catch (error) {
            this.loading = false;
            this.$message.error(error);
          }
        } else {
          return false;
        }
      });
    },
  },
  mounted: function () { },
};
</script>

<style scoped>
.login-bg {
  /* background: @gri no-repeat center center; */
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  font-weight: 400;
  /* background: rgb(64, 158, 255);
  background: linear-gradient(180deg,
      rgba(64, 158, 255, 1) 35%,
      rgba(255, 255, 255, 1) 100%); */
  background-color: var(--dark);
}

.login-box {
  width: 320px;
}

.login-button {
  display: flex;
  width: 100%;
  flex-direction: row-reverse !important;
}
</style>
