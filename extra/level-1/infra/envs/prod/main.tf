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
}

module "database" {
  source     = "../../modules/rds"
  subnet_ids = module.network.subnet_ids
  sg_id      = module.network.rds_sg_id
}