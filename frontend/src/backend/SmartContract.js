export const contractAbi = JSON.parse(
    `[
        {
            "inputs": [],
            "stateMutability": "nonpayable",
            "type": "constructor"
        },
        {
            "inputs": [
                {
                    "internalType": "uint256",
                    "name": "",
                    "type": "uint256"
                }
            ],
            "name": "candidates",
            "outputs": [
                {
                    "internalType": "string",
                    "name": "name",
                    "type": "string"
                },
                {
                    "internalType": "uint256",
                    "name": "voteCount",
                    "type": "uint256"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "getCandidates",
            "outputs": [
                {
                    "components": [
                        {
                            "internalType": "string",
                            "name": "name",
                            "type": "string"
                        },
                        {
                            "internalType": "uint256",
                            "name": "voteCount",
                            "type": "uint256"
                        }
                    ],
                    "internalType": "struct Voting.Candidate[]",
                    "name": "",
                    "type": "tuple[]"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "address",
                    "name": "_voterAddress",
                    "type": "address"
                }
            ],
            "name": "getVoterByAddress",
            "outputs": [
                {
                    "components": [
                        {
                            "internalType": "address",
                            "name": "voterAddress",
                            "type": "address"
                        },
                        {
                            "internalType": "bool",
                            "name": "voted",
                            "type": "bool"
                        },
                        {
                            "internalType": "string",
                            "name": "evuid",
                            "type": "string"
                        },
                        {
                            "internalType": "string",
                            "name": "candidateName",
                            "type": "string"
                        }
                    ],
                    "internalType": "struct Voting.Voter",
                    "name": "",
                    "type": "tuple"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [],
            "name": "getVoters",
            "outputs": [
                {
                    "components": [
                        {
                            "internalType": "address",
                            "name": "voterAddress",
                            "type": "address"
                        },
                        {
                            "internalType": "bool",
                            "name": "voted",
                            "type": "bool"
                        },
                        {
                            "internalType": "string",
                            "name": "evuid",
                            "type": "string"
                        },
                        {
                            "internalType": "string",
                            "name": "candidateName",
                            "type": "string"
                        }
                    ],
                    "internalType": "struct Voting.Voter[]",
                    "name": "",
                    "type": "tuple[]"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "string",
                    "name": "evuid",
                    "type": "string"
                }
            ],
            "name": "registerVoter",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "string",
                    "name": "candidate",
                    "type": "string"
                }
            ],
            "name": "voteForCandidate",
            "outputs": [],
            "stateMutability": "nonpayable",
            "type": "function"
        },
        {
            "inputs": [
                {
                    "internalType": "uint256",
                    "name": "",
                    "type": "uint256"
                }
            ],
            "name": "voters",
            "outputs": [
                {
                    "internalType": "address",
                    "name": "voterAddress",
                    "type": "address"
                },
                {
                    "internalType": "bool",
                    "name": "voted",
                    "type": "bool"
                },
                {
                    "internalType": "string",
                    "name": "evuid",
                    "type": "string"
                },
                {
                    "internalType": "string",
                    "name": "candidateName",
                    "type": "string"
                }
            ],
            "stateMutability": "view",
            "type": "function"
        }
    ]`
);

export const contractAddress = '0x5d6e53275ecab4b2afdd2d33a152e9fae8903e7f';

export const contractAddressGanache = '0x973c428f6aFc7439a3A3AfB204a78C62E698230B';