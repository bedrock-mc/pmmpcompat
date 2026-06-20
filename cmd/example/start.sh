#!/usr/bin/env bash
set -euo pipefail

DIR="$(cd -P "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DIR"

PHP_TAG="${PMMPCOMPAT_PHP_TAG:-pm5-php-8.2-latest}"
PHP_RELEASE_URL="https://github.com/pmmp/PHP-Binaries/releases/download/${PHP_TAG}"
PHP_DIR="${PMMPCOMPAT_PHP_DIR:-$DIR/bin}"
PLUGINS_DIR="${PMMPCOMPAT_PLUGINS:-$DIR/plugins}"
DATA_DIR="${PMMPCOMPAT_DATA:-$DIR/data}"

case "$(uname -s)" in
	Darwin) OS="MacOS" ;;
	Linux) OS="Linux" ;;
	*)
		echo "Unsupported OS: $(uname -s)" >&2
		exit 1
		;;
esac

case "$(uname -m)" in
	x86_64|amd64) ARCH="x86_64" ;;
	arm64|aarch64)
		if [ "$OS" = "Linux" ]; then
			echo "PMMP does not publish Linux arm64 PHP binaries for ${PHP_TAG}. Set PMMPCOMPAT_PHP manually." >&2
			exit 1
		fi
		ARCH="arm64"
		;;
	*)
		echo "Unsupported architecture: $(uname -m)" >&2
		exit 1
		;;
esac

# PMMP keeps the extracted runtime under bin/php7 even for current PHP 8.x builds.
archive="PHP-8.2-${OS}-${ARCH}-PM5.tar.gz"
php_bin="$PHP_DIR/bin/php7/bin/php"

if [ -z "${PMMPCOMPAT_PHP:-}" ] && [ ! -x "$php_bin" ]; then
	mkdir -p "$PHP_DIR"
	tmp="$(mktemp -d)"
	trap 'rm -rf "$tmp"' EXIT
	echo "Downloading PMMP PHP binary: ${archive}"
	if command -v curl >/dev/null 2>&1; then
		curl -fL "${PHP_RELEASE_URL}/${archive}" -o "$tmp/$archive"
	elif command -v wget >/dev/null 2>&1; then
		wget -O "$tmp/$archive" "${PHP_RELEASE_URL}/${archive}"
	else
		echo "curl or wget is required to download PMMP PHP binaries" >&2
		exit 1
	fi
	tar -xzf "$tmp/$archive" -C "$PHP_DIR"
fi

if [ -z "${PMMPCOMPAT_PHP:-}" ]; then
	if [ -x "$php_bin" ]; then
		export PMMPCOMPAT_PHP="$php_bin"
	elif command -v php >/dev/null 2>&1; then
		export PMMPCOMPAT_PHP="$(command -v php)"
	else
		echo "No PHP binary found. Set PMMPCOMPAT_PHP manually." >&2
		exit 1
	fi
fi

if [ -z "${PMMPCOMPAT_PHP_ARGS:-}" ]; then
	extension_dir="$(find "$PHP_DIR/bin/php7/lib/php/extensions" -type d -name 'no-debug-zts-*' 2>/dev/null | head -n 1 || true)"
	if [ -n "$extension_dir" ]; then
		export PMMPCOMPAT_PHP_ARGS="-d extension_dir=$extension_dir"
	fi
fi

mkdir -p "$PLUGINS_DIR" "$DATA_DIR"
export PMMPCOMPAT_PLUGINS="$PLUGINS_DIR"
export PMMPCOMPAT_DATA="$DATA_DIR"

echo "PHP: $PMMPCOMPAT_PHP"
echo "Plugins: $PMMPCOMPAT_PLUGINS"
echo "Data: $PMMPCOMPAT_DATA"
exec go run . "$@"
