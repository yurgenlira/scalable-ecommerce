variable "name" {
  description = "ECR repository name"
  type        = string
}

variable "keep_images" {
  description = "Number of images to retain"
  type        = number
  default     = 10
}
