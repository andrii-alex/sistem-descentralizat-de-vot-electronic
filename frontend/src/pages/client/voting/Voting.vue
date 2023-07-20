<template>
  <div class="vote-section">
    <el-tooltip effect="dark" content="Refresh information from blockchain">
      <el-button style="float: right; margin: 10px;" icon="el-icon-refresh-left" circle
        @click="refreshInfoBlockchain"></el-button>
    </el-tooltip>
    <el-tabs type="card" style="padding: 50px;">
      <el-tab-pane label="Voting section">
        <div class="container">
          <div class="topbar">
            <h2>Voting section
              &nbsp;
              <el-tag type="success" effect="dark" v-if="votingForm.disabled">ACCESS GRANTED</el-tag>
              <el-tag type="danger" effect="dark" v-else>ACCESS DENIED</el-tag>
              &nbsp;
              <i class="el-icon-loading" v-if="isLoading"></i>
            </h2>

            <p class="address">
              {{ selectedAddress }}
            </p>

            <p><i class="el-icon-info"></i> &nbsp; Submit voting credentials to cast your vote</p>

            <el-form :model="votingForm" ref="votingCard" @submit.prevent="submitForm">
              <el-form-item label="EVUID">
                <el-input v-model="votingForm.evuid" autocomplete="nope-1234" maxlength="15" minlength="15"
                  show-word-limit :disabled="votingForm.disabled" />
              </el-form-item>
              <el-form-item label="Central Authority Signature">
                <el-input v-model="votingForm.signature" autocomplete="nope-1234"
                  :disabled="votingForm.disabled"></el-input>
              </el-form-item>
              <el-form-item v-if="!votingForm.disabled">
                <button class="default_button" @click.prevent="submitForm">Submit</button>
              </el-form-item>
            </el-form>

          </div>

          <div class="candidates" v-if="candidates.length > 0">
            <el-card shadow="hover" v-for="candidate in candidates" :key="candidate.Id">
              <div class="candidate_card">
                <p class="nume_candidat">
                  {{ candidate[0] }}
                </p>
                <p class="date_candidate">Vote count: {{ candidate[1] }}</p>
                <br />
                <button class="default_button" @click="castVote(candidate[0])" v-if="votingForm.disabled">CAST
                  VOTE</button>
                <p v-else>CANNOT CAST VOTE</p>
              </div>
            </el-card>
          </div>
        </div>
      </el-tab-pane>
      <el-tab-pane label="Logs">
        <h2>Registered voters</h2>
        <br>
        <el-input v-model="searchEvuid" placeholder="Search for EVUID" style="width: 200px;"></el-input>
        <br>
        <br>
        <br>
        <el-table border
          :data="voters.filter(data => !searchEvuid || data.evuid.toLowerCase().includes(searchEvuid.toLowerCase()))">
          <el-table-column label="EVUID" prop="evuid">

          </el-table-column>
          <el-table-column label="Address" prop="voterAddress">

          </el-table-column>
          <el-table-column label="Voted (filterable)" prop="voted" :formatter="formatBoolVoted"
            :filters="[{ text: 'Voted', value: true }, { text: 'No vote', value: false }]" :filter-method="filterVote"
            filter-placement="bottom-end">

          </el-table-column>
          <el-table-column label="Candidate Name" prop="candidateName">

          </el-table-column>
        </el-table>
      </el-tab-pane>
    </el-tabs>

    <ConnectWalletDialog ref="connectWalletDialog" @dialogClosed="connectDialogClosed"
      :showPopupDialog="!wallet_connected"></ConnectWalletDialog>

    <FooterComponent />

  </div>
</template>

<script>
import LocalSettings from "@/backend/LocalSettings";
import * as BlindSignature from "@/utils/blindsignature";
import Web3 from 'web3';
import { Router } from 'vue-router'

