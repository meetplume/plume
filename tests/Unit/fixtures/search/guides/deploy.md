---
title: Deploy to Production
description: Steps to ship Plume to production
order: 1
---

# Deploy to Production

Make sure to run migrations before deployment.

## Prerequisites

You will need a server with PHP 8.4.

### Web Server

Nginx or Caddy works great.

## Steps

1. Pull the latest code
2. Run `composer install`
3. Restart your queue workers
