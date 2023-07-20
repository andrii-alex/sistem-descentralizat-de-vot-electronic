<template>
  <div>
    <loader v-if="userObject == null"></loader>
    <div v-else>
      <div class="card_evuid">
        <h2 class="card_title">Electronic voter unique identifier</h2>
        <p class="card_subtitle"> generated using blind signature</p>
        <div class="user-info hidden-xs-only">
          <h3>User Info</h3>
          <el-row>
            <ul>
              <el-col :md="12" :xs="24">
                <li>
                  <span class="label">First name:</span>
                  <span class="value">{{ userObject.first_name }}</span>
                </li>
                <li>
                  <span class="label">Last name:</span>
                  <span class="value">{{ userObject.last_name }}</span>
                </li>
                <li>
                  <span class="label">CNP:</span>
                  <span class="value">{{ userObject.cnp }}</span>
                </li>
                <li>
                  <span class="label">Telephone:</span>
                  <span class="value">{{ userObject.phone }}</span>
                </li>
              </el-col>
              <el-col :md="12" :xs="24">
                <li>
                  <span class="label">Email:</span>
                  <span class="value">{{ userObject.email }}</span>
                </li>
                <li>
                  <span class="label">County:</span>
                  <span class="value">{{ userObject.county }}</span>
                </li>
                <li>
                  <span class="label">Address:</span>
                  <span class="value">{{ userObject.address }}</span>
                </li>
                <li>
                  <span class="label">Remarks:</span>
                  <span class="value">
                    <el-tag type="success">Eligible to vote</el-tag>
                  </span>
                </li>
              </el-col>
            </ul>
          </el-row>
        </div>

        <div class="descriptions" v-if="!loading">
          <h3>Public Key Info</h3>
          <ul>
            <li>
              <span class="label">Public modulus (N): </span>
              <p class="large-number">{{ N }}</p>
            </li>
            <li>
              <span class="label">Exponent public (E): </span>
              <span class="large-number">{{ E }}</span>
            </li>
          </ul>
        </div>
        <div class="loading" v-else>Loading public key info...</div>

        <div class="input_button">
          <el-input v-model="voter.evuid" placeholder="EVUID" autocomplete="nope-1234" maxlength="15" minlength="15"
            show-word-limit style="width: 400px" />

          <el-row type="flex" justify="center">
            <button class="default_button" type="info" @click="blindMessage()" v-loading="loading">Blind EVUID</button>
          </el-row>

          <div v-if="voter.blinded && voter.r" class="input_button">
            <p class="bold">Blinded EVUID: </p><span class="large-number"> {{ voter.blinded }}</span>
            <p class="bold">R blinding factor: </p> <span class="large-number">{{ voter.r }}</span>

            <button class="default_button" @click="getSignature()" v-loading="isLoadingApi">Get signature</button>
          </div>

          <div v-if="voter.signed" class="descriptions input_button">
            <p class="bold">Signature of Central Authority: </p><span class="large-number"> {{ voter.signed }}</span>

            <button class="default_button" @click="unblindSignature()">Unblind</button>
          </div>

          <div v-if="voter.unblinded_signature" class="descriptions input_button">
            <p class="bold">Unblinded signature of Central Authority: </p><span class="large-number"> {{
              voter.unblinded_signature
            }}</span>

            <button class="default_button" @click="verify()" icon="el-icon-warning-outline">VERIFY</button>
            <button :disabled="!voter.unblinded_signature" class="default_button"
              @click="() => this.$refs.evuidCardDialog.showMe()" icon="el-icon-postcard">EVUID CARD</button>
          </div>

        </div>
      </div>

      <EvuidCardDialog ref="evuidCardDialog" :email="userObject.email" :evuid="voter.evuid"
        :signature="voter.unblinded_signature" @closeEvuidCardDialog="visible = false"></EvuidCardDialog>
    </div>
  </div>
</template>

<script>
import NotFound from "@/pages/NotFound.vue";
import LocalSettings from "@/backend/LocalSettings";
import "element-ui/lib/theme-chalk/display.css";
import CryptoJS from "crypto-js";
import Clipboard from "clipboard";
import * as BlindSignature from "@/utils/blindsignature";
import EvuidCardDialog from "./EvuidCardDialog.vue";
import loader from "@/components/loader.vue";

