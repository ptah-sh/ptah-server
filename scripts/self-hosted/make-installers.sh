#!/usr/bin/env bash

set -e

SCRIPT_DIR="$(dirname "$0")"

# Read the contents of agent.sh
agent_content=$(< "$SCRIPT_DIR/agent.sh")

# Read the contents of server.sh
server_content=$(< "$SCRIPT_DIR/server.sh")

# Read the contents of core.sh
core_content=$(< "$SCRIPT_DIR/core.sh")

# Replace #include:core.sh with the contents of core.sh for agent
install_agent_content="${agent_content//#include:core.sh/$core_content}"

# Replace #include:core.sh with the contents of core.sh for server
install_server_content="${server_content//#include:core.sh/$core_content}"

echo "$install_agent_content" > "$SCRIPT_DIR/install-agent.sh"
echo "$install_server_content" > "$SCRIPT_DIR/install-server.sh"

echo "install-agent.sh has been created."
echo "install-server.sh has been created."

chmod +x "$SCRIPT_DIR/install-agent.sh"
chmod +x "$SCRIPT_DIR/install-server.sh"
