#!/usr/bin/env bash
set -euo pipefail

# Ensure a known catalog state on the running instance
INSTANCE=$(terraform -chdir=extra/level-1/infra/envs/prod output -raw instance_id)
CMD=$(aws ssm send-command --instance-ids "$INSTANCE" --document-name AWS-RunShellScript \
  --parameters 'commands=["cd /var/www/scalable-ecommerce/app && php artisan db:seed --force"]' \
  --query Command.CommandId --output text)
aws ssm wait command-executed --command-id "$CMD" --instance-id "$INSTANCE" || true

# print only the status on success; on failure, dump the output to diagnose
status=$(aws ssm get-command-invocation --command-id "$CMD" --instance-id "$INSTANCE" --query Status --output text)
if [ "$status" = "Success" ]; then
  echo "Success"
else
  aws ssm get-command-invocation --command-id "$CMD" --instance-id "$INSTANCE" \
    --query '{status:Status,out:StandardOutputContent,err:StandardErrorContent}' --output json
  exit 1
fi