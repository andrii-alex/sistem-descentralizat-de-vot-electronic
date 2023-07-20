const fs = require("fs");
const NodeRSA = require("node-rsa");
const Web3 = require("web3");

function generateKeyPair() {
    const key = new NodeRSA({ b: 512 });
    return key;
}

function saveKeyPairToFile(privateKey, publicKey) {
    fs.writeFileSync("private.pem", privateKey.exportKey("pkcs1-private-pem"));
    fs.writeFileSync("public.pem", publicKey.exportKey("pkcs1-public-pem"));

    console.log("Key pair saved as private.pem and public.pem");
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

// Generate key pair and save to files
// const keyPair = generateKeyPair();
// saveKeyPairToFile(keyPair, keyPair);

// Import key pair from files
const importedKeyPair = importKeyPairFromFile();
// console.log("Private Key:", importedKeyPair.exportKey("pkcs1-private-pem"));
// console.log("Public Key:", importedKeyPair.exportKey("pkcs1-public-pem"));
console.log("E: ", importedKeyPair.keyPair.e.toString());
console.log("N: ", importedKeyPair.keyPair.n.toString());
// console.log("D: ", importedKeyPair.keyPair.d.toString());

console.log("Ethereum address: ", getEthereumAddressFromRSAKey(importedKeyPair));
