import * as secureRandom from 'secure-random';
import { BigInteger } from 'jsbn';
import * as sha256 from 'js-sha256';

var N = new BigInteger('9272042780217630811155297582132826654828005410423541796289941900394968812107255150392679253950982150115342978356886456527316446764841020800018738602681739');
var E = new BigInteger('65537');

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

function blind({ message, key }) {
    const messageHash = messageToHashInt(message);

    const bigOne = new BigInteger('1');
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
    blinded = new BigInteger(blinded.toString());
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

function verify({ unblinded, message}) {
    unblinded = new BigInteger(unblinded);
    const messageHash = messageToHashInt(message);
    // N = key ? key.keyPair.n : new BigInteger(N.toString());
    // E = key
    //     ? new BigInteger(key.keyPair.e.toString())
    //     : new BigInteger(E.toString());
    const originalMsg = unblinded.modPow(E, N);

    console.log('originalMsg', originalMsg.toString())
    console.log('messageHash', messageHash.toString())
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


export { keyProperties, blind, sign, unblind, verify, verify2, verifyBlinding, N, E }