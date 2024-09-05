#!/usr/bin/env bash

set -e


PTAH_COMPONENT_BANNER=$(cat << EOF
                                        ___   ___  _ __ __   __ ___  _ __ 
                                       / __| / _ \| '__|\ \ / // _ \| '__|
                                       \__ \|  __/| |    \ V /|  __/| |   
                                       |___/ \___||_|     \_/  \___||_|   
EOF
)

# Get the directory of the current script
SCRIPT_DIR="$(dirname "$0")"

#=== core.sh
#include:core.sh
#--- core.sh

# Function to let user choose an IP address with styling
choose_ip_address() {
    local ip_list="$1"
    local prompt="$2"
    local variable_name="$3"
    local help_text="$4"
    local IFS=' '
    read -ra ip_array <<< "$ip_list"
    
    header "$prompt"
    echo "$help_text"

    if [ ${#ip_array[@]} -eq 1 ]; then
        local chosen_ip="${ip_array[0]}"
        echo -e "$(green "Only one IP address available:") $(cyan "$chosen_ip")\n"
        eval "$variable_name='$chosen_ip'"
    else
        echo "$(bold "Please choose an IP address:")"
        for i in "${!ip_array[@]}"; do
            echo "$(yellow "$((i+1)))") $(cyan "${ip_array[i]}")"
        done
        echo "$(gray "(Enter the number of your choice)")"
        
        while true; do
            read -p "$(green ">") " choice
            if [[ "$choice" =~ ^[0-9]+$ ]] && [ "$choice" -ge 1 ] && [ "$choice" -le "${#ip_array[@]}" ]; then
                local chosen_ip="${ip_array[$((choice-1))]}"
                echo -e "\n$(green "You selected:") $(cyan "$chosen_ip")\n"
                eval "$variable_name='$chosen_ip'"
                break
            else
                echo "$(red "Invalid selection. Please try again.")"
            fi
        done
    fi
}

# Define help texts
ADVERTISED_IP_HELP=$(cat << EOF 
$(help_text "for other Swarm nodes to connect to this node")
$(help_text "usually a private IP address")
$(help_text "read more: https://r.ptah.sh/help-advertise-addr")
EOF
)

PUBLIC_IP_HELP=$(cat << EOF 
$(help_text "for external access to your node")
$(help_text "usually a public IP address")
EOF
)

# Function to get user email and password
get_user_credentials() {
    header "User Credentials"
    help_text "for your Ptah Dashboard login"
    
    # Ask for email
    while true; do
        read -p "$(green "Enter your email: ")" USER_EMAIL
        if [[ "$USER_EMAIL" =~ ^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}$ ]]; then
            break
        else
            echo "$(red "Invalid email format. Please try again.")"
        fi
    done

    # Ask for password
    while true; do
        read -s -p "$(green "Enter your password: ")" USER_PASSWORD
        echo
        read -s -p "$(green "Confirm your password: ")" USER_PASSWORD_CONFIRM
        echo
        if [ "$USER_PASSWORD" = "$USER_PASSWORD_CONFIRM" ]; then
            break
        else
            echo "$(red "Passwords do not match. Please try again.")"
        fi
    done

    echo -e "\n$(green "Credentials saved successfully.")"
}

IP_LIST=$(./ptah-agent list-ips)
choose_ip_address "$IP_LIST" "Advertised IP addresses" "ADVERTISE_ADDR" "$ADVERTISED_IP_HELP"
choose_ip_address "$IP_LIST" "Public IP address" "PUBLIC_IP" "$PUBLIC_IP_HELP"

# Now $ADVERTISE_ADDR contains the selected advertised IP address
# and $PUBLIC_IP contains the selected public IP address
echo "$(bold "Chosen Advertised Addr:") $(cyan "$ADVERTISE_ADDR")"
echo "$(bold "Chosen Public IP:") $(cyan "$PUBLIC_IP")"

# Get user credentials
# get_user_credentials

# Now $USER_EMAIL and $USER_PASSWORD contain the user's credentials
# echo "$(bold "User Email:") $(cyan "$USER_EMAIL")"
# We don't echo the password for security reasons

# Update tasks.json file
header "Updating tasks.json"

# Read the tasks.json file
input_file="tasks.json"
output_file="tasks.json.tmp"

# Generate a random 32-byte key and encode it with base64
random_key=$(openssl rand -base64 32)

# Generate a random string of 42 characters
random_token=$(openssl rand -base64 32 | tr -dc 'a-zA-Z0-9' | head -c 42)

