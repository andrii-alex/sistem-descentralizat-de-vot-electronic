<template>
  <el-dialog
    title="Selfie dialog"
    :visible.sync="showPopup"
    append-to-body
    width="90%"
  >
    <span
      >This is a dialog for making a validation selfie.<br />
      Firstly, open the camera to prompt the web browser video. Then take a
      picture and upload it.</span
    >
    <br />
    <br />
    <div class="camera" v-loading="loadingUpload">
      <div class="camera_div_buttons">
        <div class="buttons">
          <el-button
            size="small"
            type="info"
            :class="{ 'is-primary': !isCameraOpen, 'is-danger': isCameraOpen }"
            @click="toggleCamera"
            :loading="isLoading"
          >
            <span v-if="!isCameraOpen">Open camera</span>
            <span v-else>Close camera</span>
          </el-button>
          <el-button
            :disabled="!(isCameraOpen && !isLoading && !isPhotoTaken)"
            @click="takePhoto"
            size="large"
            type="info"
            circle
            icon="el-icon-camera"
          ></el-button>
        </div>
        <div class="buttons">
          <el-button
            :disabled="!(isPhotoTaken && isCameraOpen)"
            type="success"
            icon="el-icon-upload"
            @click="uploadImage"
            size="small"
          >
            <span> Upload</span>
          </el-button>
          <el-button
            :disabled="!(isPhotoTaken && isCameraOpen)"
            icon="el-icon-refresh"
            size="small"
            @click="resetCamera"
            >RESET</el-button
          >
        </div>
      </div>

      <br />

      <div v-if="isCameraOpen" v-show="!isLoading" class="camera-box">
        <video v-show="!isPhotoTaken" ref="camera" autoplay id="video"></video>

        <canvas v-show="isPhotoTaken" id="photoTaken" ref="canvas"></canvas>
      </div>
    </div>
  </el-dialog>
</template>

<script>
import axios from "axios";
import LocalSettings from "@/backend/LocalSettings";

export default {
  name: "CameraDialog",
  props: {},
  data() {
    return {
      showPopup: false,
      isCameraOpen: false,
      isPhotoTaken: false,
      isShotPhoto: false,
      isLoading: false,
      loadingUpload: false,
      link: "#",
    };
  },
  methods: {
    show_me() {
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
      formData.append("img_selfie", blobData, "selfie.jpeg");

      let status_success_upload;
      let selfie_path;

      await axios
        .post(LocalSettings.BASE_URL_PHP + "/upload_selfie", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((response) => {
          // console.log("response upload selfie", response)
          status_success_upload = "success";
          selfie_path = response.data.img_selfie;

          this.$emit("selfie-taken", {
            imageData: imageData,
            success: status_success_upload,
            selfie_path: selfie_path,
          });
        })
        .catch((error) => {
          console.error(error);
          this.$message.error("Selfie-ul nu a putut fi urcat pe server!");
        });

      this.loadingUpload = false;
      this.showPopup = false;
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
  mounted: function () {},
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

.camera_div_buttons .buttons {
  display: flex;
  flex-direction: row;
}

@media only screen and (max-width: 1200px) {
  .camera-box video {
    width: 70%;
    height: 100%;
  }
}
</style>