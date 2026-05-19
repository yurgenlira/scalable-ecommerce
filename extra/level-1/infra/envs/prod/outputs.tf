output "instance_id" {
  value = module.compute.instance_id
}

output "public_ip" {
  value = module.compute.public_ip
}

output "db_endpoint" {
  value = module.database.db_endpoint
}
