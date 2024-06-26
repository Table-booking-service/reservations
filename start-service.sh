#!/bin/bash

# Define environment variables
export APP_PORT=80
export FORWARD_DB_PORT=5433
export DB_DATABASE=reservationsdb
export DB_USERNAME=reservationsuser
export DB_PASSWORD=password
export TABLES_SERVICE_IP=1.1.1.1
export CLIENTS_SERVICE_IP=1.1.1.1

# Create a network for the containers
docker network create reservations-network

docker stop app postgres
docker rm app postgres

# Run the PostgreSQL container
docker run -d \
  --name postgres \
  --network reservations-network \
  -p ${FORWARD_DB_PORT}:5432 \
  -e POSTGRES_DB=${DB_DATABASE} \
  -e POSTGRES_USER=${DB_USERNAME} \
  -e POSTGRES_PASSWORD=${DB_PASSWORD} \
  postgres:15

# Run the application container
docker run -d \
  --pull missing \
  --name app \
  --network reservations-network \
  -p ${APP_PORT}:80 \
  psychosoc1al/reservations:latest

