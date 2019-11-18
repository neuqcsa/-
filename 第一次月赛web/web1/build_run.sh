#!/bin/bash
docker build -t calc:latest .
docker run -d -p 5000:5000 calc:latest
