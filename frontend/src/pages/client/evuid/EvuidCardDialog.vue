<template>
    <el-dialog title="Voting ballot" :visible.sync="showPopupDialog" append-to-body width="90%" center>

        <div class="card-dialog-evuid">

            <div class="voting-data">
                <p>{{ email }}</p>

                <p class="bold">{{ evuid }}</p>

                <p>{{ signature }}</p>
            </div>

            <div style="margin:50px 0px"></div>

            <el-row type="flex" justify="center">
                <button class="crazyBtn" role="button" @click="startVoting">START VOTING PROCESS</button>
            </el-row>
        </div>
    </el-dialog>
</template>

<script>
import 'element-ui/lib/theme-chalk/display.css';
import Clipboard from 'clipboard';
import LocalSettings from '@/backend/LocalSettings';

export default {
    props: {
        email: String,
        evuid: String,
        signature: String,
    },
    data: function () {
        return {
            showPopupDialog: false,
        }
    },
    methods: {
        showMe() {
            this.showPopupDialog = true;
        },
        copyToClipboard(text, objectCopiedText) {
            const clipboard = new Clipboard('.copy-btn', {
                text: () => text
            })
            clipboard.on('success', () => {
                this.$message({
                    message: `${objectCopiedText} copied to clipboard`,
                    type: 'success'
                })
                clipboard.destroy()
            })
            clipboard.on('error', () => {
                this.$message({
                    message: 'Failed to copy text to clipboard',
                    type: 'error'
                })
                clipboard.destroy()
            })
            clipboard.onClick(event)
        },
        startVoting() {
            var formdata = new FormData();
            formdata.append("email", this.email);
            formdata.append("evuid", this.evuid);
            formdata.append("signature", this.signature);

            this.$http.post(LocalSettings.BASE_URL_PHP + "/evuid_generator/send_email_voting_paper", formdata);

            console.log('sendind evuid and signature', this.evuid, this.signature)

            this.$router.push({
                path: '/vote',
                query: {
                    evuid: this.evuid,
                    auth_signature: this.signature
                }
            });
        }
    },
}
</script>

<style scoped>
.card-dialog-evuid {
    color: var(---primary-color) !important;
}

.card-dialog-evuid .voting-data {
    display: flex;
    flex-direction: column;
    gap: 30px;
    align-items: center;
    align-content: center;
    justify-content: center;
    font-size: 18px;
}
</style>