// SPDX-License-Identifier: MIT
pragma solidity >=0.4.22 <0.9.0;

contract Voting {
    struct Voter {
        address voterAddress;
        bool voted;
        string evuid;
        string candidateName;
    }

    Voter[] public voters;

    struct Candidate {
        string name;
        uint voteCount;
    }

    Candidate[] public candidates;

    constructor() {
        string[5] memory candidatesNames = [
            "John Doe",
            "Jane Smith",
            "Michael Johnson",
            "Emily Wilson",
            "David Brown"
        ];
        for (uint256 i = 0; i < candidatesNames.length; i++) {
            candidates.push(
                Candidate({name: candidatesNames[i], voteCount: 0})
            );
        }
    }

    mapping(string => bool) private registeredEvuids;
    mapping(address => bool) private registeredAddresses;

    function registerVoter(string calldata evuid) public {
        require(bytes(evuid).length > 0, "EVUID cannot be empty.");
        require(!registeredEvuids[evuid], "EVUID already registered.");
        require(
            !registeredAddresses[msg.sender],
            "Address already registered."
        );

        registeredEvuids[evuid] = true;
        registeredAddresses[msg.sender] = true;

        voters.push(
            Voter({
                voterAddress: msg.sender,
                voted: false,
                evuid: evuid,
                candidateName: ""
            })
        );
    }

    function getVoterByAddress(
        address _voterAddress
    ) public view returns (Voter memory) {
        for (uint i = 0; i < voters.length; i++) {
            if (voters[i].voterAddress == _voterAddress) {
                return voters[i];
            }
        }
        revert("Voter not found.");
    }

    function voteForCandidate(string calldata candidate) public {
        Voter memory sender = getVoterByAddress(msg.sender);

        require(sender.voterAddress != address(0), "Voter not found.");
        require(!sender.voted, "Already voted.");

        for (uint i = 0; i < voters.length; i++) {
            if (voters[i].voterAddress == msg.sender) {
                voters[i].voted = true;
                voters[i].candidateName = candidate;
            }
        }

        for (uint i = 0; i < candidates.length; i++) {
            if (
                keccak256(bytes(candidates[i].name)) ==
                keccak256(bytes(candidate))
            ) {
                candidates[i].voteCount += 1;
                return;
            }
        }

        revert("Candidate not found.");
    }

    function getCandidates() public view returns (Candidate[] memory) {
        return candidates;
    }

    function getVoters() public view returns (Voter[] memory) {
        return voters;
    }
}
