const express = require('express');
const cors = require('cors');
const BlindSignature = require('./utils/rsablind');
const app = express();

app.use(cors());
app.use(express.json());

// const EXP_PRIV = 5;
// const MOD = 14;

const central_authority = {
  key: BlindSignature.importKeyPairFromFile(),
  blinded_message: null,
};

app.get('/', (req, res) => {
  res.send('Hello, Andrii!');
});

app.get("/get_key_data", async (req, res) => {
  res.status(200).json({
    N: central_authority.key.keyPair.n.toString(),
    E: central_authority.key.keyPair.e.toString(),
    address: BlindSignature.getEthereumAddressFromRSAKey(central_authority.key),
  });
});

app.post("/sign_blinded", async (req, res) => {
  const blinded = req.body.blinded;
  const signed = BlindSignature.sign({
    blinded: blinded,
    key: central_authority.key,
  }).toString();
  res.status(200).json({
    signed: signed,
  });
});

app.listen(3000, () => {
  console.log('Server listening on port 3000');
});
