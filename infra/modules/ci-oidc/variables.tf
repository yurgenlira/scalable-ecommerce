variable "role_name" {
  description = "IAM role name for the CI pipeline"
  type        = string
  default     = "scalable-ecommerce-ci"
}

variable "create_oidc_provider" {
  description = "Create the account-level GitHub OIDC provider (false if it already exists)"
  type        = bool
  default     = true
}

variable "github_repo" {
  description = "owner/repo allowed to assume the role"
  type        = string
}

variable "ecr_repository_arn" {
  description = "ARN of the ECR repository the role can push to"
  type        = string
}
