variable "name" {
  type    = string
  default = "scalable-ecommerce"
}

variable "subnet_id" {
  type = string
}

variable "sg_id" {
  type = string
}

variable "instance_type" {
  type    = string
  default = "t3.small"
}

data "aws_ssm_parameter" "al2023" {
  name = "/aws/service/ami-amazon-linux-latest/al2023-ami-kernel-default-x86_64"
}

resource "aws_iam_role" "ec2" {
  name_prefix = "${var.name}-ec2-"
  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [{
      Action    = "sts:AssumeRole"
      Effect    = "Allow"
      Principal = { Service = "ec2.amazonaws.com" }
    }]
  })
}

resource "aws_iam_role_policy_attachment" "ssm" {
  role       = aws_iam_role.ec2.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore"
}

resource "aws_iam_role_policy_attachment" "cloudwatch" {
  role       = aws_iam_role.ec2.name
  policy_arn = "arn:aws:iam::aws:policy/CloudWatchAgentServerPolicy"
}

resource "aws_iam_instance_profile" "ec2" {
  name_prefix = "${var.name}-ec2-"
  role        = aws_iam_role.ec2.name
}

resource "aws_instance" "this" {
  ami                    = data.aws_ssm_parameter.al2023.value
  instance_type          = var.instance_type
  subnet_id              = var.subnet_id
  vpc_security_group_ids = [var.sg_id]
  iam_instance_profile   = aws_iam_instance_profile.ec2.name
  tags                   = { Name = var.name }
}

resource "aws_eip" "this" {
  domain   = "vpc"
  instance = aws_instance.this.id
  tags     = { Name = var.name }
}
