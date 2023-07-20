const secureRandom = require("secure-random");
const BigInteger = require("jsbn").BigInteger;
const sha256 = require("js-sha256");
const NodeRSA = require("node-rsa");
const fs = require("fs");
const Web3 = require("web3");

function keyGeneration(params) {
  const key = new NodeRSA(params || { b: 2048 });
  return key;
}

function importKeyPairFromFile() {
  const privateKey = fs.readFileSync("private.pem", "utf8");
  const publicKey = fs.readFileSync("public.pem", "utf8");

  const key = new NodeRSA();
  key.importKey(privateKey, "pkcs1-private-pem");
  key.importKey(publicKey, "pkcs1-public-pem");

  return key;
}

function getEthereumAddressFromRSAKey(key) {
  // Export the RSA public key
  const publicKey = key.exportKey("public");

  // Convert the public key to bytes
  const publicKeyBytes = Buffer.from(publicKey, "utf8");

  // Compute the hash of the public key bytes
  const publicKeyHash = Web3.utils.keccak256(publicKeyBytes);

  // Extract the last 20 bytes from the hash (excluding the prefix)
  const address = "0x" + publicKeyHash.slice(26);

  return address;
}

function keyProperties(key) {
  return {
    E: new BigInteger(key.keyPair.e.toString()),
    N: key.keyPair.n,
    D: key.keyPair.d,
  };
}

function messageToHash(message) {
  const messageHash = sha256(message);
  return messageHash;
}

function messageToHashInt(message) {
  const messageHash = messageToHash(message);
  const messageBig = new BigInteger(messageHash, 16);
  return messageBig;
}

function blind({ message, key, N, E }) {
  const messageHash = messageToHashInt(message);
  N = key ? key.keyPair.n : new BigInteger(N.toString());
  E = key
    ? new BigInteger(key.keyPair.e.toString())
    : new BigInteger(E.toString());

  const bigOne = new BigInteger("1");
  let gcd;
  let r;
  do {
    r = new BigInteger(secureRandom(64)).mod(N);
    gcd = r.gcd(N);
  } while (
    !gcd.equals(bigOne) ||
    r.compareTo(N) >= 0 ||
    r.compareTo(bigOne) <= 0
  );
  const blinded = messageHash.multiply(r.modPow(E, N)).mod(N);
  return {
    blinded,
    r,
  };
}

function sign({ blinded, key }) {
  const { N, D } = keyProperties(key);
  // blinded = new BigInteger(blinded.toString());
  blinded = new BigInteger(blinded);
  const signed = blinded.modPow(D, N);
  return signed;
}

function unblind({ signed, key, r, N }) {
  r = new BigInteger(r.toString());
  N = key ? key.keyPair.n : new BigInteger(N.toString());
  signed = new BigInteger(signed.toString());
  const unblinded = signed.multiply(r.modInverse(N)).mod(N);
  return unblinded;
}

function verify({ unblinded, key, message, E, N }) {
  unblinded = new BigInteger(unblinded.toString());
  const messageHash = messageToHashInt(message);
  N = key ? key.keyPair.n : new BigInteger(N.toString());
  E = key
    ? new BigInteger(key.keyPair.e.toString())
    : new BigInteger(E.toString());

  const originalMsg = unblinded.modPow(E, N);
  const result = messageHash.equals(originalMsg);
  return result;
}

function verify2({ unblinded, key, message }) {
  unblinded = new BigInteger(unblinded.toString());
  const messageHash = messageToHashInt(message);
  const { D, N } = keyProperties(key);
  const msgSig = messageHash.modPow(D, N);
  const result = unblinded.equals(msgSig);
  return result;
}

function verifyBlinding({ blinded, r, unblinded, key, E, N }) {
  const messageHash = messageToHashInt(unblinded);
  r = new BigInteger(r.toString());
  N = key ? key.keyPair.n : new BigInteger(N.toString());
  E = key
    ? new BigInteger(key.keyPair.e.toString())
    : new BigInteger(E.toString());

  const blindedHere = messageHash.multiply(r.modPow(E, N)).mod(N);
  const result = blindedHere.equals(blinded);
  return result;
}

module.exports = {
  importKeyPairFromFile,
  getEthereumAddressFromRSAKey,
  keyGeneration,
  messageToHash,
  blind,
  sign,
  unblind,
  verify,
  verify2,
  verifyBlinding,
};