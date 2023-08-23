#!/bin/bash
# Before use+
# - Save this script under /usr/local/bin/
# - Change permissions to 755

php_image="php:8.2"

# Check if Docker is installed and available
if ! command -v docker &>/dev/null; then
    echo "Docker is not installed or not in your PATH. Please install Docker."
    exit 1
fi

# Check for the correct number of arguments
if [ "$#" -lt 1 ]; then
    docker run --rm -it $php_image --help
    exit 1
fi

first_argument="$1"
hostname=""
port=""
port_forward=""
php_argument=$@

# Check if the -S option is present
if [[ "$first_argument" == *"-S"* ]]; then
    # Split value given for -S argument by ":"
    IFS=":" read -ra parts <<<"$2"
    hostname="${parts[0]}"
    port="${parts[1]}"

    [[ "$port" == "" ]] && port=80
    # Change localhost to 0.0.0.0 since php server accepsts request from port-forward of docker container
    [[ "$hostname" == "localhost" ]] && hostname="0.0.0.0"

    port_forward="-p $port:$port"
    php_argument="-S $hostname:$port"
fi

docker run --rm -it --workdir /app $port_forward -v $(pwd):/app $php_image php "$php_argument"
