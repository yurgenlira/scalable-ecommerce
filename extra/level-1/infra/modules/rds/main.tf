variable "name" {
  type    = string
  default = "scalable-ecommerce"
}

variable "subnet_ids" {
  type = list(string)
}

variable "sg_id" {
  type = string
}

variable "db_name" {
  type    = string
  default = "ecommerce"
}

variable "username" {
  type    = string
  default = "ecommerce"
}

resource "random_password" "db" {
  length  = 32
  special = false
}

resource "aws_db_subnet_group" "this" {
  name_prefix = "${var.name}-"
  subnet_ids  = var.subnet_ids
}

resource "aws_db_instance" "this" {
  identifier             = var.name
  engine                 = "postgres"
  engine_version         = "18"
  instance_class         = "db.t4g.micro"
  allocated_storage      = 20
  db_name                = var.db_name
  username               = var.username
  password               = random_password.db.result
  db_subnet_group_name   = aws_db_subnet_group.this.name
  vpc_security_group_ids  = [var.sg_id]
  multi_az               = false
  publicly_accessible    = false
  skip_final_snapshot    = true
}
