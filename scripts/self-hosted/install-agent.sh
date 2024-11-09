#!/usr/bin/env bash

set -e

# Header generated with http://www.kammerl.de/ascii/AsciiSignature.php
# Selected font - starwars

PTAH_COMPONENT_BANNER=$(cat << EOF
                                                                      _   
                                            __ _   __ _   ___  _ __  | |_ 
                                           / _\` | / _\` | / _ \| '_ \ | __|
                                          | (_| || (_| ||  __/| | | || |_ 
                                           \__,_| \__, | \___||_| |_| \__|
                                                  |___/                   
EOF
)

# Get the directory of the current script
SCRIPT_DIR="$(dirname "$0")"

#=== core.sh
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
#--- core.sh

USER="ptah"
GROUP="ptah"

if [ -z "$PTAH_TOKEN" ]; then
    echo "$(red "ERROR: PTAH_TOKEN is not set.")"

    exit 1
fi

PTAH_TOKEN="${PTAH_TOKEN:-$PTAH_TOKEN}"
PTAH_BASE_URL="${PTAH_BASE_URL:-"https://ctl.ptah.sh"}"

header "Ptah Agent Installation"

group_exists=$(sudo getent group "$GROUP" || true)
if [ -z "$group_exists" ]; then
    echo "$(bold "Creating group:") $(cyan "$GROUP")"

    sudo groupadd "$GROUP"
else
    echo "$(bold "Using group:") $(cyan "$GROUP")"
fi

user_exists=$(sudo getent passwd "$USER" || true)
if [ -z "$user_exists" ]; then
    echo "$(bold "Creating user:") $(cyan "$USER")"

    sudo useradd --create-home --no-user-group --gid "$GROUP" --groups docker --system "$USER"
else
    echo "$(bold "Using user:") $(cyan "$USER")"
fi

chown -R "$USER:$GROUP" /tmp/ptah-agent

echo "$(bold "Switching to user:") $(cyan "$USER")"

sudo -u "$USER" bash << EOF

set -e

echo "$(bold "Running in user space as:") $(cyan "\$USER")"

SEED_VERSION="\$HOME/ptah-agent/versions/v0.0.0"

mkdir -p "\$SEED_VERSION"

mv /tmp/ptah-agent "\$SEED_VERSION/ptah-agent"

ln -nsf "\$SEED_VERSION/ptah-agent" "\$HOME/ptah-agent/current"

echo "$(header "Installing docker-ingress-routing-daemon (DIRD)")"
echo "$(help_text "see https://r.ptah.sh/help-why-dird why the DIRD is needed")"
echo "$(help_text "see https://r.ptah.sh/help-what-is-dird for technical details")"

mkdir -p \$HOME/dird

curl -L https://raw.githubusercontent.com/newsnowlabs/docker-ingress-routing-daemon/b7f58dbac0038f0a925938e639c95a75392c9208/docker-ingress-routing-daemon -o \$HOME/dird/docker-ingress-routing-daemon

chmod +x \$HOME/dird/docker-ingress-routing-daemon

echo '--tcp-ports 80,443 --ingress-gateway-ips 10.0.0.2' > \$HOME/dird/params.conf

echo '#!/usr/bin/env bash' > \$HOME/dird/start.sh
echo '\$HOME/dird/docker-ingress-routing-daemon --install --preexisting --iptables-wait \$(cat \$HOME/dird/params.conf)' >> \$HOME/dird/start.sh

chmod +x \$HOME/dird/start.sh

EOF

if [ -z "$(which systemctl)" ]; then
    echo "$(red "systemctl was not found.")"
    echo ""
    echo "$(bold "Are you running in Docker?")"
    echo ""
    echo "$(bold "Please add the following commands to your init system manually:")"
    echo ""
    echo "    /home/$USER/ptah-agent/current".
    echo ""
    echo "    /home/$USER/dird/start.sh".
    echo ""
    echo "$(green "Agent installation completed.")"

    exit 0
fi

echo "$(header "Install ptah-agent systemd services")"

# TODO: add ExecStartPre and ExecStartPost to notify about agent restarts
cat <<EOF > /etc/systemd/system/ptah-agent.service
[Unit]
Description=Ptah.sh Agent
Documentation=https://ptah.sh
After=network.target

[Service]
User=$USER
Group=$GROUP
Environment=PTAH_ROOT_DIR=/home/$USER/ptah-agent
Environment=PTAH_TOKEN=$PTAH_TOKEN
Environment=PTAH_BASE_URL=$PTAH_BASE_URL
Type=exec
ExecStart=/home/$USER/ptah-agent/current
Restart=always
RestartSteps=5
RestartSec=5
RestartMaxDelaySec=30

[Install]
WantedBy=multi-user.target
EOF

cat <<EOF > /etc/systemd/system/dird.service
[Unit]
Description=Dird Service
After=sysinit.target dockerd.service
StartLimitIntervalSec=0

[Service]
ExecStart=/home/$USER/dird/start.sh
Restart=always

[Install]
WantedBy=multi-user.target
EOF

cat <<EOF > /etc/systemd/system/dird-restart.service
[Unit]
Description=Dird Service Restart
After=dird.service

[Service]
Type=oneshot
ExecStart=/bin/systemctl restart dird

[Install]
WantedBy=multi-user.target
EOF

cat <<EOF > /etc/systemd/system/dird.path
[Unit]
Description=Dird Service Params File
After=sysinit.target dockerd.service

[Path]
PathModified=/home/$USER/dird/params.conf
Unit=dird-restart.service

[Install]
WantedBy=multi-user.target
EOF


echo "$(bold "Reloading systemd...")"

systemctl daemon-reload

systemctl enable ptah-agent
systemctl start ptah-agent

systemctl enable dird
systemctl start dird

systemctl enable dird-restart
systemctl start dird-restart

systemctl enable dird.path
systemctl start dird.path

echo "$(green "Agent installation completed. Please check status on $PTAH_BASE_URL.")"
