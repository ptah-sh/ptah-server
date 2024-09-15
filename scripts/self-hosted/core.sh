# Color functions
blue() { echo -e "\033[1;34m$1\033[0m"; }
bold() { echo -e "\033[1m$1\033[0m"; }
yellow() { echo -e "\033[33m$1\033[0m"; }
cyan() { echo -e "\033[36m$1\033[0m"; }
gray() { echo -e "\033[90m$1\033[0m"; }
green() { echo -e "\033[1;32m$1\033[0m"; }
red() { echo -e "\033[1;31m$1\033[0m"; }
thin() { echo -e "\033[2m$1\033[0m"; }

header() {
    echo -e "\n$(blue "=== $1 ===")"
}

help_text() {
    echo "$(thin "$(gray "--- $1")")"
}

# Header generated with http://www.kammerl.de/ascii/AsciiSignature.php
# Selected font - starwars

blue "$(cat << EOF

.______   .___________.    ___       __    __           _______. __    __
|   _  \  |           |   /   \     |  |  |  |         /       ||  |  |  |
|  |_)  | \`---|  |----\`  /  ^  \    |  |__|  |        |   (----\`|  |__|  |
|   ___/      |  |      /  /_\  \   |   __   |         \   \    |   __   |
|  |          |  |     /  _____  \  |  |  |  |  __ .----)   |   |  |  |  |
| _|          |__|    /__/     \__\ |__|  |__| (__)|_______/    |__|  |__|
$(green "$PTAH_COMPONENT_BANNER")



EOF
)"

if [ "$(whoami)" != "root" ]; then
    echo "$(red "ERROR: You should be root to run this script.")"

    exit 1
fi

OS_NAME=$(cat /etc/os-release | grep "^ID=" | cut -d= -f2)

DRY_MODE=${DRY_MODE:-false}

if [ -z "$SKIP_CORE_INSTALL" ]; then
    case "$OS_NAME" in
        ubuntu)
            echo "$(green "Installing Ptah.sh for Ubuntu...")"

            PKG_UPDATE_REGISTRIES="apt-get update"
            PKG_INSTALL="apt-get install -yq"

            export DEBIAN_FRONTEND=noninteractive
            ;;
        *)
            echo "Unsupported OS: $OS_NAME"
            echo "We currently support only Ubuntu."
            exit 1
            ;;
    esac

    ARCH=$(uname -m)

    case "$ARCH" in
        x86_64)
            ;;
        *)
            echo "$(red "Unsupported architecture: $ARCH")"
            echo "$(red "We currently support only x86_64.")"
            exit 1
            ;;
    esac

    export DOCKER="docker"

    if [ "$DRY_MODE" = true ]; then
        echo "$(yellow "DRY_MODE is enabled. We will not install anything.")"

        PKG_UPDATE_REGISTRIES="echo $PKG_UPDATE_REGISTRIES"
        PKG_INSTALL="echo $PKG_INSTALL"

        export DOCKER="echo $DOCKER"
    fi

    header "Install System Packages"

    $PKG_UPDATE_REGISTRIES
    $PKG_INSTALL sudo curl unzip ca-certificates apache2-utils netfilter-persistent

    header "Install Docker"
    help_text "installation script provided by Docker and available at https://get.docker.com/"

    curl -fsSL https://get.docker.com/ | sh

    header "Configure Docker"
    help_text "Adding Caddy admin port to iptables"

    iptables -I DOCKER-USER -p tcp -s 127.0.0.1 --dport 2019 -j ACCEPT
    iptables -I DOCKER-USER -p tcp --dport 2019 -j REJECT --reject-with tcp-reset

    netfilter-persistent save

    rm -f /tmp/ptah-agent
    
    curl -L https://github.com/ptah-sh/ptah-agent/releases/latest/download/ptah-agent-linux-x86_64.bin -o /tmp/ptah-agent

    chmod +x /tmp/ptah-agent
fi
