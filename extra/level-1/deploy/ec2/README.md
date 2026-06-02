# Deploy (Level 1)

The app is deployed to the EC2 instance via SSM Run Command (no SSH) by the
`cd.yml` workflow on every merge to `main`. `deploy.sh` clones/updates the repo
under `/var/www/scalable-ecommerce`, builds `.env` from SSM Parameter Store,
runs migrations and reloads PHP-FPM.

## Modes

Two serving modes via the `domain` variable (default: no-domain):

- **No-domain (default)** — `make up`: serves HTTP on the Elastic IP, no DNS or
  Certbot. Access: `http://<EIP>/up`. Fast for iteration.
- **Domain + HTTPS** — `terraform apply -var="domain=jlira.dev"`: sets the Nginx
  `server_name`; then point the A records to the EIP and issue the cert with
  Certbot (see Sprint 1.3). Access: `https://jlira.dev/up`.

## DNS (Namecheap)

The domain lives in Namecheap, not Route 53. Point it to the Elastic IP:

1. `terraform -chdir=extra/level-1/infra/envs/prod output -raw public_ip`
2. Namecheap → Domain → Advanced DNS → add A records:
   - `@`   → <EIP>
   - `www` → <EIP>

> `make down` destroys the Elastic IP, so `make up` allocates a **new** IP and
> the A records must be updated again. Recreating only the instance
> (`terraform apply -replace='module.compute.aws_instance.this'`) keeps the EIP
> and the DNS untouched — prefer it while iterating.

## TLS

Certbot issues the certificate on the instance (`certbot --nginx`, HTTP-01),
adds the 443 server block + the 80→443 redirect, and installs an auto-renewal
timer. Re-run after each `make up` (the instance is ephemeral).
