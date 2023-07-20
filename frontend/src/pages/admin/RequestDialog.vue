<template>
    <el-dialog title="Accept or refuse voting request" :visible.sync="showPopupDialog" append-to-body>
        <div v-if="object_request.status == 'unprocessed'">
            <span>Check the images and decide... :)</span>

            <div class="request_dialog">
                <img :src="img_id" style="max-width: 100%; height: auto;">
                <img :src="img_selfie" style="max-width: 100%; height: auto;">
            </div>

            <div class="decide_buttons">
                <el-button type="success" icon="el-icon-check" @click="decideRequest(true)">ACCEPT</el-button>
                <el-button type="danger" icon="el-icon-close" @click="decideRequest(false)">REJECT</el-button>
            </div>
        </div>
        <div v-else>
            <el-row style="display:flex; justify-content: center;">
                <el-col>
                    <el-result icon="success" title="Request validated!"
                        subTitle="Please return to the list. A validated request cannot be changed.">
                        <template slot="extra">
                            <el-button type="primary" size="medium" @click="showPopupDialog = false">Close</el-button>
                        </template>
                    </el-result>
                </el-col>
            </el-row>
        </div>

    </el-dialog>
</template>

<script>
import LocalSettings from '@/backend/LocalSettings'
import axios from 'axios'

export default {
    name: "RequestDialog",
    props: {
        request: Object,
    },
    emits: ['refresh'],
    data() {
        return {
            showPopupDialog: false,
            img_id: '',
            img_selfie: '',
            object_request: {},
        }
    },
    computed: {
        // idCardUrl() {
        //     return require(`@/assets/images/uploads/idcards/${this.request.IdCard_path}`)
        // },
        // selfieUrl() {
        //     return require(`@/assets/images/uploads/selfies/${this.request.selfie_path}`)
        // }
    },
    methods: {
        showMe(object_request) {
            console.log("object_request: ", object_request)
            this.object_request = object_request
            this.showPopupDialog = true
            this.img_id = LocalSettings.IMAGES_FOLDER_PATH + '/ids/' + object_request.img_id
            this.img_selfie = LocalSettings.IMAGES_FOLDER_PATH + '/selfies/' + object_request.img_selfie
        },
        decideRequest(accept_status) {
            if (accept_status) {
                this.$confirm('This will permanently accept the request. Continue?', 'ACCEPT REQUEST', {
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    type: 'success'
                }).then(async () => {
                    var response = await axios.post(LocalSettings.BASE_URL_PHP + "/decide_request", {
                        id_request: this.object_request.id,
                        request_status: "accepted"
                    })
                    this.$message({
                        type: 'success',
                        message: 'Accept completed'
                    });
                    this.$emit('refresh');
                    this.showPopupDialog = false;
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'Accept canceled'
                    });
                });
            }
            else {
                this.$confirm('This will permanently accept the request. Continue?', 'REJECT REQUEST', {
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    type: 'error'
                }).then(async () => {
                    var response = await axios.post(LocalSettings.BASE_URL_PHP + "/decide_request", {
                        id_request: this.object_request.id,
                        request_status: "rejected"
                    })
                    this.$message({
                        type: 'error',
                        message: 'Reject completed'
                    });
                    this.$emit('refresh');
                    this.showPopupDialog = false;
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'Reject canceled'
                    });
                });
            }
        }
        // getInfo: async function () {
        //     var response = await axios.post(LocalSettings.API_ADMIN_URL + '/getAllRequests')
        //     console.log(response.data)
        // }
    },
    mounted: function () {
        // this.getInfo()
    }
}
</script>

<style scoped>
.decide_buttons {
    margin-top: 50px;
    display: flex;
    flex-direction: row;
    justify-content: center;
    gap: 30px;
}

.request_dialog {
    padding-top: 10px;
    display: flex;
    flex-direction: row;
    justify-content: center;
    flex-wrap: wrap;
    gap: 30px;
}
</style>