# Perform the replacements
sed -e "s|192\.168\.1\.1|$ADVERTISE_ADDR|g" \
    -e "s|ptah\.localhost|$PUBLIC_IP|g" \
    -e "s|base64:APP_KEY|base64:$random_key|g" \
    -e "s|PTAH_TOKEN|$random_token|g" \
    "$input_file" > "$output_file"

# Replace the original file with the updated one
mv "$output_file" "$input_file"

echo "$(green "tasks.json has been updated successfully.")"

random_password=$(openssl rand -base64 32 | tr -dc 'a-zA-Z0-9' | head -c 12)
password_hash=$(htpasswd -bnBC 12 "" $random_password | cut -d : -f 2)

export PTAH_TOKEN="$random_token"
export PTAH_BASE_URL="http://$PUBLIC_IP:80"

export SKIP_CORE_INSTALL=1

bash agent.sh

export PTAH_ROOT_DIR=/home/ptah/ptah-agent

/home/ptah/ptah-agent/current exec-tasks tasks.json


sql_dump_file="db.sql"
sql_dump_file_tmp="db.sql.tmp"

sed -e "s|self_hosted_password|$password_hash|g" \
    "$sql_dump_file" > "$sql_dump_file_tmp"

# Replace the original file with the updated one
mv "$sql_dump_file_tmp" "$sql_dump_file"

echo "$(green "$sql_dump_file has been updated successfully.")"

# Function to wait for PostgreSQL to be ready
wait_for_postgres() {
    local container=$1
    local max_attempts=30
    local attempt=1

    echo "$(yellow "Waiting for PostgreSQL to be ready...")"

    while [ $attempt -le $max_attempts ]; do
        if docker exec $container pg_isready -U ptah_sh > /dev/null 2>&1; then
            echo "$(green "PostgreSQL is ready!")"
            return 0
        fi
        echo "$(yellow "Attempt $attempt/$max_attempts: PostgreSQL is not ready yet. Waiting...")"
        sleep 2
        attempt=$((attempt + 1))
    done

    echo "$(red "Error: PostgreSQL did not become ready in time.")"
    return 1
}

# Function to find the PostgreSQL container with retries
find_pg_container() {
    local max_attempts=5
    local attempt=1

    while [ $attempt -le $max_attempts ]; do
        pg_container=$(docker ps --format '{{.Names}}' | grep 'ptah_pg.' || true)
        if [ -n "$pg_container" ]; then
            echo "$(green "Found PostgreSQL container:") $(cyan "$pg_container")"
            return 0
        fi
        echo "$(yellow "Attempt $attempt/$max_attempts: PostgreSQL container not found. Retrying...")"
        sleep 5
        attempt=$((attempt + 1))
    done

    echo "$(red "Error: PostgreSQL container not found after $max_attempts attempts.")"
    return 1
}

# Import SQL dump into PostgreSQL
header "Importing SQL dump into PostgreSQL"

# Find the PostgreSQL container with retries
if ! find_pg_container; then
    exit 1
fi

# Now $pg_container is accessible here
echo "Using PostgreSQL container: $pg_container"

# Wait for PostgreSQL to be ready
if ! wait_for_postgres "$pg_container"; then
    echo "$(red "Error: PostgreSQL did not start in time.")"
    exit 1
fi

# Import the SQL dump
if docker exec --env PGPASSWORD=ptah_sh -i "$pg_container" psql -U ptah_sh -d ptah_sh < db.sql; then
    echo "$(green "SQL dump has been imported successfully.")"
else
    echo "$(red "Error: Failed to import SQL dump.")"
    exit 1
fi

# Wait for Ptah.sh to be ready
header "Waiting for Ptah.sh to be ready"
max_attempts=36  # 3 minutes with 5-second intervals
attempt=1

while [ $attempt -le $max_attempts ]; do
    if curl -s -o /dev/null -w "%{http_code}" "http://$PUBLIC_IP/login" | grep -q "200"; then
        echo "$(green "Ptah.sh is ready!")"
        break
    fi
    echo "$(yellow "Attempt $attempt/$max_attempts: Ptah.sh is not ready yet. Waiting...")"
    sleep 5
    attempt=$((attempt + 1))
done

if [ $attempt -gt $max_attempts ]; then
    echo "$(red "Error: Ptah.sh did not become ready within 3 minutes.")"
    exit 1
fi

echo "$(header "Ptah.sh Installation Complete")"
echo "$(green "You can now access the Ptah.sh Dashboard at:") $(cyan "http://$PUBLIC_IP/dashboard")"
echo "$(green "Username:") $(cyan "self-hosted@localhost")"
echo "$(green "Password:") $(cyan "$random_password")"
