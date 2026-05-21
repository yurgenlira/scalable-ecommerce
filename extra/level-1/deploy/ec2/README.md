# Deploy (Level 1)

The app is deployed to the EC2 instance via SSM Run Command (no SSH) by the
`cd.yml` workflow on every merge to `main`. `deploy.sh` clones/updates the repo
under `/var/www/scalable-ecommerce`, builds `.env` from SSM Parameter Store,
runs migrations and reloads PHP-FPM.

## DNS (Namecheap)

The domain lives in Namecheap, not Route 53. Point it to the Elastic IP:

1. `terraform -chdir=extra/level-1/infra/envs/prod output -raw public_ip`
2. Namecheap → Domain → Advanced DNS → add A records:
   - `@`   → <EIP>
   - `www` → <EIP>

## TLS

Certbot issues the certificate on the instance (`certbot --nginx`, HTTP-01),
adds the 443 server block + the 80→443 redirect, and installs an auto-renewal
timer. Re-run after each `make up` (the instance is ephemeral).
