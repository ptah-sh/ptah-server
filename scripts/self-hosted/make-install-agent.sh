#!/usr/bin/env bash

set -e

SCRIPT_DIR="$(dirname "$0")"

# Read the contents of agent.sh
agent_content=$(< "$SCRIPT_DIR/agent.sh")

# Read the contents of core.sh
core_content=$(< "$SCRIPT_DIR/core.sh")

# Replace #include:core.sh with the contents of core.sh
install_agent_content="${agent_content//#include:core.sh/$core_content}"

echo "$install_agent_content" > "$SCRIPT_DIR/install-agent.sh"

echo "install-agent.sh has been created."

chmod +x "$SCRIPT_DIR/install-agent.sh"
