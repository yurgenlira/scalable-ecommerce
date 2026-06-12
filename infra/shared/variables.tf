variable "region" {
  description = "AWS region"
  type        = string
  default     = "us-east-1"
}

variable "create_oidc_provider" {
  description = "Create the account-level GitHub OIDC provider (false if it already exists)"
  type        = bool
  default     = true
}