export default {
  components: {
    NotFound,
    EvuidCardDialog,
    loader,
  },
  data() {
    return {
      api_blind_signature: LocalSettings.NODEJS_API_BLINDSIGNATURE,
      userObject: null,
      loading: false,
      isLoadingApi: false,
      N: "",
      E: "",
      error: "",
      voter: {
        cnp: "",
        evuid: "",
        blinded: "",
        r: "",
        signed: "",
        unblinded_signature: "",
      },
    };
  },
  methods: {
    get_info() {
      this.N = BlindSignature.N.toString();
      this.E = BlindSignature.E.toString();
    },
    copyToClipboard(text) {
      const clipboard = new Clipboard(".copy-btn", {
        text: () => text,
      });
      clipboard.on("success", () => {
        this.$message({
          message: "EVUID copied to clipboard",
          type: "success",
        });
        clipboard.destroy();
      });
      clipboard.on("error", () => {
        this.$message({
          message: "Failed to copy text to clipboard",
          type: "error",
        });
        clipboard.destroy();
      });
      clipboard.onClick(event);
    },
    blindMessage() {
      if (
        this.voter.evuid === null ||
        typeof this.voter.evuid !== "string" ||
        this.voter.evuid.length !== 15
      ) {
        this.$message.error("Invalid evuid");
        throw new Error("Invalid evuid");
      }

      const { blinded, r } = BlindSignature.blind({
        message: this.voter.evuid,
        N: this.N,
        E: this.E,
      });
      this.voter.blinded = blinded.toString();
      this.voter.r = r.toString();
    },
    async getSignature() {
      this.$message.info("Getting signature...");

      this.isLoadingApi = true;

      await this.$http
        .post(LocalSettings.NODEJS_API_BLINDSIGNATURE + "/sign_blinded", {
          blinded: this.voter.blinded,
        })
        .then((response) => {
          this.voter.signed = response.data.signed;
          this.$message.success("Signature received!");
        })
        .catch((error) => {
          this.error = error;
          this.$message.error("Error ocurred!");
        });

      this.isLoadingApi = false;

    },
    unblindSignature() {
      const unblinded = BlindSignature.unblind({
        signed: this.voter.signed,
        N: this.N,
        r: this.voter.r,
      });
      this.voter.unblinded_signature = unblinded.toString();
    },
    verify() {
      this.loading = true;
      const result = BlindSignature.verify({
        unblinded: this.voter.unblinded_signature,
        message: this.voter.evuid,
      });
      if (result) {
        this.$message.success("Signatures verify!");
        this.loading = false;
      } else {
        this.loading = false;
        this.$message.error("Invalid signature");
      }
    },
  },
  async mounted() {
    let url_param = this.$route.params.userToken;
    await this.$http.
      post(LocalSettings.BASE_URL_PHP + "/evuid_generator/check_token", {
        token: url_param,
      })
      .then((response) => {
        this.userObject = JSON.parse(response.data.voter);
      })
      .catch((error) => {
        this.$router.push({
          name: "not-found",
        });
        console.log(error);
      });
    this.get_info();
  },
};
</script>

<style scoped>
.card_evuid {
  min-height: 100vh;
  background-color: var(---background-color);
  color: var(---primary-color);
}

.card_title {
  padding: 0px 50px;
  padding-top: 50px;
}

.card_subtitle {
  font-size: 17px;
  font-style: italic;
  color: var(---secondary-color);
  margin-bottom: 20px;
  padding: 0px 50px;
}

.user-info {
  margin-bottom: 20px;
  padding: 10px 50px;
}

.user-info h3,
.descriptions h3 {
  font-size: 18px;
  font-weight: bold;
  text-decoration: underline;
}

.user-info ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.user-info li {
  margin-bottom: 10px;
}

.user-info .label {
  font-weight: bold;
}

.user-info .value {
  margin-left: 10px;
  overflow-y: scroll;
}

.input_button {
  margin-top: 50px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 50px;
  padding-bottom: 50px;
}

.descriptions {
  padding: 0px 50px;
}

.descriptions ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.descriptions li {
  margin-bottom: 10px;
}

.descriptions .label {
  font-weight: bold;
}

.descriptions .value {
  /* width: 100%; */
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: 14px;
}

.loading {
  font-size: 18px;
  font-weight: bold;
}

.large-number {
  word-wrap: break-word;
  font-style: italic;
}

@media screen and (max-width: 768px) {
  .card_evuid {
    padding: 20px;
  }
}
</style>