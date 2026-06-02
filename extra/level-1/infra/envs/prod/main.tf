terraform {
  required_version = ">= 1.15"
  required_providers {
    aws    = { source = "hashicorp/aws", version = "~> 6.0" }
    random = { source = "hashicorp/random", version = "~> 3.0" }
  }
  backend "s3" {
    bucket       = "scalable-ecommerce-tfstate"
    key          = "level-1/prod/terraform.tfstate"
    region       = "us-east-1"
    encrypt      = true
    use_lockfile = true
  }
}

provider "aws" {
  region = var.region
}

module "network" {
  source = "../../modules/network"
}

module "compute" {
  source    = "../../modules/compute-ec2"
  subnet_id = module.network.subnet_id
  sg_id     = module.network.ec2_sg_id
  domain = var.domain 
}

module "database" {
  source     = "../../modules/rds"
  subnet_ids = module.network.subnet_ids
  sg_id      = module.network.rds_sg_id
}

resource "random_id" "app_key" {
  byte_length = 32
}

locals {
  ssm_prefix = "/scalable-ecommerce/prod"
}

resource "aws_ssm_parameter" "app_key" {
  name  = "${local.ssm_prefix}/app_key"
  type  = "SecureString"
  value = "base64:${random_id.app_key.b64_std}"
}

resource "aws_ssm_parameter" "db_password" {
  name  = "${local.ssm_prefix}/db_password"
  type  = "SecureString"
  value = module.database.db_password
}

resource "aws_ssm_parameter" "db_host" {
  name  = "${local.ssm_prefix}/db_host"
  type  = "String"
  value = module.database.db_endpoint
}

resource "aws_ssm_parameter" "db_name" {
  name  = "${local.ssm_prefix}/db_name"
  type  = "String"
  value = module.database.db_name
}

resource "aws_ssm_parameter" "db_username" {
  name  = "${local.ssm_prefix}/db_username"
  type  = "String"
  value = module.database.db_username
}

module "observability" {
  source      = "../../modules/observability"
  instance_id = module.compute.instance_id
}