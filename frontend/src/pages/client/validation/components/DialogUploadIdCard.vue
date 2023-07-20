<template>
  <el-dialog :title="loading
    ? 'Wait for image text extraction...'
    : 'Upload Dialog for ID Card'
    " :visible.sync="showPopup" append-to-body :close-on-press-escape="false" :show-close="false" center width="90%">
    <div v-if="!loading" class="flex-center">
      <span>
        <i class="el-icon-info"></i> Please upload your ID card that corresponds
        to the previously submitted data.</span>
      <br />
      <br />
      <br />
      <el-tag type="warning">submitted CNP: {{ cnp }}</el-tag>
      <el-upload drag action="base_url + '/upload_id_card'" :auto-upload="false" name="img_id" :limit="1"
        :on-change="onChangeUpload" :on-remove="() => (receivedCNP = '')">
        <i class="el-icon-upload"></i>
        <div class="el-upload__text">
          Drop file here or <em>click to upload</em>
        </div>
        <div class="el-upload__tip" slot="tip">
          jpg/png files with a size less than 500kb
        </div>
      </el-upload>

      <br />
      <el-tag v-if="receivedCNP" :type="receivedCNP != cnp ? 'danger' : 'success'">received CNP: {{ receivedCNP
      }}</el-tag>
    </div>
    <div v-else class="loader">
      <div class="spinner">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </el-dialog>
</template>

<script>
import LocalSettings from "@/backend/LocalSettings";

export default {
  name: "DialogUploadIdCard",
  components: {},
  props: {
    cnp: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      showPopup: false,
      baseUrl: LocalSettings.BASE_URL_PHP,
      receivedCNP: "",
      loading: false,
    };
  },
  methods: {
    show_me() {
      this.showPopup = true;
    },
    async onChangeUpload(file, fileList) {
      this.loading = true;

      var response = await this.$http.post(
        this.baseUrl + "/validation_form/get_text_from_id_card",
        {
          id_card: file,
        },
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );

      var data = response.data;

      this.loading = false;

      if (!data) {
        this.$message.error("Could not read the ID Card");
        return;
      }

      this.receivedCNP = data;

      if (this.receivedCNP != this.cnp) {
        this.$message.error(
          "The CNP from the ID Card does not match the submitted CNP. Please try again."
        );
        return;
      }

      this.$message.success("The CNP from the ID Card matches the submitted CNP");

      this.$emit("upload-id-card", file);

      this.reset_all();
    },

    reset_all() {
      this.receivedCNP = "";
      this.showPopup = false;
    },
  },
};
</script>

<style scoped>
.flex-center {
  display: flex;
  flex-direction: column;
  gap: 20px;
  justify-content: start;
  align-items: center;
  height: 500px;
  color: var(---primary-color);
}

.loader {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 500px;
}

.el-upload__tip {
  color: var(--primary-color);
}
</style>

<style scoped>
.spinner {
  position: relative;
  width: 60px;
  height: 60px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50%;
  margin-left: -75px;
}

.spinner span {
  position: absolute;
  top: 50%;
  left: var(--left);
  width: 35px;
  height: 7px;
  background: #ffff;
  animation: dominos 1s ease infinite;
  box-shadow: 2px 2px 3px 0px black;
}

.spinner span:nth-child(1) {
  --left: 80px;
  animation-delay: 0.125s;
}

.spinner span:nth-child(2) {
  --left: 70px;
  animation-delay: 0.3s;
}

.spinner span:nth-child(3) {
  left: 60px;
  animation-delay: 0.425s;
}

.spinner span:nth-child(4) {
  animation-delay: 0.54s;
  left: 50px;
}

.spinner span:nth-child(5) {
  animation-delay: 0.665s;
  left: 40px;
}

.spinner span:nth-child(6) {
  animation-delay: 0.79s;
  left: 30px;
}

.spinner span:nth-child(7) {
  animation-delay: 0.915s;
  left: 20px;
}

.spinner span:nth-child(8) {
  left: 10px;
}

@keyframes dominos {
  50% {
    opacity: 0.7;
  }

  75% {
    -webkit-transform: rotate(90deg);
    transform: rotate(90deg);
  }

  80% {
    opacity: 1;
  }
}
</style>