# Level 1 — Demo script

Provision → serve → buy → observe → ship → destroy. Simulated payment.

## 1. Provision
```bash
make up
terraform -chdir=extra/level-1/infra/envs/prod output public_ip
make cost-guard ALERT_EMAIL=yurgenlira@hotmail.com   # budget + auto-stop guardrails
```
Show: EC2 + RDS created by Terraform with one command, plus the FinOps guardrails.

## 2. DNS + HTTPS
```bash
dig +short jlira.dev
curl -sI https://jlira.dev/up        # 200, valid Let's Encrypt cert
curl -sI http://jlira.dev/up         # 301 → https
```

## 3. Seed demo data
```bash
make demo
```

## 4. Purchase flow
```bash
EMAIL="demo$(date +%s)@example.com"
TOKEN=$(curl -s -X POST https://jlira.dev/api/register -H "Accept: application/json" \
  -d "name=Demo&email=$EMAIL&password=secret12" | jq -r .token)
curl -s https://jlira.dev/api/products | jq '.data[].name'
curl -s -X POST https://jlira.dev/api/cart/items -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" -d 'product_id=1&quantity=1'
curl -s -X POST https://jlira.dev/api/checkout -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" | jq    # status: paid
```

## 5. Observability
```bash
aws logs tail /scalable-ecommerce/prod/app --since 5m --format short | grep request_id
```
Show: JSON logs correlated by request_id; dashboard `scalable-ecommerce-l1` (CPU + requests).

## 6. CI/CD on push
Open a PR with a trivial change; show `ci` green (Pint, Larastan, Pest on PostgreSQL, gitleaks),
then `cd` deploying via SSM on merge:
```bash
gh run watch
```

## 7. Tear down to $0
```bash
make destroy-all        # destroy all project resources"
```
