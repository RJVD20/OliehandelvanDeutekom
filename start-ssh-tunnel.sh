#!/usr/bin/env bash
set -euo pipefail

# Start SSH tunnel for MySQL
# Keeps the session open; close with Ctrl+C
ssh -i "C:/Users/robin/.ssh/id_rsa" -N -L 3307:127.0.0.1:3306 ploi@91.202.208.2