export default {
  components: {
    FooterComponent: () => import("@/components/footer.vue"),
    ConnectWalletDialog: () => import("./ConnectWalletDialog.vue"),
  },
  data() {
    return {
      isLoading: false,
      wallet_connected: false,
      searchEvuid: "",
      auth_signature: "",
      selectedAddress: "",
      candidates: [],
      votingForm: {
        disabled: false,
        evuid: "",
        signature: "",
      },
      web3: null,
      contract: null,
      voters: [],
    };
  },
  methods: {
    connectDialogClosed() {
      if (window.ethereum && window.ethereum.selectedAddress) {
        this.wallet_connected = true;
        this.selectedAddress = window.ethereum.selectedAddress;
      }
    },
    async get_candidates() {
      this.$http
        .post(LocalSettings.BASE_URL_PHP + "/vote/get_candidates")
        .then((response) => {
          this.candidates = response.data.candidates;
        })
        .catch((error) =>
          this.$message.error("Error ocurred!" + error.message)
        );
    },
    submitForm() {
      const result = BlindSignature.verify({
        unblinded: this.votingForm.signature,
        message: this.votingForm.evuid,
      });
      if (!result) {
        this.$message.error("Invalid signature");
        return;
      }

      this.votingForm.disabled = true;
      this.registerVoter();
    },
    async castVote(candidate_name) {
      this.isLoading = true;
      const web3 = new Web3(window.ethereum);

      const contract = new web3.eth.Contract(
        LocalSettings.CONTRACT_ABI,
        LocalSettings.CONTRACT_ADDRESS
      );

      try {
        await contract.methods.voteForCandidate(candidate_name).send({ from: this.selectedAddress });
      }
      catch (e) {
        this.$message.error(e.message);
        console.log(e);
        return;
      }
      finally {
        this.isLoading = false;
      }

      this.$message.success("Vote casted!");

      this.refreshInfoBlockchain();
    },
    async refreshInfoBlockchain() {
      this.$message.info("Retrieving info from blockchain...");
      // const web3 = new Web3("http://localhost:7545");

      // const contract = new web3.eth.Contract(
      //   LocalSettings.ABI,
      //   LocalSettings.CONTRACT_ADDRESS
      // );

      const web3 = new Web3(window.ethereum);

      const contract = new web3.eth.Contract(
        LocalSettings.CONTRACT_ABI,
        LocalSettings.CONTRACT_ADDRESS
      );

      this.candidates =
        JSON.parse(JSON.stringify(await contract.methods.getCandidates().call(), (key, value) =>
          typeof value === 'bigint'
            ? value.toString()
            : value // return everything else unchanged
        ));

      this.voters = await contract.methods.getVoters().call();
    },
    async registerVoter() {
      this.isLoading = true;

      try {
        const web3 = new Web3(window.ethereum);

        const contract = new web3.eth.Contract(
          LocalSettings.CONTRACT_ABI,
          LocalSettings.CONTRACT_ADDRESS
        );

        await contract.methods.registerVoter(this.votingForm.evuid).send({ from: this.selectedAddress });
        this.$message.success("Function Register voter executed successfully!");
      } catch (error) {
        this.votingForm.disabled = false;
        this.$message.error('Error executing function:', error);
      } finally {
        this.isLoading = false;
      }
    },
    formatBoolVoted(row, column) {
      return row.voted ? "Yes" : "No";
    },
    filterVote(value, row) {
      console.log(row.voted);
      return row.voted === value;
    },
  },
  mounted() {
    if (this.$route.query.evuid && this.$route.query.auth_signature) {
      this.votingForm.evuid = this.$route.query.evuid
      this.votingForm.signature = this.$route.query.auth_signature
    }

    if (window.ethereum && window.ethereum.selectedAddress) {
      console.log("wallet connected");
      this.wallet_connected = true;
      this.selectedAddress = window.ethereum.selectedAddress;

      this.refreshInfoBlockchain();
    }

  },
};
</script>

<style>
.vote-section {
  background-color: var(---background-color);
  margin-bottom: 100px;
}

.container {
  background-color: var(---background-color);
  /* min-height: 100vh; */
  margin-bottom: 100px;
}

.topbar {
  /* width: 100%; */
  /* padding: 0 5rem; */
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  margin-bottom: 50px;
  gap: 20px;
}

.topbar .title {
  font-size: 25px;
  font-weight: 700;
  color: #fff;
}

.topbar .address {
  font-size: 15px;
  color: var(---primary-color);
  width: fit-content;
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  border: 1px solid var(---primary-color);
  padding: 10px 20px;
  border-radius: 16px;
}

.candidates {
  display: flex;
  flex-wrap: wrap;
  justify-content: start;
  gap: 20px;
  padding: 5rem;
}

.candidate_card {
  width: 400px;
  height: 200px;
  padding: 20px;
}

.candidate_card p.nume_candidat {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 10px;
}
</style>
