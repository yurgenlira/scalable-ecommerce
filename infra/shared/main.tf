terraform {
  required_version = ">= 1.15"
  required_providers {
    aws = { source = "hashicorp/aws", version = "~> 6.0" }
  }
  backend "s3" {
    bucket       = "scalable-ecommerce-tfstate"
    key          = "level-2/shared/terraform.tfstate"
    region       = "us-east-1"
    encrypt      = true
    use_lockfile = true
  }
}

provider "aws" {
  region = var.region

  default_tags {
    tags = {
      Project   = "scalable-ecommerce"
      Level     = "2"
      ManagedBy = "terraform"
    }
  }
}

module "ecr" {
  source = "../modules/ecr"
  name   = "scalable-ecommerce"
}

module "ci_oidc" {
  source               = "../modules/ci-oidc"
  github_repo          = "yurgenlira/scalable-ecommerce"
  ecr_repository_arn   = module.ecr.repository_arn
  create_oidc_provider = var.create_oidc_provider
}
