#!/usr/bin/env bash
set -euo pipefail

# Set project and ephimeral paths
TF="terraform -chdir=extra/level-1/infra/envs/prod"
COST_GUARD="terraform -chdir=extra/level-1/ephemeral/cost-guard"

# Destroy the ephemeral and the main infra
$COST_GUARD destroy -auto-approve -var="instance_id=none" -var="alert_email=none" 2>/dev/null || true
$TF destroy -auto-approve

# Verify no billable project resources remain
echo "Checking for residual resources..."
fail=0

# List resources related to scalable-ecommerce
ec2=$(aws ec2 describe-instances \
  --filters 'Name=tag:Name,Values=scalable-ecommerce' 'Name=instance-state-name,Values=pending,running,stopping,stopped' \
  --query 'Reservations[].Instances[].InstanceId' --output text)
[ -n "$ec2" ] && { echo "Residual EC2: $ec2"; fail=1; }

eip=$(aws ec2 describe-addresses --query 'Addresses[?AssociationId==`null`].PublicIp' --output text)
[ -n "$eip" ] && { echo "Unassociated EIP: $eip"; fail=1; }

rds=$(aws rds describe-db-instances \
  --query "DBInstances[?starts_with(DBInstanceIdentifier, 'scalable-ecommerce')].DBInstanceIdentifier" --output text)
[ -n "$rds" ] && { echo "Residual RDS: $rds"; fail=1; }

logs=$(aws logs describe-log-groups --log-group-name-prefix /scalable-ecommerce \
  --query 'logGroups[].logGroupName' --output text)
[ -n "$logs" ] && { echo "Residual log groups: $logs"; fail=1; }

# S3 buckets (bootstrap Terraform state) are intentionally NOT destroyed: they persist across teardowns (~ $0)
if [ "$fail" -eq 0 ]; then
  echo "\$0 cost: no residual project resources (bootstrap state bucket kept separately)"
else
  echo "Residual resources found — review above"; exit 1
fi