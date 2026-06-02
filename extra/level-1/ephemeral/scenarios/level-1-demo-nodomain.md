# Level 1 — Demo script (no-domain mode)

Provision → serve over the EIP (HTTP) → buy → observe → ship → destroy.
No DNS, no TLS. Simulated payment.

## 1. Provision (domain="")
```bash
make up
EIP=$(terraform -chdir=extra/level-1/infra/envs/prod output -raw public_ip)
echo "$EIP"
make cost-guard ALERT_EMAIL=yurgenlira@hotmail.com   # budget + auto-stop guardrails
```

## 2. Deploy the app
```bash
gh workflow run cd.yml && gh run watch
```

## 3. Refresh demo data
```bash
make demo
```

## 4. Purchase flow (over the EIP, HTTP)
```bash
EMAIL="demo$(date +%s)@example.com"
TOKEN=$(curl -s -X POST "http://$EIP/api/register" -H "Accept: application/json" \
  -d "name=Demo&email=$EMAIL&password=secret12" | jq -r .token)
curl -s "http://$EIP/api/products" | jq '.data[].name'
curl -s -X POST "http://$EIP/api/cart/items" -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" -d 'product_id=1&quantity=1'
curl -s -X POST "http://$EIP/api/checkout" -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" | jq    # status: paid
```

## 5. Observability
```bash
aws logs tail /scalable-ecommerce/prod/app --since 5m --format short | grep request_id
```

## 6. CI/CD on push
Open a PR with a trivial change; `ci` green, then `cd` deploys via SSM on merge:
```bash
gh run watch
```

## 7. Tear down to $0
```bash
make destroy-all        # destroy all project resources, verify $0 cost
```
