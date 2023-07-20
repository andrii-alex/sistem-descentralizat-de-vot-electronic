<template>
    <el-container>
        <el-header style="padding: 50px; color: white !important;">
            <!-- <p class="dashboard_title">DASHBOARD</p> -->
            <el-page-header @back="logOut" title="logout" content="DASHBOARD">
            </el-page-header>
        </el-header>
        <el-main style="margin: auto 50px !important;">
            <el-table :data="requestsTableData" empty-text="There are no voting requests..." v-loading="loading">
                <el-table-column label="Date of request" prop="time">

                </el-table-column>
                <el-table-column label="First name" prop="voter_info.first_name">

                </el-table-column>
                <el-table-column label="Last name" prop="voter_info.last_name">

                </el-table-column>
                <el-table-column label="CNP" prop="voter_info.cnp">

                </el-table-column>
                <el-table-column label="Status">
                    <template slot-scope="scope">
                        <el-tag>
                            {{ scope.row.status }}
                        </el-tag>
                    </template>

                </el-table-column>
                <el-table-column label="Actions">
                    <template slot-scope="scope">
                        <el-button type="info" icon="el-icon-edit" plain circle
                            @click="openDialogRequest(scope.row)"></el-button>
                        <RequestDialog ref="dialogRequest" :request="scope.row" @refresh="refresh_info()">
                        </RequestDialog>
                    </template>
                </el-table-column>
            </el-table>
        </el-main>
    </el-container>
</template>

<script>
import LocalSettings from '@/backend/LocalSettings';
import axios from 'axios';
import RequestDialog from './RequestDialog.vue';

export default {
    name: "DashboardAdmin",
    comments: {
        RequestDialog
    },
    data() {
        return {
            loading: false,
            requestsTableData: [],
        };
    },
    methods: {
        refresh_info: async function () {
            this.loading = true;
            var response = await axios.post(LocalSettings.BASE_URL_PHP + "/admin/getAllRequests");
            this.requestsTableData = response.data.requests;
            this.loading = false;
            // setTimeout(() => {
            //     this.loading = false;
            // }, 1000);
        },
        openDialogRequest(id_request) {
            this.$refs.dialogRequest.showMe(id_request);
        },
        logOut() {
            LocalSettings.logout()
        },
    },
    mounted() {
        this.refresh_info();
        // LocalSettings.verifyLoginAndRedirect();
    },
    components: { RequestDialog }
}
</script>

<style scoped>
.dashboard_title {
    font-size: 30px;
    font-weight: 700;
    padding: 10px;
    margin-bottom: 200px;
}
</style>

<style>
.el-page-header__content {
    color: white !important;
}
</style>