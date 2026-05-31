variable "instance_id" { type = string }

resource "aws_iam_role" "scheduler" {
  name = "scalable-ecommerce-auto-stop"
  assume_role_policy = jsonencode({
    Version   = "2012-10-17"
    Statement = [{ Effect = "Allow", Principal = { Service = "scheduler.amazonaws.com" }, Action = "sts:AssumeRole" }]
  })
}

resource "aws_iam_role_policy" "scheduler" {
  role = aws_iam_role.scheduler.id
  policy = jsonencode({
    Version   = "2012-10-17"
    Statement = [{ Effect = "Allow", Action = "ec2:StopInstances", Resource = "*" }]
  })
}

resource "aws_scheduler_schedule" "auto_stop" {
  name                         = "scalable-ecommerce-auto-stop"
  schedule_expression          = "cron(30 6 * * ? *)"   # 06:30 UTC daily
  schedule_expression_timezone = "UTC"
  flexible_time_window { mode = "OFF" }

  target {
    arn      = "arn:aws:scheduler:::aws-sdk:ec2:stopInstances"
    role_arn = aws_iam_role.scheduler.arn
    input    = jsonencode({ InstanceIds = [var.instance_id] })
  }
}