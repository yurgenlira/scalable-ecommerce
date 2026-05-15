# Terraform remote state bootstrap

Creates the S3 bucket that stores the remote Terraform state for the Level 1
environment. **Run once per account/region** — it is not part of `make up` /
`make down`.

State locking is handled natively by the S3 backend (`use_lockfile = true`),
so there is no DynamoDB table.

## Usage

```bash
terraform init
terraform apply
```

## Teardown

Only when abandoning the project — this destroys the state bucket:

```bash
terraform destroy
```
