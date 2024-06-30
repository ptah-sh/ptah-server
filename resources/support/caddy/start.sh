# This is the entrypoint for Caddy.
# By default, it starts with a blank Caddyfile with a standard "Hello from Caddy" page.

if [ -f "/config/caddy/autosave.json" ]; then
    echo "Starting with autosave.json"

    caddy run --config "/config/caddy/autosave.json"
else
    echo "Starting with the default Caddyfile"

    caddy run --config "/etc/caddy/Caddyfile"
fi
