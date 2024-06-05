#!/bin/bash

#sudo docker login -u psychosoc1al   # only once
sudo docker-compose build
sudo docker tag reservations psychosoc1al/reservations
sudo docker push psychosoc1al/reservations
