output "ecr_repository_url" {
  value = module.ecr.repository_url
}

output "ci_role_arn" {
  value = module.ci_oidc.role_arn
}
