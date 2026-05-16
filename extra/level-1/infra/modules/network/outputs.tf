output "vpc_id" {
  value = aws_vpc.this.id
}

output "subnet_id" {
  description = "Public subnet for the EC2 instance"
  value       = aws_subnet.public[0].id
}

output "subnet_ids" {
  description = "Subnets for the RDS subnet group"
  value       = aws_subnet.public[*].id
}

output "ec2_sg_id" {
  value = aws_security_group.ec2.id
}

output "rds_sg_id" {
  value = aws_security_group.rds.id
}
