variable "instance_id" {
  type = string
}

variable "region" {
  type    = string
  default = "us-east-1"
}

resource "aws_cloudwatch_log_group" "app" {
  name              = "/scalable-ecommerce/prod/app"
  retention_in_days = 3
}

resource "aws_cloudwatch_log_group" "nginx" {
  name              = "/scalable-ecommerce/prod/nginx"
  retention_in_days = 3
}

resource "aws_cloudwatch_dashboard" "main" {
  dashboard_name = "scalable-ecommerce-l1"
  dashboard_body = jsonencode({
    widgets = [
      {
        type = "metric", x = 0, y = 0, width = 12, height = 6,
        properties = {
          title   = "EC2 CPU (%)",
          region  = var.region,
          stat    = "Average",
          period  = 60,
          metrics = [["ScalableEcommerce/L1", "cpu_usage_active", "InstanceId", var.instance_id]]
        }
      },
      {
        type = "log", x = 0, y = 6, width = 24, height = 6,
        properties = {
          title  = "App requests (/up incluido)",
          region = var.region,
          view   = "table",
          query  = "SOURCE '/scalable-ecommerce/prod/app' | fields @timestamp, context.request_id, context.path, context.status | sort @timestamp desc | limit 50"
        }
      }
    ]
  })
}
