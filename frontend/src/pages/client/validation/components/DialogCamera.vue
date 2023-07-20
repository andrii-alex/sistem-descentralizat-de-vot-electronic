<template>
  <el-dialog title="Selfie dialog" :visible.sync="showPopup" append-to-body width="90%" center>
    <span>This is a dialog for making a validation selfie.<br />
      Firstly, open the camera to prompt the web browser video. Then take a
      picture and upload it.</span>
    <br />
    <br />
    <div class="camera">
      <div class="camera_div_buttons">
        <el-button size="small" type="info" @click="toggleCamera" :loading="isLoading">
          <span v-if="!isCameraOpen">Open camera</span>
          <span v-else>Close camera</span>
        </el-button>
        <el-button :disabled="!(isCameraOpen && !isLoading && !isPhotoTaken)" @click="takePhoto" size="large"
          type="warning" circle icon="el-icon-camera"></el-button>

        <el-button :disabled="!(isPhotoTaken && isCameraOpen)" icon="el-icon-refresh" size="small"
          @click="resetCamera">RESET</el-button>

        <el-button :disabled="!(isPhotoTaken && isCameraOpen)" type="success" icon="el-icon-upload" @click="uploadImage"
          size="small">
          <span> Upload</span>
        </el-button>
      </div>

      <div v-if="isCameraOpen" v-show="!isLoading" class="selfie_frame" v-loading="loadingUpload">
        <video v-show="!isPhotoTaken" ref="camera" autoplay id="video"></video>

        <canvas v-show="isPhotoTaken" id="photoTaken" ref="canvas"></canvas>
      </div>
    </div>
  </el-dialog>
</template>

<script>
import LocalSettings from "@/backend/LocalSettings";

export default {
  name: "CameraDialog",
  emits: ["upload-selfie"],
  data() {
    return {
      showPopup: false,
      isCameraOpen: false,
      isPhotoTaken: false,
      isShotPhoto: false,
      isLoading: false,
      loadingUpload: false,
      link: "#",
      uploadIdCard: null,
    };
  },
  methods: {
    show_me(id_card) {
      this.uploadIdCard = id_card;
      console.log("show_me_uploadIdCard", this.uploadIdCard);
      this.showPopup = true;
    },
    toggleCamera() {

      if (this.isCameraOpen) {
        this.isCameraOpen = false;
        this.isPhotoTaken = false;
        this.isShotPhoto = false;
        this.stopCameraStream();
      } else {
        this.isCameraOpen = true;
        this.createCameraElement();
      }
    },
    createCameraElement() {
      this.isLoading = true;

      const constraints = (window.constraints = {
        audio: false,
        video: true,
      });

      navigator.mediaDevices
        .getUserMedia(constraints)
        .then((stream) => {
          this.isLoading = false;
          this.$refs.camera.srcObject = stream;
        })
        .catch((error) => {
          this.isLoading = false;
          alert(
            "May the browser didn't support or there is some errors." + error
          );
        });
    },
    stopCameraStream() {
      let tracks = this.$refs.camera.srcObject.getTracks();

      tracks.forEach((track) => {
        track.stop();
      });
    },
    takePhoto() {
      this.isPhotoTaken = !this.isPhotoTaken;

      const video = document.getElementById("video");
      const canvas = this.$refs.canvas;
      const context = canvas.getContext("2d");
      canvas.width = video.clientWidth;
      canvas.height = video.clientHeight;
      context.drawImage(this.$refs.camera, 0, 0, canvas.width, canvas.height);

      this.stopCameraStream();
    },
    async uploadImage() {
      this.loadingUpload = true;

      const imageData = document
        .getElementById("photoTaken")
        .toDataURL("image/jpeg");

      const blobData = this.dataURItoBlob(imageData);

      const formData = new FormData();
      formData.append("selfie", blobData, "selfie.jpeg");
      formData.append("id_card", this.uploadIdCard.raw);

      try {
        var response = await this.$http.post(
          LocalSettings.BASE_URL_PHP +
          "/validation_form/compare_idcard_with_selfie",
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );
      }
      catch (error) {
        this.$message({
          message: "Validation failed! ID and Selfie do not match! Please try again.",
          type: "error",
        });
        return;
      }
      finally {
        this.loadingUpload = false;
      }

      if (!response.data) {
        this.$message({
          message:
            "Validation failed! ID and Selfie do not match! Please try again.",
          type: "error",
        });
        return;
      }

      this.$message({
        message: "Selfie confirmed successfully!",
        type: "success",
      });

      this.resetCamera();

      this.showPopup = false;

      setTimeout(() => {
        this.$emit("upload-selfie", blobData);
      }, 1000);
    },
    resetCamera() {
      this.isCameraOpen = false;
      this.isPhotoTaken = false;
      this.isShotPhoto = false;
      this.stopCameraStream();
    },
    dataURItoBlob(dataURI) {
      const byteString = atob(dataURI.split(",")[1]);
      const mimeString = dataURI.split(",")[0].split(":")[1].split(";")[0];
      const ab = new ArrayBuffer(byteString.length);
      const ia = new Uint8Array(ab);
      for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
      }
      return new Blob([ab], { type: mimeString });
    },
  },
  mounted: function () { },
};
</script>

<style scoped>
.camera {
  margin-top: 20px;
}

.camera-box {
  margin: 20px 0px;
  display: flex;
  justify-content: center;
  /* 16:9 aspect ratio */
}

.camera-box video {
  width: 70%;
  height: auto;
}

.camera_div_buttons {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 20px;
}

.selfie_frame {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 100px;
}

@media only screen and (max-width: 1200px) {
  .selfie_frame video {
    width: 70%;
    height: 100%;
  }
}
</style>
