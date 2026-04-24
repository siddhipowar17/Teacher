"""
Lightweight Blockchain Module
Implements a SHA-256 based blockchain for secure IoT data storage.
"""

import hashlib
import json
from datetime import datetime, timezone


class Block:
    """Represents a single block in the blockchain."""

    def __init__(self, index, data, previous_hash="0"):
        self.index = index
        self.timestamp = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S UTC")
        self.data = data
        self.previous_hash = previous_hash
        self.hash = self.calculate_hash()

    def calculate_hash(self):
        """Generate SHA-256 hash of the block contents."""
        block_string = json.dumps({
            "index": self.index,
            "timestamp": self.timestamp,
            "data": self.data,
            "previous_hash": self.previous_hash
        }, sort_keys=True)
        return hashlib.sha256(block_string.encode()).hexdigest()

    def to_dict(self):
        """Convert block to dictionary for JSON serialization."""
        return {
            "index": self.index,
            "timestamp": self.timestamp,
            "data": self.data,
            "hash": self.hash,
            "previous_hash": self.previous_hash
        }


class Blockchain:
    """Lightweight blockchain implementation for IoT data."""

    def __init__(self):
        self.chain = []
        self._create_genesis_block()

    def _create_genesis_block(self):
        """Create the first block in the chain."""
        genesis = Block(0, {"message": "Genesis Block - IoT Blockchain Initialized"}, "0")
        self.chain.append(genesis)

    def add_block(self, data):
        """Add a new block to the chain after edge processing."""
        previous_block = self.chain[-1]
        new_block = Block(
            index=len(self.chain),
            data=data,
            previous_hash=previous_block.hash
        )
        self.chain.append(new_block)
        return new_block.to_dict()

    def get_chain(self):
        """Return the full blockchain as a list of dictionaries."""
        return [block.to_dict() for block in self.chain]

    def is_chain_valid(self):
        """Verify the integrity of the blockchain."""
        for i in range(1, len(self.chain)):
            current = self.chain[i]
            previous = self.chain[i - 1]
            if current.hash != current.calculate_hash():
                return False
            if current.previous_hash != previous.hash:
                return False
        return True

    def get_latest_block(self):
        """Return the most recent block."""
        return self.chain[-1].to_dict()
