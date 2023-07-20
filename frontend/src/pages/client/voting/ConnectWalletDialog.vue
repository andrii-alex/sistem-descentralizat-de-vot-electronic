<template>
    <el-dialog :visible.sync="visible" append-to-body @close="closeDialog" fullscreen>
        <div class="dialog-container">
            <h2 class="dialog-title">Connect MetaMask Wallet</h2>
            <p class="dialog-description">
                To continue, please connect your MetaMask wallet.
            </p>
            <el-button type="primary" @click="connectWallet">
                CONNECT WALLET
            </el-button>
        </div>
    </el-dialog>
</template>

<script>
export default {
    props: {
        showPopupDialog: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            visible: false,
        };
    },
    watch: {
        showPopupDialog: function (val) {
            this.visible = val;
        },
    },
    methods: {
        connectWallet() {
            if (window.ethereum) {
                window.ethereum.request({ method: 'eth_requestAccounts' })
                    .then(() => {
                        this.$message.success("MetaMask Wallet connected.");
                        this.showPopupDialog = false;
                    })
                    .catch((error) => {
                        this.$message.error(error);
                        console.error(error);
                    });
            } else {
                this.$message.error("MetaMask is not installed");
            }
        },
        closeDialog() {
            this.$emit('dialogClosed');
        }
    },
    mounted() {
        this.visible = this.showPopupDialog;
    },
}
</script>
<style scoped>
.dialog-container {
    display: flex;
    flex-direction: column;
    min-height: 90vh;
    text-align: center;
    align-items: center;
    gap: 40px;
}

.dialog-title {
    font-size: 24px;
    color: #fff;
}

.dialog-description {
    font-size: 16px;
    color: #fff;
}
</style>
